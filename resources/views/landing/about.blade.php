@extends('landing.layouts.landing')

@section('content')
<section class="pt-32 pb-20 bg-gradient-to-br from-sky-50 via-cyan-50 to-teal-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-sky-600 dark:text-cyan-400 mb-4">{{ $page['hero_subtitle'] }}</p>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-slate-900 dark:text-white">
            {{ $page['hero_title_1'] }}<br>{{ $page['hero_title_2'] }}
        </h1>
        <p class="mt-6 text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">{{ $page['hero_description'] }}</p>
    </div>
</section>

<section class="py-20 sm:py-28 bg-white dark:bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-sky-600 dark:text-cyan-400 mb-3">{{ $page['mission_subtitle'] }}</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $page['mission_title'] }}</h2>
                <p class="mt-5 text-lg text-slate-500 dark:text-slate-400 leading-relaxed">{{ $page['mission_description'] }}</p>
                <div class="mt-8 grid grid-cols-2 gap-6">
                    @foreach($page['stats'] as $stat)
                    <div class="p-5 rounded-2xl {{ $loop->first ? 'bg-sky-50 dark:bg-cyan-900/20 border border-sky-100 dark:border-cyan-800/50' : 'bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700' }}">
                        <p class="text-3xl font-extrabold {{ $loop->first ? 'text-sky-600 dark:text-cyan-400' : 'text-slate-900 dark:text-white' }}">{{ $stat['value'] }}</p>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">{{ $stat['label'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-br from-sky-200 to-teal-100 dark:from-cyan-800/20 dark:to-teal-900/20 rounded-3xl rotate-3 scale-105 opacity-50"></div>
                <div class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 p-8 sm:p-10">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-500 to-teal-400 flex items-center justify-center text-2xl font-extrabold text-white shadow-lg">Q</div>
                        <div>
                            <p class="font-extrabold text-slate-900 dark:text-white text-lg">{{ $clinicName }}</p>
                            <p class="text-sm text-slate-400">Live Queue Dashboard</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach(['Waiting patients served instantly' => 'emerald', 'Staff assigned per counter' => 'sky', 'Live display for waiting area' => 'slate'] as $item => $color)
                        <div class="flex items-center gap-3 p-3 rounded-xl @if($color === 'emerald') bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50 @elseif($color === 'sky') bg-sky-50 dark:bg-cyan-900/20 border border-sky-100 dark:border-cyan-800/50 @else bg-slate-50 dark:bg-slate-700 border border-slate-100 dark:border-slate-600 @endif">
                            <div class="w-8 h-8 rounded-lg @if($color === 'emerald') bg-emerald-100 dark:bg-emerald-800/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 @elseif($color === 'sky') bg-sky-100 dark:bg-cyan-800/30 flex items-center justify-center text-sky-600 dark:text-cyan-400 @else bg-slate-100 dark:bg-slate-600 flex items-center justify-center text-slate-600 dark:text-slate-300 @endif">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $item }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="clients" class="py-20 bg-slate-50 dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-sky-600 dark:text-cyan-400 mb-3">Trusted By</p>
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $page['clients_title'] }}</h2>
        <p class="mt-4 text-lg text-slate-500 dark:text-slate-400 max-w-xl mx-auto">{{ $page['clients_description'] }}</p>
        <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($page['clients'] as $client)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-cyan-900/30 flex items-center justify-center mx-auto mb-3">
                    <span class="text-xl font-extrabold text-sky-600 dark:text-cyan-400">{{ substr($client['name'], 0, 1) }}</span>
                </div>
                <p class="font-bold text-slate-900 dark:text-white">{{ $client['name'] }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $client['city'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section id="partners" class="py-20 bg-white dark:bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-sky-600 dark:text-cyan-400 mb-3">Partners</p>
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $page['partners_title'] }}</h2>
        <p class="mt-4 text-lg text-slate-500 dark:text-slate-400 max-w-xl mx-auto">{{ $page['partners_description'] }}</p>
        <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($page['partners'] as $partner)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 shadow-sm">
                <p class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $partner['name'] }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $partner['type'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-20 bg-gradient-to-br from-sky-50 to-teal-50 dark:from-slate-800 dark:to-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $page['cta_title'] }}</h2>
        <p class="mt-4 text-lg text-slate-500 dark:text-slate-400">{{ $page['cta_description'] }}</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('landing') }}" class="px-8 py-4 rounded-2xl bg-gradient-to-r from-sky-600 to-teal-500 text-white font-bold text-lg hover:from-sky-500 hover:to-teal-400 transition shadow-xl">Get Started Free</a>
            <a href="{{ route('landing.contact') }}" class="px-8 py-4 rounded-2xl border-2 border-sky-200 dark:border-sky-700 text-sky-700 dark:text-cyan-400 font-bold text-lg hover:bg-sky-50 dark:hover:bg-sky-900/30 transition">Contact Sales</a>
        </div>
    </div>
</section>
@endsection
