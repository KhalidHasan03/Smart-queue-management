<x-app-layout>
    @section('page-title', 'Dashboard')
    @php
        // Static class map: Tailwind purges dynamically built "bg-{{ $color }}-500"
        // names from the built CSS, leaving the bars uncoloured.
        $barColors = [
            'rose' => 'bg-rose-500',
            'amber' => 'bg-amber-500',
            'teal' => 'bg-teal-500',
            'emerald' => 'bg-emerald-500',
        ];
    @endphp
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div><p class="text-[11px] font-bold uppercase tracking-widest text-indigo-500">Today • {{ now()->format('d M Y') }}</p>
            <h2 class="text-2xl font-extrabold tracking-tight">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ explode(' ', auth()->user()->name)[0] }} ✨</h2>
            <p class="text-sm text-slate-500">Live clinic pulse — waiting, serving & done at a glance.</p></div>
            <div class="flex gap-2">
                @if(auth()->user()->hasPermission('serials.create'))<a href="{{ route('tokens.create') }}" class="qc-btn-primary">+ New Token</a>@endif
                @if(auth()->user()->hasPermission('queue.next'))<a href="{{ route('queue.index') }}" class="qc-btn-dark">⚡ My Queue</a>@endif
                @if(auth()->user()->role === 'staff')<a href="{{ route('staff.index') }}" class="qc-btn-dark">🩺 My Patients</a>@endif
                @if(auth()->user()->hasPermission('display.manage'))<a href="{{ route('display.manage') }}" class="qc-btn-dark">🖥️ Display Setup</a>@endif
                <a href="{{ route('display') }}" target="_blank" class="qc-btn-soft">📺 Display</a>
            </div>
        </div>
    </x-slot>
    @isset($stats)
    @php $cards = [
        ['Total', $stats['total'] ?? 0, '🎫', 'from-indigo-500 to-violet-500', 'all tokens today'],
        ['Waiting', $stats['waiting'] ?? 0, '⏳', 'from-amber-400 to-orange-500', 'in queue'],
        ['Calling', $stats['calling'] ?? 0, '📢', 'from-sky-400 to-blue-600', 'on display'],
        ['Serving', $stats['serving'] ?? 0, '⚡', 'from-violet-500 to-purple-600', 'with doctor'],
        ['Done', $stats['completed'] ?? 0, '✅', 'from-emerald-400 to-teal-500', 'completed'],
        ['Skipped', $stats['skipped'] ?? 0, '⏭️', 'from-orange-400 to-rose-400', 'no-show'],
        ['Cancelled', $stats['cancelled'] ?? 0, '✕', 'from-slate-400 to-slate-600', 'cancelled'],
    ]; @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-7 gap-3">
        @foreach($cards as $c)
        <div class="qc-card p-4 relative overflow-hidden group hover:-translate-y-0.5 transition">
            <div class="qc-stat-icon bg-gradient-to-br {{ $c[3] }} text-white shadow-lg">{{ $c[2] }}</div>
            <p class="mt-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">{{ $c[0] }}</p>
            <p class="text-3xl font-extrabold tabular-nums">{{ $c[1] }}</p>
            <p class="text-[11px] text-slate-400">{{ $c[4] }}</p>
        </div>
        @endforeach
    </div>
    @if(($stats['waiting_paused'] ?? 0) > 0)
    <div class="qc-card mt-3 px-4 py-3 flex flex-wrap items-center gap-3 border-amber-200 bg-amber-50/70">
        <span class="text-lg leading-none">🔒</span>
        <p class="text-sm font-semibold text-amber-800">
            {{ $stats['waiting_paused'] }} patient{{ $stats['waiting_paused'] === 1 ? ' is' : 's are' }} waiting at a closed counter
            <span class="font-normal text-amber-700/80">— hidden from the display and from the public waiting count until that counter is switched on.</span>
        </p>
    </div>
    @endif
    <div class="grid lg:grid-cols-5 gap-4 mt-4">
        <div class="qc-card lg:col-span-3 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold">🔴 Live activity</h3>
                <input id="dash-filter" placeholder="Filter token / patient…" class="qc-input !w-56 !py-1.5">
            </div>
            <div class="overflow-x-auto"><table class="min-w-full qc-table" id="dash-table">
                <thead><tr><th>Token</th><th>Patient</th><th>Service</th><th>Counter</th><th>Status</th></tr></thead>
                <tbody>@forelse($recent as $t)<tr data-search="{{ strtolower($t->token_no.' '.$t->patient->name) }}">
                    <td class="font-mono font-extrabold text-indigo-600">{{ $t->token_no }}</td>
                    <td class="font-semibold">{{ $t->patient->name }}</td>
                    <td>{{ $t->service->name }}</td><td>{{ $t->counter->name }}</td>
                    <td><span class="pill-{{ $t->status }}">{{ $t->status }}</span></td></tr>
                @empty<tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">No tokens yet — @if(auth()->user()->hasPermission('serials.create'))<a class="text-indigo-600 font-bold" href="{{ route('tokens.create') }}">issue the first one →</a>@endif</td></tr>@endforelse</tbody>
            </table></div>
        </div>
        <div class="lg:col-span-2 space-y-4">
            <div class="qc-card p-5 bg-gradient-to-br from-[#141b34] to-[#2b1b5b] !border-0 text-white">
                <p class="text-[11px] font-bold uppercase tracking-widest text-indigo-200">Throughput</p>
                @php $done = ($stats['completed'] ?? 0); $tot = max(1, $stats['total'] ?? 1); $pct = round($done / $tot * 100); @endphp
                <p class="text-4xl font-extrabold mt-1">{{ $pct }}<span class="text-lg">%</span></p>
                <div class="h-2.5 rounded-full bg-white/15 mt-3 overflow-hidden"><div class="h-full rounded-full bg-gradient-to-r from-emerald-300 to-teal-300 transition-all" style="width:{{ $pct }}%"></div></div>
                <p class="text-xs text-indigo-100 mt-2">{{ $done }} of {{ $stats['total'] ?? 0 }} tokens completed today</p>
            </div>
            <div class="qc-card p-5">
                <h3 class="font-bold mb-3">⚡ Quick actions</h3>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    @if(auth()->user()->hasPermission('serials.create'))<a href="{{ route('tokens.create') }}" class="qc-btn-soft">+ Register</a>@endif
                    @if(auth()->user()->hasPermission('queue.next'))<a href="{{ route('queue.index') }}" class="qc-btn-soft">⚡ Counter</a>@endif
                    @if(auth()->user()->role === 'staff')<a href="{{ route('staff.index') }}" class="qc-btn-soft">🩺 Patients</a>@endif
                    @if(auth()->user()->hasPermission('serials.view'))<a href="{{ route('tokens.index') }}" class="qc-btn-soft">🔍 Search</a>@endif
                    @if(auth()->user()->hasPermission('reports.view'))<a href="{{ route('reports.index') }}" class="qc-btn-soft">📊 Reports</a>@endif
                    @if(auth()->user()->hasPermission('display.manage'))<a href="{{ route('display.manage') }}" class="qc-btn-soft">🖥️ Display</a>@endif
                </div>
            </div>
        </div>
    </div>
    @if(auth()->user()->role === 'staff')
    <div class="qc-card mt-4 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold">🩺 My assigned patients today</h3>
            <a href="{{ route('staff.index') }}" class="text-sm font-bold text-indigo-600">Open workspace →</a>
        </div>
        <div class="overflow-x-auto"><table class="min-w-full qc-table">
            <thead><tr><th>Token</th><th>Patient</th><th>Service</th><th>Status</th></tr></thead>
            <tbody>@forelse($staffTokens as $t)<tr>
                <td class="font-mono font-extrabold text-indigo-600">{{ $t->token_no }}</td>
                <td class="font-semibold">{{ $t->patient->name }}</td>
                <td>{{ $t->service->name }}</td>
                <td><span class="pill-{{ $t->status }}">{{ $t->status }}</span></td></tr>
            @empty<tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No assigned patients today — ask admin to set your service assignment.</td></tr>@endforelse</tbody>
        </table></div>
    </div>
    @endif
    @if($reviewStats)
    <div class="grid lg:grid-cols-3 gap-4 mt-4">
        <div class="qc-card p-5">
            <h3 class="font-bold mb-3">💬 Patient feedback</h3>
            @if($reviewStats['total'] > 0)
                <div class="flex items-center gap-4">
                    <p class="text-5xl font-extrabold">{{ number_format($reviewStats['average'], 1) }}</p>
                    <div>
                        <p class="text-3xl">{{ \App\Models\Review::RATINGS[max(1, min(4, round($reviewStats['average'])))]['emoji'] ?? '' }}</p>
                        <p class="text-xs text-slate-400">average of {{ $reviewStats['total'] }} reviews</p>
                    </div>
                </div>
                <div class="mt-4 space-y-1.5">
                    @foreach(\App\Models\Review::RATINGS as $value => $meta)
                        @php $c = $reviewStats['distribution'][$value] ?? 0; $pct = $reviewStats['total'] > 0 ? round($c / $reviewStats['total'] * 100) : 0; @endphp
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-16">{{ $meta['emoji'] }} {{ $meta['label'] }}</span>
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full {{ $barColors[$meta['color']] ?? 'bg-slate-400' }} rounded-full" style="width:{{ $pct }}%"></div></div>
                            <span class="w-6 text-right text-slate-400">{{ $c }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-400">No feedback yet. Share the kiosk link on token slips to start collecting reviews.</p>
            @endif
            @if(auth()->user()->hasPermission('reviews.view'))
                <a href="{{ route('admin.reviews.index') }}" class="qc-btn-soft w-full mt-4">Open reviews →</a>
            @endif
        </div>
        <a href="{{ route('admin.reviews.index', ['preset' => 'awaiting']) }}" class="qc-card p-5 hover:shadow-md transition">
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Awaiting moderation</p>
            <p class="text-4xl font-extrabold mt-1 text-amber-600">{{ $reviewStats['pending'] }}</p>
            <p class="text-xs text-slate-500 mt-2">Reviews waiting on a person</p>
        </a>
        <a href="{{ route('admin.reviews.index', ['preset' => 'needs_attention']) }}" class="qc-card p-5 hover:shadow-md transition">
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Needs attention</p>
            <p class="text-4xl font-extrabold mt-1 text-rose-600">{{ $reviewStats['negative_pending'] }}</p>
            <p class="text-xs text-slate-500 mt-2">Unhappy ratings to respond to</p>
        </a>
    </div>
    @endif
    @if(auth()->user()->hasPermission('display.manage'))
    <div class="qc-card mt-4 p-5 flex flex-wrap items-center justify-between gap-3">
        <div><h3 class="font-bold">🖥️ Display control</h3><p class="text-sm text-slate-500">{{ $displayCounters->where('show_on_display', true)->count() }} of {{ $displayCounters->count() }} counters visible on TV.</p></div>
        <div class="flex gap-2"><a href="{{ route('display.manage') }}" class="qc-btn-dark">Manage display</a><a href="{{ route('display') }}" target="_blank" class="qc-btn-soft">Open TV screen</a></div>
    </div>
    @endif
    <script>document.getElementById('dash-filter')?.addEventListener('input', e => { const v = e.target.value.toLowerCase();
        document.querySelectorAll('#dash-table tbody tr').forEach(r => r.style.display = (r.dataset.search || '').includes(v) ? '' : 'none'); });</script>
    @else
    <div class="qc-card p-8 text-center"><p class="font-bold text-lg">Welcome, {{ auth()->user()->name }} 👋</p><p class="text-sm text-slate-500">Your dashboard stats appear here.</p></div>
    @endisset
</x-app-layout>
