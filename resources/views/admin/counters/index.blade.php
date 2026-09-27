<x-app-layout>
    @section('page-title', 'Counters')
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-3"><h2 class="text-2xl font-extrabold tracking-tight">🏢 Counters / Rooms</h2><a href="{{ route('admin.counters.create') }}" class="qc-btn-primary">+ Add counter</a></div></x-slot>
    <div class="qc-card overflow-hidden"><div class="overflow-x-auto"><table class="min-w-full qc-table">
        <thead><tr><th>Counter</th><th>Service</th><th class="text-center">Room</th><th class="text-center">Enabled</th><th class="text-center">Queue</th><th class="text-right">Action</th></tr></thead>
        <tbody>@forelse($counters as $c)<tr>
            <td class="font-bold">{{ $c->name }}</td>
            <td>{{ $c->service->name ?? '-' }}</td>
            <td class="text-center font-semibold">{{ $c->room_no ?? '—' }}</td>
            <td class="text-center">{!! $c->is_active ? '<span class="pill-completed">active</span>' : '<span class="pill-cancelled">off</span>' !!}</td>
            <td class="text-center">
                @if(! $c->is_active)
                    <span class="pill-cancelled">disabled</span>
                @elseif($c->isOpen())
                    <span class="pill-completed">open</span>
                @else
                    <span class="pill-cancelled">closed</span>
                @endif
            </td>
            <td class="text-right whitespace-nowrap">
                <form method="POST" action="{{ route('admin.counters.toggle', $c) }}" class="inline" onsubmit="return confirm('{{ $c->isOpen() ? 'Close' : 'Open' }} ' + @js($c->name) + '?')">@csrf
                    <button class="me-3 font-bold {{ $c->isOpen() ? 'text-amber-600' : 'text-emerald-600' }} hover:underline">{{ $c->isOpen() ? 'Close counter' : 'Open counter' }}</button>
                </form>
                <a href="{{ route('admin.counters.edit', $c) }}" class="font-bold text-indigo-600 hover:underline">Edit</a>
                <form method="POST" action="{{ route('admin.counters.destroy', $c) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="ms-3 font-bold text-red-500 hover:underline">Delete</button></form>
            </td>
        </tr>@empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">No counters yet — <a class="text-indigo-600 font-bold" href="{{ route('admin.counters.create') }}">add the first one →</a></td></tr>
        @endforelse
    </table></div><div class="p-4 border-t border-slate-100">{{ $counters->links() }}</div></div>
    <p class="text-xs text-slate-400 mt-3">
        <b>Enabled</b> decides whether the counter exists in the queue at all. <b>Queue</b> is the live switch a counter operator flips on their console — while a counter is closed its waiting patients are hidden from the display.
    </p>
</x-app-layout>
