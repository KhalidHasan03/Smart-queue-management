<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKioskReviewRequest;
use App\Http\Requests\VerifyReviewCodeRequest;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Token;
use App\Models\User;
use App\Notifications\NegativeReviewNotification;
use App\Support\ReviewCode;
use App\Support\ReviewSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * The patient-facing feedback kiosk. A single screen that walks a patient
     * from their printed review code to a submitted rating, then resets so the
     * next patient never sees the previous one's details.
     */
    public function index(Request $request)
    {
        abort_unless(ReviewSettings::enabled(), 404);

        $clinicName = Setting::get('clinic_name', config('app.name'));
        $ratings = Review::RATINGS;
        $categories = Review::CATEGORIES;
        $idleSeconds = ReviewSettings::idleSeconds();
        $requireCommentBelow = ReviewSettings::requireCommentBelow();
        $prefillCode = ReviewCode::normalize($request->query('code'));

        return response()->view('review.kiosk', compact(
            'clinicName',
            'ratings',
            'categories',
            'idleSeconds',
            'requireCommentBelow',
            'prefillCode'
        ))
            // A shared kiosk screen must never hand the next patient a cached
            // copy of the previous one's visit details.
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Called by the kiosk when it idles or a patient presses back. Clears the
     * server-side proof so a replayed submit cannot use the previous patient's
     * verified visit.
     */
    public function resetSession(Request $request)
    {
        $request->session()->forget('review_kiosk_verified');

        return response()->json(['ok' => true])->header('Cache-Control', 'no-store');
    }

    /**
     * Step 1: resolve the review code and confirm the patient attended by
     * matching the last 4 digits of the phone on file.
     *
     * A code alone is never enough, and the failure message is deliberately
     * identical for an unknown code, an ineligible visit and a wrong phone
     * number so the endpoint cannot be used to probe which codes are real.
     */
    public function verify(VerifyReviewCodeRequest $request)
    {
        $this->abortIfKioskDisabled();

        $token = Token::with(['patient', 'service', 'doctor'])
            ->where('review_code', $request->validated('code'))
            ->first();

        $phoneLast4 = (string) ($token?->patient?->phone ?? '');

        if (! $token || ! $token->canBeReviewed() || substr($phoneLast4, -4) !== $request->validated('phone_last_4')) {
            throw ValidationException::withMessages([
                'code' => 'We could not match that code and phone number to a completed visit.',
            ]);
        }

        // The rating step reads this back so the phone check cannot be skipped
        // by posting straight to the submit endpoint.
        $request->session()->put('review_kiosk_verified', $token->id);

        return response()->json([
            'ok' => true,
            'visit' => [
                'token_no' => $token->token_no,
                'service' => $token->service->name,
                'doctor' => $token->doctor->name ?? '—',
                'date' => $token->token_date->format('d M Y'),
                'first_name' => $this->maskedFirstName($token->patient?->name ?? ''),
            ],
        ]);
    }

    /**
     * Step 2: record the rating. Ratings at or above the configured threshold
     * publish immediately; anything lower waits for moderation.
     */
    public function submit(StoreKioskReviewRequest $request)
    {
        $this->abortIfKioskDisabled();

        $token = $this->verifiedToken($request);

        $rating = (int) $request->validated('rating');
        $autoApprove = ReviewSettings::autoApproveThresholdReached($rating);

        $review = Review::create([
            'token_id' => $token->id,
            'patient_id' => $token->patient_id,
            'rating' => $rating,
            'category' => $request->validated('category'),
            'comment' => $request->validated('comment'),
            'display_name' => $request->validated('display_name'),
            // Default to anonymous server-side: a client that omits the flag
            // must never cause the patient's real name to be published.
            'is_anonymous' => $request->has('is_anonymous')
                ? $request->boolean('is_anonymous')
                : true,
            'is_approved' => $autoApprove,
            'status' => $autoApprove ? Review::STATUS_APPROVED : Review::STATUS_PENDING,
            'reviewed_at' => $autoApprove ? now() : null,
        ]);

        $token->update([
            'review_status' => $autoApprove ? Review::STATUS_APPROVED : 'submitted',
        ]);

        // Only escalate what actually lands in the moderation queue.
        if ($review->isPending() && $review->isNegative()) {
            $this->notifyNegativeReview($review);
        }

        $request->session()->forget('review_kiosk_verified');

        return response()->json([
            'ok' => true,
            'published' => $autoApprove,
            'rating' => $review->rating_label,
            'emoji' => $review->rating_emoji,
            'message' => $autoApprove
                ? 'Thank you! Your feedback is now live.'
                : 'Thank you! Our team will review your feedback personally.',
        ]);
    }

    /**
     * Standalone confirmation page for a patient who comes back later with the
     * code on their slip. Resolved by the unguessable review code, never by a
     * sequential token number.
     */
    public function thanks(Request $request)
    {
        $this->abortIfKioskDisabled();

        $token = Token::with(['review', 'service'])
            ->where('review_code', ReviewCode::normalize($request->query('code')))
            ->whereHas('review')
            ->first();

        abort_unless($token, 404);

        $clinicName = Setting::get('clinic_name', config('app.name'));

        return response()->view('review.thanks', compact('token', 'clinicName'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Turning the kiosk off must close every step of the flow, not just the
     * landing page — otherwise verify/submit would still accept ratings while
     * the screen looks switched off.
     */
    protected function abortIfKioskDisabled(): void
    {
        abort_unless(ReviewSettings::enabled(), 404);
    }

    /**
     * The verified token recorded during step 1, rejected if it is no longer
     * eligible (for example staff deleted the review in between).
     */
    protected function verifiedToken(Request $request): Token
    {
        $token = Token::with('patient')->find($request->session()->get('review_kiosk_verified'));

        if (! $token || ! $token->canBeReviewed()) {
            throw ValidationException::withMessages([
                'code' => 'This session has expired. Please enter your review code again.',
            ]);
        }

        return $token;
    }

    /**
     * "Rahim U." — enough for the patient to recognise their own visit, not
     * enough to identify them to the next person at a shared screen.
     */
    protected function maskedFirstName(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $first = $parts[0] ?? '';
        $last = count($parts) > 1 ? mb_substr((string) end($parts), 0, 1) : '';

        return trim($first.($last !== '' ? ' '.$last.'.' : ''));
    }

    protected function notifyNegativeReview(Review $review): void
    {
        $admins = User::whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN])
            ->where('is_active', true)
            ->get();

        foreach ($admins as $admin) {
            try {
                $admin->notify(new NegativeReviewNotification($review));
            } catch (\Throwable $e) {
                // The review is already stored and visible in the moderation
                // queue, so a mail/notification failure must never turn the
                // patient's submission into an error on a public kiosk.
                report($e);
            }
        }
    }
}
