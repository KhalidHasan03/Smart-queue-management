<x-app-layout>
    @section('page-title', 'Services')
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-3"><h2 class="text-2xl font-extrabold tracking-tight">🩺 Services</h2><a href="{{ route('admin.services.create') }}" class="qc-btn-primary">+ Add service</a></div></x-slot>
    <div class="qc-card overflow-hidden"><div class="overflow-x-auto"><table class="min-w-full qc-table">
        <thead><tr><th>Name</th><th class="text-center">Prefix</th><th class="text-center">Starts at</th><th class="text-center">Status</th><th class="text-right">Action</th></tr></thead>
        <tbody>@foreach($services as $s)<tr><td class="font-bold">{{ $s->name }}</td>
            <td class="text-center"><span class="font-mono font-extrabold bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg">{{ $s->prefix }}</span></td>
            <td class="text-center tabular-nums">{{ $s->start_number }}</td>
            <td class="text-center">{!! $s->is_active ? '<span class="pill-completed">active</span>' : '<span class="pill-cancelled">off</span>' !!}</td>
            <td class="text-right whitespace-nowrap"><a href="{{ route('admin.services.edit',$s) }}" class="font-bold text-indigo-600 hover:underline">Edit</a><form method="POST" action="{{ route('admin.services.destroy',$s) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="ms-3 font-bold text-red-500 hover:underline">Delete</button></form></td></tr>@endforeach</tbody>
    </table></div><div class="p-4 border-t border-slate-100">{{ $services->links() }}</div></div>
</x-app-layout>
