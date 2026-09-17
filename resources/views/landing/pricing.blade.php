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

<section id="pricing" class="py-20 sm:py-28 bg-white dark:bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            @foreach($page['plans'] as $plan)
            @if($plan['dark'] ?? false)
            <div class="relative bg-gradient-to-br from-slate-900 to-teal-900 dark:from-slate-800 dark:to-slate-900 rounded-3xl p-8 sm:p-10 text-white shadow-2xl shadow-teal-900/30 dark:shadow-slate-900/50 border border-slate-700/50">
                @if($plan['badge'] ?? false)
                <div class="absolute top-0 right-8 transform -translate-y-1/2">
                    <span class="px-4 py-1.5 rounded-full bg-teal-400 text-slate-900 text-xs font-extrabold uppercase tracking-wider">{{ $plan['badge'] }}</span>
                </div>
                @endif
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold">{{ $plan['name'] }}</h3>
                        <p class="text-sm text-teal-200">{{ $plan['subtitle'] }}</p>
                    </div>
                </div>
                <div class="mb-6">
                    <span class="text-4xl font-extrabold">{{ $plan['price'] }}</span>
                    <span>{{ $plan['period'] }}</span>
                    <p class="text-sm text-teal-200 mt-1">{{ $plan['note'] }}</p>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach($plan['features'] as $feature)
                    <li class="flex items-center gap-3 text-sm text-teal-100">
                        <svg class="w-5 h-5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('landing.contact') }}" class="block w-full text-center px-6 py-3.5 rounded-xl bg-white text-slate-900 font-bold text-sm hover:bg-slate-100 transition shadow-lg">{{ $plan['cta'] }}</a>
            </div>
            @else
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 p-8 sm:p-10 shadow-sm hover:shadow-lg transition">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                        <svg class="w-6 h-6 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $plan['name'] }}</h3>
                        <p class="text-sm text-slate-400">{{ $plan['subtitle'] }}</p>
                    </div>
                </div>
                <div class="mb-6">
                    <span class="text-4xl font-extrabold text-slate-900 dark:text-white">{{ $plan['price'] }}</span>
                    <span class="text-slate-400">{{ $plan['period'] }}</span>
                    <p class="text-sm text-slate-400 mt-1">{{ $plan['note'] }}</p>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach($plan['features'] as $feature)
                    <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                        <svg class="w-5 h-5 text-sky-500 dark:text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('landing.contact') }}" class="block w-full text-center px-6 py-3.5 rounded-xl border-2 border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-bold text-sm hover:border-sky-300 dark:hover:border-cyan-600 hover:text-sky-600 dark:hover:text-cyan-400 transition">{{ $plan['cta'] }}</a>
            </div>
            @endif
            @endforeach
        </div>

        <div class="mt-16 text-center">
            <p class="text-sm text-slate-500 dark:text-slate-400">{!! $page['trial_note'] !!}</p>
        </div>
    </div>
</section>

<section class="py-20 bg-slate-50 dark:bg-slate-900">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $page['faqs_title'] }}</h2>
        </div>
        <div class="space-y-3" x-data="{ open: null }">
            @foreach($page['faqs'] as $i => $faq)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-300"
                 :class="open === {{ $i }} ? 'shadow-[0_8px_30px_rgb(0,0,0,0.06)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.3)] border-sky-200 dark:border-cyan-700' : 'hover:border-slate-200 dark:hover:border-slate-600'">
                <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-5 text-left gap-4">
                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $faq['q'] }}</span>
                    <svg class="w-5 h-5 text-slate-400 dark:text-slate-500 shrink-0 transition-transform duration-300" :class="open === {{ $i }} ? 'rotate-180 text-sky-500 dark:text-cyan-400' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === {{ $i }}" x-collapse x-cloak>
                    <div class="px-6 pb-5 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ $faq['a'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
