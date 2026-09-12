<x-app-layout>
    @section('page-title', 'New doctor')
    <x-slot name="header"><h2 class="text-2xl font-extrabold tracking-tight">New doctor</h2></x-slot>
    <form method="POST" action="{{ route('admin.doctors.store') }}" class="qc-card p-6 max-w-xl space-y-4">@csrf
        <div><label class="qc-label">Name *</label><input name="name" value="{{ old('name') }}" required placeholder="Dr. Name" class="qc-input mt-1"></div>
        <div><label class="qc-label">Specialization</label><input name="specialization" value="{{ old('specialization') }}" placeholder="e.g. Dentist" class="qc-input mt-1"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="qc-label">Service *</label><select name="service_id" required class="qc-input mt-1">@foreach($services as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->prefix }})</option>@endforeach</select></div>
            <div><label class="qc-label">Room</label><input name="room_no" value="{{ old('room_no') }}" placeholder="101" class="qc-input mt-1"></div>
        </div>
        <label class="text-sm font-semibold flex gap-2 items-center"><input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 accent-indigo-600"> Active</label>
        <button class="qc-btn-primary">Save doctor</button>
    </form>
</x-app-layout>
