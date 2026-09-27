<x-app-layout>
    @section('page-title', 'Tokens issued')
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-2xl font-extrabold tracking-tight">{{ $tokens->count() }} {{ \Illuminate\Support\Str::plural('token', $tokens->count()) }} issued</h2>
        <div class="flex gap-2">
            <a href="{{ route('tokens.create') }}" class="qc-btn-primary">+ New registration</a>
            <a href="{{ route('tokens.index') }}" class="qc-btn-soft">View today's list</a>
        </div>
    </div></x-slot>

    @if($tokens->isEmpty())
        <div class="qc-card p-10 text-center text-slate-400">No tokens were found for this registration.</div>
    @else
        @php $patient = $tokens->first()->patient; @endphp
        <div class="qc-card p-5 mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Patient</p>
                <p class="font-bold text-lg">{{ $patient->name }}</p>
                <p class="text-sm text-slate-400">{{ $patient->phone }}</p>
            </div>
            <p class="text-sm text-slate-400">Each service below is queued separately and called on its own counter.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($tokens as $token)
                <div class="qc-card p-6 text-center relative overflow-hidden flex flex-col">
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-emerald-400 to-teal-400"></div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">{{ $token->service->name }}</p>
                    <p class="text-5xl font-extrabold tracking-tight my-2 font-mono">{{ $token->token_no }}</p>
                    <span class="pill-{{ $token->status }} self-center">{{ $token->status }}</span>

                    <dl class="text-sm text-left mt-5 space-y-2 flex-1">
                        <div class="flex justify-between border-b border-slate-100 pb-2"><dt class="text-slate-400">Doctor</dt><dd class="font-semibold">{{ $token->doctor->name }}</dd></div>
                        <div class="flex justify-between border-b border-slate-100 pb-2"><dt class="text-slate-400">Counter</dt><dd class="font-semibold">{{ $token->counter->name }}@if($token->counter->room_no) • R-{{ $token->counter->room_no }}@endif</dd></div>
                        <div class="flex justify-between border-b border-slate-100 pb-2"><dt class="text-slate-400">Queued at</dt><dd class="font-semibold">{{ $token->created_at->format('h:i A') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Review code</dt><dd class="font-mono font-bold text-indigo-600">{{ $token->review_code ?? '—' }}</dd></div>
                    </dl>

                    <div class="flex gap-2 justify-center mt-5">
                        <a href="{{ route('tokens.print', $token) }}" target="_blank" class="qc-btn-dark">🖨 Print</a>
                        <a href="{{ route('tokens.show', $token) }}" class="qc-btn-soft">Details</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="qc-card p-4 mt-4 text-sm flex flex-wrap items-center justify-between gap-3">
            <p class="text-slate-500">Keep every slip and watch the live display — each token is called independently.</p>
            <a href="{{ route('tokens.index', ['q' => $patient->phone]) }}" class="font-bold text-indigo-600 hover:underline">All {{ $patient->tokens()->count() }} tokens for this patient →</a>
        </div>
    @endif
</x-app-layout>
