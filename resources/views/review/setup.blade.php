<x-app-layout>
    @section('page-title', 'Review Kiosk Setup')
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-indigo-500">Patient feedback</p>
                <h2 class="text-2xl font-extrabold tracking-tight">Review kiosk setup 💬</h2>
            </div>
            <a href="{{ route('review.kiosk') }}" target="_blank" class="qc-btn-soft">Open kiosk ↗</a>
        </div>
    </x-slot>

    @php
        // Setting keys are dotted (e.g. "reviews.kiosk_headline"), but a dot is
        // not valid in an HTML field name — PHP rewrites it to an underscore on
        // submit. So the form posts dot-free field names and the controller maps
        // them back via ReviewSettings::field().
        $enabled   = \App\Support\ReviewSettings::ENABLED;
        $headline  = \App\Support\ReviewSettings::HEADLINE;
        $instruction = \App\Support\ReviewSettings::INSTRUCTION;
        $idleSecs  = \App\Support\ReviewSettings::IDLE_SECS;
        $autoFrom  = \App\Support\ReviewSettings::AUTO_APPROVE_FROM;
        $reqComment = \App\Support\ReviewSettings::REQUIRE_COMMENT_BELOW;
        $throttle  = \App\Support\ReviewSettings::THROTTLE_PER_MIN;
        $field = fn ($key) => \App\Support\ReviewSettings::field($key);
    @endphp

    @if(session('success'))
        <div class="qc-toast-success mb-4">✓ {{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('reviews.setup.update') }}" class="grid lg:grid-cols-2 gap-4">@csrf @method('PUT')
        <div class="qc-card p-6 space-y-5">
            <div>
                <h3 class="font-bold">Availability</h3>
                <p class="text-xs text-slate-500">Turn the kiosk off to hide the public review page entirely.</p>
            </div>
            <label class="flex items-center gap-3 rounded-xl border border-slate-100 px-4 py-3 cursor-pointer hover:bg-slate-50">
                <input type="hidden" name="{{ $field($enabled) }}" value="0">
                <input type="checkbox" name="{{ $field($enabled) }}" value="1"
                       @checked(old($field($enabled), $settings[$enabled] ?? '1') === '1')
                       class="w-4 h-4 accent-indigo-600">
                <span class="font-semibold text-sm">Kiosk is live</span>
            </label>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Patient-facing link</p>
                <code class="text-sm text-indigo-700 break-all">{{ $kioskUrl }}</code>
                <p class="text-xs text-slate-400 mt-2">Also printed on every token slip, and linked from the counter queue.</p>
            </div>
        </div>

        <div class="qc-card p-6 space-y-5">
            <div>
                <h3 class="font-bold">Screen text</h3>
                <p class="text-xs text-slate-500">What patients see on the attract screen.</p>
            </div>
            <div>
                <label class="qc-label">Headline</label>
                <input name="{{ $field($headline) }}" maxlength="120"
                       value="{{ old($field($headline), $settings[$headline]) }}" class="qc-input mt-1">
                @error($field($headline)) <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="qc-label">Instruction</label>
                <input name="{{ $field($instruction) }}" maxlength="255"
                       value="{{ old($field($instruction), $settings[$instruction]) }}" class="qc-input mt-1">
                @error($field($instruction)) <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="qc-label">Auto-reset after (seconds)</label>
                <input name="{{ $field($idleSecs) }}" type="number" min="5" max="300"
                       value="{{ old($field($idleSecs), $settings[$idleSecs]) }}" class="qc-input mt-1">
                <p class="text-xs text-slate-400 mt-1">The screen returns to the attract view after this long with no activity.</p>
            </div>
        </div>

        <div class="qc-card p-6 space-y-5">
            <div>
                <h3 class="font-bold">Moderation policy</h3>
                <p class="text-xs text-slate-500">Choose which ratings publish instantly.</p>
            </div>
            <div>
                <label class="qc-label">Auto-approve from</label>
                <select name="{{ $field($autoFrom) }}" class="qc-input mt-1">
                    @foreach([1 => '😞 Bad and above', 2 => '😐 Normal and above', 3 => '🙂 Good and above (recommended)', 4 => '🤩 Excellent only', 5 => 'Never — every review waits for a person'] as $value => $label)
                        <option value="{{ $value }}" @selected(old($field($autoFrom), $settings[$autoFrom]) == $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">Ratings below this line go to the moderation queue and alert admins.</p>
            </div>
            <div>
                <label class="qc-label">Require a comment when rating is</label>
                <select name="{{ $field($reqComment) }}" class="qc-input mt-1">
                    <option value="0" @selected(old($field($reqComment), $settings[$reqComment]) == 0)>Never require a comment</option>
                    <option value="1" @selected(old($field($reqComment), $settings[$reqComment]) == 1)>😞 Bad</option>
                    <option value="2" @selected(old($field($reqComment), $settings[$reqComment]) == 2)>😐 Normal or worse</option>
                    <option value="3" @selected(old($field($reqComment), $settings[$reqComment]) == 3)>🙂 Good or worse</option>
                    <option value="4" @selected(old($field($reqComment), $settings[$reqComment]) == 4)>🤩 Any rating (always required)</option>
                </select>
            </div>
        </div>

        <div class="qc-card p-6 space-y-5 h-fit">
            <div>
                <h3 class="font-bold">Security</h3>
                <p class="text-xs text-slate-500">Rate limiting for the public kiosk endpoints.</p>
            </div>
            <div>
                <label class="qc-label">Attempts allowed per minute (per device)</label>
                <input name="{{ $field($throttle) }}" type="number" min="3" max="60"
                       value="{{ old($field($throttle), $settings[$throttle]) }}" class="qc-input mt-1">
                <p class="text-xs text-slate-400 mt-1">Keep this low. Every verify and submit counts against it.</p>
            </div>
            <button class="qc-btn-primary w-full">Save kiosk setup</button>
        </div>
    </form>
</x-app-layout>
