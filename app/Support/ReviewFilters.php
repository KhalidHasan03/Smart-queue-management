<?php

namespace App\Support;

use App\Models\Review;
use App\Models\Token;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * The review list query, filters and aggregates, shared by the admin
 * moderation dashboard and the counter-scoped operator page so both always
 * show the same thing and the CSV export matches what is on screen.
 */
class ReviewFilters
{
    /**
     * The named one-click views shown as chips above the list.
     *
     * @var array<string, string>
     */
    public const PRESETS = [
        'needs_attention' => 'Needs attention',
        'awaiting' => 'Awaiting moderation',
        'negative' => 'Negative only',
        'no_comment' => 'No comment',
        'published_this_week' => 'Published this week',
    ];

    /**
     * Base query: relations, ordering, and the caller's visibility scope.
     */
    public static function query(?User $user = null): Builder
    {
        $query = Review::with(['token.service', 'token.doctor', 'token.counter', 'patient', 'moderator'])
            ->orderByDesc('created_at');

        return $user ? $query->forUser($user) : $query;
    }

    /**
     * Apply the request's query-string filters on top of the base query.
     */
    public static function apply(Request $request, Builder $query, ?User $user = null): Builder
    {
        $query = $user ? $query->forUser($user) : $query;

        if ($request->filled('preset') && isset(self::PRESETS[$request->input('preset')])) {
            match ($request->input('preset')) {
                'needs_attention' => $query
                    ->where('status', Review::STATUS_PENDING)
                    ->where('rating', '<=', Review::NEGATIVE_THRESHOLD),
                'awaiting' => $query->where('status', Review::STATUS_PENDING),
                'negative' => $query->where('rating', '<=', Review::NEGATIVE_THRESHOLD),
                'no_comment' => $query->where(fn ($q) => $q->whereNull('comment')->orWhere('comment', '')),
                'published_this_week' => $query
                    ->where('status', Review::STATUS_APPROVED)
                    ->where('reviewed_at', '>=', now()->subDays(7)),
            };
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->integer('rating'));
        }

        if ($request->filled('status')) {
            $status = (string) $request->input('status');
            $query->where('status', in_array($status, Review::STATUSES, true) ? $status : Review::STATUS_PENDING);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('service_id')) {
            $query->whereHas('token', fn ($q) => $q->where('service_id', $request->integer('service_id')));
        }

        if ($request->filled('doctor_id')) {
            $query->whereHas('token', fn ($q) => $q->where('doctor_id', $request->integer('doctor_id')));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhereHas('token', fn ($tq) => $tq->where('token_no', 'like', "%{$search}%"));
            });
        }

        return $query;
    }

    /**
     * Single entry point: base query + request filters + user scope.
     */
    public static function filtered(Request $request, ?User $user = null): Builder
    {
        return self::apply($request, self::query(), $user);
    }

    /**
     * Aggregate figures for the moderation dashboard and the main dashboard.
     * Pass a user to scope every figure to what that user may see.
     *
     * @return array<string, mixed>
     */
    public static function stats(?User $user = null): array
    {
        $scope = fn (Builder $q) => $user ? $q->forUser($user) : $q;

        $total = $scope(Review::query())->count();
        $approved = $scope(Review::query())->where('status', Review::STATUS_APPROVED)->count();
        $pending = $scope(Review::query())->where('status', Review::STATUS_PENDING)->count();
        $rejected = $scope(Review::query())->where('status', Review::STATUS_REJECTED)->count();
        $negativePending = $scope(Review::query())
            ->where('status', Review::STATUS_PENDING)
            ->where('rating', '<=', Review::NEGATIVE_THRESHOLD)
            ->count();
        $avg = (float) ($scope(Review::query())->avg('rating') ?? 0);

        // Only visits that actually carry a code can be rated, so legacy rows
        // migrated before the review code existed must not inflate the funnel.
        $eligibleQuery = Token::where('status', Token::COMPLETED)->whereNotNull('review_code');

        if ($user && ! $user->isAdmin()) {
            $eligibleQuery->where('counter_id', $user->counter_id ?: $user->counter?->id ?: 0);
        }

        $eligible = (clone $eligibleQuery)->count();
        $reviewed = (clone $eligibleQuery)->whereHas('review')->count();
        $unreviewed = max(0, $eligible - $reviewed);

        $distribution = $scope(Review::query())
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->all();

        return [
            'total' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'rejected' => $rejected,
            'negative_pending' => $negativePending,
            'average' => round($avg, 2),
            'eligible' => $eligible,
            'reviewed' => $reviewed,
            'unreviewed' => $unreviewed,
            'response_rate' => $eligible > 0 ? round(($reviewed / $eligible) * 100) : 0,
            'distribution' => $distribution,
        ];
    }
}
