<x-guest-layout>
    @section('page-title', 'Feedback Received')

    <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
        <div class="max-w-md w-full text-center">
            <div class="qc-card p-8 sm:p-10">
                <div class="text-6xl mb-4" aria-hidden="true">🎉</div>

                <h1 class="text-2xl font-bold text-slate-900 mb-2">Thank you!</h1>
                <p class="text-slate-600 mb-6">Your feedback has been received.</p>

                @if ($token->review)
                    <div class="flex flex-col items-center gap-2 rounded-2xl bg-slate-50 p-6 mb-6">
                        <span class="text-5xl leading-none" aria-hidden="true">{{ $token->review->rating_emoji }}</span>
                        <p class="font-bold text-slate-800">{{ $token->review->rating_label }}</p>
                        @if ($token->review->comment)
                            <p class="text-sm text-slate-600 italic mt-1">“{{ $token->review->comment }}”</p>
                        @endif
                    </div>
                @endif

                <div class="bg-slate-50 rounded-xl p-4 mb-6 text-left">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-slate-500">Token</span>
                        <span class="font-bold text-slate-900">{{ $token->token_no }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-slate-500">Service</span>
                        <span class="text-slate-900">{{ $token->service->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Status</span>
                        <span class="font-medium {{ $token->review?->status === \App\Models\Review::STATUS_APPROVED ? 'text-emerald-600' : 'text-amber-600' }}">
                            @if ($token->review?->status === \App\Models\Review::STATUS_APPROVED)
                                Published
                            @else
                                Being checked by our team
                            @endif
                        </span>
                    </div>
                </div>

                <p class="text-sm text-slate-500 mb-6">
                    @if ($token->review?->status === \App\Models\Review::STATUS_APPROVED)
                        Your feedback is now live. Thanks for helping us improve.
                    @else
                        Our team checks every response before it goes live. Thanks for helping us improve.
                    @endif
                </p>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('landing') }}" class="flex-1 qc-btn-primary py-3 text-center">
                        Back to Home
                    </a>
                    <a href="{{ route('landing.services') }}" class="flex-1 qc-btn-secondary py-3 text-center">
                        Book Another Visit
                    </a>
                </div>
            </div>

            <p class="mt-6 text-sm text-slate-500">
                <a href="{{ route('landing') }}" class="text-teal-600 hover:underline">
                    {{ $clinicName }}
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>
