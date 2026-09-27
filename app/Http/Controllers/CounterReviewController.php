<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Service;
use App\Support\ReviewFilters;
use Illuminate\Http\Request;

/**
 * The operator-facing review page at /queue/reviews.
 *
 * It is deliberately the same list, filters and actions as the admin
 * moderation dashboard — same query layer, same blade partials — but scoped to
 * the signed-in operator's own counter. Admins and super admins keep the
 * clinic-wide view at /admin/reviews; this page is the counter panel's
 * shortcut into it and never widens a non-admin's scope.
 */
class CounterReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->hasPermission('reviews.manage')) {
            abort(403);
        }

        $reviews = ReviewFilters::filtered($request, $user)->paginate(20)->withQueryString();
        $stats = ReviewFilters::stats($user);

        $counter = $user->counter()->with('service')->first();

        // Only the dropdowns that still make sense for this operator: their
        // own service and the doctors working it.
        $services = $counter
            ? Service::where('id', $counter->service_id)->get(['id', 'name'])
            : collect();
        $doctors = $counter
            ? Doctor::where('service_id', $counter->service_id)->where('is_active', true)
                ->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('queue.reviews', [
            'reviews' => $reviews,
            'stats' => $stats,
            'counter' => $counter,
            'services' => $services,
            'doctors' => $doctors,
        ]);
    }
}
