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
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($page['items'] as $item)
            <div class="group bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 p-8 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl {{ $item['color'] === 'teal' ? 'bg-teal-50 dark:bg-teal-900/30 group-hover:bg-teal-100 dark:group-hover:bg-teal-900/50' : 'bg-emerald-50 dark:bg-emerald-900/30 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/50' }} flex items-center justify-center transition">
                    <svg class="w-7 h-7 {{ $item['color'] === 'teal' ? 'text-teal-600 dark:text-teal-400' : 'text-emerald-600 dark:text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/></svg>
                </div>
                <h3 class="mt-5 text-lg font-extrabold text-slate-900 dark:text-white">{{ $item['title'] }}</h3>
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ $item['desc'] }}</p>
                <ul class="mt-4 space-y-2">
                    @foreach($item['features'] as $feature)
                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                        <svg class="w-4 h-4 text-sky-500 dark:text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('landing.contact') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-sky-600 dark:text-cyan-400 hover:text-sky-700 dark:hover:text-cyan-300 transition">
                    Learn more
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-20 bg-gradient-to-br from-sky-50 to-teal-50 dark:from-slate-800 dark:to-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $page['cta_title'] }}</h2>
        <p class="mt-4 text-lg text-slate-500 dark:text-slate-400">{{ $page['cta_description'] }}</p>
        <a href="{{ route('landing.contact') }}" class="mt-8 inline-block px-8 py-4 rounded-2xl bg-gradient-to-r from-sky-600 to-teal-500 text-white font-bold text-lg hover:from-sky-500 hover:to-teal-400 transition shadow-xl">Contact Sales</a>
    </div>
</section>
@endsection
