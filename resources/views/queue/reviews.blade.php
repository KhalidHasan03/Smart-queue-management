<x-app-layout>
    @section('page-title', 'Counter Reviews')
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-indigo-500">
                    @if($counter){{ $counter->name }} • {{ $counter->service->name ?? '' }}@else Patient feedback @endif
                </p>
                <h2 class="text-2xl font-extrabold tracking-tight">Counter reviews 💬</h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    Feedback from visits served at this counter. Approve to publish, reject to hide.
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('queue.index') }}" class="qc-btn-soft">← Console</a>
                <a href="{{ route('review.kiosk') }}" target="_blank" rel="noopener" class="qc-btn-soft">📝 Kiosk ↗</a>
            </div>
        </div>
    </x-slot>

    @if(! $counter)
        <div class="qc-card p-10 text-center">
            <p class="text-3xl leading-none">🏢</p>
            <p class="font-bold mt-2">No counter assigned</p>
            <p class="text-sm text-slate-500 mt-1">Ask an admin to attach you to a counter to see its feedback.</p>
        </div>
    @else
        <div class="space-y-4">
            @include('reviews.partials.stats-cards', ['stats' => $stats, 'baseUrl' => route('queue.reviews')])

            {{-- Preset chips, derived from the shared preset list. --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Quick views</span>
                @foreach(\App\Support\ReviewFilters::PRESETS as $key => $label)
                    @php $active = request('preset') === $key; @endphp
                    <a href="{{ $active ? route('queue.reviews') : route('queue.reviews', ['preset' => $key]) }}"
                       class="px-3 py-1.5 rounded-full text-xs font-bold border transition
                              {{ $active ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-slate-200 text-slate-600 hover:border-indigo-300 hover:text-indigo-600' }}">
                        {{ $label }}
                        @if($key === 'needs_attention' && $stats['negative_pending'] > 0)
                            <span class="ms-1 {{ $active ? 'text-indigo-100' : 'text-rose-500' }}">{{ $stats['negative_pending'] }}</span>
                        @elseif($key === 'awaiting' && $stats['pending'] > 0)
                            <span class="ms-1 {{ $active ? 'text-indigo-100' : 'text-amber-500' }}">{{ $stats['pending'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>

            @include('reviews.partials.filters', [
                'baseUrl' => route('queue.reviews'),
                'services' => $services,
                'doctors' => $doctors,
                'scoped' => true,
            ])

            @include('reviews.partials.table', ['reviews' => $reviews, 'showCounter' => false])

            <p class="text-xs text-slate-400">
                Reopening or deleting a review is an administrator action. Ask the front desk if something needs to come off the record entirely.
            </p>
        </div>
    @endif
</x-app-layout>
