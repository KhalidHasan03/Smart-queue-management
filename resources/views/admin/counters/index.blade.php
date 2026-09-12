<x-app-layout>
    @section('page-title', 'Counters')
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-3"><h2 class="text-2xl font-extrabold tracking-tight">🏢 Counters / Rooms</h2><a href="{{ route('admin.counters.create') }}" class="qc-btn-primary">+ Add counter</a></div></x-slot>
    <div class="qc-card overflow-hidden"><div class="overflow-x-auto"><table class="min-w-full qc-table">
        <thead><tr><th>Counter</th><th>Service</th><th class="text-center">Room</th><th class="text-center">Status</th><th class="text-right">Action</th></tr></thead>
        <tbody>@foreach($counters as $c)<tr><td class="font-bold">{{ $c->name }}</td><td>{{ $c->service->name ?? '-' }}</td>
            <td class="text-center font-semibold">{{ $c->room_no ?? '—' }}</td>
            <td class="text-center">{!! $c->is_active ? '<span class="pill-completed">active</span>' : '<span class="pill-cancelled">off</span>' !!}</td>
            <td class="text-right whitespace-nowrap"><a href="{{ route('admin.counters.edit',$c) }}" class="font-bold text-indigo-600 hover:underline">Edit</a><form method="POST" action="{{ route('admin.counters.destroy',$c) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="ms-3 font-bold text-red-500 hover:underline">Delete</button></form></td></tr>@endforeach</tbody>
    </table></div><div class="p-4 border-t border-slate-100">{{ $counters->links() }}</div></div>
</x-app-layout>
