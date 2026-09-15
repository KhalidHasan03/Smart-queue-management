<x-app-layout>
    @section('page-title', 'Settings')
    <x-slot name="header"><h2 class="text-2xl font-extrabold tracking-tight">⚙️ Clinic settings</h2></x-slot>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="qc-card p-6 max-w-xl space-y-4">@csrf @method('PUT')
        <div><label class="qc-label">Clinic name *</label><input name="clinic_name" value="{{ old('clinic_name',$settings['clinic_name'] ?? 'Queue-Pro Clinic') }}" required class="qc-input mt-1"></div>
        <div><label class="qc-label">Address</label><input name="clinic_address" value="{{ old('clinic_address',$settings['clinic_address'] ?? '') }}" class="qc-input mt-1"></div>
        <div><label class="qc-label">Token header note</label><textarea name="token_header" rows="2" class="qc-input mt-1">{{ old('token_header',$settings['token_header'] ?? '') }}</textarea></div>
        <div><label class="qc-label">Token footer note</label><textarea name="token_footer" rows="2" class="qc-input mt-1">{{ old('token_footer',$settings['token_footer'] ?? 'Please wait in waiting area') }}</textarea></div>
        <button class="qc-btn-primary">Save settings</button>
    </form>
</x-app-layout>
