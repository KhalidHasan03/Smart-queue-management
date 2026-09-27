<!DOCTYPE html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Queue-Pro • Live Display</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,700,800&display=swap" rel="stylesheet" />
<style>body{font-family:'Plus Jakarta Sans',sans-serif;background:radial-gradient(1200px 600px at 20% -10%,#312e81 0%,transparent 60%),radial-gradient(1000px 500px at 90% 0%,#6d28d9 0%,transparent 55%),#070b1a;color:#fff}
.flash{animation:flash 1.1s infinite}@keyframes flash{50%{opacity:.55}}
.ticker{animation:slide 26s linear infinite}@keyframes slide{from{transform:translateX(20%)}to{transform:translateX(-100%)}}
.card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);backdrop-filter:blur(12px)}
</style>
</head>
<body class="min-h-screen">
@php
    $pos = $advertPosition ?? 'right';
    $isLeft = $pos === 'left';
    $isBottom = $pos === 'bottom';
    $counterWidth = $isBottom ? 'lg:col-span-12' : 'lg:col-span-4';
    $advertWidth = $isBottom ? 'lg:col-span-12' : 'lg:col-span-8';
    $counterOrder = $isLeft ? 'lg:order-2' : 'lg:order-1';
    $advertOrder = $isLeft ? 'lg:order-1' : 'lg:order-2';
    $counterRows = $isBottom ? '' : 'lg:grid-rows-[minmax(0,3fr)_minmax(0,2fr)]';
@endphp
<div class="max-w-[1700px] mx-auto w-full min-h-screen flex flex-col p-4 sm:p-6 gap-4">
    <header class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-white/10">
        <div class="flex items-center gap-3"><div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 via-indigo-500 to-violet-500 flex items-center justify-center text-2xl font-extrabold shadow-xl">Q</div>
        <div><h1 id="clinic" class="text-2xl sm:text-3xl font-extrabold tracking-tight">Queue-Pro</h1><p class="text-xs text-indigo-200 tracking-widest uppercase">Live serial display • please watch your number</p></div></div>
        <div class="text-right"><p id="date" class="text-indigo-200 text-sm"></p><p id="time" class="text-3xl font-mono font-extrabold"></p></div>
    </header>

    <div data-advert-position="{{ $pos }}" class="grid grid-cols-1 lg:grid-cols-12 gap-4 flex-1 min-h-0 items-stretch">
        <div class="{{ $counterWidth }} {{ $counterOrder }} grid grid-cols-1 gap-4 min-h-0 {{ $counterRows }}">
            <section class="card rounded-2xl overflow-hidden flex flex-col min-h-0">
                <div class="flex items-center justify-between px-5 py-3 bg-white/5 border-b border-white/10">
                    <h2 class="text-[11px] sm:text-sm font-bold uppercase tracking-widest text-indigo-200">Recent Display</h2>
                    <span class="text-[10px] sm:text-xs text-white/40">Live counters</span>
                </div>
                <div class="flex-1 overflow-auto min-h-0">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0">
                        <tr class="bg-white/10 border-b border-white/10 text-[10px] uppercase tracking-widest text-indigo-200">
                            <th class="px-5 py-3">Counter</th>
                            <th class="px-5 py-3">Token</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                        </thead>
                        <tbody id="nowTable"></tbody>
                    </table>
                </div>
            </section>

            <section class="card rounded-2xl p-3 sm:p-4 flex flex-col min-h-0 overflow-auto">
                <h2 class="text-[11px] sm:text-sm font-bold uppercase tracking-widest text-indigo-200 mb-2">Upcoming in Queue</h2>
                <div id="upcoming" class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 2xl:grid-cols-6 gap-1.5 sm:gap-2 content-start"></div>
            </section>
        </div>

        <aside class="{{ $advertWidth }} {{ $advertOrder }} card rounded-2xl overflow-hidden flex flex-col min-h-0">
            <div class="flex items-center justify-between px-5 py-3 bg-white/5 border-b border-white/10">
                <h2 class="text-sm font-bold uppercase tracking-widest text-indigo-200">Advertisement</h2>
                <span id="advertIndex" class="text-xs text-white/40 font-mono"></span>
            </div>
            <div id="advert" class="flex-1 min-h-[240px]"></div>
        </aside>

        <section class="lg:col-span-12 card rounded-2xl p-4 flex flex-col min-h-0 overflow-hidden">
            <h2 class="text-[11px] sm:text-sm font-bold uppercase tracking-widest text-indigo-200 mb-2">Notice</h2>
            <div class="flex-1 flex items-center overflow-hidden min-h-0">
                <div id="notice" class="ticker whitespace-nowrap text-base sm:text-lg">✦ Please keep your token with you ✦&nbsp;</div>
            </div>
        </section>
    </div>
