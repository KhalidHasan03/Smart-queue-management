{{-- Reviews --}}
<section id="reviews" class="py-24 sm:py-32 bg-white dark:bg-slate-950" x-data="reviewSystem()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-600 dark:text-teal-400 mb-3">What patients say</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Verified <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-600">patient reviews.</span>
            </h2>
            <p class="mt-4 text-lg text-slate-500 dark:text-slate-400">Every review is verified by token number. Only real patients can leave feedback.</p>
        </div>

        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-start">
            <div class="bg-teal-50 dark:bg-slate-800 rounded-3xl p-8 border border-teal-100 dark:border-slate-700" x-show="step !== 'done'">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-extrabold transition-all"
                              :class="step === 'verify' ? 'bg-teal-600 text-white' : 'bg-emerald-500 text-white'">1</span>
                        <span class="text-sm font-bold" :class="step === 'verify' ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500'">Verify Visit</span>
                    </div>
                    <div class="flex-1 h-[2px] rounded-full" :class="step === 'verify' ? 'bg-slate-200 dark:bg-slate-600' : 'bg-emerald-400'"></div>
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-extrabold bg-slate-200 dark:bg-slate-600 text-slate-500 dark:text-slate-400"
                              :class="step === 'review' ? '!bg-teal-600 !text-white' : ''">2</span>
                        <span class="text-sm font-bold" :class="step === 'review' ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500'">Leave Review</span>
                    </div>
                </div>

                <div x-show="step === 'verify'">
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white mb-1">Find your visit</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Enter your token number and name to verify your appointment.</p>
                    <div x-show="error" class="mb-4 p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-sm font-semibold text-red-600 dark:text-red-400" x-text="error"></div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Token Number *</label>
                            <input x-model="verifyToken" type="text" placeholder="e.g. G-001" class="mt-1.5 w-full rounded-xl border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-teal-400 focus:ring-4 focus:ring-teal-100 dark:focus:ring-teal-900/30 transition font-mono font-bold">
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Your Name *</label>
                            <input x-model="verifyName" type="text" placeholder="Name on the token" class="mt-1.5 w-full rounded-xl border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-teal-400 focus:ring-4 focus:ring-teal-100 dark:focus:ring-teal-900/30 transition">
                        </div>
                        <button @click="verifyVisit()" :disabled="verifying"
                                class="w-full py-3.5 text-sm font-bold text-white bg-gradient-to-r from-teal-600 to-emerald-600 rounded-2xl shadow-lg shadow-teal-600/20 hover:shadow-teal-600/40 transition-all disabled:opacity-50 active:scale-[0.98]">
                            <span x-show="!verifying">Verify Visit →</span>
                            <span x-show="verifying">Verifying...</span>
                        </button>
                    </div>
                </div>

                <div x-show="step === 'review'">
                    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 mb-6">
                        <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">✓ Visit verified</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-1" x-text="verifiedInfo"></p>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white mb-1">How was your experience?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Your feedback helps us improve.</p>
                    <div x-show="error" class="mb-4 p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-sm font-semibold text-red-600 dark:text-red-400" x-text="error"></div>
                    <div class="mb-6">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3 block">Rating *</label>
                        <div class="flex gap-1.5">
                            @foreach([1,2,3,4,5] as $star)
                            <button @click="rating = {{ $star }}" class="w-11 h-11 rounded-xl transition-all duration-200 flex items-center justify-center star-btn"
                                    :class="rating >= {{ $star }} ? 'bg-amber-400 text-white shadow-lg shadow-amber-400/30 scale-110' : 'bg-slate-100 dark:bg-slate-700 text-slate-300 dark:text-slate-500 hover:bg-amber-100 dark:hover:bg-amber-900/30 hover:text-amber-400'">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </button>
                            @endforeach
                        </div>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-2" x-text="rating > 0 ? ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][rating] : 'Tap a star to rate'"></p>
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Comment (optional)</label>
                        <textarea x-model="comment" rows="3" placeholder="Tell us about your experience..." class="mt-1.5 w-full rounded-xl border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-teal-400 focus:ring-4 focus:ring-teal-100 dark:focus:ring-teal-900/30 transition resize-none"></textarea>
                    </div>
                    <div class="mb-6">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Display Name (optional)</label>
                        <input x-model="displayName" type="text" placeholder="How should we show your name?" class="mt-1.5 w-full rounded-xl border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-teal-400 focus:ring-4 focus:ring-teal-100 dark:focus:ring-teal-900/30 transition">
                    </div>
                    <button @click="submitReview()" :disabled="submitting || rating === 0"
                            class="w-full py-3.5 text-sm font-bold text-white bg-gradient-to-r from-teal-600 to-emerald-600 rounded-2xl shadow-lg shadow-teal-600/20 hover:shadow-teal-600/40 transition-all disabled:opacity-50 active:scale-[0.98]">
                        <span x-show="!submitting">Submit Review</span>
                        <span x-show="submitting">Submitting...</span>
                    </button>
                </div>
            </div>

            <div x-show="step === 'done'" class="bg-teal-50 dark:bg-slate-800 rounded-3xl p-8 border border-teal-100 dark:border-slate-700 text-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center mx-auto shadow-xl shadow-emerald-500/30">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="mt-6 text-2xl font-extrabold text-slate-900 dark:text-white">Thank you!</h3>
                <p class="mt-2 text-slate-500 dark:text-slate-400">Your review has been submitted and will appear after approval.</p>
                <button @click="reset()" class="mt-6 px-6 py-2.5 text-sm font-bold text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-900/30 rounded-xl hover:bg-teal-100 dark:hover:bg-teal-900/50 transition">Submit Another Review</button>
            </div>

            <div>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Recent Reviews</h3>
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full" x-text="reviews.length + ' reviews'"></span>
                </div>
                <div class="space-y-4">
                    <template x-for="(r, i) in reviews" :key="i">
                        <div class="p-5 rounded-2xl bg-teal-50 dark:bg-slate-800 border border-teal-100 dark:border-slate-700 hover:border-teal-200 dark:hover:border-slate-600 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <template x-for="s in 5">
                                        <svg class="w-4 h-4" :class="s <= r.rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-600'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </template>
                                </div>
                                <span class="text-xs font-bold text-slate-400 dark:text-slate-500" x-text="r.date"></span>
                            </div>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed" x-text="r.comment || 'No comment'"></p>
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                                <span class="text-sm font-bold text-slate-900 dark:text-white" x-text="r.display_name || 'Anonymous'"></span>
                                <span class="text-xs font-mono font-bold text-teal-500 dark:text-teal-400 bg-teal-50 dark:bg-teal-900/30 px-2 py-0.5 rounded" x-text="r.token_no"></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="reviews.length === 0">
                        <div class="text-center py-12 text-slate-400 dark:text-slate-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-200 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <p class="font-bold">No reviews yet</p>
                            <p class="text-sm mt-1">Be the first to leave a review!</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function reviewSystem() {
    return {
        step: 'verify',
        verifyToken: '',
        verifyName: '',
        verifiedInfo: '',
        verifiedTokenId: null,
        verifiedPatientId: null,
        rating: 0,
        comment: '',
        displayName: '',
        error: '',
        verifying: false,
        submitting: false,
        reviews: @json($reviews ?? []),

        async verifyVisit() {
            this.error = '';
            if (!this.verifyToken || !this.verifyName) { this.error = 'Please enter both token number and your name.'; return; }
            this.verifying = true;
            try {
                const r = await fetch('{{ route('reviews.verify') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ token_no: this.verifyToken, patient_name: this.verifyName })
                });
                const d = await r.json();
                if (!r.ok) throw new Error(d.message || 'Verification failed');
                this.verifiedTokenId = d.token_id;
                this.verifiedPatientId = d.patient_id;
                this.verifiedInfo = d.token_no + ' — ' + d.doctor + ' — ' + d.date;
                this.step = 'review';
            } catch (e) { this.error = e.message; }
            this.verifying = false;
        },
        async submitReview() {
            this.error = '';
            if (this.rating === 0) { this.error = 'Please select a rating.'; return; }
            this.submitting = true;
            try {
                const r = await fetch('{{ route('reviews.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ token_id: this.verifiedTokenId, patient_id: this.verifiedPatientId, rating: this.rating, comment: this.comment, display_name: this.displayName })
                });
                const d = await r.json();
                if (!r.ok) throw new Error(d.message || 'Submission failed');
                this.reviews.unshift(d.review);
                this.step = 'done';
            } catch (e) { this.error = e.message; }
            this.submitting = false;
        },
        reset() { this.step = 'verify'; this.verifyToken = ''; this.verifyName = ''; this.verifiedInfo = ''; this.verifiedTokenId = null; this.verifiedPatientId = null; this.rating = 0; this.comment = ''; this.displayName = ''; this.error = ''; }
    }
}
</script>
