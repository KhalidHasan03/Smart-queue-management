@extends('landing.layouts.landing')

@section('content')
<section class="pt-32 pb-20 bg-gradient-to-br from-slate-900 via-slate-800 to-teal-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-400 mb-4">Pricing</p>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
            Simple, transparent <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400">pricing.</span>
        </h1>
        <p class="mt-6 text-lg text-slate-300 max-w-2xl mx-auto">Start free. Upgrade when you're ready. No hidden fees, no surprises.</p>
    </div>
</section>

<section id="pricing" class="py-20 sm:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-10 shadow-sm hover:shadow-lg transition">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">Starter</h3>
                        <p class="text-sm text-slate-400">For small clinics</p>
                    </div>
                </div>
                <div class="mb-6">
                    <span class="text-4xl font-extrabold text-slate-900">$490</span>
                    <span class="text-slate-400">/year</span>
                    <p class="text-sm text-slate-400 mt-1">~$41/month &middot; Billed annually</p>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach(['Up to 3 departments', 'Up to 5 staff users', '1 live display screen', 'Basic analytics dashboard', 'Token issuance & queue', 'Email support', '30-day free trial'] as $feature)
                    <li class="flex items-center gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('landing.contact') }}" class="block w-full text-center px-6 py-3.5 rounded-xl border-2 border-slate-200 text-slate-700 font-bold text-sm hover:border-teal-300 hover:text-teal-600 transition">Start Free Trial</a>
            </div>

            <div class="relative bg-gradient-to-br from-slate-900 to-teal-900 rounded-3xl p-8 sm:p-10 text-white shadow-2xl shadow-teal-900/30">
                <div class="absolute top-0 right-8 transform -translate-y-1/2">
                    <span class="px-4 py-1.5 rounded-full bg-teal-400 text-slate-900 text-xs font-extrabold uppercase tracking-wider">Most Popular</span>
                </div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold">Enterprise</h3>
                        <p class="text-sm text-teal-200">For hospitals & networks</p>
                    </div>
                </div>
                <div class="mb-6">
                    <span class="text-4xl font-extrabold">Custom</span>
                    <p class="text-sm text-teal-200 mt-1">Tailored to your needs</p>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach(['Unlimited departments', 'Unlimited staff users', 'Multiple display screens', 'Advanced analytics & reports', 'Custom integrations (API)', 'Self-service kiosks', '24/7 priority support', 'On-site setup & training', 'Annual maintenance contract'] as $feature)
                    <li class="flex items-center gap-3 text-sm text-teal-100">
                        <svg class="w-5 h-5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('landing.contact') }}" class="block w-full text-center px-6 py-3.5 rounded-xl bg-white text-slate-900 font-bold text-sm hover:bg-slate-100 transition shadow-lg">Contact Sales</a>
            </div>
        </div>

        <div class="mt-16 text-center">
            <p class="text-sm text-slate-500">All plans include a <strong class="text-slate-700">30-day free trial</strong>. No credit card required. Cancel anytime.</p>
        </div>
    </div>
</section>

<section class="py-20 bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-slate-900">Frequently asked questions</h2>
        </div>
        <div class="space-y-3" x-data="{ open: null }">
            @php
            $faqs = [
                ['q' => 'Is there really a free trial?', 'a' => 'Yes. Every plan starts with a 30-day free trial — no card required. You can cancel anytime during the trial period.'],
                ['q' => 'Can I upgrade from Starter to Enterprise later?', 'a' => 'Absolutely. You can upgrade at any time. We prorate the difference so you only pay what you owe.'],
                ['q' => 'Do you offer discounts for non-profits?', 'a' => 'Yes. We offer special pricing for non-profit hospitals and government health facilities. Contact us for details.'],
                ['q' => 'What happens to my data if I cancel?', 'a' => 'Your data stays available for 30 days after cancellation. You can export everything before that. After 30 days, data is permanently deleted.'],
            ];
            @endphp
            @foreach($faqs as $i => $faq)
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden transition-all duration-300"
                 :class="open === {{ $i }} ? 'shadow-[0_8px_30px_rgb(0,0,0,0.06)] border-teal-200' : 'hover:border-slate-200'">
                <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-5 text-left gap-4">
                    <span class="text-sm font-bold text-slate-900">{{ $faq['q'] }}</span>
                    <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform duration-300" :class="open === {{ $i }} ? 'rotate-180 text-teal-500' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === {{ $i }}" x-collapse x-cloak>
                    <div class="px-6 pb-5 text-sm text-slate-500 leading-relaxed">{{ $faq['a'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
