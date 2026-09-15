<section id="products" class="py-24 sm:py-32 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-600 mb-3">Products & Solutions</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                Built for <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-600">modern hospitals.</span>
            </h2>
            <p class="mt-4 text-lg text-slate-500">Everything a hospital needs — from patient flow to digital signage.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @php
            $features = [
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>', 'title' => 'Customer Flow', 'desc' => 'Real-time patient queue tracking across departments.', 'gradient' => 'from-teal-500 to-emerald-500'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>', 'title' => 'Self-Service Kiosks', 'desc' => 'Patients check in at kiosks — no desk needed.', 'gradient' => 'from-cyan-500 to-blue-500'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>', 'title' => 'Digital Signage', 'desc' => 'Any screen becomes a live display.', 'gradient' => 'from-violet-500 to-purple-500'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>', 'title' => 'Patient Feedback', 'desc' => 'Token-gated reviews from real patients.', 'gradient' => 'from-pink-500 to-rose-500'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>', 'title' => 'e-Appointment', 'desc' => 'Book appointments online, auto-assign queue.', 'gradient' => 'from-blue-400 to-cyan-500'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'title' => 'Banking Kiosk', 'desc' => 'Integrated payment kiosks for co-payments.', 'gradient' => 'from-emerald-400 to-teal-500'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>', 'title' => 'Visitor Mgmt', 'desc' => 'Track visitors, issue passes, manage flow.', 'gradient' => 'from-amber-400 to-orange-500'],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>', 'title' => 'Smart Analytics', 'desc' => 'Wait times, peak hours, performance.', 'gradient' => 'from-red-400 to-pink-500'],
            ];
            @endphp

            @foreach($features as $f)
            <a href="{{ route('landing.features') }}" class="group relative p-6 rounded-3xl bg-white border border-slate-100 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $f['gradient'] }} flex items-center justify-center shadow-lg opacity-90 group-hover:opacity-100 group-hover:scale-105 transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $f['icon'] !!}</svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900">{{ $f['title'] }}</h3>
                <p class="mt-2 text-sm text-slate-500 leading-relaxed">{{ $f['desc'] }}</p>
            </a>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('landing.features') }}" class="inline-flex items-center gap-2 text-sm font-bold text-teal-600 hover:text-teal-700 transition">
                View all features
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>
</section>
