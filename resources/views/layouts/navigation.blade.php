<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur border-b border-slate-100 sticky top-0 z-20">
    <div class="px-4 sm:px-6 h-16 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <button @click="$dispatch('toggle-sidebar')" class="lg:hidden w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">☰</button>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-indigo-500">{{ now()->format('l, d M Y') }}</p>
                <p class="font-bold text-slate-800 leading-tight">Welcome back 👋</p>
            </div>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="hidden md:flex items-center gap-2 bg-slate-100 rounded-xl px-3 py-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span id="qc-clock" class="text-sm font-mono font-semibold text-slate-600">--:--:--</span>
            </div>
            <a href="{{ route('display') }}" target="_blank" class="qc-btn-soft !py-2 hidden sm:inline-flex">📺 Display</a>
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white pl-1.5 pr-3 py-1.5 hover:shadow transition">
                        <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 text-white flex items-center justify-center font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        <span class="text-left hidden sm:block"><span class="block text-sm font-bold leading-tight">{{ Auth::user()->name }}</span><span class="block text-[11px] capitalize text-slate-400">{{ \App\Support\Rbac::roleLabel(Auth::user()->role) }}</span></span>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
            <button @click="open = ! open" class="sm:hidden w-10 h-10 rounded-xl bg-slate-100">▾</button>
        </div>
    </div>
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-100 px-4 py-3">
        <div class="text-sm font-semibold">{{ Auth::user()->name }}</div>
        <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
    </div>
</nav>
<script>
(function(){ const el = document.getElementById('qc-clock'); if(!el) return;
    const tick = () => el.textContent = new Date().toLocaleTimeString('en-US', { timeZone: 'Asia/Dhaka' }); tick(); setInterval(tick, 1000); })();
</script>
