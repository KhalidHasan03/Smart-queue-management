<x-app-layout>
    @section('page-title', 'Reports & history')
    <x-slot name="header"><h2 class="text-2xl font-extrabold tracking-tight">Reports <span class="text-slate-400 font-medium">/ date-wise history</span></h2></x-slot>
    <form method="GET" class="qc-card p-3 mb-4 grid sm:grid-cols-2 lg:grid-cols-5 gap-2">
        <input type="date" name="date" value="{{ $date }}" class="qc-input !bg-white">
        <select name="service_id" class="qc-input !bg-white"><option value="">All services</option>@foreach($services as $s)<option value="{{ $s->id }}" @selected(request('service_id')==$s->id)>{{ $s->name }}</option>@endforeach</select>
        <select name="status" class="qc-input !bg-white"><option value="">All statuses</option>@foreach(['waiting','calling','serving','completed','skipped','cancelled'] as $st)<option value="{{ $st }}" @selected(request('status')==$st)>{{ ucfirst($st) }}</option>@endforeach</select>
        <input name="q" value="{{ request('q') }}" placeholder="Token / name / phone…" class="qc-input !bg-white">
        <button class="qc-btn-dark">Apply filter</button>
    </form>
    <div class="qc-card overflow-hidden"><div class="overflow-x-auto"><table class="min-w-full qc-table">
        <thead><tr><th>Token</th><th>Patient</th><th>Service</th><th>Doctor</th><th>Counter</th><th>Status</th><th>Time</th></tr></thead>
        <tbody>@forelse($tokens as $t)<tr><td class="font-mono font-extrabold text-indigo-600">{{ $t->token_no }}</td>
            <td><b>{{ $t->patient->name }}</b><span class="block text-xs text-slate-400">{{ $t->patient->phone }}</span></td>
            <td class="text-center">{{ $t->service->name }}</td><td>{{ $t->doctor->name }}</td><td class="text-center font-semibold">{{ $t->counter->name }}</td>
            <td class="text-center"><span class="pill-{{ $t->status }}">{{ $t->status }}</span></td>
            <td class="text-center text-xs text-slate-500">{{ $t->created_at->format('h:i A') }}</td></tr>
        @empty<tr><td colspan="7" class="px-4 py-12 text-center text-slate-400">No records for this filter.</td></tr>@endforelse</tbody>
    </table></div><div class="p-4 border-t border-slate-100">{{ $tokens->links() }}</div></div>
</x-app-layout>
