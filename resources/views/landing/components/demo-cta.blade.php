<section class="py-24 sm:py-32 relative overflow-hidden" style="background: radial-gradient(1200px 600px at 25% 0%,#0d9488 0%,transparent 50%),radial-gradient(1000px 500px at 80% 100%,#0f766e 0%,transparent 45%),linear-gradient(170deg,#f0fdfa 0%,#e6f7f5 100%)">
    <div class="absolute top-0 left-1/3 w-[500px] h-[500px] rounded-full bg-teal-200/30 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] rounded-full bg-emerald-200/20 blur-[100px] pointer-events-none"></div>
    <div class="absolute inset-0 opacity-[0.03]" style="background-image:radial-gradient(circle,#134e4a 1px,transparent 1px);background-size:40px 40px"></div>
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-teal-100 border border-teal-200 mb-8">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-bold text-teal-600 uppercase tracking-widest">Live demo available</span>
        </div>
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
            See your hospital queue in <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-600">action.</span>
        </h2>
        <p class="mt-5 text-lg text-slate-500 max-w-xl mx-auto">
            Open the demo and watch a live hospital queue — patients joining, counters calling, screens updating. No sign-up needed.
        </p>
        <div class="flex flex-wrap gap-4 justify-center mt-10">
            <a href="{{ route('display') }}" target="_blank"
               class="group inline-flex items-center gap-2 px-10 py-4 text-base font-bold text-white bg-gradient-to-r from-teal-600 to-teal-500 rounded-2xl shadow-xl shadow-teal-600/30 hover:shadow-teal-600/50 hover:from-teal-500 hover:to-teal-400 transition-all duration-300 active:scale-[0.97]">
                Open the Live Demo
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-4 text-base font-bold text-teal-600 bg-teal-50 rounded-2xl hover:bg-teal-100 transition-all duration-300">
                Sign In to Dashboard
            </a>
        </div>
        <p class="mt-8 text-sm text-slate-400">A real hospital, running live. No sign-up required.</p>
    </div>
</section>
