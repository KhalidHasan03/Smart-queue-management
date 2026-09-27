@php
    $ringMap = [
        'rose' => 'peer-checked:border-rose-400 peer-checked:bg-rose-500/10',
        'amber' => 'peer-checked:border-amber-400 peer-checked:bg-amber-500/10',
        'teal' => 'peer-checked:border-teal-400 peer-checked:bg-teal-500/10',
        'emerald' => 'peer-checked:border-emerald-400 peer-checked:bg-emerald-500/10',
    ];
    $emojiRing = [
        'rose' => 'peer-checked:scale-110',
        'amber' => 'peer-checked:scale-110',
        'teal' => 'peer-checked:scale-110',
        'emerald' => 'peer-checked:scale-110',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Feedback Kiosk • {{ $clinicName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: radial-gradient(1100px 560px at 20% -10%, #312e81 0%, transparent 60%), radial-gradient(900px 500px at 90% 0%, #6d28d9 0%, transparent 55%), #070b1a; }
        .kiosk-face { transition: transform .15s ease; }
        .no-keyboard { -webkit-user-select: none; user-select: none; }
    </style>
</head>
<body class="min-h-screen text-white no-keyboard" style="font-family:'Plus Jakarta Sans',Figtree,sans-serif">
<div class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-8"
     x-data="reviewKiosk(@js($prefillCode), @js($idleSeconds), @js($requireCommentBelow))"
     x-on:keydown.escape.window="reset()"
     x-on:input.debounce.2s="armIdle()"
     x-on:click.debounce="armIdle()">

    {{-- Attract / idle screen --}}
    <template x-if="step === 'attract'">
        <div class="text-center fade-in max-w-2xl">
            <div class="text-7xl mb-6" aria-hidden="true">💬</div>
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight" x-text="headline"></h1>
            <p class="text-indigo-200/80 text-lg sm:text-2xl mt-4" x-text="instruction"></p>
            <div class="mt-10 flex justify-center gap-4 text-5xl sm:text-6xl" aria-hidden="true">
                @foreach ($ratings as $meta)
                    <span class="opacity-80">{{ $meta['emoji'] }}</span>
                @endforeach
            </div>
            <button type="button" x-on:click="start()"
                    class="mt-12 qc-btn-primary text-xl px-12 py-6 rounded-2xl shadow-2xl">
                👆 Touch to start
            </button>
            <p class="text-indigo-200/40 text-xs mt-8">Your feedback is private and helps us improve.</p>
        </div>
    </template>

    {{-- Step 1: identify (review code + phone last 4 together) --}}
    <template x-if="step === 'code'">
        <div class="w-full max-w-lg fade-in">
            <button type="button" x-on:click="reset()" class="text-indigo-200/60 hover:text-white text-sm mb-6">← Back</button>
            <h2 class="text-3xl font-extrabold mb-1">Confirm your visit</h2>
            <p class="text-indigo-200/70 mb-6" x-text="instruction"></p>

            <label class="block">
                <span class="text-indigo-200/80 font-semibold text-sm">Review code from your slip</span>
                <input x-model="code" type="text" inputmode="text" autocomplete="off" autocapitalize="characters" spellcheck="false"
                       maxlength="8" placeholder="XXXXXXXX"
                       x-on:input="code = code.toUpperCase().replace(/[^A-Z0-9]/g,'')"
                       class="mt-2 w-full text-center text-3xl sm:text-4xl font-mono font-extrabold tracking-[0.3em] rounded-2xl bg-white/10 border-2 border-white/20 text-white placeholder-white/30 px-4 py-5 outline-none focus:border-teal-400 focus:bg-white/15 transition">
            </label>

            <label class="block mt-5">
                <span class="text-indigo-200/80 font-semibold text-sm">Last 4 digits of your phone</span>
                <input x-model="phoneLast4" type="tel" inputmode="numeric" maxlength="4" placeholder="••••"
                       x-on:input="phoneLast4 = phoneLast4.replace(/\D/g,'')"
                       class="mt-2 w-full text-center text-3xl font-mono font-extrabold tracking-[0.4em] rounded-2xl bg-white/10 border-2 border-white/20 text-white placeholder-white/30 px-4 py-5 outline-none focus:border-teal-400 focus:bg-white/15 transition">
            </label>

            <div class="grid grid-cols-3 gap-2 mt-3 no-keyboard">
                <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="n">
                    <button type="button" x-on:click="phoneLast4 = (phoneLast4 + n).slice(0,4)" class="py-4 rounded-xl bg-white/10 border border-white/15 text-xl font-bold active:bg-white/25" x-text="n"></button>
                </template>
                <button type="button" x-on:click="phoneLast4 = phoneLast4.slice(0,-1)" class="py-4 rounded-xl bg-white/10 border border-white/15 text-lg active:bg-white/25">⌫</button>
                <button type="button" x-on:click="phoneLast4 = (phoneLast4 + '0').slice(0,4)" class="py-4 rounded-xl bg-white/10 border-white/15 text-xl font-bold active:bg-white/25">0</button>
                <button type="button" x-on:click="verify()" class="py-4 rounded-xl bg-teal-500 text-white text-sm font-extrabold">Verify</button>
            </div>

            <p x-show="error" x-text="error" class="mt-4 text-rose-300 font-semibold text-center"></p>
            <button type="button" x-on:click="verify()" x-bind:disabled="busy || code.length < 8 || phoneLast4.length < 4"
                    class="mt-5 w-full qc-btn-primary text-lg py-5 rounded-2xl">Continue →</button>
        </div>
    </template>

    {{-- Step 2: confirm the visit details before rating --}}
    <template x-if="step === 'confirm'">
        <div class="w-full max-w-lg fade-in">
            <h2 class="text-3xl font-extrabold mb-1">Is this your visit?</h2>
            <p class="text-indigo-200/70 mb-6">Please check the details below are correct.</p>
            <div class="rounded-2xl bg-white/10 border border-white/15 p-6 space-y-3 text-lg">
                <div class="flex justify-between gap-4"><span class="text-indigo-200/70 shrink-0">Name</span><b class="text-right" x-text="visit.first_name"></b></div>
                <div class="flex justify-between gap-4"><span class="text-indigo-200/70 shrink-0">Service</span><b class="text-right" x-text="visit.service"></b></div>
                <div class="flex justify-between gap-4"><span class="text-indigo-200/70 shrink-0">Doctor</span><b class="text-right" x-text="visit.doctor"></b></div>
                <div class="flex justify-between gap-4"><span class="text-indigo-200/70 shrink-0">Token</span><b class="text-right font-mono" x-text="visit.token_no"></b></div>
                <div class="flex justify-between gap-4"><span class="text-indigo-200/70 shrink-0">Date</span><b class="text-right" x-text="visit.date"></b></div>
            </div>
            <p x-show="error" x-text="error" class="mt-4 text-rose-300 font-semibold text-center"></p>
            <div class="mt-6 flex gap-3">
                <button type="button" x-on:click="step = 'code'; error = ''" class="flex-1 qc-btn-secondary bg-white/10 text-white border-white/20 py-5 text-lg rounded-2xl">← Back</button>
                <button type="button" x-on:click="step = 'rate'; armIdle()" class="flex-[2] qc-btn-primary text-lg py-5 rounded-2xl">Yes, this is me →</button>
            </div>
        </div>
    </template>

    {{-- Step 3: rate --}}
    <template x-if="step === 'rate'">
        <div class="w-full max-w-2xl fade-in">
            <h2 class="text-3xl font-extrabold mb-1">How was your visit?</h2>
            <p class="text-indigo-200/70 mb-6">Tap the face that matches your experience.</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" role="radiogroup" aria-label="Rating">
                @foreach ($ratings as $value => $meta)
                    <label class="cursor-pointer">
                        <input type="radio" name="rating" value="{{ $value }}" class="sr-only peer" x-model="rating">
                        <span class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-white/20 bg-white/5 px-2 py-6 text-center transition hover:bg-white/10 {{ $ringMap[$meta['color']] }} peer-checked:border-white/60">
                            <span class="text-5xl sm:text-6xl leading-none kiosk-face {{ $emojiRing[$meta['color']] }}" aria-hidden="true">{{ $meta['emoji'] }}</span>
                            <span class="text-sm font-bold text-indigo-100">{{ $meta['label'] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>

            <div class="mt-6 grid sm:grid-cols-2 gap-4">
                <label class="block">
                    <span class="text-indigo-200/80 font-semibold text-sm">What is this about? (optional)</span>
                    <select x-model="category" class="mt-2 w-full rounded-xl bg-white/10 border-2 border-white/20 text-white px-4 py-3 outline-none focus:border-teal-400">
                        <option value="" class="text-slate-900">Choose…</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" class="text-slate-900">{{ ucwords(str_replace('_', ' ', $cat)) }}</option>
                        @endforeach
                    </select>
                </label>

                {{-- Privacy is the default: unless the patient explicitly opts
                     in, the review is stored as anonymous and their real name is
                     never attached to it. --}}
                <div class="block">
                    <span class="text-indigo-200/80 font-semibold text-sm">Your name</span>
                    <label class="mt-2 flex items-center gap-3 rounded-xl bg-white/10 border-2 border-white/20 px-4 py-3 cursor-pointer">
                        <input type="checkbox" x-model="showName" class="w-5 h-5 accent-teal-400">
                        <span class="text-sm font-semibold">Add a name to this feedback</span>
                    </label>
                    <input x-model="displayName" x-bind:disabled="!showName" maxlength="100"
                           x-bind:placeholder="showName ? 'Your name (optional)' : 'Staying anonymous'"
                           class="mt-2 w-full rounded-xl bg-white/10 border-2 border-white/20 text-white placeholder-white/40 px-4 py-3 outline-none focus:border-teal-400 disabled:opacity-40">
                </div>
            </div>

            {{-- Honeypot: hidden from patients, but naive bots fill every input. --}}
            <input type="text" x-model="honeypot" name="website" tabindex="-1" autocomplete="off"
                   aria-hidden="true"
                   style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;opacity:0;pointer-events:none;"
                   class="pointer-events-none">

            <label class="block mt-4">
                <span class="text-indigo-200/80 font-semibold text-sm">
                    Tell us more <span x-show="requireCommentBelow > 0 && rating && rating <= requireCommentBelow" class="text-rose-300">(required for this rating)</span>
                </span>
                <textarea x-model="comment" rows="3" maxlength="1000" placeholder="Share a detail that would help us improve…"
                          class="mt-2 w-full rounded-xl bg-white/10 border-2 border-white/20 text-white placeholder-white/40 px-4 py-3 outline-none focus:border-teal-400 resize-none"></textarea>
            </label>

            <p x-show="error" x-text="error" class="mt-4 text-rose-300 font-semibold text-center"></p>
            <div class="mt-6 flex gap-3">
                <button type="button" x-on:click="step = 'confirm'; error = ''" class="flex-1 qc-btn-secondary bg-white/10 text-white border-white/20 py-4 text-lg rounded-2xl">← Back</button>
                <button type="button" x-on:click="submit()" x-bind:disabled="busy || !rating"
                        class="flex-[2] qc-btn-primary text-lg py-4 rounded-2xl">Submit Feedback</button>
            </div>
        </div>
    </template>

    {{-- Step 4: thanks --}}
    <template x-if="step === 'thanks'">
        <div class="text-center fade-in max-w-xl">
            <div class="text-8xl mb-4" aria-hidden="true" x-text="submitted.emoji"></div>
            <h2 class="text-4xl font-extrabold">Thank you!</h2>
            <p class="text-indigo-200/80 text-xl mt-3" x-text="submitted.message"></p>
            <p class="text-indigo-200/50 mt-8 text-sm">This screen will reset in <span x-text="resetIn"></span>s…</p>
        </div>
    </template>
</div>

<script>
function reviewKiosk(prefillCode, idleSeconds, requireCommentBelow) {
    return {
        step: 'attract',
        headline: @js(\App\Support\ReviewSettings::headline()),
        instruction: @js(\App\Support\ReviewSettings::instruction()),
        idleSeconds: idleSeconds,
        requireCommentBelow: requireCommentBelow,
        code: prefillCode || '',
        phoneLast4: '',
        visit: {},
        rating: null,
        category: '',
        comment: '',
        showName: false,
        displayName: '',
        honeypot: '',
        error: '',
        busy: false,
        submitted: {},
        resetIn: idleSeconds,
        _idleTimer: null,
        _resetTimer: null,
        _resetPromise: null,

        csrf() { return document.querySelector('meta[name="csrf-token"]').content; },
        url(n) { return @js(route('review.kiosk')).replace(/\/$/, '') + n; },

        // Pull the first human-readable message out of a Laravel error payload.
        // Written defensively: a naive "d.errors.code[0]" throws a TypeError
        // whenever the error is attached to some other field, and that raw JS
        // error would be shown to the patient instead of a real message.
        firstError(d) {
            if (d && d.errors) {
                for (const key of Object.keys(d.errors)) {
                    const val = d.errors[key];
                    if (Array.isArray(val) && val.length) return val[0];
                    if (typeof val === 'string' && val) return val;
                }
            }
            return (d && d.message) || 'Something went wrong. Please try again.';
        },

        start() {
            this.error = '';
            this.step = 'code';
            this.armIdle();
            // A prefilled code is not verified here: the phone confirmation
            // (step 1) is still required, so jumping straight to verify would
            // only flash an error before the patient has typed anything.
        },

        armIdle() {
            // Never arm while a request is in flight: a timer firing mid-request
            // would reset the screen and wipe the session that the pending
            // /verify or /submit still depends on.
            if (this.busy) return;
            this.holdIdle();
            this._idleTimer = setTimeout(() => this.reset(), this.idleSeconds * 1000);
        },

        holdIdle() {
            clearTimeout(this._idleTimer);
            this._idleTimer = null;
        },

        // A reset from the previous patient must reach the server before this
        // request writes to the session, or it would land afterwards and erase
        // the verification we are about to create.
        async settleReset() {
            if (this._resetPromise) {
                await this._resetPromise;
                this._resetPromise = null;
            }
        },

        async verify() {
            if (this.busy) return;
            this.busy = true; this.error = '';
            await this.settleReset();
            this.holdIdle();
            try {
                const r = await fetch(this.url('/verify'), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    body: JSON.stringify({ code: this.code, phone_last_4: this.phoneLast4 }),
                });
                const d = await r.json();
                if (!r.ok) throw new Error(this.firstError(d));
                this.visit = d.visit;
                this.step = 'confirm';
            } catch (e) {
                this.error = e.message;
            } finally {
                this.busy = false;
                if (this.step === 'code' || this.step === 'confirm') this.armIdle();
            }
        },

        async submit() {
            if (this.busy || !this.rating) return;
            this.busy = true; this.error = '';
            await this.settleReset();
            this.holdIdle();
            try {
                const r = await fetch(this.url('/submit'), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    body: JSON.stringify({
                        rating: this.rating, category: this.category || null,
                        comment: this.comment || null,
                        // Staying anonymous is the default; the patient must
                        // tick the box before any name is attached.
                        is_anonymous: !this.showName,
                        display_name: this.showName ? (this.displayName || null) : null,
                        honeypot: this.honeypot,
                    }),
                });
                const d = await r.json();
                if (!r.ok) throw new Error(this.firstError(d));
                this.submitted = d;
                this.step = 'thanks';
                this._resetTimer && clearInterval(this._resetTimer);
                this.resetIn = this.idleSeconds;
                this._resetTimer = setInterval(() => { this.resetIn--; if (this.resetIn <= 0) { clearInterval(this._resetTimer); this.reset(); } }, 1000);
            } catch (e) {
                this.error = e.message;
            } finally {
                this.busy = false;
                // Only re-arm if the patient is still filling in the form; the
                // thanks screen runs its own visible countdown instead.
                if (this.step === 'rate' || this.step === 'confirm') this.armIdle();
            }
        },

        reset() {
            this.holdIdle();
            clearInterval(this._resetTimer);
            this.step = 'attract';
            this.code = ''; this.phoneLast4 = ''; this.visit = {};
            this.rating = null; this.category = ''; this.comment = '';
            this.showName = false; this.displayName = ''; this.honeypot = '';
            this.error = ''; this.submitted = {}; this.busy = false;
            // Replace the URL so a reload never replays a previous patient's code.
            history.replaceState(null, '', @js(route('review.kiosk')));
            // Drop the server-side proof too, so a replayed /submit from this
            // shared browser cannot reuse the previous patient's verified visit.
            // The promise is kept so the next verify/submit waits for it.
            this._resetPromise = this.clearServerSession();
        },

        async clearServerSession() {
            try {
                await fetch(this.url('/reset'), {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    keepalive: true,
                });
            } catch (e) { /* best effort; the client state is already cleared */ }
        },
    };
}
</script>
</body>
</html>
