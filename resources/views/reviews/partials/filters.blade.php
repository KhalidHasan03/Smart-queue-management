{{--
    Filter form for the review list. Shared by the admin dashboard and the
    counter page so both offer exactly the same narrowing options.

    @param string $baseUrl
    @param \Illuminate\Support\Collection $services
    @param \Illuminate\Support\Collection $doctors
    @param bool $scoped  true when the page is already counter-scoped, in which
                        case the service/doctor dropdowns are not offered.
--}}
<form method="GET" action="{{ $baseUrl }}" class="qc-card p-5">
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
            <label class="qc-label">Search</label>
            <input name="search" value="{{ request('search') }}" placeholder="Token, name, comment…" class="qc-input mt-1">
        </div>
        <div>
            <label class="qc-label">Moderation status</label>
            <select name="status" class="qc-input mt-1">
                <option value="">All statuses</option>
                @foreach(\App\Models\Review::STATUSES as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="qc-label">Rating</label>
            <select name="rating" class="qc-input mt-1">
                <option value="">All ratings</option>
                @foreach(\App\Models\Review::RATINGS as $value => $meta)
                    <option value="{{ $value }}" @selected((string) request('rating') === (string) $value)>{{ $meta['emoji'] }} {{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="qc-label">Category</label>
            <select name="category" class="qc-input mt-1">
                <option value="">All categories</option>
                @foreach(\App\Models\Review::CATEGORIES as $cat)
                    <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ ucwords(str_replace('_', ' ', $cat)) }}</option>
                @endforeach
            </select>
        </div>

        @unless($scoped ?? false)
            <div>
                <label class="qc-label">Service</label>
                <select name="service_id" class="qc-input mt-1">
                    <option value="">All services</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" @selected((string) request('service_id') === (string) $service->id)>{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="qc-label">Doctor</label>
                <select name="doctor_id" class="qc-input mt-1">
                    <option value="">All doctors</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected((string) request('doctor_id') === (string) $doctor->id)>{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
        @endunless

        <div>
            <label class="qc-label">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="qc-input mt-1">
        </div>
        <div>
            <label class="qc-label">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="qc-input mt-1">
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 mt-4">
        <button class="qc-btn-primary">Apply filters</button>
        <a href="{{ $baseUrl }}" class="qc-btn-soft">Reset</a>
        @if(request()->hasAny(['search', 'status', 'rating', 'category', 'service_id', 'doctor_id', 'date_from', 'date_to', 'preset']))
            <span class="text-xs text-slate-400">Filtered view — <a href="{{ $baseUrl }}" class="font-semibold text-indigo-600 hover:underline">clear all</a></span>
        @endif
    </div>
</form>
