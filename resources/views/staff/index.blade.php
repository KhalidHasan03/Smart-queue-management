<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-[11px] font-bold uppercase tracking-widest text-indigo-500">My assignment • {{ auth()->user()->service->name ?? 'no service set' }}</p>
            <h2 class="text-2xl font-extrabold tracking-tight">Patient workspace 🩺</h2></div>
        </div>
    </x-slot>
    @if(! auth()->user()->service_id && ! auth()->user()->doctor_id)
        <div class="qc-card p-10 text-center">No assignment yet — ask an admin to set your service/doctor.</div>
    @else
    <div class="qc-card overflow-hidden"><div class="overflow-x-auto"><table class="min-w-full qc-table">
        <thead><tr><th>Token</th><th>Patient</th><th>Service / Doctor</th><th>Counter</th><th>Status</th><th class="text-right">Action</th></tr></thead>
        <tbody>@forelse($tokens as $t)<tr>
            <td class="font-mono font-extrabold text-indigo-600">{{ $t->token_no }}</td>
            <td><b>{{ $t->patient->name }}</b><span class="block text-xs text-slate-400">{{ $t->patient->phone }}</span></td>
            <td class="text-center">{{ $t->service->name }} / {{ $t->doctor->name }}</td>
            <td class="text-center font-semibold">{{ $t->counter->name }}</td>
            <td class="text-center"><span class="pill-{{ $t->status }}">{{ $t->status }}</span></td>
            <td class="text-right">
                @if(in_array($t->status, ['calling', 'serving']))
                <form method="POST" action="{{ route('staff.process', $t) }}">@csrf
                    <button class="qc-btn-soft !py-1.5">{{ $t->status === 'calling' ? '▶ Process' : '✔ Complete' }}</button></form>
                @else<span class="text-xs text-slate-400">—</span>@endif
            </td></tr>
        @empty<tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">No assigned tokens today.</td></tr>@endforelse</tbody>
    </table></div></div>
    @endif
</x-app-layout>
