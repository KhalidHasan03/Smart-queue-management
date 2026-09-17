<section id="faq" class="py-24 sm:py-32 bg-slate-50 dark:bg-slate-900">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-600 dark:text-cyan-400 mb-3">{{ $homeFaq['subtitle'] }}</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                {{ $homeFaq['title'] }}
            </h2>
        </div>
        <div class="space-y-3" x-data="{ open: null }">
            @foreach($homeFaq['items'] as $i => $faq)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-300"
                 :class="open === {{ $i }} ? 'shadow-[0_8px_30px_rgb(0,0,0,0.06)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.3)] border-cyan-200 dark:border-cyan-700' : 'hover:border-slate-200 dark:hover:border-slate-600'">
                <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-5 text-left gap-4">
                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $faq['q'] }}</span>
                     <svg class="w-5 h-5 text-slate-400 dark:text-slate-500 shrink-0 transition-transform duration-300" :class="open === {{ $i }} ? 'rotate-180 text-cyan-500 dark:text-cyan-400' : ''"
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
