<section class="py-16 sm:py-20 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">Trusted by leading hospitals</p>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-8">
            @php
            $logos = [
                ['name' => 'Al-Shifa Hospital', 'initials' => 'AS', 'color' => 'from-teal-500 to-emerald-500'],
                ['name' => 'Jordan Medical Center', 'initials' => 'JM', 'color' => 'from-emerald-500 to-cyan-500'],
                ['name' => 'Royal Health Clinic', 'initials' => 'RH', 'color' => 'from-cyan-500 to-teal-500'],
                ['name' => 'City Hospital', 'initials' => 'CH', 'color' => 'from-teal-400 to-emerald-400'],
                ['name' => 'Al-Noor Medical', 'initials' => 'AN', 'color' => 'from-emerald-400 to-cyan-400'],
                ['name' => 'Star Care Clinic', 'initials' => 'SC', 'color' => 'from-cyan-400 to-teal-400'],
            ];
            @endphp
            @foreach($logos as $logo)
            <div class="flex items-center gap-3 opacity-50 hover:opacity-100 transition-opacity duration-300 grayscale hover:grayscale-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $logo['color'] }} flex items-center justify-center shadow-md">
                    <span class="text-sm font-extrabold text-white">{{ $logo['initials'] }}</span>
                </div>
                <span class="text-sm font-bold text-slate-500 hidden sm:block">{{ $logo['name'] }}</span>
            </div>
            @endforeach
        </div>
        <div class="mt-10 text-center">
            <p class="text-xs text-slate-400 font-semibold">Over <span class="text-teal-600 font-bold">50+ hospitals</span> across the region trust Queue-Pro</p>
        </div>
    </div>
</section>
