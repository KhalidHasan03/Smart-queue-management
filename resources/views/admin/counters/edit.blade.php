<x-app-layout>
    @section('page-title', 'Edit counter')
    <x-slot name="header"><h2 class="text-2xl font-extrabold tracking-tight">Edit — {{ $counter->name }}</h2></x-slot>
    <form method="POST" action="{{ route('admin.counters.update',$counter) }}" class="qc-card p-6 max-w-xl space-y-4">@csrf @method('PUT')
        <div><label class="qc-label">Name *</label><input name="name" value="{{ old('name',$counter->name) }}" required class="qc-input mt-1"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="qc-label">Service *</label><select name="service_id" class="qc-input mt-1">@foreach($services as $s)<option value="{{ $s->id }}" @selected(old('service_id',$counter->service_id)==$s->id)>{{ $s->name }}</option>@endforeach</select></div>
            <div><label class="qc-label">Room</label><input name="room_no" value="{{ old('room_no',$counter->room_no) }}" class="qc-input mt-1"></div>
        </div>
        @if(isset($operators))
        <div><label class="qc-label">Assign operators</label>
            <select name="operator_ids[]" multiple class="qc-input mt-1 h-28">@foreach($operators as $o)<option value="{{ $o->id }}" @selected($o->counter_id==$counter->id)>{{ $o->name }} — {{ $o->email }}</option>@endforeach</select>
            <p class="text-xs text-slate-400 mt-1">Selecting replaces assignment for this counter.</p></div>
        @endif
        <label class="text-sm font-semibold flex gap-2 items-center"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$counter->is_active)) class="w-4 h-4 accent-indigo-600"> Active</label>
        <label class="text-sm font-semibold flex gap-2 items-center">
            <input type="checkbox" name="is_open" value="1" @checked(old('is_open',$counter->is_open)) class="w-4 h-4 accent-indigo-600">
            Counter open
            <span class="text-xs font-normal text-slate-400">
                — while closed, waiting patients for this desk are hidden from the display
                @if($counter->opened_at) <span class="block">Last opened {{ $counter->opened_at->format('d M Y, g:i A') }}@if($counter->closed_at) • closed {{ $counter->closed_at->format('d M Y, g:i A') }}@endif</span> @endif
            </span>
        </label>
        <label class="text-sm font-semibold flex gap-2 items-center"><input type="checkbox" name="show_on_display" value="1" @checked(old('show_on_display',$counter->show_on_display)) class="w-4 h-4 accent-indigo-600"> Show on TV display</label>
        <button class="qc-btn-primary">Update</button>
    </form>
</x-app-layout>
