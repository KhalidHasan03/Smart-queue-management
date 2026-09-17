<section id="how-it-works" class="py-24 sm:py-32 bg-white dark:bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-600 dark:text-cyan-400 mb-3">{{ $howItWorks['subtitle'] }}</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                {{ $howItWorks['title'] }}
            </h2>
            <p class="mt-4 text-lg text-slate-500 dark:text-slate-400">{{ $howItWorks['description'] }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
            @foreach($howItWorks['steps'] as $step)
            <div class="relative group">
                @if(!$loop->last)
                <div class="hidden md:block absolute top-12 left-[60%] w-[80%] h-[2px] bg-gradient-to-r from-cyan-200 dark:from-cyan-800 to-transparent"></div>
                @endif
                <div class="relative bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 p-8 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="w-16 h-16 rounded-2xl bg-cyan-50 dark:bg-cyan-900/30 group-hover:bg-cyan-100 dark:group-hover:bg-cyan-900/50 flex items-center justify-center mb-5 transition">
                        <svg class="w-8 h-8 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $step['icon'] }}"/></svg>
                    </div>
                    <div class="absolute top-6 right-6 text-5xl font-extrabold text-cyan-100 dark:text-cyan-900/40 group-hover:text-cyan-200 dark:group-hover:text-cyan-800/60 transition">{{ $step['num'] }}</div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $step['title'] }}</h3>
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
