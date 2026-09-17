<section id="products" class="py-24 sm:py-32 bg-slate-50 dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-600 dark:text-cyan-400 mb-3">{{ $homeFeatures['subtitle'] }}</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                {{ $homeFeatures['title'] }}
            </h2>
            <p class="mt-4 text-lg text-slate-500 dark:text-slate-400">{{ $homeFeatures['description'] }}</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($homeFeatures['items'] as $f)
            <a href="{{ route('landing.features') }}" class="group relative p-6 rounded-3xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-[0_2px_20px_rgb(0,0,0,0.04)] dark:shadow-[0_2px_20px_rgb(0,0,0,0.2)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)] dark:hover:shadow-[0_8px_40px_rgb(0,0,0,0.3)] hover:-translate-y-1 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $f['gradient'] }} flex items-center justify-center shadow-lg opacity-90 group-hover:opacity-100 group-hover:scale-105 transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $f['icon'] }}"/></svg>
                </div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900 dark:text-white">{{ $f['title'] }}</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ $f['desc'] }}</p>
            </a>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('landing.features') }}" class="inline-flex items-center gap-2 text-sm font-bold text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 transition">
                View all features
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>
</section>
