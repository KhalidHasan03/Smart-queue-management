<x-app-layout>
    @section('page-title', 'New service')
    <x-slot name="header"><h2 class="text-2xl font-extrabold tracking-tight">New service</h2></x-slot>
    <form method="POST" action="{{ route('admin.services.store') }}" class="qc-card p-6 max-w-xl space-y-4">@csrf
        <div><label class="qc-label">Name *</label><input name="name" value="{{ old('name') }}" required placeholder="e.g. General" class="qc-input mt-1"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="qc-label">Prefix *</label><input name="prefix" value="{{ old('prefix') }}" required maxlength="5" placeholder="G" class="qc-input mt-1 uppercase font-mono font-bold"></div>
            <div><label class="qc-label">Start number</label><input name="start_number" type="number" value="{{ old('start_number',1) }}" min="1" class="qc-input mt-1"></div>
        </div>
        <label class="text-sm font-semibold flex gap-2 items-center"><input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 accent-indigo-600"> Active</label>
        <button class="qc-btn-primary">Save service</button>
    </form>
</x-app-layout>
