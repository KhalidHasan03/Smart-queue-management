<nav class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
     x-data="{ scrolled: false, megaOpen: null, dark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches) }"
     x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 }); if(dark) document.documentElement.classList.add('dark')"
     :class="scrolled ? 'bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl shadow-[0_2px_20px_rgba(0,0,0,0.06)] border-b border-slate-200/60 dark:border-slate-700/60' : 'bg-transparent'">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-[72px]">
            <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-teal-600 to-teal-500 flex items-center justify-center text-lg font-extrabold text-white shadow-lg shadow-teal-600/20 group-hover:shadow-teal-600/40 transition-shadow">Q</div>
                <div>
                    <span class="font-extrabold text-lg tracking-tight text-slate-900 dark:text-white">Queue-Pro</span>
                    <p class="text-[9px] text-teal-600 dark:text-teal-400 font-bold uppercase tracking-widest">Hospital Queue Management</p>
                </div>
            </a>

            <div class="hidden lg:flex items-center gap-0.5">
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl transition flex items-center gap-1" :class="open ? 'text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-slate-800' : ''">
                        Company
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute top-full left-0 mt-1 w-56 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-200/60 dark:shadow-slate-900/60 border border-slate-100 dark:border-slate-700 p-2 z-50">
                        <a href="{{ route('landing.about') }}" class="block px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-slate-700 hover:text-teal-600 dark:hover:text-teal-400 rounded-xl transition">About Queue-Pro</a>
                        <a href="{{ route('landing.about') }}#clients" class="block px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-slate-700 hover:text-teal-600 dark:hover:text-teal-400 rounded-xl transition">Clients</a>
                        <a href="{{ route('landing.about') }}#partners" class="block px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-slate-700 hover:text-teal-600 dark:hover:text-teal-400 rounded-xl transition">Partners</a>
                    </div>
                </div>

                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl transition flex items-center gap-1" :class="open ? 'text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-slate-800' : ''">
                        Solutions
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute top-full left-0 mt-1 w-80 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-200/60 dark:shadow-slate-900/60 border border-slate-100 dark:border-slate-700 p-4 z-50">
                        <div class="grid grid-cols-2 gap-2">
                            @foreach([
                                ['name' => 'Customer Flow', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                                ['name' => 'Self-Service Kiosks', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                ['name' => 'Digital Signage', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                                ['name' => 'Patient Feedback', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                                ['name' => 'e-Appointment', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                ['name' => 'Visitor Management', 'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
                            ] as $sol)
                            <a href="{{ route('landing.features') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-teal-50 dark:hover:bg-slate-700 transition group">
                                <div class="w-9 h-9 rounded-lg bg-teal-50 dark:bg-slate-700 group-hover:bg-teal-100 dark:group-hover:bg-slate-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $sol['icon'] }}"/></svg>
                                </div>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400">{{ $sol['name'] }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl transition flex items-center gap-1" :class="open ? 'text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-slate-800' : ''">
                        Industries
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute top-full left-0 mt-1 w-64 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-200/60 dark:shadow-slate-900/60 border border-slate-100 dark:border-slate-700 p-2 z-50">
                        @foreach(['Healthcare', 'Banking & Finance', 'Government', 'Education', 'Retail', 'Others'] as $ind)
                        <a href="{{ route('landing.industry') }}" class="block px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-slate-700 hover:text-teal-600 dark:hover:text-teal-400 rounded-xl transition">{{ $ind }}</a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('landing.features') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl transition">Products</a>
                <a href="{{ route('landing.services') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl transition">Services</a>
                <a href="{{ route('landing.pricing') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl transition">Pricing</a>
                <a href="{{ route('landing.contact') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl transition">Contact</a>
            </div>

            <div class="flex items-center gap-3">
                <button @click="
                    dark = !dark;
                    localStorage.setItem('theme', dark ? 'dark' : 'light');
                    document.documentElement.classList.toggle('dark', dark);
                " class="p-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" :title="dark ? 'Switch to light mode' : 'Switch to dark mode'">
                    <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="dark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                <a href="{{ route('login') }}" class="hidden sm:inline-flex px-5 py-2.5 text-sm font-bold text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-slate-800 rounded-2xl hover:bg-teal-100 dark:hover:bg-slate-700 transition-all duration-200">Login</a>
                <a href="{{ route('display') }}" target="_blank" class="px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-teal-600 to-teal-500 rounded-2xl shadow-lg shadow-teal-600/25 hover:shadow-teal-600/40 hover:from-teal-500 hover:to-teal-400 transition-all duration-200 active:scale-[0.97]">
                    Live Demo
                </a>
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-xl transition-colors text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
         @click.away="mobileOpen = false"
         class="lg:hidden bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-b border-slate-100 dark:border-slate-700 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 py-4 space-y-1">
            <a href="{{ route('landing.about') }}" @click="mobileOpen = false" class="block px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl">About</a>
            <a href="{{ route('landing.features') }}" @click="mobileOpen = false" class="block px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl">Products</a>
            <a href="{{ route('landing.services') }}" @click="mobileOpen = false" class="block px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl">Services</a>
            <a href="{{ route('landing.industry') }}" @click="mobileOpen = false" class="block px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl">Industries</a>
            <a href="{{ route('landing.pricing') }}" @click="mobileOpen = false" class="block px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl">Pricing</a>
            <a href="{{ route('landing.contact') }}" @click="mobileOpen = false" class="block px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-xl">Contact</a>
            <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="text-center px-4 py-3 text-sm font-bold text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-slate-800 rounded-xl">Login</a>
                <a href="{{ route('display') }}" target="_blank" class="text-center px-4 py-3 text-sm font-bold text-white bg-gradient-to-r from-teal-600 to-teal-500 rounded-2xl">Live Demo</a>
            </div>
        </div>
    </div>
</nav>