</div>
<script>
const STATUS_PILLS = {
    waiting:   { label:'Waiting',   cls:'bg-amber-400/15 text-amber-300 border-amber-300/30' },
    calling:   { label:'Calling',   cls:'bg-blue-400/15 text-blue-300 border-blue-300/40 animate-pulse' },
    serving:   { label:'Serving',   cls:'bg-teal-400/15 text-teal-300 border-teal-300/40' },
    completed: { label:'Completed', cls:'bg-emerald-400/15 text-emerald-300 border-emerald-300/30' },
    skipped:   { label:'Skipped',   cls:'bg-orange-400/15 text-orange-300 border-orange-300/30' },
    cancelled: { label:'Cancelled', cls:'bg-red-400/15 text-red-300 border-red-300/30' },
};
let lastSig = '', lastAdvertSig = '', advertTimer = null, advertIdx = 0;
function beep(freq=880, t=0.25){ try{ const C = new (window.AudioContext||window.webkitAudioContext)(); const o = C.createOscillator(), g = C.createGain();
    o.connect(g); g.connect(C.destination); o.frequency.value = freq; o.type='sine'; g.gain.value=0.12; o.start(); o.stop(C.currentTime+t);}catch(e){} }
function renderNow(list){
    const tbody = document.getElementById('nowTable');
    if(!list || !list.length){ tbody.innerHTML = '<tr><td colspan="3" class="px-5 py-8 text-center text-white/35">No counters on display yet.</td></tr>'; return; }
    tbody.innerHTML = list.map(n => {
        const st = STATUS_PILLS[n.status] || {};
        const token = n.token_no
            ? `<span class="text-lg sm:text-xl font-mono font-extrabold tracking-tight break-all ${n.is_live ? 'flash text-emerald-300' : 'text-white'}">${n.token_no}</span>`
            : '<span class="text-lg sm:text-xl font-mono font-extrabold text-white/20">–––</span>';
        const status = n.token_no
            ? `<span class="inline-block border px-3 py-1 rounded-full text-xs sm:text-sm font-bold uppercase tracking-wider ${st.cls || 'bg-white/10 text-white/70 border-white/20'}">${st.label || n.status_label || n.status}</span>`
            : (n.is_open === false
                ? '<span class="inline-block border border-rose-300/30 bg-rose-400/15 text-rose-300 px-3 py-1 rounded-full text-xs sm:text-sm font-bold uppercase tracking-wider">Closed</span>'
                : '<span class="text-xs text-white/25">No token yet</span>');
        const meta = [n.room ? 'Room '+n.room : '', n.service].filter(Boolean).join(' • ');
        const closedBadge = n.is_open === false && !n.is_live
            ? ' <span class="align-middle text-[9px] sm:text-[10px] uppercase tracking-widest font-bold text-rose-300/80">• closed</span>'
            : '';
        return `<tr class="border-b border-white/5 ${n.is_live ? 'bg-emerald-400/5' : ''}">
            <td class="px-5 py-3"><div class="font-bold text-base sm:text-lg break-words">${n.counter}${closedBadge}</div>${meta ? '<div class="text-[10px] sm:text-xs text-indigo-200/70 break-words">'+meta+'</div>' : ''}</td>
            <td class="px-5 py-3">${token}</td>
            <td class="px-5 py-3">${status}</td>
        </tr>`;
    }).join('');
}
function renderUpcoming(list, paused){
    const el = document.getElementById('upcoming');
    const empty = paused > 0
        ? `<span class="col-span-full text-white/40 text-sm">No counters are open right now${paused === 1 ? ' — 1 patient is' : ' — '+paused+' patients are'} waiting. Please check back shortly.</span>`
        : '<span class="col-span-full text-white/40 text-sm">Queue clear — no waiting tokens 🎉</span>';
    el.innerHTML = list.length
        ? list.map(u => `<span class="bg-white/10 border border-white/15 px-2.5 py-1.5 rounded-lg text-center"><span class="block text-sm sm:text-base font-mono font-extrabold leading-tight break-all">${u.token_no}</span>${u.counter ? '<span class="block text-[10px] leading-tight text-indigo-200/70 mt-0.5 truncate">'+u.counter+'</span>' : ''}</span>`).join('')
        : empty;
}
function renderAdvert(a){
    const pane = document.getElementById('advert');
    if(!a || !a.enabled || !a.items || !a.items.length){ pane.innerHTML = '<div class="flex h-full w-full flex-col items-center justify-center text-center p-6 text-white/35"><div class="text-4xl font-extrabold">A</div><p class="mt-2 text-sm">No advertisement scheduled<br>by admin</p></div>'; document.getElementById('advertIndex').textContent=''; return; }
    const item = a.items[advertIdx % a.items.length];
    pane.innerHTML = item.media_type === 'text'
        ? `<div class="flex h-full w-full items-center justify-center text-center p-6 break-words"><p class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-extrabold leading-snug">${item.text}</p></div>`
        : item.media_type === 'youtube'
        ? (item.has_embed
            ? `<div class="flex h-full w-full items-center justify-center"><div class="relative w-full max-h-full aspect-video">${item.youtube_embed}</div></div>`
            : `<div class="flex h-full w-full items-center justify-center"><div class="relative w-full max-h-full aspect-video"><iframe class="h-full w-full absolute inset-0" src="${item.youtube}" title="${item.title}" allow="autoplay; picture-in-picture; encrypted-media" allowfullscreen></iframe></div></div>`)
        : item.media_type === 'video'
        ? `<div class="flex h-full w-full items-center justify-center p-4"><video class="max-h-full max-w-full object-contain" src="${item.video}" type="${item.media_mime || 'video/mp4'}" autoplay muted loop playsinline></video></div>`
        : `<div class="flex h-full w-full items-center justify-center p-4"><img src="${item.image}" class="max-h-full max-w-full object-contain" alt="${item.title}"></div>`;
    const total = a.items.length;
    document.getElementById('advertIndex').textContent = (advertIdx % total + 1) + ' / ' + total;
}
function cycleTo(a, idx){
    advertIdx = idx % a.items.length;
    renderAdvert(a);
    const item = a.items[advertIdx];
    let secs = Math.max(3, parseInt(item.duration_secs || a.duration_secs || '15', 10));
    const pane = document.getElementById('advert');
    const video = item.media_type === 'video' && pane ? pane.querySelector('video') : null;
    const arm = () => {
        clearTimeout(advertTimer);
        secs = Math.min(600, Math.max(3, isFinite(secs) ? secs : 15));
        advertTimer = setTimeout(() => cycleTo(a, advertIdx + 1), secs * 1000);
    };
    if (video) {
        // A video that never fires loadedmetadata (unsupported codec, dead file,
        // blocked network, wrong MIME) must not stall the playlist forever, so
        // we arm the rotation on metadata, error, or a short safety timeout.
        const fallback = () => setTimeout(() => { secs = Math.min(secs, 12); arm(); }, 4000);
        const timer = fallback();
        const onLoad = () => {
            clearTimeout(timer);
            if (video.duration && isFinite(video.duration) && video.duration > 1) {
                secs = Math.max(3, Math.round(video.duration));
            }
            arm();
        };
        if (video.readyState >= 1) onLoad();
        else {
            video.addEventListener('loadedmetadata', onLoad, { once: true });
            video.addEventListener('error', onLoad, { once: true });
        }
    } else {
        arm();
    }
}
function scheduleAdvert(a){
    const sig = JSON.stringify(a || null);
    if (sig === lastAdvertSig) return;
    lastAdvertSig = sig;
    clearTimeout(advertTimer); advertTimer = null; advertIdx = 0;
    if (!a || !a.enabled || !a.items || !a.items.length) { renderAdvert(a); return; }
    if (a.mode === 'single') { renderAdvert(a); return; }
    cycleTo(a, 0);
}
async function load() {
    try {
        const r = await fetch("{{ route('display.api') }}"); const d = await r.json();
        document.getElementById('clinic').textContent = d.clinic || 'Queue-Pro';
        document.getElementById('date').textContent = d.date; document.getElementById('time').textContent = d.time;
        document.getElementById('notice').textContent = '✦ ' + (d.ticker || 'Please keep your token with you') + ' ✦\u00a0';
        const secs = Math.min(30, Math.max(2, parseInt(d.refresh_secs || '4', 10)));
        if (secs * 1000 !== pollMs) { clearInterval(poller); poller = setInterval(load, secs * 1000); pollMs = secs * 1000; }
        const sig = JSON.stringify(d.now);
        if (lastSig && sig !== lastSig) { beep(880); setTimeout(beep, 300, 660); }
        lastSig = sig;
        renderNow(d.now);
        renderUpcoming(d.upcoming, d.paused || 0);
        scheduleAdvert(d.advert);
    } catch(e) {}
}
let pollMs = 4000, poller = null;
load(); poller = setInterval(load, pollMs);
</script>
</body></html>