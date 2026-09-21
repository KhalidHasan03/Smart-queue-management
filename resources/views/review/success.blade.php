<x-guest-layout>
    @section('page-title', 'Review Submitted')
    <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
        <div class="max-w-md w-full text-center">
            <div class="qc-card p-8 sm:p-10">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-emerald-100 mb-6">
                    <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-slate-900 mb-2">Thank You!</h1>
                <p class="text-slate-600 mb-6">Your review has been submitted successfully.</p>

                <div class="bg-slate-50 rounded-lg p-4 mb-6 text-left">
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
                        <span class="font-medium text-indigo-600">Pending Approval</span>
                    </div>
                </div>

                <p class="text-sm text-slate-500 mb-6">
                    Our team will review your feedback and publish it shortly.
                    You'll receive a notification once it's live.
                </p>

                <div class="flex gap-3">
                    <a href="{{ route('landing') }}"
                       class="flex-1 qc-btn-primary py-3 text-center">
                        Back to Home
                    </a>
                    <a href="{{ route('landing.services') }}"
                       class="flex-1 qc-btn-secondary py-3 text-center">
                        Book Another Visit
                    </a>
                </div>
            </div>

            <p class="mt-6 text-sm text-slate-500">
                <a href="{{ route('landing') }}" class="text-indigo-600 hover:underline">
                    {{ $clinicName }}
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>