{{-- FAQ --}}
<section id="faq" class="py-24 sm:py-32 bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-teal-600 mb-3">FAQ</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                Questions hospitals ask <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-600">before they start.</span>
            </h2>
        </div>
        <div class="space-y-3" x-data="{ open: null }">
            @php
            $faqs = [
                ['q' => 'What is Queue-Pro?', 'a' => 'Queue-Pro is a hospital queue management system. It gives reception a live board of every patient, lets patients check in from their phone, and turns any screen into a waiting-room display.'],
                ['q' => 'Do patients need to install an app?', 'a' => 'No. Patients scan the QR code at the entrance and register in their browser. They follow their place in the queue from the same page — no download, no account, no password.'],
                ['q' => 'What hardware do I need for the display?', 'a' => 'Any television or monitor that can open a web page. A smart TV\'s browser is enough. Displays reload themselves after updates.'],
                ['q' => 'Does Queue-Pro work in Arabic?', 'a' => 'Yes. The interface, printed tickets, and spoken calls are all available in Arabic with proper right-to-left layout.'],
                ['q' => 'Can I try before paying?', 'a' => 'Yes. Every plan starts with a 30-day free trial — no card required. Cancel anytime.'],
                ['q' => 'How long does setup take?', 'a' => 'A clinic is taking real patients the same morning. Add departments, print the QR code, open the screen URL.'],
                ['q' => 'Can several receptionists use it?', 'a' => 'Yes. Each desk signs in as its own station, so multiple receptionists see each other\'s changes immediately.'],
                ['q' => 'Is patient data private?', 'a' => 'Every clinic\'s data is isolated and every request is authorised against the clinic it belongs to.'],
            ];
            @endphp
            @foreach($faqs as $i => $faq)
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden transition-all duration-300"
                 :class="open === {{ $i }} ? 'shadow-[0_8px_30px_rgb(0,0,0,0.06)] border-teal-200' : 'hover:border-slate-200'">
                <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-5 text-left gap-4">
                    <span class="text-sm font-bold text-slate-900">{{ $faq['q'] }}</span>
                     <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform duration-300" :class="open === {{ $i }} ? 'rotate-180 text-teal-500' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === {{ $i }}" x-collapse x-cloak>
                    <div class="px-6 pb-5 text-sm text-slate-500 leading-relaxed">{{ $faq['a'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
