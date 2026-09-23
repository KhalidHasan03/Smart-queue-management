<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-extrabold tracking-tight">📢 Advertisements</h2>
            <div class="flex gap-2">
                @permission('adverts.configure')<a href="{{ route('admin.advertisements.settings') }}" class="qc-btn-soft">⚙️ Settings</a>@endpermission
                @permission('adverts.manage')<a href="{{ route('admin.advertisements.create') }}" class="qc-btn-soft">＋ Full form</a>@endpermission
            </div>
        </div>
    </x-slot>

    @permission('adverts.manage')
    <div class="qc-card p-5 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <div>
                <h3 class="font-extrabold text-lg">➕ Add YouTube advertisement</h3>
                <p class="text-sm text-slate-500">Paste a YouTube link or its <code class="text-slate-700 bg-slate-100 px-1 rounded">&lt;iframe&gt;</code> embed code — it plays on the live display.</p>
            </div>
            <a href="{{ route('admin.advertisements.create') }}" class="text-sm font-bold text-indigo-600 hover:underline">Use full form (image / video / text) →</a>
        </div>

        <form method="POST" action="{{ route('admin.advertisements.store') }}" enctype="multipart/form-data" class="grid md:grid-cols-12 gap-4 items-end">
            @csrf
            <input type="hidden" name="media_type" value="youtube">
            <div class="md:col-span-3">
                <label class="qc-label">Title <span class="text-slate-400 font-normal">(optional)</span></label>
                <input name="title" value="{{ old('title') }}" class="qc-input mt-1" placeholder="Auto from video if empty">
            </div>
            <div class="md:col-span-5">
                <label class="qc-label">YouTube embed link or URL *</label>
                <input name="youtube_embed" id="qc-quick-yt" value="{{ old('youtube_embed') }}" class="qc-input mt-1" placeholder="https://www.youtube.com/watch?v=... or <iframe src=…></iframe>">
                <div id="qc-quick-preview" class="hidden mt-2">
                    <iframe id="qc-quick-iframe" width="100%" height="180" frameborder="0" allowfullscreen class="rounded-xl border border-slate-200"></iframe>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="qc-label">Duration (secs)</label>
                <input name="duration_secs" type="number" min="3" max="600" value="{{ old('duration_secs', '15') }}" class="qc-input mt-1">
            </div>
            <div class="md:col-span-2">
                <label class="qc-label">&nbsp;</label>
                <button class="qc-btn-primary w-full">＋ Add video</button>
            </div>
            <label class="md:col-span-12 flex items-center gap-2 text-sm font-semibold text-slate-600 mt-1">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 accent-teal-600"> Active (visible on display)
            </label>
        </form>
    </div>
    @endpermission

    <div class="qc-card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="font-extrabold">🗂️ Manage advertisements</h3>
            <span class="text-xs text-slate-400">Drag to reorder • changes save automatically</span>
        </div>
        @if($advertisements->isEmpty())
            <div class="p-10 text-center text-slate-400">
                No advertisements yet.
                @permission('adverts.manage')Add a YouTube video above, or <a href="{{ route('admin.advertisements.create') }}" class="text-indigo-600 font-bold hover:underline">create one here</a>.@endpermission
            </div>
        @else
        <div class="space-y-2 p-4" id="ad-sortable">
            @foreach($advertisements as $ad)
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg border border-slate-100 sort-item" data-id="{{ $ad->id }}">
                <span class="cursor-grab text-slate-400 hover:text-slate-600" title="Drag to reorder">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16M4 12h16"/></svg>
                </span>
                @if($ad->youtube_thumbnail)
                <img src="{{ $ad->youtube_thumbnail }}" alt="{{ $ad->title }}" class="w-16 h-10 object-cover rounded-lg border border-slate-200">
                @elseif($ad->media_type === 'image' && $ad->media_url)
                <img src="{{ $ad->media_url }}" alt="{{ $ad->title }}" class="w-16 h-10 object-cover rounded-lg border border-slate-200">
                @elseif($ad->media_type === 'video' && $ad->media_url)
                <video src="{{ $ad->media_url }}" muted class="w-16 h-10 object-cover rounded-lg border border-slate-200"></video>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="font-bold truncate">{{ $ad->title }}</div>
                    @if($ad->media_type === 'youtube' && $ad->youtube_url)
                    <div class="text-xs text-slate-500 truncate">{{ $ad->youtube_url }}</div>
                    @elseif($ad->description)
                    <div class="text-xs text-slate-500 truncate">{{ $ad->description }}</div>
                    @endif
                </div>
                @php
                    $typeBadge = match ($ad->media_type) {
                        'youtube' => 'bg-red-100 text-red-700',
                        'video' => 'bg-purple-100 text-purple-700',
                        'image' => 'bg-blue-100 text-blue-700',
                        default => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $typeBadge }}">
                    {{ $ad->media_type_label }}
                </span>
                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $ad->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                    {{ $ad->is_active ? 'Active' : 'Paused' }}
                </span>
                @if($ad->is_live)
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700 animate-pulse">● LIVE</span>
                @endif
                <div class="flex items-center gap-2 whitespace-nowrap">
                    @permission('adverts.manage')
                    @if(! $ad->is_live && $ad->is_active)
                    <form method="POST" action="{{ route('admin.advertisements.live', $ad) }}" class="inline">@csrf<button class="font-bold text-emerald-600 hover:underline text-sm">▶ Live</button></form>
                    @endif
                    <form method="POST" action="{{ route('admin.advertisements.toggle', $ad) }}" class="inline">@csrf<button class="font-bold text-slate-500 hover:underline text-sm">{{ $ad->is_active ? 'Pause' : 'Resume' }}</button></form>
                    <a href="{{ route('admin.advertisements.edit', $ad) }}" class="font-bold text-indigo-600 hover:underline text-sm">Edit</a>
                    <form method="POST" action="{{ route('admin.advertisements.destroy', $ad) }}" class="inline" onsubmit="return confirm('Delete this advertisement?')">@csrf @method('DELETE')<button class="font-bold text-red-500 hover:underline text-sm">Delete</button></form>
                    @endpermission
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

    <script>
        (function () {
            const ytInput = document.getElementById('qc-quick-yt');
            const ytPreview = document.getElementById('qc-quick-preview');
            const ytIframe = document.getElementById('qc-quick-iframe');

            function extractYoutubeId(value) {
                const m = value.match(/(?:youtube(?:-nocookie)?\.com\/(?:watch\?v=|embed\/|shorts\/|live\/|v\/)|youtu\.be\/)([A-Za-z0-9_-]{6,})/);
                return m ? m[1] : null;
            }

            ytInput?.addEventListener('input', function () {
                const id = extractYoutubeId(this.value);
                if (id) {
                    ytIframe.src = 'https://www.youtube.com/embed/' + id + '?rel=0';
                    ytPreview.classList.remove('hidden');
                } else {
                    ytPreview.classList.add('hidden');
                    ytIframe.src = '';
                }
            });

            @permission('adverts.manage')
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
            @endpermission
        })();
    </script>
</x-app-layout>