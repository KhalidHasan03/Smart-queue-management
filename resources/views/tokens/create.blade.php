<x-app-layout>
    @section('page-title', 'New registration')
    <x-slot name="header"><h2 class="text-2xl font-extrabold tracking-tight">New token <span class="text-slate-400 font-medium">/ patient registration</span></h2></x-slot>

    @php
        $initialRows = collect(old('services'))
            ->map(fn($row) => [
                'service_id' => (string) ($row['service_id'] ?? ''),
                'doctor_id' => (string) ($row['doctor_id'] ?? ''),
            ])->values()->all();
        if ($initialRows === []) {
            $initialRows = [['service_id' => '', 'doctor_id' => '']];
        }
        $serviceErrors = collect($errors->getBag('default')->getMessages())
            ->filter(fn($messages, $key) => $key === 'services' || str_starts_with($key, 'services.'));
    @endphp

    <script>
        window.__QC_ROWS = {!! json_encode($initialRows, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) !!};
        window.__QC_DOCTORS = {!! json_encode($doctorOptions, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) !!};
    </script>

    <div class="grid lg:grid-cols-3 gap-4">
        <div class="qc-card p-5 h-fit">
            <h3 class="font-bold">🔍 Returning patient?</h3><p class="text-xs text-slate-500">Search by phone to prefill instantly.</p>
            <form method="GET" action="{{ route('tokens.create') }}" class="flex gap-2 mt-3"><input name="phone" value="{{ request('phone') }}" placeholder="01XXXXXXXXX" class="qc-input"><button class="qc-btn-dark shrink-0">Find</button></form>
            @if(isset($prefill) && $prefill)<p class="mt-3 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl px-3 py-2">✓ Found {{ $prefill->name }} — details prefilled.</p>@endif
            <div class="mt-4 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white p-4 text-sm"><p class="font-bold">How it works</p><p class="text-indigo-100 text-xs mt-1">Add one row per service. Each row gets its own auto-numbered token and counter, all issued together for this one patient.</p></div>
        </div>

        <form method="POST" action="{{ route('tokens.store') }}" class="qc-card p-6 lg:col-span-2" x-data="tokenServices">@csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="qc-label">Patient name *</label><input name="name" value="{{ old('name', $prefill->name ?? '') }}" required placeholder="e.g. Rahim Uddin" class="qc-input mt-1"></div>
                <div><label class="qc-label">Phone *</label><input name="phone" value="{{ old('phone', request('phone', $prefill->phone ?? '')) }}" required placeholder="01XXXXXXXXX" class="qc-input mt-1"></div>
                <div><label class="qc-label">Age</label><input name="age" type="number" min="0" max="150" value="{{ old('age', $prefill->age ?? '') }}" class="qc-input mt-1"></div>
                <div><label class="qc-label">Gender</label><select name="gender" class="qc-input mt-1"><option value="">— Select —</option><option value="male" @selected(old('gender',$prefill->gender ?? '')=='male')>Male</option><option value="female" @selected(old('gender',$prefill->gender ?? '')=='female')>Female</option><option value="other" @selected(old('gender',$prefill->gender ?? '')=='other')>Other</option></select></div>
            </div>
            <div class="mt-4"><label class="qc-label">Address</label><input name="address" value="{{ old('address', $prefill->address ?? '') }}" placeholder="Area / street" class="qc-input mt-1"></div>

            <div class="mt-6">
                <div class="flex items-center justify-between gap-3">
                    <label class="qc-label">Services &amp; tokens <span class="text-red-500">*</span></label>
                    <span class="text-xs text-slate-400"><span x-text="rows.length"></span> of 10 selected</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">One token per row. Add a row for every extra service this patient needs.</p>

                @if($serviceErrors->isNotEmpty())
                    <ul class="mt-2 space-y-1 text-sm text-red-600 font-semibold">
                        @foreach($serviceErrors as $messages)
                            @foreach($messages as $message)<li>{{ $message }}</li>@endforeach
                        @endforeach
                    </ul>
                @endif

                <div class="mt-3 space-y-2">
                    <template x-for="(row, i) in rows" :key="i">
                        <div class="grid grid-cols-12 gap-2 items-stretch">
                            <div class="col-span-12 sm:col-span-5">
                                <select x-model="row.service_id" @change="row.doctor_id = ''"
                                        :name="'services[' + i + '][service_id]'" required class="qc-input h-full">
                                    <option value="">Select service…</option>
                                    @foreach($services as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->prefix }})</option>@endforeach
                                </select>
                            </div>
                            <div class="col-span-10 sm:col-span-5">
                                <select x-model="row.doctor_id" :name="'services[' + i + '][doctor_id]'" required
                                        class="qc-input h-full" :disabled="! row.service_id">
                                    <option value="">Choose doctor…</option>
                                    <template x-for="d in doctorsFor(row.service_id)" :key="d.id">
                                        <option :value="d.id" :text="d.name + (d.room ? ' • Room ' + d.room : '')"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-span-2 sm:col-span-2">
                                <button type="button" @click="removeRow(i)" x-show="rows.length > 1"
                                        class="w-full h-full min-h-[42px] rounded-xl border border-slate-200 text-slate-400 hover:border-red-300 hover:text-red-500 hover:bg-red-50 transition"
                                        title="Remove this service">✕</button>
                            </div>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addRow()" x-show="rows.length < 10" class="mt-3 qc-btn-soft">＋ Add another service</button>
            </div>

            <div class="flex flex-wrap gap-2 mt-6">
                <button class="qc-btn-primary !px-8 !py-3">🎫 Issue <span x-text="rows.length > 1 ? 'tokens' : 'token'"></span></button>
                <a href="{{ route('tokens.index') }}" class="qc-btn-soft">View today's list</a>
            </div>
        </form>
    </div>

    <script>
        // Vite modules are deferred, so this runs before Alpine.start() and the
        // listener is in place when alpine:init fires.
        document.addEventListener('alpine:init', () => {
            Alpine.data('tokenServices', () => ({
                max: 10,
                doctors: window.__QC_DOCTORS || [],
                rows: (window.__QC_ROWS && window.__QC_ROWS.length)
                    ? window.__QC_ROWS
                    : [{ service_id: '', doctor_id: '' }],
                doctorsFor(serviceId) {
                    if (! serviceId) return [];
                    return this.doctors.filter(d => String(d.service_id) === String(serviceId));
                },
                addRow() {
                    if (this.rows.length < this.max) this.rows.push({ service_id: '', doctor_id: '' });
                },
                removeRow(index) {
                    if (this.rows.length > 1) this.rows.splice(index, 1);
                },
            }));
        });
    </script>
</x-app-layout>
