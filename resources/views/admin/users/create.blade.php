<x-app-layout>
    @section('page-title', 'New user')
    <x-slot name="header"><h2 class="text-2xl font-extrabold tracking-tight">New user</h2></x-slot>
    <form method="POST" action="{{ route('admin.users.store') }}" class="qc-card p-6 max-w-xl space-y-4">@csrf
        <div><label class="qc-label">Name *</label><input name="name" value="{{ old('name') }}" required class="qc-input mt-1"></div>
        <div><label class="qc-label">Email *</label><input name="email" type="email" value="{{ old('email') }}" required class="qc-input mt-1"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="qc-label">Password *</label><input name="password" type="password" required class="qc-input mt-1"></div>
            <div><label class="qc-label">Confirm *</label><input name="password_confirmation" type="password" required class="qc-input mt-1"></div>
        </div>
        <div class="grid grid-cols-2 gap-4 items-end">
            <div><label class="qc-label">Role *</label><select name="role" class="qc-input mt-1">@foreach($roles as $r)<option value="{{ $r }}" @selected(old('role')==$r)>{{ \App\Support\Rbac::roleLabel($r) }}</option>@endforeach</select></div>
            <label class="text-sm font-semibold flex gap-2 items-center pb-2.5"><input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 accent-indigo-600"> Active</label>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="qc-label">Counter (operators)</label><select name="counter_id" class="qc-input mt-1"><option value="">— None —</option>@foreach($counters as $c)<option value="{{ $c->id }}" @selected(old('counter_id')==$c->id)>{{ $c->name }} • {{ $c->service->name ?? '' }}</option>@endforeach</select></div>
            <div><label class="qc-label">Service (staff)</label><select name="service_id" class="qc-input mt-1"><option value="">— None —</option>@foreach($services as $s)<option value="{{ $s->id }}" @selected(old('service_id')==$s->id)>{{ $s->name }}</option>@endforeach</select></div>
        </div>
        <div><label class="qc-label">Doctor (staff, optional)</label><select name="doctor_id" class="qc-input mt-1"><option value="">— None —</option>@foreach($doctors as $d)<option value="{{ $d->id }}" @selected(old('doctor_id')==$d->id)>{{ $d->name }}</option>@endforeach</select></div>
        <button class="qc-btn-primary">Create user</button>
    </form>
</x-app-layout>
