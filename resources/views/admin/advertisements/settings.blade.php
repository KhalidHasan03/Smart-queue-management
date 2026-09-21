<x-app-layout>
    @section('page-title', 'Advertisement settings')
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-extrabold tracking-tight">Advertisement settings</h2>
            <a href="{{ route('admin.advertisements.index') }}" class="qc-btn-soft">Back to list</a>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.advertisements.settings.update') }}" class="qc-card p-6 max-w-2xl space-y-5">
        @csrf @method('PATCH')

        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_enabled" id="is_enabled" value="1" {{ $config['is_enabled'] ? 'checked' : '' }} class="w-4 h-4 accent-indigo-600">
            <label for="is_enabled" class="qc-label">Enable advertisements on the patient display</label>
        </div>

        <div>
            <label class="qc-label">Playback mode *</label>
            <select name="mode" class="qc-input mt-1">
                <option value="cycle" {{ $config['mode'] === 'cycle' ? 'selected' : '' }}>Cycle through all active ads</option>
                <option value="single" {{ $config['mode'] === 'single' ? 'selected' : '' }}>Show only the LIVE ad</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="qc-label">Duration per ad (secs, 3–600) *</label>
                <input name="duration_secs" type="number" min="3" max="600" value="{{ $config['duration_secs'] }}" class="qc-input mt-1">
            </div>
            <div>
                <label class="qc-label">Display position *</label>
                <select name="position" class="qc-input mt-1">
                    <option value="right" {{ $config['position'] === 'right' ? 'selected' : '' }}>Right side</option>
                    <option value="left" {{ $config['position'] === 'left' ? 'selected' : '' }}>Left side</option>
                    <option value="bottom" {{ $config['position'] === 'bottom' ? 'selected' : '' }}>Bottom bar</option>
                </select>
            </div>
        </div>

        @if($errors->any())
        <div class="qc-alert-error p-3"><ul class="list-disc ms-5">@foreach($errors->all() as $e)<li class="text-sm">{{ $e }}</li>@endforeach</ul></div>
        @endif

        <button class="qc-btn-primary">Save settings</button>
    </form>
</x-app-layout>
