@extends('landing.layouts.landing')

@section('content')
<section class="pt-32 pb-20 bg-gradient-to-br from-sky-50 via-cyan-50 to-teal-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-sky-600 dark:text-cyan-400 mb-4">{{ $page['hero_subtitle'] }}</p>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-slate-900 dark:text-white">
            {{ $page['hero_title_1'] }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-teal-600">{{ $page['hero_title_2'] }}</span>
        </h1>
        <p class="mt-6 text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">{{ $page['hero_description'] }}</p>
    </div>
</section>

<section class="py-20 sm:py-28 bg-white dark:bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-5 gap-12">
            <div class="lg:col-span-3">
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-6">Send us a message</h2>
                <form x-data="{ sending: false, sent: false, error: '' }" @submit.prevent="
                    sending = true; error = '';
                    const fd = new FormData($el);
                    fetch('{{ route('contact.submit') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                        body: fd
                    }).then(r => r.json().then(d => ({ ok: r.ok, d })))
                    .then(({ ok, d }) => { if (!ok) throw new Error(d.message || 'Failed'); sent = true; })
                    .catch(e => { error = e.message; })
                    .finally(() => { sending = false; });
                " class="space-y-5">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="text-sm font-semibold text-slate-600 dark:text-slate-400 block mb-1.5">Full Name *</label>
                            <input name="name" type="text" required class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-slate-50/60 dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-cyan-400 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-cyan-100 dark:focus:ring-cyan-900/30 transition" placeholder="Dr. Ahmed">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-600 dark:text-slate-400 block mb-1.5">Email *</label>
                            <input name="email" type="email" required class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-slate-50/60 dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-cyan-400 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-cyan-100 dark:focus:ring-cyan-900/30 transition" placeholder="ahmed@hospital.com">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="text-sm font-semibold text-slate-600 dark:text-slate-400 block mb-1.5">Phone</label>
                            <input name="phone" type="tel" class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-slate-50/60 dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-cyan-400 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-cyan-100 dark:focus:ring-cyan-900/30 transition" placeholder="+880 1XXX XXXXXX">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-600 dark:text-slate-400 block mb-1.5">Hospital / Clinic</label>
                            <input name="hospital" type="text" class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-slate-50/60 dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-cyan-400 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-cyan-100 dark:focus:ring-cyan-900/30 transition" placeholder="Al-Shifa Hospital">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600 dark:text-slate-400 block mb-1.5">Subject *</label>
                        <select name="subject" required class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-slate-50/60 dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-cyan-400 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-cyan-100 dark:focus:ring-cyan-900/30 transition">
                            <option>Demo Request</option>
                            <option>Enterprise Plan</option>
                            <option>Custom Integration</option>
                            <option>Support</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600 dark:text-slate-400 block mb-1.5">Message *</label>
                        <textarea name="message" rows="4" required class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-slate-50/60 dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-cyan-400 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-cyan-100 dark:focus:ring-cyan-900/30 transition resize-none" placeholder="Tell us about your hospital's needs..."></textarea>
                    </div>
                    <div x-show="error" class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-sm font-semibold text-red-600 dark:text-red-400" x-text="error"></div>
                    <div x-show="sent" class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                        Thank you! We'll get back to you within 24 hours.
                    </div>
                    <button type="submit" :disabled="sending" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-500 text-white font-bold text-sm shadow-lg shadow-cyan-600/20 hover:shadow-cyan-600/40 hover:from-cyan-500 hover:to-teal-400 transition-all duration-200 active:scale-[0.97] disabled:opacity-50">
                        <span x-show="!sending">Send Message</span>
                        <span x-show="sending">Sending...</span>
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 p-8 shadow-sm">
                    <h3 class="font-extrabold text-slate-900 dark:text-white mb-4">Contact Information</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-cyan-900/30 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-sky-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Email</p>
                                <a href="mailto:{{ $page['email'] }}" class="text-sm text-sky-600 dark:text-cyan-400 hover:underline">{{ $page['email'] }}</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-cyan-900/30 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-sky-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Phone</p>
                                <a href="{{ $page['phone_link'] }}" target="_blank" class="text-sm text-sky-600 dark:text-cyan-400 hover:underline">{{ $page['phone'] }}</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-cyan-900/30 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-sky-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Address</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $clinicAddress ?: 'Dhaka, Bangladesh' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-sky-600 to-teal-500 rounded-3xl p-8 text-white shadow-lg shadow-sky-600/20">
                    <h3 class="font-extrabold mb-2">Prefer a quick call?</h3>
                    <p class="text-sm text-sky-100 mb-4">Book a 15-minute demo call with our team.</p>
                    <a href="{{ $page['phone_link'] }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-sky-600 font-bold text-sm hover:bg-sky-50 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Book on WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
