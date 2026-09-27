{{--
    Permission-aware moderation buttons for one review.

    Every moderation surface (admin list, admin detail, counter page) includes
    this so the available actions can never drift between them. Each button
    submits the surrounding form via `formaction`, which is why none of them is
    wrapped in its own <form> — nested forms are invalid HTML and silently drop
    the CSRF token.

    @param \App\Models\Review $review
--}}
<div class="flex items-center gap-2 flex-wrap">
    <a href="{{ route('admin.reviews.show', $review) }}" class="qc-btn-secondary text-xs py-1 px-2">View</a>

    @if(auth()->user()->hasPermission('reviews.manage'))
        @if($review->status !== \App\Models\Review::STATUS_APPROVED)
            <button type="submit" formaction="{{ route('admin.reviews.approve', $review) }}"
                    name="_method" value="PATCH" class="qc-btn-success text-xs py-1 px-2"
                    title="Approve and publish this review"
                    onclick="return confirm('Approve and publish this review?')">Approve</button>
        @endif

        @if($review->status !== \App\Models\Review::STATUS_REJECTED)
            <button type="submit" formaction="{{ route('admin.reviews.reject', $review) }}"
                    name="_method" value="PATCH" class="qc-btn-warning text-xs py-1 px-2"
                    title="Reject — hidden from public view"
                    onclick="return confirm('Reject this review? It will be hidden from public view.')">Reject</button>
        @endif
    @endif

    @if(auth()->user()->hasPermission('reviews.delete'))
        @if($review->status !== \App\Models\Review::STATUS_PENDING)
            <button type="submit" formaction="{{ route('admin.reviews.restore', $review) }}"
                    name="_method" value="PATCH" class="qc-btn-soft text-xs py-1 px-2"
                    title="Return to the moderation queue">Reopen</button>
        @endif
        <button type="submit" formaction="{{ route('admin.reviews.destroy', $review) }}"
                name="_method" value="DELETE" class="qc-btn-danger text-xs py-1 px-2"
                title="Delete permanently"
                onclick="return confirm('Delete this review permanently?')">Delete</button>
    @endif
</div>
