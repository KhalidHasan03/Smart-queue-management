@extends('landing.layouts.landing')

@section('content')
<section class="pt-32 pb-20 bg-gradient-to-br from-slate-900 via-slate-800 to-teal-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-400 mb-4">Our Services</p>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
            Services we <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400">provide.</span>
        </h1>
        <p class="mt-6 text-lg text-slate-300 max-w-2xl mx-auto">From consultation to deployment, we handle everything so your team can focus on patients.</p>
    </div>
</section>

<section class="py-20 sm:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            @php
            $svcServices = [
                ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'System Setup & Training', 'desc' => 'We install Queue-Pro on your servers, configure departments, counters, and staff roles, then train your team on day-one usage.'],
                ['icon' => 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z', 'title' => 'Custom Integrations', 'desc' => 'Connect Queue-Pro with your existing hospital management system, EMR, or payment gateway through our REST API.'],
                ['icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'title' => '24/7 Support', 'desc' => 'Our support team is available around the clock via WhatsApp, phone, or email. Priority response for Enterprise plans.'],
                ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'title' => 'On-Site Consultation', 'desc' => 'Our team visits your facility to map patient flow, identify bottlenecks, and design the optimal queue layout.'],
                ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'title' => 'Annual Maintenance', 'desc' => 'Software updates, security patches, database backups, and performance monitoring included in every plan.'],
                ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'Analytics & Reporting', 'desc' => 'Custom reports on wait times, peak hours, department performance, and patient satisfaction metrics.'],
            ];
            @endphp

            @foreach($svcServices as $svc)
            <div class="group bg-white rounded-3xl border border-slate-100 p-8 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-teal-50 group-hover:bg-teal-100 flex items-center justify-center transition">
                    <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $svc['icon'] }}"/></svg>
                </div>
                <h3 class="mt-5 text-lg font-extrabold text-slate-900">{{ $svc['title'] }}</h3>
                <p class="mt-3 text-sm text-slate-500 leading-relaxed">{{ $svc['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-600 mb-3">Department Services</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Active departments in <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-600">{{ $clinicName }}</span></h2>
            <p class="mt-4 text-lg text-slate-500">Each department has dedicated doctors and counters.</p>
        </div>

        @if($services->count())
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($services as $svc)
            <div class="bg-white rounded-3xl border border-slate-100 p-8 shadow-sm hover:shadow-lg transition">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-400 flex items-center justify-center text-white font-extrabold text-lg shadow-lg shadow-teal-500/20">{{ $svc->prefix }}</div>
                    <div>
                        <h3 class="font-extrabold text-slate-900">{{ $svc->name }}</h3>
                        <p class="text-xs text-slate-400">Prefix: {{ $svc->prefix }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-sm text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $svc->doctors_count }} doctors
                    </span>
                    <span>Starting at {{ $svc->start_number }}</span>
                </div>
                @if($svc->doctors->count())
                <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                    @foreach($svc->doctors as $doc)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">{{ substr($doc->name, -2) }}</div>
                        <div>
                            <p class="text-sm font-semibold text-slate-700">{{ $doc->name }}</p>
                            <p class="text-xs text-slate-400">{{ $doc->specialization ?? 'General' }} @if($doc->room_no) &middot; Room {{ $doc->room_no }} @endif</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 bg-white rounded-3xl border border-slate-100">
            <p class="text-slate-400">No active departments yet. Contact us to get started.</p>
        </div>
        @endif
    </div>
</section>

<section class="py-20 bg-gradient-to-br from-slate-900 to-teal-900 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Need a custom solution?</h2>
        <p class="mt-4 text-lg text-slate-300">We tailor Queue-Pro to fit your hospital's exact workflow.</p>
        <a href="{{ route('landing.contact') }}" class="mt-8 inline-block px-8 py-4 rounded-2xl bg-white text-slate-900 font-bold text-lg hover:bg-slate-100 transition shadow-xl">Get in Touch</a>
    </div>
</section>
@endsection
