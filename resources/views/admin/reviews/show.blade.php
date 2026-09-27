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
                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-900 mb-3">Visit Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-slate-400">Token</p>
                            <p class="font-bold text-lg">{{ $review->token?->token_no ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Date</p>
                            <p>{{ $review->token?->token_date?->format('d M Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Service</p>
                            <p>{{ $review->token?->service?->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Doctor</p>
                            <p>{{ $review->token?->doctor?->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Counter</p>
                            <p>{{ $review->token?->counter?->name ?? 'N/A' }}@if ($review->token?->counter?->room_no) • R-{{ $review->token->counter->room_no }} @endif</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Token status</p>
                            <p><span class="pill-{{ $review->token?->status }}">{{ $review->token?->status ?? 'unknown' }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-3">Rating &amp; Category</h3>
                        <div class="flex items-center gap-3">
                            <span class="text-5xl leading-none" aria-hidden="true">{{ $review->rating_emoji }}</span>
                            <span class="text-lg font-semibold text-slate-800">{{ $review->rating_label }}</span>
                        </div>
                        @if ($review->category)
                            <div class="mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-700">
                                    {{ ucwords(str_replace('_', ' ', $review->category)) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-900 mb-3">Comment</h3>
                        @if ($review->comment)
                            <p class="text-slate-700 whitespace-pre-wrap">{{ $review->comment }}</p>
                        @else
                            <p class="text-slate-400">No comment provided.</p>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-900 mb-3">Patient Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-slate-400">Public author name</p>
                                <p>{{ $review->author_name }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Anonymous</p>
                                <p>{{ $review->is_anonymous ? 'Yes' : 'No' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Patient on file</p>
                                <p>{{ $review->patient?->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Phone on file</p>
                                <p>{{ $review->patient?->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if ($review->is_anonymous)
                            <p class="mt-3 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                                Submitted as anonymous — the name and phone above are internal and must not be published.
                            </p>
                        @endif
                    </div>

                    @if (auth()->user()->hasPermission('reviews.manage'))
                        <div class="pt-4 border-t border-slate-200">
                            <h3 class="font-semibold text-slate-900 mb-3">Moderation</h3>
                            @php
                                $statusStyles = [
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-rose-100 text-rose-700',
                                ];
                            @endphp
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusStyles[$review->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $review->status === 'approved' ? 'Approved & published' : ucfirst($review->status) }}
                                </span>

                                {{-- Audit line: who made the call, or that the system
                                     published it without a person involved. --}}
                                @if ($review->moderator)
                                    <p class="text-xs text-slate-500">
                                        <span class="font-semibold text-slate-700">{{ $review->moderator->name }}</span>
                                        {{ $review->status === 'pending' ? 'reopened this' : 'marked this as '.($review->status === 'approved' ? 'approved' : 'rejected').' on' }}
                                        {{ $review->reviewed_at?->format('d M Y, h:i A') }}.
                                    </p>
                                @elseif ($review->reviewed_at)
                                    <p class="text-xs text-slate-500">
                                        Published automatically on {{ $review->reviewed_at->format('d M Y, h:i A') }} — no moderator was involved.
                                    </p>
                                @else
                                    <p class="text-xs text-slate-500">Not yet moderated.</p>
                                @endif
                            </div>

                            <div class="mt-4">
                                @include('reviews.partials.actions', ['review' => $review])
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-900 mb-3">Meta</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-400">Submitted</dt>
                            <dd>{{ $review->created_at->format('d M Y, h:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Reviewed</dt>
                            <dd>{{ $review->reviewed_at?->format('d M Y, h:i A') ?? 'Not yet moderated' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Moderated by</dt>
                            <dd>{{ $review->moderator?->name ?? 'System (auto-published)' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Review ID</dt>
                            <dd class="font-mono">{{ $review->id }}</dd>
                        </div>
                    </dl>
                </div>

                <a href="{{ route('admin.reviews.index', array_filter(['status' => $review->status])) }}" class="qc-btn-soft w-full">
                    More {{ ucfirst($review->status) }} reviews
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
