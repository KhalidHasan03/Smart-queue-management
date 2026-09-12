<x-app-layout>
    @section('page-title', 'Edit doctor')
    <x-slot name="header"><h2 class="text-2xl font-extrabold tracking-tight">Edit — {{ $doctor->name }}</h2></x-slot>
    <form method="POST" action="{{ route('admin.doctors.update',$doctor) }}" class="qc-card p-6 max-w-xl space-y-4">@csrf @method('PUT')
        <div><label class="qc-label">Name *</label><input name="name" value="{{ old('name',$doctor->name) }}" required class="qc-input mt-1"></div>
        <div><label class="qc-label">Specialization</label><input name="specialization" value="{{ old('specialization',$doctor->specialization) }}" class="qc-input mt-1"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="qc-label">Service *</label><select name="service_id" class="qc-input mt-1">@foreach($services as $s)<option value="{{ $s->id }}" @selected(old('service_id',$doctor->service_id)==$s->id)>{{ $s->name }}</option>@endforeach</select></div>
            <div><label class="qc-label">Room</label><input name="room_no" value="{{ old('room_no',$doctor->room_no) }}" class="qc-input mt-1"></div>
        </div>
        <label class="text-sm font-semibold flex gap-2 items-center"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$doctor->is_active)) class="w-4 h-4 accent-indigo-600"> Active</label>
        <button class="qc-btn-primary">Update</button>
    </form>
</x-app-layout>
