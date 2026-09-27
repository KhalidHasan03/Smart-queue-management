<x-app-layout>
    @section('page-title', 'Counter Queue')
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-[11px] font-bold uppercase tracking-widest text-indigo-500">@if($counter){{ $counter->name }} • {{ $counter->service->name ?? '' }}@else No counter @endif</p>
            <h2 class="text-2xl font-extrabold tracking-tight">Counter console ⚡</h2></div>
            <div class="flex gap-2">
                @if(auth()->user()->hasPermission('queue.previous'))
                <a href="{{ route('queue.previous') }}" class="qc-btn-soft !px-5 !py-3 !text-base" title="Show last finished token">← Previous</a>
                @endif
                @if(auth()->user()->hasPermission('queue.next'))
                <form method="POST" action="{{ route('queue.next') }}">@csrf
                    <button class="qc-btn-primary !px-6 !py-3 !text-base" @disabled(!$counter || $current || ! $counterOpen)>Next token →</button></form>
                @endif
            </div>
        </div>
    </x-slot>
    @if(!$counter)
        <div class="qc-card p-10 text-center">No counter assigned — contact admin.</div>
    @else

    {{-- Open / closed switch. Closing hides this desk's waiting patients from
         the TV display and blocks new calls, without touching anyone already
         being served. --}}
    @if(auth()->user()->hasPermission('queue.next'))
    <div class="qc-card mb-4 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 px-5 py-4 {{ $counterOpen ? 'bg-emerald-50/70 border-b border-emerald-100' : 'bg-rose-50/70 border-b border-rose-100' }}">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <span class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl font-bold text-white shrink-0 {{ $counterOpen ? 'bg-emerald-500' : 'bg-rose-500' }}">{{ $counterOpen ? '🔓' : '🔒' }}</span>
                <div class="min-w-0">
                    <p class="font-extrabold text-lg leading-tight {{ $counterOpen ? 'text-emerald-800' : 'text-rose-800' }}">
                        {{ $counterOpen ? 'Counter open' : 'Counter closed' }}
                    </p>
                    <p class="text-xs mt-0.5 {{ $counterOpen ? 'text-emerald-700/80' : 'text-rose-700/80' }}">
                        @if($counterOpen)
                            Accepting patients since {{ $counter->opened_at?->format('g:i A') ?? '—' }}. Waiting patients are showing on the display.
                        @else
                            @if($hiddenWaiting > 0)
                                {{ $hiddenWaiting }} waiting patient{{ $hiddenWaiting === 1 ? '' : 's' }} {{ $hiddenWaiting === 1 ? 'is' : 'are' }} hidden from the display.
                            @else
                                No patients are waiting right now.
                            @endif
                            @if($current)
                                <span class="block mt-1 font-semibold text-rose-700">You can still finish {{ $current->token_no }} — closing does not interrupt a visit in progress.</span>
                            @endif
                        @endif
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('queue.counter.toggle') }}" class="shrink-0" onsubmit="return confirm('{{ $counterOpen ? 'Close this counter? Its waiting patients will be hidden from the display.' : 'Open this counter? Its waiting patients will appear on the display again.' }}')">
                @csrf
                <button class="w-full sm:w-auto {{ $counterOpen ? 'qc-btn-danger' : 'qc-btn-success' }} !px-6 !py-3 !text-base">
                    {{ $counterOpen ? 'Close counter' : '▶ Open counter' }}
                </button>
            </form>
        </div>
        @unless($counterOpen)
        <div class="px-5 py-3 text-xs text-rose-700 bg-rose-50/40">
            Next token is disabled. Patients keep their place in the queue and reappear on the display the moment you open the counter.
        </div>
        @endunless
    </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-4" x-data="{ q: '' }">
        <div class="qc-card p-6 relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500"></div>
            <h3 class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Now serving</h3>
            @if($current)
                <p class="text-6xl font-extrabold tracking-tight mt-2 text-slate-900">{{ $current->token_no }}</p>
                <div class="mt-2 flex items-center gap-2"><span class="pill-{{ $current->status }}">{{ $current->status }}</span><span class="text-xs text-slate-400">{{ $current->updated_at->diffForHumans() }}</span></div>
                <div class="mt-4 rounded-2xl bg-slate-50 p-4 text-sm space-y-1">
                    <p class="font-bold text-base">{{ $current->patient->name }}</p>
                    <p class="text-slate-500">📞 {{ $current->patient->phone }} • 👨‍⚕️ {{ $current->doctor->name }}</p>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-4">
                    @if($current->status === 'calling' && auth()->user()->hasPermission('queue.complete'))
                    <form method="POST" action="{{ route('queue.action', [$current, 'start']) }}">@csrf<button class="qc-btn-success w-full">▶ Serve</button></form>
                    @endif
                    @if($current->status === 'calling' && auth()->user()->hasPermission('queue.recall'))
                    <form method="POST" action="{{ route('queue.action', [$current, 'recall']) }}">@csrf<button class="qc-btn-soft w-full">🔔 Recall</button></form>
                    @endif
                    @if(auth()->user()->hasPermission('queue.complete'))
                    <form method="POST" action="{{ route('queue.action', [$current, 'complete']) }}">@csrf<button class="qc-btn-dark w-full">✔ Complete</button></form>
                    @endif
                    @if(auth()->user()->hasPermission('queue.skip'))
                    <form method="POST" action="{{ route('queue.action', [$current, 'skip']) }}">@csrf<button class="qc-btn-soft w-full">⏭ Skip</button></form>
                    @endif
                    @if(auth()->user()->hasPermission('queue.cancel'))
                    <form method="POST" action="{{ route('queue.action', [$current, 'cancel']) }}" onsubmit="return confirm('Cancel this token?')" class="col-span-2">@csrf<button class="qc-btn-danger-soft w-full">Cancel token</button></form>
                    @endif
                </div>
            @else
                <div class="text-center py-8"><p class="text-5xl">☕</p><p class="font-bold mt-2">Counter idle</p><p class="text-sm text-slate-500">Press <b>Next token</b> to call the queue.</p></div>
            @endif
            @if(isset($previous) && $previous)
            <div class="mt-4 rounded-2xl bg-slate-50 p-3 text-sm flex items-center justify-between">
                <span class="text-slate-500">← Previous: <b class="font-mono">{{ $previous->token_no }}</b> {{ $previous->patient->name ?? '' }}</span>
                <span class="pill-{{ $previous->status }}">{{ $previous->status }}</span>
            </div>
            @endif
            <p class="text-[11px] text-slate-400 mt-4">Waiting list filters live • press Next when idle</p>
        </div>
        <div class="qc-card p-5">
            @if(! $counterOpen)
            <h3 class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Waiting</h3>
            <div class="mt-3 rounded-2xl border border-dashed border-rose-200 bg-rose-50/50 px-5 py-8 text-center">
                <p class="text-3xl leading-none">🔒</p>
                <p class="font-bold text-rose-800 mt-2">Counter is closed</p>
                <p class="text-sm text-rose-700/80 mt-1">
                    @if($hiddenWaiting > 0)
                        {{ $hiddenWaiting }} patient{{ $hiddenWaiting === 1 ? '' : 's' }} {{ $hiddenWaiting === 1 ? 'is' : 'are' }} waiting but hidden from the display.
                    @else
                        No one is waiting for this counter.
                    @endif
                </p>
                <p class="text-xs text-slate-500 mt-3">Open the counter above to see and call them.</p>
            </div>
            @else
            <div class="flex items-center justify-between gap-2"><h3 class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Waiting • {{ $waiting->count() }}</h3>
            <input x-model="q" placeholder="Filter…" class="qc-input !w-36 !py-1.5"></div>
            <ul class="divide-y divide-slate-100 mt-2 max-h-[520px] overflow-auto">
                @forelse($waiting as $w)
                <li x-show="'{{ strtolower($w->token_no.' '.$w->patient->name) }}'.includes(q.toLowerCase())" class="py-2.5 flex items-center gap-3">
                    <span class="font-mono font-extrabold text-indigo-600 bg-indigo-50 rounded-lg px-2.5 py-1">{{ $w->token_no }}</span>
                    <span class="flex-1 min-w-0"><span class="block font-semibold truncate">{{ $w->patient->name }}</span><span class="block text-xs text-slate-400">{{ $w->doctor->name }}</span></span>
                    @if(auth()->user()->hasPermission('queue.cancel'))
                    <form method="POST" action="{{ route('queue.action', [$w, 'cancel']) }}" onsubmit="return confirm('Cancel?')">@csrf<button class="text-xs font-bold text-red-500 hover:underline">Cancel</button></form>
                    @endif
                </li>
                @empty<li class="py-10 text-center text-slate-400">Queue clear 🎉</li>@endforelse
            </ul>
            @endif
        </div>
        <div class="qc-card p-5">
            <h3 class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Recent at my counter</h3>
            <ul class="mt-2 space-y-2">
                @forelse($done as $d)
                <li class="flex items-center justify-between gap-2 bg-slate-50 rounded-xl px-3 py-2">
                    <span class="font-mono font-bold">{{ $d->token_no }}</span>
                    <span class="flex items-center gap-2">
                        @if($d->canBeReviewed())
                            <a href="{{ route('review.kiosk', ['code' => $d->review_code]) }}" class="text-xs font-bold text-teal-600 hover:underline whitespace-nowrap">😊 Rate</a>
                        @elseif($d->status === \App\Models\Token::COMPLETED && $d->review)
                            <span class="text-xs" title="Review submitted">{{ $d->review->rating_emoji }}</span>
                        @endif
                        <span class="pill-{{ $d->status }}">{{ $d->status }}</span>
                    </span>
                </li>
                @empty<li class="text-sm text-slate-400 py-6 text-center">Nothing yet.</li>@endforelse
            </ul>
            <a href="{{ route('tokens.index') }}" class="qc-btn-soft w-full mt-4">🔍 Search all tokens →</a>
        </div>
    </div>

    {{-- Quick peek at feedback waiting for this counter. The full moderation
         list lives on the counter review page. --}}
    @if($pendingReviews->isNotEmpty())
    <div class="qc-card p-5 mt-4 border-amber-200 bg-amber-50/40">
        <div class="flex items-center justify-between gap-3 mb-1">
            <h3 class="text-[11px] font-bold uppercase tracking-widest text-amber-700">💬 Feedback waiting for you</h3>
            <a href="{{ route('queue.reviews') }}" class="text-xs font-bold text-amber-700 hover:underline whitespace-nowrap">Review all →</a>
        </div>
        <p class="text-xs text-amber-700/70 mb-3">Patients rated your counter. Quick approve or dismiss, or open the full list with filters.</p>
        <ul class="space-y-2">
            @foreach($pendingReviews as $r)
                <li class="flex flex-col sm:flex-row sm:items-center gap-3 bg-white rounded-xl px-4 py-3 border border-amber-100">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        <span class="text-3xl leading-none shrink-0" aria-hidden="true">{{ $r->rating_emoji }}</span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $r->author_name }} · {{ $r->token?->token_no }} <span class="font-normal text-slate-400">({{ $r->rating_label }})</span></p>
                            @if($r->comment)
                                <p class="text-sm text-slate-600 mt-0.5 line-clamp-2">{{ $r->comment }}</p>
                            @else
                                <p class="text-sm text-slate-400 italic mt-0.5">No comment left.</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <form method="POST" action="{{ route('admin.reviews.approve', $r) }}">@csrf @method('PATCH')
                            <button class="qc-btn-success !px-4 !py-2 !text-xs" title="Approve and publish">✓ Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.reviews.reject', $r) }}">@csrf @method('PATCH')
                            <button class="qc-btn-danger-soft !px-4 !py-2 !text-xs" title="Hide from public view">✕ Dismiss</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    @endif
    @endif
</x-app-layout>
