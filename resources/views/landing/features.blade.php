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
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($page['items'] as $item)
            <div class="group relative p-6 rounded-3xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-[0_2px_20px_rgb(0,0,0,0.04)] dark:shadow-[0_2px_20px_rgb(0,0,0,0.2)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)] dark:hover:shadow-[0_8px_40px_rgb(0,0,0,0.3)] hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl {{ $item['color'] === 'teal' ? 'bg-teal-50 dark:bg-teal-900/30 group-hover:bg-teal-100 dark:group-hover:bg-teal-900/50' : 'bg-emerald-50 dark:bg-emerald-900/30 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/50' }} flex items-center justify-center transition">
                    <svg class="w-6 h-6 {{ $item['color'] === 'teal' ? 'text-teal-600 dark:text-teal-400' : 'text-emerald-600 dark:text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/></svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900 dark:text-white">{{ $item['title'] }}</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-20 bg-slate-50 dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-sky-600 dark:text-cyan-400 mb-3">How It Works</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $page['how_it_works_title'] }}</h2>
                <div class="mt-8 space-y-6">
                    @foreach($page['steps'] as $step)
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-cyan-900/30 flex items-center justify-center text-sm font-extrabold text-sky-700 dark:text-cyan-400 shrink-0">{{ $step['num'] }}</div>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed pt-2">{{ $step['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 p-8 shadow-lg">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-teal-400 flex items-center justify-center text-white font-bold">Q</div>
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white">Live Queue Board</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Real-time status</p>
                    </div>
                </div>
                <div class="space-y-3">
                    @foreach(['G-001' => 'serving', 'G-002' => 'calling', 'G-003' => 'waiting', 'D-001' => 'completed'] as $tok => $status)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700">
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $tok }}</span>
                        <span class="pill-{{ $status }}">{{ $status }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-gradient-to-br from-sky-50 to-teal-50 dark:from-slate-800 dark:to-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $page['cta_title'] }}</h2>
        <p class="mt-4 text-lg text-slate-500 dark:text-slate-400">{{ $page['cta_description'] }}</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('landing') }}" class="px-8 py-4 rounded-2xl bg-gradient-to-r from-sky-600 to-teal-500 text-white font-bold text-lg hover:from-sky-500 hover:to-teal-400 transition shadow-xl">Get Started Free</a>
            <a href="{{ route('landing.pricing') }}" class="px-8 py-4 rounded-2xl border-2 border-sky-200 dark:border-sky-700 text-sky-700 dark:text-cyan-400 font-bold text-lg hover:bg-sky-50 dark:hover:bg-sky-900/30 transition">View Pricing</a>
        </div>
    </div>
</section>
@endsection
