<section id="pricing" class="py-24 sm:py-32 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-600 mb-3">Pricing</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                Simple, transparent <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-600">pricing for hospitals.</span>
            </h2>
            <p class="mt-4 text-lg text-slate-500">Start free. Upgrade when you're ready.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <div class="relative bg-white rounded-3xl border border-slate-200 shadow-[0_8px_40px_rgb(0,0,0,0.06)] p-8 lg:p-10 hover:shadow-[0_12px_60px_rgb(0,0,0,0.1)] transition-shadow duration-300">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-2xl bg-slate-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">Starter</h3>
                        <span class="text-[10px] font-bold text-teal-600 uppercase tracking-widest bg-teal-50 px-2 py-0.5 rounded-full">For Clinics</span>
                    </div>
                </div>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-5xl font-extrabold text-slate-900">$490</span>
                    <span class="text-sm text-slate-400 font-semibold">/year</span>
                </div>
                <p class="text-sm text-slate-400 mb-8">~$41/month &middot; 30-day free trial</p>
                <div class="space-y-3 mb-8">
                    @foreach(['5 doctor rooms', '2 reception desks', 'Up to 100 patients/day', '2 waiting-room screens', 'Live queue dashboard', 'Ticket printing', 'Basic analytics'] as $feature)
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-teal-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span class="text-sm font-semibold text-slate-600">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('landing.contact') }}" class="block w-full py-3.5 text-center text-sm font-bold text-white bg-gradient-to-r from-teal-600 to-teal-500 rounded-2xl shadow-lg shadow-teal-600/20 hover:shadow-teal-600/40 hover:from-teal-500 hover:to-teal-400 transition-all duration-200 active:scale-[0.98]">
                    Start Free Trial
                </a>
                <p class="text-center text-xs text-slate-400 mt-3">No card required</p>
            </div>

            <div class="relative bg-[#0c1a1a] rounded-3xl p-8 lg:p-10 text-white overflow-hidden shadow-2xl shadow-teal-950/30">
                <div class="absolute inset-0 bg-gradient-to-br from-teal-600/10 via-emerald-600/10 to-cyan-600/10"></div>
                <div class="absolute -top-24 -right-24 w-48 h-48 rounded-full bg-teal-500/10 blur-[80px]"></div>
                <div class="relative">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-teal-400 to-emerald-400 flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold">Enterprise</h3>
                            <span class="text-[10px] font-bold text-teal-300 uppercase tracking-widest bg-teal-500/20 px-2 py-0.5 rounded-full">Most Popular</span>
                        </div>
                    </div>
                    <p class="text-sm text-teal-200/60 mb-6">For hospitals & multi-branch clinics</p>
                    <div class="flex items-baseline gap-1 mb-8">
                        <span class="text-5xl font-extrabold">Custom</span>
                    </div>
                    <p class="text-sm text-teal-200/40 mb-8">Priced on a call</p>
                    <div class="space-y-3 mb-8">
                        @foreach(['Unlimited departments', 'Unlimited staff users', 'Multiple display screens', 'Advanced analytics & reports', 'Custom integrations (API)', 'Self-service kiosks', '24/7 priority support', 'On-site setup & training'] as $feature)
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-teal-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span class="text-sm font-semibold text-teal-100/80">{{ $feature }}</span>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('landing.contact') }}" class="block w-full py-3.5 text-center text-sm font-bold text-white bg-gradient-to-r from-teal-400 to-emerald-400 rounded-2xl shadow-lg shadow-teal-400/20 hover:shadow-teal-400/40 transition-all duration-200 active:scale-[0.98]">
                        Contact Sales
                    </a>
                    <p class="text-center text-xs text-teal-200/40 mt-3">Custom pricing for your hospital</p>
                </div>
            </div>
        </div>
    </div>
</section>
