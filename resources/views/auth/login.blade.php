<x-guest-layout>
    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-500">Welcome back</p>
    <h2 class="text-[26px] font-extrabold tracking-tight text-slate-900 mt-1">Sign in to Queue-Pro</h2>
    <p class="text-sm text-slate-500 mt-1 mb-6">Access your counter, queue and clinic dashboard.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Work email')" />
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">✉️</span>
                <x-text-input id="email" class="!ps-11" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@clinic.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">🔒</span>
                <x-text-input id="password" class="!ps-11 !pe-16" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 text-sm font-bold px-1">Show</button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-200" name="remember">
                <span class="ms-2 text-sm font-semibold text-slate-600">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-bold text-indigo-600 hover:text-indigo-500" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
            @endif
        </div>

        <x-primary-button class="w-full mt-6 !py-3.5 !text-[15px]">
            {{ __('Sign in →') }}
        </x-primary-button>

        <div class="mt-6 rounded-2xl bg-slate-50 border border-slate-100 p-4">
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Demo — tap a role to fill login</p>
            <div class="flex flex-wrap gap-1.5 mt-2.5">
                @foreach(['superadmin@queuecare.local' => 'Super', 'admin@queuecare.local' => 'Admin', 'reception@queuecare.local' => 'Reception', 'operator@queuecare.local' => 'Counter', 'staff@queuecare.local' => 'Staff', 'display@queuecare.local' => 'Display'] as $mail => $label)
                <button type="button" onclick="fillLogin({{ Js::from($mail) }})" class="text-xs font-bold bg-white border border-slate-200 rounded-full px-3 py-1.5 hover:border-indigo-300 hover:text-indigo-600 transition">{{ $label }}</button>
                @endforeach
            </div>
        </div>
    </form>

    <script>
        (function () {
            var toggle = document.getElementById('toggle-password');
            var pwd = document.getElementById('password');
            if (toggle && pwd) {
                toggle.addEventListener('click', function () {
                    var show = pwd.type === 'password';
                    pwd.type = show ? 'text' : 'password';
                    toggle.textContent = show ? 'Hide' : 'Show';
                });
            }
        })();
        function fillLogin(email) {
            var emailInput = document.getElementById('email');
            var pwdInput = document.getElementById('password');
            if (emailInput) emailInput.value = email;
            if (pwdInput) pwdInput.value = 'password123';
            if (emailInput) emailInput.focus();
        }
    </script>
</x-guest-layout>
