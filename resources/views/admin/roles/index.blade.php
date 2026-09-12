<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-[11px] font-bold uppercase tracking-widest text-indigo-500">Access control matrix</p>
            <h2 class="text-2xl font-extrabold tracking-tight">🔑 Roles & permissions</h2></div>
        </div>
    </x-slot>
    <div class="qc-card p-5 mb-4 text-sm text-slate-600">
        Tick / untick checkboxes and press <b>Save changes</b>. Defaults come from
        <code class="bg-slate-100 px-1.5 py-0.5 rounded font-mono text-xs">config/rbac.php</code>;
        your edits are stored as overrides (no duplicates possible).
        Super Admin is locked to full access and can never be restricted.
    </div>
    <form method="POST" action="{{ route('admin.roles.update') }}">@csrf @method('PUT')
    <div class="qc-card overflow-hidden"><div class="overflow-x-auto"><table class="min-w-full qc-table">
        <thead><tr><th class="sticky left-0 bg-slate-50">Permission</th>
            @foreach($roles as $key => $role)<th class="text-center">{{ $role['label'] }}<span class="block font-mono font-normal text-[10px] text-slate-400">{{ $key }}</span></th>@endforeach
        </tr></thead>
        <tbody>@foreach($permissions as $perm)<tr>
            <td class="font-mono text-xs font-bold sticky left-0 bg-white">{{ $perm }}</td>
            @foreach($roles as $key => $role)
            <td class="text-center">
                @if($key === 'super_admin')
                    <input type="checkbox" checked disabled class="w-4 h-4 accent-emerald-500" title="Locked: full access">
                @else
                    <input type="checkbox" name="perms[{{ $key }}][]" value="{{ $perm }}"
                        @checked(in_array($perm, $effective[$key] ?? [], true)) class="w-4 h-4 accent-indigo-600">
                @endif
            </td>
            @endforeach
        </tr>@endforeach</tbody>
    </table></div></div>
    <div class="mt-4 flex gap-2">
        <button class="qc-btn-primary">💾 Save changes</button>
        <a href="{{ route('admin.roles.index') }}" class="qc-btn-soft">Reset view</a>
    </div>
    </form>
</x-app-layout>
