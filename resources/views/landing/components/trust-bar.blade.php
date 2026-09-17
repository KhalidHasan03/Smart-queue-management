<section class="py-16 sm:py-20 bg-white dark:bg-slate-950 border-y border-slate-100 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400 dark:text-slate-500">{{ $trust['title'] }}</p>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-8">
            @foreach($trust['logos'] as $logo)
            <div class="flex items-center gap-3 opacity-50 hover:opacity-100 transition-opacity duration-300 grayscale hover:grayscale-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $logo['color'] }} flex items-center justify-center shadow-md">
                    <span class="text-sm font-extrabold text-white">{{ $logo['initials'] }}</span>
                </div>
                <span class="text-sm font-bold text-slate-500 dark:text-slate-400 hidden sm:block">{{ $logo['name'] }}</span>
            </div>
            @endforeach
        </div>
        <div class="mt-10 text-center">
            <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold">{!! $trust['stat_text'] !!}</p>
        </div>
    </div>
</section>
