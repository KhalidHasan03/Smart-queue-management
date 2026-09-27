<x-app-layout>
    @section('page-title', 'Reviews')
    {{--
        Registered before Alpine boots: app.js is a deferred module in the head,
        so a plain inline script here always runs first. This layout has no
        @stack, so the script lives inside the slot.
    --}}
    <script>
    window.reviewLive = function (query) {
        return {
            live: false,
            secs: 15,
            timer: null,
            baseline: {{ $stats['pending'] }},
            newCount: 0,
            get stale() { return this.newCount > 0 },
            get endpoint() {
                const p = new URLSearchParams(query);
                p.set('_pending_only', '1');
                return `{{ route('admin.reviews.index') }}?${p.toString()}`;
            },
            // Polls in the background and only raises a banner when the pending
            // count moves — the table is never rewritten under the moderator.
            toggleLive() {
                clearInterval(this.timer);
                if (!this.live) { this.newCount = 0; return; }
                this.timer = setInterval(() => this.check(), this.secs * 1000);
                this.check();
            },
            async check() {
                try {
                    const res = await fetch(this.endpoint, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.newCount = Math.max(0, (data.pending ?? 0) - this.baseline);
                } catch (e) { /* offline is fine — manual reload still works */ }
            },
        };
    };
    </script>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-indigo-500">Patient feedback</p>
                <h2 class="text-2xl font-extrabold tracking-tight">Reviews management 💬</h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.reviews.export', request()->query()) }}" class="qc-btn-soft">⬇ Export CSV</a>
                <a href="{{ route('review.kiosk') }}" target="_blank" rel="noopener" class="qc-btn-soft">📝 Kiosk ↗</a>
                <a href="{{ route('reviews.setup') }}" class="qc-btn-primary">Kiosk setup</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4" x-data="reviewLive({{ json_encode(array_map('urlencode', request()->query())) }})">
        @include('reviews.partials.stats-cards', ['stats' => $stats, 'baseUrl' => route('admin.reviews.index')])

        {{-- Rating distribution, straight from the aggregate. --}}
        @php
            $barColors = [
                'rose' => 'bg-rose-500',
                'amber' => 'bg-amber-500',
                'teal' => 'bg-teal-500',
                'emerald' => 'bg-emerald-500',
            ];
        @endphp
        @if($stats['total'] > 0)
        <div class="qc-card p-5">
            <h3 class="font-bold mb-3">Rating distribution</h3>
            <div class="grid sm:grid-cols-2 gap-x-8 gap-y-2">
                @foreach(\App\Models\Review::RATINGS as $value => $meta)
                    @php
                        $count = $stats['distribution'][$value] ?? 0;
                        $pct = $stats['total'] > 0 ? round($count / $stats['total'] * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-20 text-sm shrink-0">{{ $meta['emoji'] }} {{ $meta['label'] }}</span>
                        <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $barColors[$meta['color']] ?? 'bg-slate-400' }} rounded-full" style="width:{{ $pct }}%"></div>
                        </div>
                        <span class="w-14 text-right text-xs text-slate-500 tabular-nums">{{ $count }} · {{ $pct }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Quick views</span>
                @foreach(\App\Support\ReviewFilters::PRESETS as $key => $label)
                    @php $active = request('preset') === $key; @endphp
                    <a href="{{ $active ? route('admin.reviews.index') : route('admin.reviews.index', ['preset' => $key]) }}"
                       class="px-3 py-1.5 rounded-full text-xs font-bold border transition
                              {{ $active ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-slate-200 text-slate-600 hover:border-indigo-300 hover:text-indigo-600' }}">
                        {{ $label }}
                        @if($key === 'needs_attention' && $stats['negative_pending'] > 0)
                            <span class="ms-1 {{ $active ? 'text-indigo-100' : 'text-rose-500' }}">{{ $stats['negative_pending'] }}</span>
                        @elseif($key === 'awaiting' && $stats['pending'] > 0)
                            <span class="ms-1 {{ $active ? 'text-indigo-100' : 'text-amber-500' }}">{{ $stats['pending'] }}</span>
                        @endif
                    </a>
                @endforeach
                @if(request()->hasAny(['search', 'status', 'rating', 'category', 'service_id', 'doctor_id', 'date_from', 'date_to', 'preset']))
                    <a href="{{ route('admin.reviews.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">✕ Clear all filters</a>
                @endif
            </div>

            {{-- Opt-in live refresh. Off by default so a moderator is never
                 interrupted mid-approve by a page swap. --}}
            <label class="flex items-center gap-2 text-xs font-semibold text-slate-500 select-none">
                <input type="checkbox" x-model="live" @change="toggleLive()" class="w-4 h-4 accent-indigo-600">
                Auto-refresh
                <span x-show="live" x-cloak class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-full" x-text="`every ${secs}s`"></span>
            </label>
        </div>

        @include('reviews.partials.filters', [
            'baseUrl' => route('admin.reviews.index'),
            'services' => $services,
            'doctors' => $doctors,
        ])

        <div x-show="stale" x-cloak class="qc-card px-4 py-3 flex flex-wrap items-center justify-between gap-3 border-amber-200 bg-amber-50/80">
            <p class="text-sm font-semibold text-amber-800">
                <span x-text="newCount"></span> new review<span x-show="newCount !== 1">s</span> arrived while you were working.
            </p>
            <button type="button" class="qc-btn-primary text-xs py-1.5 px-3" x-on:click="window.location.reload()">Reload now</button>
        </div>

        @include('reviews.partials.table', ['reviews' => $reviews])
    </div>
</x-app-layout>
