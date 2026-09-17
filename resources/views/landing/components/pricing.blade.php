<section id="pricing" class="py-24 sm:py-32 bg-white dark:bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-600 dark:text-cyan-400 mb-3">{{ $homePricing['subtitle'] }}</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                {{ $homePricing['title'] }}
            </h2>
            <p class="mt-4 text-lg text-slate-500 dark:text-slate-400">{{ $homePricing['description'] }}</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            @foreach($homePricing['plans'] as $plan)
            @if($plan['dark'] ?? false)
            <div class="relative bg-[#0c1a1a] dark:bg-gradient-to-br dark:from-slate-800 dark:to-slate-900 rounded-3xl p-8 lg:p-10 text-white overflow-hidden shadow-2xl shadow-teal-950/30 dark:shadow-slate-900/50 border border-slate-700/50 dark:border-slate-600/50">
                <div class="absolute inset-0 bg-gradient-to-br from-cyan-600/10 via-teal-600/10 to-emerald-600/10"></div>
                <div class="absolute -top-24 -right-24 w-48 h-48 rounded-full bg-cyan-500/10 blur-[80px]"></div>
                <div class="relative">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-cyan-400 to-teal-400 flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold">{{ $plan['name'] }}</h3>
                            <span class="text-[10px] font-bold text-cyan-300 uppercase tracking-widest bg-cyan-500/20 px-2 py-0.5 rounded-full">{{ $plan['badge'] }}</span>
                        </div>
                    </div>
                    <p class="text-sm text-cyan-200/60 mb-6">{{ $plan['subtitle'] ?? '' }}</p>
                    <div class="flex items-baseline gap-1 mb-8">
                        <span class="text-5xl font-extrabold">{{ $plan['price'] }}</span>
                    </div>
                    <p class="text-sm text-cyan-200/40 mb-8">{{ $plan['note'] }}</p>
                    <div class="space-y-3 mb-8">
                        @foreach($plan['features'] as $feature)
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-cyan-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span class="text-sm font-semibold text-cyan-100/80">{{ $feature }}</span>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('landing.contact') }}" class="block w-full py-3.5 text-center text-sm font-bold text-white bg-gradient-to-r from-cyan-400 to-teal-400 rounded-2xl shadow-lg shadow-cyan-400/20 hover:shadow-cyan-400/40 transition-all duration-200 active:scale-[0.98]">
                        {{ $plan['cta'] }}
                    </a>
                    <p class="text-center text-xs text-cyan-200/40 mt-3">Custom pricing for your hospital</p>
                </div>
            </div>
            @else
            <div class="relative bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-[0_8px_40px_rgb(0,0,0,0.06)] dark:shadow-[0_8px_40px_rgb(0,0,0,0.3)] p-8 lg:p-10 hover:shadow-[0_12px_60px_rgb(0,0,0,0.1)] dark:hover:shadow-[0_12px_60px_rgb(0,0,0,0.4)] transition-shadow duration-300">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $plan['name'] }}</h3>
                        <span class="text-[10px] font-bold text-cyan-600 dark:text-cyan-400 uppercase tracking-widest bg-cyan-50 dark:bg-cyan-900/30 px-2 py-0.5 rounded-full">{{ $plan['badge'] }}</span>
                    </div>
                </div>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-5xl font-extrabold text-slate-900 dark:text-white">{{ $plan['price'] }}</span>
                    <span class="text-sm text-slate-400 font-semibold">{{ $plan['period'] }}</span>
                </div>
                <p class="text-sm text-slate-400 mb-8">{{ $plan['note'] }}</p>
                <div class="space-y-3 mb-8">
                    @foreach($plan['features'] as $feature)
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-cyan-500 dark:text-cyan-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('landing.contact') }}" class="block w-full py-3.5 text-center text-sm font-bold text-white bg-gradient-to-r from-cyan-600 to-teal-500 rounded-2xl shadow-lg shadow-cyan-600/20 hover:shadow-cyan-600/40 hover:from-cyan-500 hover:to-teal-400 transition-all duration-200 active:scale-[0.98]">
                    {{ $plan['cta'] }}
                </a>
                <p class="text-center text-xs text-slate-400 mt-3">No card required</p>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</section>
