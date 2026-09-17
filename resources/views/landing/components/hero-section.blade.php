<section class="relative min-h-[95vh] flex items-center overflow-hidden dark:bg-slate-950" style="background: radial-gradient(1400px 800px at 15% -5%,#0891b2 0%,transparent 50%),radial-gradient(1200px 700px at 85% 5%,#06b6d4 0%,transparent 45%),radial-gradient(900px 500px at 50% 100%,#0e7490 0%,transparent 45%),linear-gradient(170deg,#f0fdfa 0%,#ecfeff 40%,#f8fffe 100%)">
    <div class="dark:hidden absolute top-10 right-[5%] w-[600px] h-[600px] rounded-full bg-cyan-200/20 blur-[120px] pointer-events-none"></div>
    <div class="dark:hidden absolute bottom-0 left-[10%] w-[500px] h-[500px] rounded-full bg-teal-200/15 blur-[100px] pointer-events-none"></div>
    <div class="dark:hidden absolute top-1/3 right-1/4 w-[400px] h-[400px] rounded-full bg-sky-200/10 blur-[150px] pointer-events-none"></div>
    <div class="dark:hidden absolute inset-0 opacity-[0.03]" style="background-image:radial-gradient(circle,#164e63 1px,transparent 1px);background-size:40px 40px"></div>

    <div class="dark:hidden absolute top-10 right-[5%] w-[600px] h-[600px] rounded-full bg-cyan-800/10 blur-[120px] pointer-events-none hidden dark:block"></div>
    <div class="dark:hidden absolute bottom-0 left-[10%] w-[500px] h-[500px] rounded-full bg-teal-800/10 blur-[100px] pointer-events-none hidden dark:block"></div>

    <div class="relative max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-20 lg:pt-36 lg:pb-28">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-cyan-50 dark:bg-cyan-900/30 border border-cyan-100 dark:border-cyan-800/50 mb-8">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-cyan-600 dark:text-cyan-400 uppercase tracking-widest">{{ $hero['badge'] }}</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-[3.2rem] xl:text-6xl font-extrabold leading-[1.05] tracking-tight text-slate-900 dark:text-white">
                    {{ $hero['title_1'] }}<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-teal-600">{{ $hero['title_2'] }}</span>
                </h1>

                <p class="mt-5 text-lg sm:text-xl text-slate-500 dark:text-slate-400 leading-relaxed max-w-xl">
                    {{ $hero['description'] }}
                </p>

                <div class="flex flex-wrap gap-3 mt-10">
                    <a href="{{ route('display') }}" target="_blank"
                       class="group inline-flex items-center gap-2 px-8 py-4 text-base font-bold text-white bg-gradient-to-r from-cyan-600 to-teal-500 rounded-2xl shadow-xl shadow-cyan-600/25 hover:shadow-cyan-600/40 hover:from-cyan-500 hover:to-teal-400 transition-all duration-300 active:scale-[0.97]">
                        {{ $hero['cta_primary'] }}
                        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="#how-it-works"
                       class="inline-flex items-center gap-2 px-8 py-4 text-base font-bold text-cyan-600 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-900/30 rounded-2xl hover:bg-cyan-100 dark:hover:bg-cyan-900/50 transition-all duration-300">
                        {{ $hero['cta_secondary'] }}
                    </a>
                </div>

                <div class="flex flex-wrap gap-6 mt-10">
                    @foreach($hero['badges'] as $badge)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400">{{ $badge }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="relative hidden lg:block">
                <div class="absolute -inset-8 bg-gradient-to-r from-cyan-200/30 via-teal-200/30 to-emerald-200/30 rounded-3xl blur-3xl dark:from-cyan-800/20 dark:via-teal-800/20 dark:to-emerald-800/20"></div>

                <div class="relative bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl shadow-slate-200/50 dark:shadow-slate-900/50 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-gradient-to-r from-cyan-50 to-teal-50 dark:from-slate-800 dark:to-slate-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-teal-400 flex items-center justify-center text-sm font-extrabold text-white shadow-lg">Q</div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $clinicName ?? 'Queue-Pro Hospital' }}</p>
                                <p class="text-[10px] text-cyan-500 dark:text-cyan-400 uppercase tracking-widest">Live Queue Display</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">LIVE</span>
                        </div>
                    </div>

                    <div class="p-5 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl p-4 border border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <p class="text-[10px] font-bold text-cyan-500 dark:text-cyan-400 uppercase tracking-widest">Reception A · Room 101</p>
                            <p class="text-4xl font-extrabold tracking-tight mt-2 text-cyan-600 dark:text-cyan-400">G-001</p>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">Ahmed Hassan</p>
                        </div>
                        <div class="rounded-2xl p-4 border border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                            <p class="text-[10px] font-bold text-teal-500 dark:text-teal-400 uppercase tracking-widest">Specialty · Room 102</p>
                            <p class="text-4xl font-extrabold tracking-tight mt-2 text-slate-300 dark:text-slate-600">---  --</p>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">Waiting...</p>
                        </div>
                    </div>

                    <div class="px-5 pb-5">
                        <p class="text-[10px] font-bold text-cyan-500 dark:text-cyan-400 uppercase tracking-widest mb-3">Up Next</p>
                        <div class="flex gap-2">
                            <span class="px-3 py-1.5 rounded-lg bg-cyan-50 dark:bg-cyan-900/30 border border-cyan-100 dark:border-cyan-800/50 text-sm font-mono font-bold text-cyan-700 dark:text-cyan-300">G-002</span>
                            <span class="px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-900/30 border border-teal-100 dark:border-teal-800/50 text-sm font-mono font-bold text-teal-700 dark:text-teal-300">G-003</span>
                            <span class="px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800/50 text-sm font-mono font-bold text-emerald-700 dark:text-emerald-300">G-004</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 inset-x-0 h-24 bg-gradient-to-t from-slate-50 dark:from-slate-950 to-transparent"></div>
</section>
