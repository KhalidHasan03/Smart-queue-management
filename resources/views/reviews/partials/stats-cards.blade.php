{{--
    Moderation stat cards. Clicking a card applies it as a filter link, so the
    same figures double as navigation. `baseUrl` keeps every link pointed at
    whichever page this partial was rendered on.

    @param array<string, mixed> $stats
    @param string $baseUrl
--}}
<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3">
    <a href="{{ $baseUrl }}" class="qc-card p-4 hover:-translate-y-0.5 transition block">
        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Total</p>
        <p class="text-3xl font-extrabold tabular-nums">{{ $stats['total'] }}</p>
        <p class="text-[11px] text-slate-400">all feedback</p>
    </a>
    <a href="{{ $baseUrl }}?{{ http_build_query(['preset' => 'awaiting']) }}" class="qc-card p-4 hover:-translate-y-0.5 transition block border-amber-200">
        <p class="text-[11px] font-bold uppercase tracking-widest text-amber-600">Awaiting</p>
        <p class="text-3xl font-extrabold tabular-nums text-amber-600">{{ $stats['pending'] }}</p>
        <p class="text-[11px] text-amber-600/70">in the queue</p>
    </a>
    <a href="{{ $baseUrl }}?{{ http_build_query(['preset' => 'needs_attention']) }}" class="qc-card p-4 hover:-translate-y-0.5 transition block border-rose-200">
        <p class="text-[11px] font-bold uppercase tracking-widest text-rose-600">Needs attention</p>
        <p class="text-3xl font-extrabold tabular-nums text-rose-600">{{ $stats['negative_pending'] }}</p>
        <p class="text-[11px] text-rose-600/70">negative &amp; pending</p>
    </a>
    <a href="{{ $baseUrl }}?{{ http_build_query(['status' => \App\Models\Review::STATUS_APPROVED]) }}" class="qc-card p-4 hover:-translate-y-0.5 transition block">
        <p class="text-[11px] font-bold uppercase tracking-widest text-emerald-600">Published</p>
        <p class="text-3xl font-extrabold tabular-nums text-emerald-600">{{ $stats['approved'] }}</p>
        <p class="text-[11px] text-emerald-600/70">approved</p>
    </a>
    <a href="{{ $baseUrl }}?{{ http_build_query(['status' => \App\Models\Review::STATUS_REJECTED]) }}" class="qc-card p-4 hover:-translate-y-0.5 transition block">
        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Rejected</p>
        <p class="text-3xl font-extrabold tabular-nums text-slate-500">{{ $stats['rejected'] }}</p>
        <p class="text-[11px] text-slate-400">hidden</p>
    </a>
    <div class="qc-card p-4">
        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Average</p>
        <p class="text-3xl font-extrabold tabular-nums">
            {{ $stats['average'] > 0 ? number_format($stats['average'], 1) : '—' }}
            @if($stats['average'] > 0)
                <span class="text-lg">{{ \App\Models\Review::RATINGS[round($stats['average'])]['emoji'] ?? '🙂' }}</span>
            @endif
        </p>
        <p class="text-[11px] text-slate-400">{{ $stats['response_rate'] }}% response rate</p>
    </div>
</div>
