<x-app-layout>
    @section('page-title', "Today's tokens")
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-2xl font-extrabold tracking-tight">Today's tokens</h2>
        <a href="{{ route('tokens.create') }}" class="qc-btn-primary">+ New token</a></div></x-slot>
    <form method="GET" class="qc-card p-3 mb-4 flex gap-2"><input name="q" value="{{ $q }}" placeholder="🔍 Search token no / patient / phone…" class="qc-input !bg-white"><button class="qc-btn-dark shrink-0">Search</button></form>
    <div class="qc-card overflow-hidden"><div class="overflow-x-auto"><table class="min-w-full qc-table">
        <thead><tr><th>Token</th><th>Patient</th><th>Service / Doctor</th><th>Counter</th><th>Status</th><th class="text-right">Action</th></tr></thead>
        <tbody>@forelse($tokens as $t)<tr>
            <td class="font-mono font-extrabold text-indigo-600 text-base">{{ $t->token_no }}</td>
            <td><span class="font-semibold">{{ $t->patient->name }}</span><span class="block text-xs text-slate-400">{{ $t->patient->phone }}</span></td>
            <td class="text-center"><span class="font-bold">{{ $t->service->prefix }}</span> <span class="text-slate-400">/</span> {{ $t->doctor->name }}</td>
            <td class="text-center font-semibold">{{ $t->counter->name }}</td>
            <td class="text-center"><span class="pill-{{ $t->status }}">{{ $t->status }}</span></td>
            <td class="text-right whitespace-nowrap"><a href="{{ route('tokens.show',$t) }}" class="font-bold text-indigo-600 hover:underline">View</a><a href="{{ route('tokens.print',$t) }}" target="_blank" class="ms-3 font-bold text-slate-500 hover:underline">🖨 Print</a></td></tr>
        @empty<tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">No tokens today. <a href="{{ route('tokens.create') }}" class="text-indigo-600 font-bold">Issue one →</a></td></tr>@endforelse</tbody>
    </table></div><div class="p-4 border-t border-slate-100">{{ $tokens->links() }}</div></div>
</x-app-layout>
