<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-extrabold tracking-tight">📢 Advertisements</h2>
            <div class="flex gap-2">
                @can('adverts.configure')<a href="{{ route('admin.advertisements.settings') }}" class="qc-btn-soft">⚙️ Settings</a>@endcan
                @can('adverts.manage')<a href="{{ route('admin.advertisements.create') }}" class="qc-btn-primary">+ Add advertisement</a>@endcan
            </div>
        </div>
    </x-slot>

    @if(session('success'))<div class="qc-alert-info p-4 mb-4">{{ session('success') }}</div>@endif

    <div class="qc-card overflow-hidden">
        @if($advertisements->isEmpty())
            <div class="p-10 text-center text-slate-400">No advertisements yet. <a href="{{ route('admin.advertisements.create') }}" class="text-indigo-600 font-bold hover:underline">Create one</a></div>
        @else
        <div class="space-y-2 p-4" id="ad-sortable">
            @foreach($advertisements as $ad)
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg border border-slate-100 sort-item" data-id="{{ $ad->id }}">
                <span class="cursor-grab text-slate-400 hover:text-slate-600" title="Drag to reorder">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16M4 12h16"/></svg>
                </span>
                <div class="flex-1 min-w-0">
                    <div class="font-bold truncate">{{ $ad->title }}</div>
                    @if($ad->description)
                    <div class="text-xs text-slate-500 truncate">{{ $ad->description }}</div>
                    @endif
                </div>
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $ad->media_type === 'youtube' ? 'red' : ($ad->media_type === 'video' ? 'purple' : ($ad->media_type === 'image' ? 'blue' : 'gray')) }}-100 text-{{ $ad->media_type === 'youtube' ? 'red' : ($ad->media_type === 'video' ? 'purple' : ($ad->media_type === 'image' ? 'blue' : 'gray')) }}-700">
                    {{ $ad->media_type_label }}
                </span>
                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $ad->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                    {{ $ad->is_active ? 'Active' : 'Paused' }}
                </span>
                @if($ad->is_live)
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700 animate-pulse">● LIVE</span>
                @endif
                <div class="flex items-center gap-2 whitespace-nowrap">
                    @can('adverts.manage')
                    @if(! $ad->is_live && $ad->is_active)
                    <form method="POST" action="{{ route('admin.advertisements.live', $ad) }}" class="inline">@csrf<button class="font-bold text-emerald-600 hover:underline text-sm">▶ Live</button></form>
                    @endif
                    <form method="POST" action="{{ route('admin.advertisements.toggle', $ad) }}" class="inline">@csrf<button class="font-bold text-slate-500 hover:underline text-sm">{{ $ad->is_active ? 'Pause' : 'Resume' }}</button></form>
                    <a href="{{ route('admin.advertisements.edit', $ad) }}" class="font-bold text-indigo-600 hover:underline text-sm">Edit</a>
                    <form method="POST" action="{{ route('admin.advertisements.destroy', $ad) }}" class="inline" onsubmit="return confirm('Delete this advertisement?')">@csrf @method('DELETE')<button class="font-bold text-red-500 hover:underline text-sm">Delete</button></form>
                    @endcan
                </div>
            </div>
            @endforeach
        </div>
        <div class="p-4 border-t border-slate-100 flex justify-between items-center">
            <p class="text-sm text-slate-500">Drag items to reorder. Changes save automatically.</p>
            {{ $advertisements->links() }}
        </div>
        @endif
    </div>

    @can('adverts.manage')
    <script>
        (function () {
            const container = document.getElementById('ad-sortable');
            if (!container) return;

            let dragSrc = null;

            container.querySelectorAll('.sort-item').forEach(item => {
                item.draggable = true;

                item.addEventListener('dragstart', e => {
                    dragSrc = item;
                    item.classList.add('opacity-50', 'ring-2', 'ring-indigo-500');
                    e.dataTransfer.effectAllowed = 'move';
                });

                item.addEventListener('dragend', () => {
                    item.classList.remove('opacity-50', 'ring-2', 'ring-indigo-500');
                    dragSrc = null;
                });

                item.addEventListener('dragover', e => {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    const after = getDragAfterElement(container, e.clientY);
                    if (after == null) {
                        container.appendChild(dragSrc);
                    } else {
                        container.insertBefore(dragSrc, after);
                    }
                });
            });

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.sort-item:not(.opacity-50)')];
                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    }
                    return closest;
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }

            // Debounced save
            let saveTimer = null;
            container.addEventListener('dragend', () => {
                clearTimeout(saveTimer);
                saveTimer = setTimeout(saveOrder, 500);
            });

            async function saveOrder() {
                const order = [...container.querySelectorAll('.sort-item')].map(el => parseInt(el.dataset.id, 10));
                try {
                    await fetch('{{ route('admin.advertisements.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({ order })
                    });
                } catch (e) {
                    console.error('Failed to save order:', e);
                }
            }
        })();
    </script>
    @endcan
</x-app-layout>
