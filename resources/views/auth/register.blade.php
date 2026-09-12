<x-guest-layout>
    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-500">Get started</p>
    <h2 class="text-[26px] font-extrabold tracking-tight text-slate-900 mt-1">Create your account</h2>
    <p class="text-sm text-slate-500 mt-1 mb-6">Join your clinic's queue workspace in seconds.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Full name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="e.g. Rahim Uddin" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Work email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@clinic.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="grid sm:grid-cols-2 gap-4 mt-4">
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Min. 8 characters" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm')" />
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat it" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <x-primary-button class="w-full mt-6 !py-3.5 !text-[15px]">
            {{ __('Create account →') }}
        </x-primary-button>

        <p class="text-center text-sm text-slate-500 mt-4">
            <a class="font-bold text-indigo-600 hover:text-indigo-500" href="{{ route('login') }}">{{ __('Already registered? Sign in') }}</a>
        </p>
    </form>
</x-guest-layout>
