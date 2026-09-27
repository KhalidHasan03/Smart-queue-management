{{--
    The review list. Shared by the admin dashboard and the counter page.

    The whole table is wrapped in one bulk-action form. Row actions use
    `formaction` + `name="_method"` rather than nested <form> elements, which
    are invalid HTML and would drop the CSRF token from the submission.

    @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $reviews
    @param bool $showCounter  whether to render the counter column
--}}
@php
    $statusStyles = [
        'pending' => 'bg-amber-100 text-amber-700',
        'approved' => 'bg-emerald-100 text-emerald-700',
        'rejected' => 'bg-rose-100 text-rose-700',
    ];
    $canBulk = auth()->user()->hasPermission('reviews.manage');
@endphp

<form method="POST" action="{{ route('admin.reviews.bulk') }}">
    @csrf
    <div class="qc-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full qc-table">
                <thead>
                    <tr>
                        @if($canBulk)
                            <th class="w-10"><input type="checkbox" x-data="{}" x-ref="all" @change="$el.checked = ! $el.checked; $el.closest('table').querySelectorAll('input[name=\"ids[]\"]').forEach(i => i.checked = $el.checked)" class="w-4 h-4 accent-indigo-600" title="Select all on this page"></th>
                        @endif
                        <th>Token</th>
                        <th>Date</th>
                        <th>Author</th>
                        <th>Rating</th>
                        <th>Category</th>
                        <th>Comment</th>
                        @if($showCounter ?? true)<th>Service / Doctor</th>@endif
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr class="hover:bg-slate-50">
                            @if($canBulk)
                                <td class="py-4">
                                    <input type="checkbox" name="ids[]" value="{{ $review->id }}" class="w-4 h-4 accent-indigo-600" title="Select {{ $review->token?->token_no }}">
                                </td>
                            @endif
                            <td class="py-4 font-mono text-sm">{{ $review->token?->token_no ?? 'N/A' }}</td>
                            <td class="py-4 text-sm">{{ $review->token?->token_date?->format('d M Y') ?? '' }}</td>
                            <td class="py-4 text-sm">
                                {{ $review->author_name }}
                                @if ($review->is_anonymous)
                                    <span class="block text-xs text-slate-400">Anonymous</span>
                                @endif
                            </td>
                            <td class="py-4 whitespace-nowrap">
                                <span class="text-2xl leading-none">{{ $review->rating_emoji }}</span>
                                <span class="ml-1 text-sm font-semibold text-slate-600">{{ $review->rating_label }}</span>
                            </td>
                            <td class="py-4 text-sm">
                                @if($review->category)
                                    {{ ucwords(str_replace('_', ' ', $review->category)) }}
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 text-sm max-w-xs">
                                @if ($review->comment)
                                    <div class="truncate" title="{{ $review->comment }}">{{ $review->comment }}</div>
                                @else
                                    <span class="text-slate-400">No comment</span>
                                @endif
                            </td>
                            @if($showCounter ?? true)
                                <td class="py-4 text-sm">
                                    <div>{{ $review->token?->service?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-400">
                                        {{ $review->token?->doctor?->name ?? 'N/A' }}
                                        @if($review->token?->counter)
                                            • {{ $review->token->counter->name }}
                                        @endif
                                    </div>
                                </td>
                            @endif
                            <td class="py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusStyles[$review->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($review->status) }}
                                </span>
                                @if($review->moderator)
                                    <span class="block mt-1 text-[10px] text-slate-400" title="Moderated by {{ $review->moderator->name }} at {{ $review->reviewed_at?->format('d M Y, h:i A') }}">
                                        by {{ $review->moderator->name }}
                                    </span>
                                @elseif($review->reviewed_at)
                                    <span class="block mt-1 text-[10px] text-slate-400" title="Published automatically at {{ $review->reviewed_at->format('d M Y, h:i A') }}">
                                        auto-published
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 text-sm">
                                @include('reviews.partials.actions', ['review' => $review])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ ($canBulk ? 1 : 0) + 9 }}" class="px-4 py-14 text-center">
                                @if($reviews->total() === 0)
                                    <p class="text-4xl leading-none">💬</p>
                                    <p class="font-bold text-slate-700 mt-2">No feedback yet</p>
                                    <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                                        Reviews appear once a patient rates a completed visit at the kiosk.
                                        Share the kiosk link — it is printed on every token slip.
                                    </p>
                                    <div class="mt-4 flex items-center justify-center gap-2">
                                        <code class="text-xs bg-slate-100 rounded-lg px-3 py-1.5 text-slate-600 break-all">{{ route('review.kiosk') }}</code>
                                        <button type="button" class="qc-btn-soft text-xs" x-data x-on:click="navigator.clipboard.writeText(@js(route('review.kiosk'))); $el.textContent = 'Copied ✓'">Copy link</button>
                                    </div>
                                @else
                                    <p class="font-bold text-slate-700">No reviews match these filters</p>
                                    <p class="text-sm text-slate-500 mt-1">
                                        {{ number_format($reviews->total()) }} review{{ $reviews->total() === 1 ? '' : 's' }} exist for this view, but none match the current selection.
                                    </p>
                                    <a href="{{ route(request()->routeIs('admin.reviews.*') ? 'admin.reviews.index' : 'queue.reviews') }}" class="qc-btn-soft mt-4">Clear filters</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->total() > 0)
            <div class="px-5 py-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs text-slate-500">
                    Showing <b>{{ $reviews->firstItem() }}–{{ $reviews->lastItem() }}</b> of <b>{{ number_format($reviews->total()) }}</b> review{{ $reviews->total() === 1 ? '' : 's' }}
                </p>
                @if($canBulk)
                    <div class="flex items-center gap-2">
                        <button name="action" value="approve" class="qc-btn-success text-xs py-1.5 px-3" onclick="return confirm('Approve the selected reviews?')">✓ Approve selected</button>
                        <button name="action" value="reject" class="qc-btn-danger-soft text-xs py-1.5 px-3" onclick="return confirm('Reject the selected reviews?')">✕ Reject selected</button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</form>

@if($reviews->hasPages())
    <div class="mt-6">{{ $reviews->links() }}</div>
@endif
