@extends('landing.layouts.landing')

@section('content')
<section class="pt-32 pb-20 bg-gradient-to-br from-slate-900 via-slate-800 to-teal-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-400 mb-4">Industries</p>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
            Queue management for <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400">every industry.</span>
        </h1>
        <p class="mt-6 text-lg text-slate-300 max-w-2xl mx-auto">From hospitals to banks, Queue-Pro adapts to any environment where people wait.</p>
    </div>
</section>

<section class="py-20 sm:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="group bg-white rounded-3xl border border-slate-100 p-8 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-teal-50 group-hover:bg-teal-100 flex items-center justify-center transition">
                    <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="mt-5 text-lg font-extrabold text-slate-900">Healthcare</h3>
                <p class="mt-3 text-sm text-slate-500 leading-relaxed">Hospitals, clinics, labs, and pharmacies. Reduce patient wait times by 40%. Manage multiple departments, doctors, and counters from one dashboard.</p>
                <ul class="mt-4 space-y-2">
                    @foreach(['Patient flow management', 'Doctor queue assignment', 'Waiting room displays', 'Token-based reviews'] as $item)
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <svg class="w-4 h-4 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('landing.contact') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-teal-600 hover:text-teal-700 transition">
                    Learn more
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>

            <div class="group bg-white rounded-3xl border border-slate-100 p-8 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center transition">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="mt-5 text-lg font-extrabold text-slate-900">Banking & Finance</h3>
                <p class="mt-3 text-sm text-slate-500 leading-relaxed">Banks, insurance offices, and financial institutions. Manage teller queues, priority customers, and service windows efficiently.</p>
                <ul class="mt-4 space-y-2">
                    @foreach(['Teller queue management', 'Priority customer routing', 'Digital banking kiosks', 'Service time analytics'] as $item)
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <svg class="w-4 h-4 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('landing.contact') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-teal-600 hover:text-teal-700 transition">
                    Learn more
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>

            <div class="group bg-white rounded-3xl border border-slate-100 p-8 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-teal-50 group-hover:bg-teal-100 flex items-center justify-center transition">
                    <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                </div>
                <h3 class="mt-5 text-lg font-extrabold text-slate-900">Government</h3>
                <p class="mt-3 text-sm text-slate-500 leading-relaxed">Municipalities, civil services, and public offices. Handle high-volume walk-in traffic with fair, transparent queue ordering.</p>
                <ul class="mt-4 space-y-2">
                    @foreach(['Walk-in queue management', 'Multi-department routing', 'Public display screens', 'Wait time forecasting'] as $item)
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <svg class="w-4 h-4 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('landing.contact') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-teal-600 hover:text-teal-700 transition">
                    Learn more
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>

            <div class="group bg-white rounded-3xl border border-slate-100 p-8 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center transition">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <h3 class="mt-5 text-lg font-extrabold text-slate-900">Education</h3>
                <p class="mt-3 text-sm text-slate-500 leading-relaxed">Universities, schools, and training centers. Manage student services, admissions, and administrative counters.</p>
                <ul class="mt-4 space-y-2">
                    @foreach(['Student service desks', 'Admissions flow', 'Exam scheduling queues', 'Parent visit management'] as $item)
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <svg class="w-4 h-4 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('landing.contact') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-teal-600 hover:text-teal-700 transition">
                    Learn more
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>

            <div class="group bg-white rounded-3xl border border-slate-100 p-8 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-teal-50 group-hover:bg-teal-100 flex items-center justify-center transition">
                    <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                </div>
                <h3 class="mt-5 text-lg font-extrabold text-slate-900">Retail</h3>
                <p class="mt-3 text-sm text-slate-500 leading-relaxed">Retail stores, service centers, and customer support desks. Manage customer flow during peak hours.</p>
                <ul class="mt-4 space-y-2">
                    @foreach(['Customer service queues', 'Return & exchange flow', 'Peak hour management', 'Customer satisfaction tracking'] as $item)
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <svg class="w-4 h-4 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('landing.contact') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-teal-600 hover:text-teal-700 transition">
                    Learn more
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>

            <div class="group bg-white rounded-3xl border border-slate-100 p-8 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center transition">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="mt-5 text-lg font-extrabold text-slate-900">Others</h3>
                <p class="mt-3 text-sm text-slate-500 leading-relaxed">Telecom offices, visa centers, salons, repair shops — any business where customers wait for service.</p>
                <ul class="mt-4 space-y-2">
                    @foreach(['Telecom service centers', 'Visa & immigration offices', 'Salon & spa bookings', 'Repair & service shops'] as $item)
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <svg class="w-4 h-4 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('landing.contact') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-teal-600 hover:text-teal-700 transition">
                    Learn more
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-gradient-to-br from-slate-900 to-teal-900 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Ready for your industry?</h2>
        <p class="mt-4 text-lg text-slate-300">Queue-Pro adapts to any environment. Let's build your custom solution.</p>
        <a href="{{ route('landing.contact') }}" class="mt-8 inline-block px-8 py-4 rounded-2xl bg-white text-slate-900 font-bold text-lg hover:bg-slate-100 transition shadow-xl">Contact Sales</a>
    </div>
</section>
@endsection
