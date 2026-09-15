@extends('landing.layouts.landing')

@section('content')
<section class="pt-32 pb-20 bg-gradient-to-br from-slate-900 via-slate-800 to-teal-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-400 mb-4">Products & Features</p>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
            Everything your hospital <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400">needs.</span>
        </h1>
        <p class="mt-6 text-lg text-slate-300 max-w-2xl mx-auto">Eight integrated modules that cover every aspect of patient flow management.</p>
    </div>
</section>

<section class="py-20 sm:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="group relative p-6 rounded-3xl bg-white border border-slate-100 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 group-hover:bg-teal-100 flex items-center justify-center transition">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900">Customer Flow Management</h3>
                <p class="mt-2 text-sm text-slate-500 leading-relaxed">Real-time patient queue tracking across departments and counters.</p>
            </div>

            <div class="group relative p-6 rounded-3xl bg-white border border-slate-100 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center transition">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900">Self-Service Kiosks</h3>
                <p class="mt-2 text-sm text-slate-500 leading-relaxed">Patients check in at kiosks with QR scanning or phone number entry.</p>
            </div>

            <div class="group relative p-6 rounded-3xl bg-white border border-slate-100 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 group-hover:bg-teal-100 flex items-center justify-center transition">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900">Digital Signage</h3>
                <p class="mt-2 text-sm text-slate-500 leading-relaxed">Any screen becomes a live display showing calling names and numbers.</p>
            </div>

            <div class="group relative p-6 rounded-3xl bg-white border border-slate-100 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center transition">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900">Patient Feedback</h3>
                <p class="mt-2 text-sm text-slate-500 leading-relaxed">Token-gated reviews ensure only real patients can leave feedback.</p>
            </div>

            <div class="group relative p-6 rounded-3xl bg-white border border-slate-100 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 group-hover:bg-teal-100 flex items-center justify-center transition">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900">e-Appointment Booking</h3>
                <p class="mt-2 text-sm text-slate-500 leading-relaxed">Patients book appointments online and auto-receive queue numbers.</p>
            </div>

            <div class="group relative p-6 rounded-3xl bg-white border border-slate-100 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center transition">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900">Digital Banking Kiosk</h3>
                <p class="mt-2 text-sm text-slate-500 leading-relaxed">Integrated payment kiosks for co-payments and fees.</p>
            </div>

            <div class="group relative p-6 rounded-3xl bg-white border border-slate-100 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 group-hover:bg-teal-100 flex items-center justify-center transition">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900">Visitor Management</h3>
                <p class="mt-2 text-sm text-slate-500 leading-relaxed">Track visitors, issue passes, and manage visitor flow.</p>
            </div>

            <div class="group relative p-6 rounded-3xl bg-white border border-slate-100 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center transition">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900">Smart Analytics</h3>
                <p class="mt-2 text-sm text-slate-500 leading-relaxed">Wait times, peak hours, department performance — all in one dashboard.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-600 mb-3">How It Works</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Three steps to a faster clinic.</h2>
                <div class="mt-8 space-y-6">
                    @foreach(['Register the patient at reception or kiosk — token is issued instantly.' => '01', 'Patient watches the live display and gets called to the right counter.' => '02', 'Staff completes the visit, token is marked done. Analytics updated in real-time.' => '03'] as $step => $num)
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center text-sm font-extrabold text-teal-700 shrink-0">{{ $num }}</div>
                        <p class="text-sm text-slate-600 leading-relaxed pt-2">{{ $step }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-white rounded-3xl border border-slate-100 p-8 shadow-lg">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-teal-400 flex items-center justify-center text-white font-bold">Q</div>
                    <div>
                        <p class="font-bold text-slate-900">Live Queue Board</p>
                        <p class="text-xs text-slate-500">Real-time status</p>
                    </div>
                </div>
                <div class="space-y-3">
                    @foreach(['G-001' => 'serving', 'G-002' => 'calling', 'G-003' => 'waiting', 'D-001' => 'completed'] as $tok => $status)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-sm font-bold text-slate-800">{{ $tok }}</span>
                        <span class="pill-{{ $status }}">{{ $status }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-gradient-to-br from-slate-900 to-teal-900 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Start using Queue-Pro today.</h2>
        <p class="mt-4 text-lg text-slate-300">Free 30-day trial. No card required. Cancel anytime.</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('landing') }}" class="px-8 py-4 rounded-2xl bg-white text-slate-900 font-bold text-lg hover:bg-slate-100 transition shadow-xl">Get Started Free</a>
            <a href="{{ route('landing.pricing') }}" class="px-8 py-4 rounded-2xl border border-white/20 text-white font-bold text-lg hover:bg-white/10 transition">View Pricing</a>
        </div>
    </div>
</section>
@endsection
