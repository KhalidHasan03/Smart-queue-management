<x-app-layout>
    @section('page-title', 'Doctors')
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-3"><h2 class="text-2xl font-extrabold tracking-tight">👨‍⚕️ Doctors</h2><a href="{{ route('admin.doctors.create') }}" class="qc-btn-primary">+ Add doctor</a></div></x-slot>
    <div class="qc-card overflow-hidden"><div class="overflow-x-auto"><table class="min-w-full qc-table">
        <thead><tr><th>Doctor</th><th>Service</th><th class="text-center">Room</th><th class="text-center">Status</th><th class="text-right">Action</th></tr></thead>
        <tbody>@foreach($doctors as $d)<tr><td><b>{{ $d->name }}</b>@if($d->specialization)<span class="block text-xs text-slate-400">{{ $d->specialization }}</span>@endif</td>
            <td><span class="bg-violet-50 text-violet-600 font-bold text-xs px-2.5 py-1 rounded-full">{{ $d->service->name ?? '-' }}</span></td>
            <td class="text-center font-semibold">{{ $d->room_no ?? '—' }}</td>
            <td class="text-center">{!! $d->is_active ? '<span class="pill-completed">active</span>' : '<span class="pill-cancelled">off</span>' !!}</td>
            <td class="text-right whitespace-nowrap"><a href="{{ route('admin.doctors.edit',$d) }}" class="font-bold text-indigo-600 hover:underline">Edit</a><form method="POST" action="{{ route('admin.doctors.destroy',$d) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="ms-3 font-bold text-red-500 hover:underline">Delete</button></form></td></tr>@endforeach</tbody>
    </table></div><div class="p-4 border-t border-slate-100">{{ $doctors->links() }}</div></div>
</x-app-layout>
