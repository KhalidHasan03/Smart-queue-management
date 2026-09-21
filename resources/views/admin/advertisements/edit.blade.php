<x-app-layout>
    @php
        $types = [
            'image' => ['label' => 'Image', 'accept' => 'image/*', 'ext' => 'jpg,jpeg,png,webp,gif'],
            'video' => ['label' => 'Video', 'accept' => 'video/*', 'ext' => 'mp4,webm,mov,ogg'],
        ];
        $hasErrors = $errors->any();
        $currentMediaType = $advertisement->media_type;
        $currentYoutubeUrl = $advertisement->youtube_url ?? '';
        $currentMediaPath = $advertisement->media_path;
        $currentTextContent = $advertisement->media_type === \App\Models\Advertisement::TYPE_TEXT ? $advertisement->description : '';
    @endphp
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-extrabold tracking-tight">Edit advertisement: {{ $advertisement->title }}</h2>
            <a href="{{ route('admin.advertisements.index') }}" class="qc-btn-soft">← Back</a>
        </div>
    </x-slot>

    @if($hasErrors)
    <div class="qc-alert-error mb-4">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.advertisements.update', $advertisement) }}" enctype="multipart/form-data" class="qc-card p-6 max-w-2xl space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="qc-label">Title *</label>
            <input name="title" value="{{ old('title', $advertisement->title) }}" required class="qc-input mt-1" placeholder="e.g. Welcome to our clinic">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="qc-label">Media type *</label>
                <select name="media_type" id="qc-ad-media-type" class="qc-input mt-1">
                    <option value="text" {{ old('media_type', $currentMediaType) === 'text' ? 'selected' : '' }}>Text</option>
                    <option value="image" {{ old('media_type', $currentMediaType) === 'image' ? 'selected' : '' }}>Image</option>
                    <option value="video" {{ old('media_type', $currentMediaType) === 'video' ? 'selected' : '' }}>Video</option>
                    <option value="youtube" {{ old('media_type', $currentMediaType) === 'youtube' ? 'selected' : '' }}>YouTube video</option>
                </select>
            </div>
            <div>
                <label class="qc-label">Duration (seconds) *</label>
                <input name="duration_secs" type="number" min="3" max="600" value="{{ old('duration_secs', $advertisement->duration_secs) }}" class="qc-input mt-1">
            </div>
        </div>

        <div id="qc-ad-file" class="hidden space-y-2">
            <label class="qc-label" id="qc-ad-file-label">Media file *</label>
            <input type="file" name="media_file" id="qc-ad-file-input" class="qc-input mt-1" accept="{{ $types['image']['accept'] }},{{ $types['video']['accept'] }}">
            @if($currentMediaPath)
                <div id="qc-file-preview" class="mt-2">
                    @if($currentMediaType === 'image')
                        <img src="{{ asset('storage/'.$currentMediaPath) }}" class="max-h-48 rounded-lg border" alt="Current image">
                    @else
                        <video src="{{ asset('storage/'.$currentMediaPath) }}" controls class="max-h-48 rounded-lg border"></video>
                    @endif
                    <p class="text-xs text-slate-500 mt-1">Current file (will be replaced if new file uploaded)</p>
                </div>
            @else
                <div id="qc-file-preview" class="hidden"></div>
            @endif
        </div>
        <div id="qc-ad-youtube" class="hidden space-y-2">
            <label class="qc-label">YouTube URL *</label>
            <input name="youtube_url" id="qc-youtube-url" value="{{ old('youtube_url', $currentYoutubeUrl) }}" class="qc-input mt-1" placeholder="https://www.youtube.com/watch?v=...">
            <div id="qc-youtube-preview" class="{{ $currentYoutubeUrl ? '' : 'hidden' }}">
                <iframe id="qc-youtube-iframe" width="100%" height="200" frameborder="0" allowfullscreen></iframe>
                @if($currentYoutubeUrl)
                    <p class="text-xs text-slate-500 mt-1">Current: <a href="{{ $currentYoutubeUrl }}" target="_blank" class="text-indigo-600 hover:underline">{{ $currentYoutubeUrl }}</a></p>
                @endif
            </div>
        </div>
        <div id="qc-ad-text" class="hidden">
            <label class="qc-label">Ad text *</label>
            <textarea name="text_content" rows="3" class="qc-input mt-1" placeholder="Text to display on screen" required>{{ old('text_content', $currentTextContent) }}</textarea>
        </div>

        <div>
            <label class="qc-label">Description (internal notes)</label>
            <textarea name="description" rows="2" class="qc-input mt-1" placeholder="Optional internal notes">{{ old('description', $advertisement->description) }}</textarea>
        </div>

        <label class="flex items-center gap-2 text-sm font-semibold text-slate-600 mt-1">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $advertisement->is_active) ? 'checked' : '' }} class="w-4 h-4 accent-indigo-600"> Active (visible on display)
        </label>

        <button class="qc-btn-primary">Update advertisement</button>
    </form>

    <script>
        (function () {
            const typeEl = document.getElementById('qc-ad-media-type');
            const fileEl = document.getElementById('qc-ad-file');
            const ytEl = document.getElementById('qc-ad-youtube');
            const txtEl = document.getElementById('qc-ad-text');
            const fileInput = document.getElementById('qc-ad-file-input');
            const fileLabel = document.getElementById('qc-ad-file-label');
            const filePreview = document.getElementById('qc-file-preview');
            const ytUrlInput = document.getElementById('qc-youtube-url');
            const ytPreview = document.getElementById('qc-youtube-preview');
            const ytIframe = document.getElementById('qc-youtube-iframe');
            const accepts = {
                image: ['{{ $types["image"]["accept"] }}'],
                video: ['{{ $types["video"]["accept"] }}'],
                text: [], youtube: []
            };

            function toggle() {
                const t = typeEl.value;
                fileEl.classList.toggle('hidden', t !== 'image' && t !== 'video');
                ytEl.classList.toggle('hidden', t !== 'youtube');
                txtEl.classList.toggle('hidden', t !== 'text');
                if (fileInput) fileInput.accept = (accepts[t] || []).join(',');
                if (fileLabel) fileLabel.textContent = t === 'video' ? 'Video file *' : 'Image file *';
            }

            // File preview for new upload
            fileInput?.addEventListener('change', function () {
                const file = this.files[0];
                filePreview.innerHTML = '';
                filePreview.classList.add('hidden');
                if (!file) return;
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'mt-2 max-h-48 rounded-lg border';
                    filePreview.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const vid = document.createElement('video');
                    vid.src = URL.createObjectURL(file);
                    vid.controls = true;
                    vid.className = 'mt-2 max-h-48 rounded-lg border';
                    filePreview.appendChild(vid);
                }
                filePreview.classList.remove('hidden');
            });

            // YouTube preview
            function extractYoutubeId(url) {
                const m = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/|live\/|v\/)|youtu\.be\/)([A-Za-z0-9_-]{6,})/);
                return m ? m[1] : null;
            }

            ytUrlInput?.addEventListener('input', function () {
                const url = this.value.trim();
                const id = extractYoutubeId(url);
                if (id) {
                    ytIframe.src = 'https://www.youtube.com/embed/' + id + '?rel=0';
                    ytPreview.classList.remove('hidden');
                } else {
                    ytPreview.classList.add('hidden');
                    ytIframe.src = '';
                }
            });

            // Initialize YouTube preview if URL exists
            if (ytUrlInput?.value) {
                const id = extractYoutubeId(ytUrlInput.value);
                if (id) {
                    ytIframe.src = 'https://www.youtube.com/embed/' + id + '?rel=0';
                    ytPreview.classList.remove('hidden');
                }
            }

            typeEl.addEventListener('change', toggle);
            toggle();
        })();
    </script>
</x-app-layout>