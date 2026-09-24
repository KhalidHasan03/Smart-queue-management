<x-guest-layout>
    <div class="text-center">
        <div class="text-5xl mb-4">📦</div>
        <h1 class="text-2xl font-extrabold mb-2">File too large</h1>
        <p class="text-sm text-slate-500 mb-6">
            This server accepts uploads up to <strong>{{ $limit }}</strong> per request.
            Compress or re-encode the video and try again.
        </p>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="qc-btn-primary inline-block">← Go back</a>
    </div>
</x-guest-layout>