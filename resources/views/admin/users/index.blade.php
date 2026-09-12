<x-app-layout>
    @section('page-title', 'Users')
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-3"><h2 class="text-2xl font-extrabold tracking-tight">👥 Users & roles</h2><a href="{{ route('admin.users.create') }}" class="qc-btn-primary">+ Add user</a></div></x-slot>
    <div class="qc-card overflow-hidden"><div class="overflow-x-auto"><table class="min-w-full qc-table">
        <thead><tr><th>User</th><th class="text-center">Role</th><th class="text-center">Status</th><th class="text-right">Action</th></tr></thead>
        <tbody>@foreach($users as $u)<tr>
            <td><div class="flex items-center gap-3"><span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 text-white flex items-center justify-center font-bold">{{ substr($u->name,0,1) }}</span><span><b>{{ $u->name }}</b><span class="block text-xs text-slate-400">{{ $u->email }}</span></span></div></td>
            <td class="text-center"><span class="bg-slate-100 font-bold text-xs px-2.5 py-1 rounded-full">{{ \App\Support\Rbac::roleLabel($u->role) }}</span></td>
            <td class="text-center">{!! $u->is_active ? '<span class="pill-completed">active</span>' : '<span class="pill-cancelled">off</span>' !!}</td>
            <td class="text-right whitespace-nowrap"><a href="{{ route('admin.users.edit', $u) }}" class="font-bold text-indigo-600 hover:underline">Edit</a>
                @can('delete', $u)<form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="ms-3 font-bold text-red-500 hover:underline">Delete</button></form>@endcan</td></tr>
        @endforeach</tbody>
    </table></div><div class="p-4 border-t border-slate-100">{{ $users->links() }}</div></div>
</x-app-layout>
