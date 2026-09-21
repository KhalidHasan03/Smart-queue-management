<x-app-layout>
    @section('page-title', 'Review Details')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-extrabold tracking-tight">Review Details</h2>
            <a href="{{ route('admin.reviews.index') }}" class="qc-btn-secondary">← Back</a>
        </div>
    </x-slot>

    <div class="qc-card p-6">
        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Token Info -->
                <div class="bg-slate-50 rounded-lg p-4">
                    <h3 class="font-semibold text-slate-900 mb-3">Visit Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-slate-400">Token</p>
                            <p class="font-bold text-lg">{{ $review->token->token_no }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Date</p>
                            <p>{{ $review->token->token_date?->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Service</p>
                            <p>{{ $review->token->service->name }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Doctor</p>
                            <p>{{ $review->token->doctor->name }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Counter</p>
                            <p>{{ $review->token->counter->name }}@if($review->token->counter->room_no) • R-{{ $review->token->counter->room_no }}@endif</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Status</p>
                            <p><span class="pill-{{ $review->token->status }}">{{ $review->token->status }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Review Details -->
                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-3">Rating & Category</h3>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="text-3xl {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-300' }}">★</span>
                                @endfor
                            </div>
                            <span class="text-lg font-semibold">{{ $review->rating }}/5</span>
                        </div>
                        @if($review->category)
                            <div class="mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-700">
                                    {{ ucfirst(str_replace('_', ' ', $review->category)) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-900 mb-3">Comment</h3>
                        <p class="text-slate-700 whitespace-pre-wrap">{{ $review->comment ?? '<span class="text-slate-400">No comment provided</span>' }}</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-900 mb-3">Patient Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-slate-400">Display Name</p>
                                <p>{{ $review->is_anonymous ? 'Anonymous' : ($review->display_name ?? 'Not provided') }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Anonymous</p>
                                <p>{{ $review->is_anonymous ? 'Yes' : 'No' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Actual Name</p>
                                <p>{{ $review->token->patient->name }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Phone</p>
                                <p>{{ $review->token->patient->phone }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-200">
                        <h3 class="font-semibold text-slate-900 mb-3">Moderation</h3>
                        <div class="flex items-center gap-4">
                            @if($review->is_approved)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-700">Approved & Published</span>
                                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="qc-btn-warning"
                                            onclick="return confirm('Reject this review? It will be hidden from public view.')">Reject</button>
                                </form>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-700">Pending Approval</span>
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="qc-btn-success"
                                            onclick="return confirm('Approve and publish this review?')">Approve</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-slate-50 rounded-lg p-4">
                    <h3 class="font-semibold text-slate-900 mb-3">Meta</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-400">Submitted</dt>
                            <dd>{{ $review->created_at->format('d M Y, h:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Review ID</dt>
                            <dd class="font-mono">{{ $review->id }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-slate-50 rounded-lg p-4">
                    <h3 class="font-semibold text-slate-900 mb-3">Actions</h3>
                    <div class="space-y-2">
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="qc-btn-danger w-full"
                                    onclick="return confirm('Delete this review permanently? This cannot be undone.')">
                                Delete Permanently
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>