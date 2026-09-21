<x-app-layout>
    @section('page-title', 'Token details')
    <x-slot name="header"><h2 class="text-2xl font-extrabold tracking-tight">Token <span class="text-indigo-600">{{ $token->token_no }}</span></h2></x-slot>
    <div class="grid lg:grid-cols-2 gap-4 max-w-4xl">
        <div class="qc-card p-8 text-center relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-emerald-400 to-teal-400"></div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Your serial</p>
            <p class="text-7xl font-extrabold tracking-tight my-2">{{ $token->token_no }}</p>
            <span class="pill-{{ $token->status }}">{{ $token->status }}</span>
            <div class="flex gap-2 justify-center mt-6">
                <a href="{{ route('tokens.print', $token) }}" target="_blank" class="qc-btn-dark">🖨 Print</a>
                <a href="{{ route('tokens.create') }}" class="qc-btn-primary">+ New</a>
                @if($token->status === \App\Models\Token::COMPLETED && $token->canBeReviewed())
                    <a href="{{ route('review.show', $token->token_no) }}" class="qc-btn-success">⭐ Leave Review</a>
                @elseif($token->status === \App\Models\Token::COMPLETED && $token->review?->exists)
                    <a href="{{ route('review.success', $token->token_no) }}" class="qc-btn-secondary">📝 View Review</a>
                @endif
            </div>
        </div>
        <div class="qc-card p-6 text-sm space-y-3">
            <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-400">Patient</span><b>{{ $token->patient->name }} • {{ $token->patient->phone }}</b></div>
            <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-400">Service</span><b>{{ $token->service->name }} ({{ $token->service->prefix }})</b></div>
            <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-400">Doctor</span><b>{{ $token->doctor->name }}</b></div>
            <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-400">Counter / Room</span><b>{{ $token->counter->name }}@if($token->counter->room_no) • R-{{ $token->counter->room_no }}@endif</b></div>
            <div class="flex justify-between"><span class="text-slate-400">Issued</span><b>{{ $token->created_at->format('d M, h:i A') }}</b></div>
            <div class="pt-2"><a href="{{ route('tokens.index', ['q' => $token->patient->phone]) }}" class="font-bold text-indigo-600 hover:underline text-sm">View full history of this patient ({{ $token->patient->tokens()->count() }} tokens) →</a></div>
        </div>
    </div>
</x-app-layout>
