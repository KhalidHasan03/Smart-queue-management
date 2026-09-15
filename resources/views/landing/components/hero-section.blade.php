<section class="relative min-h-[95vh] flex items-center overflow-hidden" style="background: radial-gradient(1400px 800px at 15% -5%,#0d9488 0%,transparent 50%),radial-gradient(1200px 700px at 85% 5%,#14b8a6 0%,transparent 45%),radial-gradient(900px 500px at 50% 100%,#0f766e 0%,transparent 45%),linear-gradient(170deg,#f0fdfa 0%,#e6f7f5 40%,#f8fffe 100%)">
    <div class="absolute top-10 right-[5%] w-[600px] h-[600px] rounded-full bg-teal-200/20 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-0 left-[10%] w-[500px] h-[500px] rounded-full bg-emerald-200/15 blur-[100px] pointer-events-none"></div>
    <div class="absolute top-1/3 right-1/4 w-[400px] h-[400px] rounded-full bg-cyan-200/10 blur-[150px] pointer-events-none"></div>
    <div class="absolute inset-0 opacity-[0.03]" style="background-image:radial-gradient(circle,#134e4a 1px,transparent 1px);background-size:40px 40px"></div>

    <div class="relative max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-20 lg:pt-36 lg:pb-28">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-teal-50 border border-teal-100 mb-8">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-teal-600 uppercase tracking-widest">Trusted by hospitals worldwide</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-[3.2rem] xl:text-6xl font-extrabold leading-[1.05] tracking-tight text-slate-900">
                    Smarter Patient<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-600">Flow Management</span>
                </h1>

                <p class="mt-5 text-lg sm:text-xl text-slate-500 leading-relaxed max-w-xl">
                    Streamline hospital queues from check-in to consultation. Reduce wait times, improve patient experience, and keep your facility running efficiently.
                </p>

                <div class="flex flex-wrap gap-3 mt-10">
                    <a href="{{ route('display') }}" target="_blank"
                       class="group inline-flex items-center gap-2 px-8 py-4 text-base font-bold text-white bg-gradient-to-r from-teal-600 to-teal-500 rounded-2xl shadow-xl shadow-teal-600/25 hover:shadow-teal-600/40 hover:from-teal-500 hover:to-teal-400 transition-all duration-300 active:scale-[0.97]">
                        See Live Demo
                        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="#how-it-works"
                       class="inline-flex items-center gap-2 px-8 py-4 text-base font-bold text-teal-600 bg-teal-50 rounded-2xl hover:bg-teal-100 transition-all duration-300">
                        How It Works
                    </a>
                </div>

                <div class="flex flex-wrap gap-6 mt-10">
                    @foreach(['No app needed', 'Works on any TV', '30-day free trial', '24/7 Support'] as $badge)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span class="text-sm font-semibold text-slate-600">{{ $badge }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="relative hidden lg:block">
                <div class="absolute -inset-8 bg-gradient-to-r from-teal-200/30 via-emerald-200/30 to-cyan-200/30 rounded-3xl blur-3xl"></div>

                <div class="relative bg-white/95 backdrop-blur-xl rounded-3xl border border-slate-200 shadow-2xl shadow-slate-200/50 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-teal-50 to-emerald-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-teal-400 flex items-center justify-center text-sm font-extrabold text-white shadow-lg">Q</div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $clinicName ?? 'Queue-Pro Hospital' }}</p>
                                <p class="text-[10px] text-teal-500 uppercase tracking-widest">Live Queue Display</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-emerald-600">LIVE</span>
                        </div>
                    </div>

                    <div class="p-5 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl p-4 border border-slate-100 bg-slate-50/50">
                            <p class="text-[10px] font-bold text-teal-500 uppercase tracking-widest">Reception A &middot; Room 101</p>
                            <p class="text-4xl font-extrabold tracking-tight mt-2 text-teal-600">G-001</p>
                            <p class="text-xs font-semibold text-slate-500 mt-1">Ahmed Hassan</p>
                        </div>
                        <div class="rounded-2xl p-4 border border-slate-100 bg-slate-50/50">
                            <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Specialty &middot; Room 102</p>
                            <p class="text-4xl font-extrabold tracking-tight mt-2 text-slate-300">&mdash;&ndash;</p>
                            <p class="text-xs font-semibold text-slate-500 mt-1">Waiting...</p>
                        </div>
                    </div>

                    <div class="px-5 pb-5">
                        <p class="text-[10px] font-bold text-teal-500 uppercase tracking-widest mb-3">Up Next</p>
                        <div class="flex gap-2">
                            <span class="px-3 py-1.5 rounded-lg bg-teal-50 border border-teal-100 text-sm font-mono font-bold text-teal-700">G-002</span>
                            <span class="px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-100 text-sm font-mono font-bold text-emerald-700">G-003</span>
                            <span class="px-3 py-1.5 rounded-lg bg-cyan-50 border border-cyan-100 text-sm font-mono font-bold text-cyan-700">G-004</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 inset-x-0 h-24 bg-gradient-to-t from-slate-50 to-transparent"></div>
</section>
