<x-app-layout>
    @section('page-title', 'New registration')
    <x-slot name="header"><h2 class="text-2xl font-extrabold tracking-tight">New token <span class="text-slate-400 font-medium">/ patient registration</span></h2></x-slot>
    <div class="grid lg:grid-cols-3 gap-4" x-data="{ svc: '{{ old('service_id') }}' }">
        <div class="qc-card p-5 h-fit">
            <h3 class="font-bold">🔍 Returning patient?</h3><p class="text-xs text-slate-500">Search by phone to prefill instantly.</p>
            <form method="GET" action="{{ route('tokens.create') }}" class="flex gap-2 mt-3"><input name="phone" value="{{ request('phone') }}" placeholder="01XXXXXXXXX" class="qc-input"><button class="qc-btn-dark shrink-0">Find</button></form>
            @if(isset($prefill) && $prefill)<p class="mt-3 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl px-3 py-2">✓ Found {{ $prefill->name }} — details prefilled.</p>@endif
            <div class="mt-4 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white p-4 text-sm"><p class="font-bold">How it works</p><p class="text-indigo-100 text-xs mt-1">Pick service → doctor filters live → token auto-numbers (G-001…) & assigns the right counter.</p></div>
        </div>
        <form method="POST" action="{{ route('tokens.store') }}" class="qc-card p-6 lg:col-span-2">@csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="qc-label">Patient name *</label><input name="name" value="{{ old('name', $prefill->name ?? '') }}" required placeholder="e.g. Rahim Uddin" class="qc-input mt-1"></div>
                <div><label class="qc-label">Phone *</label><input name="phone" value="{{ old('phone', request('phone', $prefill->phone ?? '')) }}" required placeholder="01XXXXXXXXX" class="qc-input mt-1"></div>
                <div><label class="qc-label">Age</label><input name="age" type="number" min="0" max="150" value="{{ old('age', $prefill->age ?? '') }}" class="qc-input mt-1"></div>
                <div><label class="qc-label">Gender</label><select name="gender" class="qc-input mt-1"><option value="">— Select —</option><option value="male" @selected(old('gender',$prefill->gender ?? '')=='male')>Male</option><option value="female" @selected(old('gender',$prefill->gender ?? '')=='female')>Female</option><option value="other" @selected(old('gender',$prefill->gender ?? '')=='other')>Other</option></select></div>
            </div>
            <div class="mt-4"><label class="qc-label">Address</label><input name="address" value="{{ old('address', $prefill->address ?? '') }}" placeholder="Area / street" class="qc-input mt-1"></div>
            <div class="grid sm:grid-cols-2 gap-4 mt-4">
                <div><label class="qc-label">Service *</label><select id="service_id" name="service_id" x-model="svc" required class="qc-input mt-1"><option value="">Select service…</option>@foreach($services as $s)<option value="{{ $s->id }}" @selected(old('service_id')==$s->id)>{{ $s->name }} ({{ $s->prefix }})</option>@endforeach</select></div>
                <div><label class="qc-label">Doctor * <span class="font-normal text-slate-400" x-show="svc">(filtered live)</span></label><select id="doctor_id" name="doctor_id" required class="qc-input mt-1"><option value="">Select service first…</option></select></div>
            </div>
            <div class="flex flex-wrap gap-2 mt-6"><button class="qc-btn-primary !px-8 !py-3">🎫 Issue token</button><a href="{{ route('tokens.index') }}" class="qc-btn-soft">View today's list</a></div>
        </form>
    </div>
    <script>
        const doctors = {!! $doctorsJson !!};
        const svc = document.getElementById('service_id'), doc = document.getElementById('doctor_id');
        const oldDoc = "{{ old('doctor_id') }}";
        function fill() {
            const sid = svc.value, list = doctors.filter(d => String(d.service_id) === String(sid));
            doc.innerHTML = list.length ? '<option value="">Choose doctor…</option>' : '<option value="">No doctors in this service</option>';
            list.forEach(d => { const o = document.createElement('option'); o.value = d.id;
                o.textContent = d.name + (d.room ? ' • Room ' + d.room : '');
                if (String(d.id) === oldDoc) o.selected = true; doc.appendChild(o); });
        }
        svc.addEventListener('change', fill); fill();
    </script>
</x-app-layout>
