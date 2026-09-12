<x-guest-layout>
    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-500">Recovery</p>
    <h2 class="text-[26px] font-extrabold tracking-tight text-slate-900 mt-1">Reset your password</h2>
    <p class="text-sm text-slate-500 mt-1 mb-6">Enter your work email and we'll send you a secure reset link.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Work email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="you@clinic.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full mt-6 !py-3.5 !text-[15px]">
            {{ __('Send reset link →') }}
        </x-primary-button>

        <p class="text-center text-sm text-slate-500 mt-4">
            <a class="font-bold text-indigo-600 hover:text-indigo-500" href="{{ route('login') }}">← Back to sign in</a>
        </p>
    </form>
</x-guest-layout>
