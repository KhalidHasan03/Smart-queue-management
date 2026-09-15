<section id="how-it-works" class="py-24 sm:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-600 mb-3">How It Works</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                Three steps to a <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-600">faster clinic.</span>
            </h2>
            <p class="mt-4 text-lg text-slate-500">From patient registration to checkout, Queue-Pro handles the entire flow.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
            @php
            $steps = [
                ['num' => '01', 'title' => 'Register', 'desc' => 'Patient checks in at reception or self-service kiosk. Token is issued instantly with service and doctor assignment.', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ['num' => '02', 'title' => 'Call & Serve', 'desc' => 'Counter staff calls the next patient. The live display updates immediately showing the called number and counter.', 'icon' => 'M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122'],
                ['num' => '03', 'title' => 'Complete & Analyze', 'desc' => 'Visit is completed, analytics are updated in real-time. Track wait times, peak hours, and staff performance.', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ];
            @endphp

            @foreach($steps as $step)
            <div class="relative group">
                @if(!$loop->last)
                <div class="hidden md:block absolute top-12 left-[60%] w-[80%] h-[2px] bg-gradient-to-r from-teal-200 to-transparent"></div>
                @endif
                <div class="relative bg-white rounded-3xl border border-slate-100 p-8 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="w-16 h-16 rounded-2xl bg-teal-50 group-hover:bg-teal-100 flex items-center justify-center mb-5 transition">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $step['icon'] }}"/></svg>
                    </div>
                    <div class="absolute top-6 right-6 text-5xl font-extrabold text-teal-100 group-hover:text-teal-200 transition">{{ $step['num'] }}</div>
                    <h3 class="text-xl font-extrabold text-slate-900">{{ $step['title'] }}</h3>
                    <p class="mt-3 text-sm text-slate-500 leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
