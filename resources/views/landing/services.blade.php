@extends('landing.layouts.landing')

@section('content')
<section class="pt-32 pb-20 bg-gradient-to-br from-sky-50 via-cyan-50 to-teal-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-sky-600 dark:text-cyan-400 mb-4">{{ $page['hero_subtitle'] }}</p>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-slate-900 dark:text-white">
            {{ $page['hero_title_1'] }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-teal-600">{{ $page['hero_title_2'] }}</span>
        </h1>
        <p class="mt-6 text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">{{ $page['hero_description'] }}</p>
    </div>
</section>

<section class="py-20 sm:py-28 bg-white dark:bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($page['svc_services'] as $svc)
            <div class="group bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 p-8 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-sky-50 dark:bg-cyan-900/30 group-hover:bg-sky-100 dark:group-hover:bg-cyan-900/50 flex items-center justify-center transition">
                    <svg class="w-7 h-7 text-sky-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $svc['icon'] }}"/></svg>
                </div>
                <h3 class="mt-5 text-lg font-extrabold text-slate-900 dark:text-white">{{ $svc['title'] }}</h3>
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ $svc['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-20 bg-slate-50 dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-sky-600 dark:text-cyan-400 mb-3">Department Services</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $page['dept_title'] }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-teal-600">{{ $clinicName }}</span></h2>
            <p class="mt-4 text-lg text-slate-500 dark:text-slate-400">{{ $page['dept_description'] }}</p>
        </div>

        @if($services->count())
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($services as $svc)
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 p-8 shadow-sm hover:shadow-lg transition">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-500 to-teal-400 flex items-center justify-center text-white font-extrabold text-lg shadow-lg shadow-sky-500/20">{{ $svc->prefix }}</div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white">{{ $svc->name }}</h3>
                        <p class="text-xs text-slate-400">Prefix: {{ $svc->prefix }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-sky-500 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $svc->doctors_count }} doctors
                    </span>
                    <span>Starting at {{ $svc->start_number }}</span>
                </div>
                @if($svc->doctors->count())
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 space-y-2">
                    @foreach($svc->doctors as $doc)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-500 dark:text-slate-400">{{ substr($doc->name, -2) }}</div>
                        <div>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $doc->name }}</p>
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
        <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700">
            <p class="text-slate-400 dark:text-slate-500">No active departments yet. Contact us to get started.</p>
        </div>
        @endif
    </div>
</section>

<section class="py-20 bg-gradient-to-br from-sky-50 to-teal-50 dark:from-slate-800 dark:to-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $page['cta_title'] }}</h2>
        <p class="mt-4 text-lg text-slate-500 dark:text-slate-400">{{ $page['cta_description'] }}</p>
        <a href="{{ route('landing.contact') }}" class="mt-8 inline-block px-8 py-4 rounded-2xl bg-gradient-to-r from-sky-600 to-teal-500 text-white font-bold text-lg hover:from-sky-500 hover:to-teal-400 transition shadow-xl">Get in Touch</a>
    </div>
</section>
@endsection
