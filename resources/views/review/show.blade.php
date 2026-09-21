<x-guest-layout>
    @section('page-title', 'Share Your Feedback')
    <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-100 mb-4">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 110 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 11-4 0v-1a1 1 0 00-1-1H8a1 1 0 01-1-1V8a1 1 0 011-1h1a2 2 0 110-4H8a1 1 0 01-1-1V5a1 1 0 011-1h3a1 1 0 001-1V4z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12h.01" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-slate-900">How was your visit?</h1>
                <p class="text-slate-600 mt-2">Your feedback helps us improve our service</p>
            </div>

            <div class="qc-card p-6 sm:p-8">
                <div class="bg-slate-50 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-3 gap-4 text-center text-sm">
                        <div>
                            <p class="text-slate-400 font-medium">Token</p>
                            <p class="font-bold text-lg text-slate-900">{{ $token->token_no }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 font-medium">Service</p>
                            <p class="font-medium text-slate-900">{{ $token->service->name }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 font-medium">Date</p>
                            <p class="font-medium text-slate-900">{{ $token->token_date->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('review.store', $token->token_no) }}" method="POST" id="reviewForm">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-3">Your Rating <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2" role="radiogroup" aria-label="Rating">
                            @for ($i = 5; $i >= 1; $i--)
                                <label class="cursor-pointer flex flex-col items-center">
                                    <input type="radio" name="rating" value="{{ $i }}" required
                                           class="sr-only peer"
                                           @if(old('rating') == $i) checked @endif>
                                    <span class="text-4xl text-slate-300 peer-checked:text-amber-400 peer-hover:text-amber-300 transition-colors"
                                          aria-label="{{ $i }} stars">
                                        ★
                                    </span>
                                    <span class="text-xs text-slate-500 mt-1 peer-checked:text-amber-600">
                                        @match($i)
                                            @case(5) Excellent @break
                                            @case(4) Good @break
                                            @case(3) Average @break
                                            @case(2) Poor @break
                                            @case(1) Terrible @break
                                        @endmatch
                                    </span>
                                </label>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="category" class="block text-sm font-medium text-slate-700 mb-2">Category (optional)</label>
                        <select name="category" id="category" class="qc-input w-full">
                            <option value="">Select a category</option>
                            <option value="service" @if(old('category') === 'service') selected @endif>Service Quality</option>
                            <option value="wait_time" @if(old('category') === 'wait_time') selected @endif>Wait Time</option>
                            <option value="staff" @if(old('category') === 'staff') selected @endif>Staff Behavior</option>
                            <option value="facility" @if(old('category') === 'facility') selected @endif>Facility & Cleanliness</option>
                            <option value="overall" @if(old('category') === 'overall') selected @endif>Overall Experience</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="comment" class="block text-sm font-medium text-slate-700 mb-2">Your Comments (optional)</label>
                        <textarea name="comment" id="comment" rows="4"
                                  class="qc-input w-full resize-none"
                                  placeholder="Tell us about your experience... (max 1000 characters)"
                                  maxlength="1000">{{ old('comment') }}</textarea>
                        <p class="mt-1 text-sm text-slate-500 text-right"><span id="charCount">0</span>/1000 characters</p>
                        @error('comment')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="display_name" class="block text-sm font-medium text-slate-700 mb-2">Display Name (optional)</label>
                        <input type="text" name="display_name" id="display_name"
                               class="qc-input w-full"
                               placeholder="How should we display your name?"
                               value="{{ old('display_name') }}"
                               maxlength="100">
                        @error('display_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6 flex items-start gap-3">
                        <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1"
                               class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                               @if(old('is_anonymous')) checked @endif>
                        <label for="is_anonymous" class="text-sm text-slate-700 cursor-pointer">
                            Post as anonymous (your name won't be shown publicly)
                        </label>
                    </div>

                    <input type="hidden" name="honeypot" value="" tabindex="-1" autocomplete="off" style="display:none;">

                    <div class="flex gap-3 pt-4 border-t border-slate-200">
                        <button type="submit"
                                class="flex-1 qc-btn-primary py-3 text-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                            Submit Review
                        </button>
                        <a href="{{ route('landing') }}" class="flex-1 qc-btn-secondary py-3 text-lg font-semibold text-center">
                            Cancel
                        </a>
                    </div>
                </form>

                <div class="mt-6 text-center text-sm text-slate-500">
                    <p>This review will be moderated before publishing.</p>
                    <p class="mt-1">By submitting, you agree to our <a href="#" class="text-indigo-600 hover:underline">Terms of Service</a> and <a href="#" class="text-indigo-600 hover:underline">Privacy Policy</a>.</p>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('landing') }}" class="text-sm text-slate-500 hover:text-slate-700">
                    ← Back to {{ $clinicName }}
                </a>
            </div>
        </div>
    </div>

    <script>
        const comment = document.getElementById('comment');
        const charCount = document.getElementById('charCount');
        comment.addEventListener('input', () => {
            charCount.textContent = comment.value.length;
        });
    </script>
</x-guest-layout>