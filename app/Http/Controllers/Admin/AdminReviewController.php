<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Review;
use App\Models\Service;
use App\Support\ReviewFilters;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $stats = $this->stats();

        // Lightweight poll target for the auto-refresh banner: the same
        // visibility scope and filters, but figures only.
        if ($request->boolean('_pending_only')) {
            return response()->json([
                'pending' => $stats['pending'],
                'total' => $stats['total'],
            ]);
        }

        $reviews = $this->filtered($request)->paginate(20)->withQueryString();

        $services = Service::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $doctors = Doctor::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $categories = Review::CATEGORIES;

        return view('admin.reviews.index', compact('reviews', 'stats', 'services', 'doctors', 'categories'));
    }

    public function show(Review $review)
    {
        // Counter operators can read the detail page for their own counter
        // only, matching the guard on the approve/reject actions.
        $this->guardCounterScope($review);

        $review->loadMissing(['token.patient', 'token.service', 'token.doctor', 'token.counter', 'moderator']);

        return view('admin.reviews.show', compact('review'));
    }

    public function approve(Review $review)
    {
        $this->guardCounterScope($review);
        $review->moderate(Review::STATUS_APPROVED);

        return back()->with('success', 'Review approved and published.');
    }

    public function reject(Review $review)
    {
        $this->guardCounterScope($review);
        $review->moderate(Review::STATUS_REJECTED);

        return back()->with('success', 'Review rejected and hidden from public view.');
    }

    public function restore(Review $review)
    {
        $this->guardAdmin('Only an administrator can return a review to the moderation queue.');
        $review->moderate(Review::STATUS_PENDING);

        return back()->with('success', 'Review returned to the moderation queue.');
    }

    public function destroy(Review $review)
    {
        $this->guardAdmin('Only an administrator can delete a review.');
        $token = $review->token;
        $review->delete();

        // The visit becomes reviewable again so the patient can resubmit.
        $token?->update(['review_status' => $token->status === $token::COMPLETED ? 'pending' : 'none']);

        return back()->with('success', 'Review deleted permanently.');
    }

    /**
     * Bulk approve/reject from the moderation dashboard. Each review is run
     * through the same counter-scope guard as the single actions so an operator
     * can never bulk-touch another counter's feedback.
     */
    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:reviews,id'],
        ]);

        $status = $data['action'] === 'approve' ? Review::STATUS_APPROVED : Review::STATUS_REJECTED;
        $done = 0;
        $skipped = 0;

        foreach (Review::whereIn('id', $data['ids'])->get() as $review) {
            try {
                $this->guardCounterScope($review);
                $review->moderate($status);
                $done++;
            } catch (AuthorizationException) {
                $skipped++;
            }
        }

        $message = $done.' review'.($done === 1 ? '' : 's').' '.($status === Review::STATUS_APPROVED ? 'approved' : 'rejected').'.';

        if ($skipped > 0) {
            $message .= " {$skipped} skipped (outside your counter).";
        }

        return back()->with('success', $message);
    }

    /**
     * Aggregate figures for the moderation dashboard and the main dashboard.
     * Kept as a method because the main dashboard resolves this controller and
     * calls it directly; the maths itself lives in the shared layer.
     */
    public function stats()
    {
        return ReviewFilters::stats();
    }

    /**
     * A counter operator may only moderate feedback from their own counter.
     * Admins and super admins bypass the check.
     */
    protected function guardCounterScope(Review $review): void
    {
        $user = auth()->user();
        if (! $user || $user->isAdmin()) {
            return;
        }

        $counterId = $user->counter_id ?: $user->counter?->id;

        // Throw an AuthorizationException rather than abort() so the bulk
        // action can catch it per-review and report a skip, instead of the
        // whole request dying. Rendered as a 403 for the single actions.
        if (! $counterId || (int) $review->token?->counter_id !== (int) $counterId) {
            throw new AuthorizationException('You can only moderate feedback for your own counter.');
        }
    }

    protected function guardAdmin(string $message): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403, $message);
    }

    /**
     * Neutralise spreadsheet formula injection (CWE-1236).
     *
     * The comment and display name arrive from a public, unauthenticated kiosk,
     * so a value such as "=HYPERLINK(...)" or "=cmd|'/c calc'!A0" would be
     * executed by Excel/LibreOffice the moment a moderator opens the export.
     * Prefixing with an apostrophe forces the cell to be treated as text.
     */
    protected static function csvSafe(mixed $value): string
    {
        $value = (string) ($value ?? '');

        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$value;
        }

        return $value;
    }

    public function export(Request $request)
    {
        $reviews = $this->filtered($request)->get();

        $filename = 'reviews-export-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($reviews) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Token No', 'Visit Date', 'Rating', 'Rating Label', 'Moderation Status',
                'Category', 'Comment', 'Display Name', 'Anonymous', 'Published',
                'Service', 'Doctor', 'Counter', 'Moderated By',
                // Blank for anonymous reviews: the export is the artefact most
                // likely to be shared around, so it must not quietly undo the
                // anonymity the patient chose at the kiosk.
                'Patient Name', 'Patient Phone', 'Submitted At', 'Reviewed At',
            ]);

            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->id,
                    $review->token?->token_no ?? '',
                    $review->token?->token_date?->format('Y-m-d') ?? '',
                    $review->rating,
                    $review->rating_label,
                    $review->status,
                    self::csvSafe($review->category),
                    self::csvSafe($review->comment),
                    self::csvSafe($review->display_name),
                    $review->is_anonymous ? 'Yes' : 'No',
                    $review->is_approved ? 'Yes' : 'No',
                    self::csvSafe($review->token?->service?->name),
                    self::csvSafe($review->token?->doctor?->name),
                    self::csvSafe($review->token?->counter?->name),
                    // Auto-approved reviews never pass through a person.
                    self::csvSafe($review->moderator?->name),
                    $review->is_anonymous ? '' : self::csvSafe($review->patient?->name),
                    $review->is_anonymous ? '' : self::csvSafe($review->patient?->phone),
                    $review->created_at?->format('Y-m-d H:i:s') ?? '',
                    $review->reviewed_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Single source of truth for the review list and the CSV export so the
     * exported rows always match what the moderator is looking at.
     */
    protected function filtered(Request $request): Builder
    {
        return ReviewFilters::filtered($request);
    }
}
