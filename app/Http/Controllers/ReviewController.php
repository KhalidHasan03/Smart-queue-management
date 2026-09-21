<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Setting;
use App\Models\Token;
use App\Models\User;
use App\Notifications\NegativeReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    public function show(string $tokenNo)
    {
        $token = Token::with(['patient', 'service', 'doctor', 'counter'])
            ->where('token_no', $tokenNo)
            ->where('status', Token::COMPLETED)
            ->firstOrFail();

        if (! $token->canBeReviewed()) {
            if ($token->review?->exists) {
                return redirect()->route('review.success', $token->token_no);
            }
            abort(404, 'This visit is not eligible for review.');
        }

        $clinicName = Setting::get('clinic_name', config('app.name'));

        return view('review.show', compact('token', 'clinicName'));
    }

    public function store(Request $request, string $tokenNo)
    {
        $token = Token::with('patient')
            ->where('token_no', $tokenNo)
            ->where('status', Token::COMPLETED)
            ->firstOrFail();

        if (! $token->canBeReviewed()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'This visit has already been reviewed.'], 422);
            }

            return redirect()->route('review.success', $token->token_no);
        }

        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'category' => ['nullable', Rule::in(['service', 'wait_time', 'staff', 'facility', 'overall'])],
            'comment' => ['nullable', 'string', 'max:1000'],
            'display_name' => ['nullable', 'string', 'max:100'],
            'is_anonymous' => ['sometimes', 'boolean'],
            'honeypot' => ['nullable', 'string', 'max:0'], // Spam protection
        ]);

        $review = Review::create([
            'token_id' => $token->id,
            'patient_id' => $token->patient_id,
            'rating' => $request->integer('rating'),
            'category' => $request->input('category'),
            'comment' => $request->input('comment'),
            'display_name' => $request->input('display_name'),
            'is_anonymous' => $request->boolean('is_anonymous'),
            'is_approved' => false, // Requires admin approval
        ]);

        $token->update([
            'review_status' => 'submitted',
        ]);

        // Trigger notification for negative reviews (1-2 stars)
        if ($review->rating <= 2) {
            $this->notifyNegativeReview($review);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Review submitted successfully. Thank you for your feedback!',
                'redirect' => route('review.success', $token->token_no),
            ]);
        }

        return redirect()->route('review.success', $token->token_no);
    }

    public function success(string $tokenNo)
    {
        $token = Token::where('token_no', $tokenNo)->firstOrFail();
        $clinicName = Setting::get('clinic_name', config('app.name'));

        return view('review.success', compact('token', 'clinicName'));
    }

    protected function notifyNegativeReview(Review $review): void
    {
        $admins = User::whereIn('role', ['super_admin', 'admin'])
            ->where('is_active', true)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new NegativeReviewNotification($review));
        }
    }
}
