@extends('landing.layouts.landing')

@section('content')
<section class="pt-32 pb-20 bg-gradient-to-br from-slate-900 via-slate-800 to-teal-900 text-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-400 mb-4">About Queue-Pro</p>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
            Modernizing patient<br>flow <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400">since 2020.</span>
        </h1>
        <p class="mt-6 text-lg text-slate-300 max-w-2xl mx-auto">Queue-Pro was built to solve a simple problem: patients waiting too long and clinics losing control of their flow. We give both sides a better experience.</p>
    </div>
</section>

<section class="py-20 sm:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-600 mb-3">Our Mission</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Reduce wait times to zero.</h2>
                <p class="mt-5 text-lg text-slate-500 leading-relaxed">Every minute a patient waits is a minute the clinic could be serving someone else. Queue-Pro eliminates idle time, removes confusion, and gives both staff and patients full visibility of the queue.</p>
                <div class="mt-8 grid grid-cols-2 gap-6">
                    <div class="p-5 rounded-2xl bg-teal-50 border border-teal-100">
                        <p class="text-3xl font-extrabold text-teal-600">40%</p>
                        <p class="text-sm text-slate-600 mt-1">Average reduction in wait time</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-3xl font-extrabold text-slate-900">3x</p>
                        <p class="text-sm text-slate-600 mt-1">More patients served per day</p>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-br from-teal-200 to-emerald-100 rounded-3xl rotate-3 scale-105 opacity-50"></div>
                <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-100 p-8 sm:p-10">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-400 flex items-center justify-center text-2xl font-extrabold text-white shadow-lg">Q</div>
                        <div>
                            <p class="font-extrabold text-slate-900 text-lg">{{ $clinicName }}</p>
                            <p class="text-sm text-slate-400">Live Queue Dashboard</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach(['Waiting patients served instantly' => 'emerald', 'Staff assigned per counter' => 'teal', 'Live display for waiting area' => 'slate'] as $item => $color)
                        <div class="flex items-center gap-3 p-3 rounded-xl @if($color === 'emerald') bg-emerald-50 border border-emerald-100 @elseif($color === 'teal') bg-teal-50 border border-teal-100 @else bg-slate-50 border border-slate-100 @endif">
                            <div class="w-8 h-8 rounded-lg @if($color === 'emerald') bg-emerald-100 flex items-center justify-center text-emerald-600 @elseif($color === 'teal') bg-teal-100 flex items-center justify-center text-teal-600 @else bg-slate-100 flex items-center justify-center text-slate-600 @endif">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">{{ $item }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="clients" class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-600 mb-3">Trusted By</p>
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Hospitals that trust Queue-Pro.</h2>
        <p class="mt-4 text-lg text-slate-500 max-w-xl mx-auto">From small clinics to large hospital networks, Queue-Pro scales to fit any size.</p>
        <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach(['Al-Shifa Hospital' => 'Amman', 'Jordan Medical Center' => 'Irbid', 'Royal Health Clinic' => 'Zarqa', 'City Hospital' => 'Aqaba'] as $name => $city)
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center mx-auto mb-3">
                    <span class="text-xl font-extrabold text-teal-600">{{ substr($name, 0, 1) }}</span>
                </div>
                <p class="font-bold text-slate-900">{{ $name }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $city }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section id="partners" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-600 mb-3">Partners</p>
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Built with trusted technology partners.</h2>
        <p class="mt-4 text-lg text-slate-500 max-w-xl mx-auto">We integrate with the tools hospitals already use.</p>
        <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach(['Laravel' => 'Framework', 'MySQL' => 'Database', 'Tailwind CSS' => 'UI', 'Alpine.js' => 'Interactivity'] as $name => $type)
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <p class="text-xl font-extrabold text-slate-900">{{ $name }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $type }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-20 bg-gradient-to-br from-slate-900 to-teal-900 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Ready to modernize your hospital?</h2>
        <p class="mt-4 text-lg text-slate-300">Start your free 30-day trial today. No card required.</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('landing') }}" class="px-8 py-4 rounded-2xl bg-white text-slate-900 font-bold text-lg hover:bg-slate-100 transition shadow-xl">Get Started Free</a>
            <a href="{{ route('landing.contact') }}" class="px-8 py-4 rounded-2xl border border-white/20 text-white font-bold text-lg hover:bg-white/10 transition">Contact Sales</a>
        </div>
    </div>
</section>
@endsection
