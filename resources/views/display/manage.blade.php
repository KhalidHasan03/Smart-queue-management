<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-[11px] font-bold uppercase tracking-widest text-indigo-500">TV / monitor control</p>
            <h2 class="text-2xl font-extrabold tracking-tight">Display setup 🖥️</h2></div>
            <a href="{{ route('display') }}" target="_blank" class="qc-btn-soft">Open TV screen ↗</a>
        </div>
    </x-slot>
    <form method="POST" action="{{ route('display.update') }}" class="grid lg:grid-cols-2 gap-4">@csrf @method('PUT')
        <div class="qc-card p-6">
            <h3 class="font-bold mb-1">Visible counters</h3>
            <p class="text-xs text-slate-500 mb-4">Uncheck a counter to hide it from the TV wall.</p>
            <div class="space-y-2">
                @foreach($counters as $c)
                <label class="flex items-center gap-3 rounded-xl border border-slate-100 px-4 py-3 hover:bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="visible[]" value="{{ $c->id }}" @checked(old('visible', $c->show_on_display)) class="w-4 h-4 accent-indigo-600">
                    <span class="flex-1"><b>{{ $c->name }}</b><span class="block text-xs text-slate-400">{{ $c->service->name ?? '' }}@if($c->room_no) • Room {{ $c->room_no }}@endif</span></span>
                    {!! $c->is_active ? '<span class="pill-completed">active</span>' : '<span class="pill-cancelled">off</span>' !!}
                </label>
                @endforeach
            </div>
        </div>
        <div class="qc-card p-6 space-y-4 h-fit">
            <h3 class="font-bold">Screen options</h3>
            <div><label class="qc-label">Refresh every (seconds)</label><input name="refresh_secs" type="number" min="2" max="30" value="{{ old('refresh_secs', $settings['refresh_secs']) }}" class="qc-input mt-1"></div>
            <div><label class="qc-label">Ticker message</label><input name="ticker" value="{{ old('ticker', $settings['ticker']) }}" placeholder="Shown scrolling on the TV" class="qc-input mt-1"></div>
            <label class="text-sm font-semibold flex gap-2 items-center"><input type="checkbox" name="show_patient" value="1" @checked(old('show_patient', $settings['show_patient'] === '1')) class="w-4 h-4 accent-indigo-600"> Show patient names on TV</label>
            <button class="qc-btn-primary w-full">Save display setup</button>
        </div>
    </form>
</x-app-layout>
