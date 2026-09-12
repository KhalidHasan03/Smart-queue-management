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
                    <button class="qc-btn-primary !px-6 !py-3 !text-base" @disabled(!$counter || $current)>Next token →</button></form>
                @endif
            </div>
        </div>
    </x-slot>
    @if(!$counter)
        <div class="qc-card p-10 text-center">No counter assigned — contact admin.</div>
    @else
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
        </div>
        <div class="qc-card p-5">
            <h3 class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Recent at my counter</h3>
            <ul class="mt-2 space-y-2">
                @forelse($done as $d)<li class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2"><span class="font-mono font-bold">{{ $d->token_no }}</span><span class="pill-{{ $d->status }}">{{ $d->status }}</span></li>
                @empty<li class="text-sm text-slate-400 py-6 text-center">Nothing yet.</li>@endforelse
            </ul>
            <a href="{{ route('tokens.index') }}" class="qc-btn-soft w-full mt-4">🔍 Search all tokens →</a>
        </div>
    </div>
    @endif
</x-app-layout>
