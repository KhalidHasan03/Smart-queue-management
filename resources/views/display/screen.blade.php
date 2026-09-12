<!DOCTYPE html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>QueueCare • Live Display</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,700,800&display=swap" rel="stylesheet" />
<style>body{font-family:'Plus Jakarta Sans',sans-serif;background:radial-gradient(1200px 600px at 20% -10%,#312e81 0%,transparent 60%),radial-gradient(1000px 500px at 90% 0%,#6d28d9 0%,transparent 55%),#070b1a;color:#fff}
.flash{animation:flash 1.1s infinite}@keyframes flash{50%{opacity:.55}}
.ticker{animation:slide 22s linear infinite}@keyframes slide{from{transform:translateX(20%)}to{transform:translateX(-100%)}}
.card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);backdrop-filter:blur(12px)}</style>
</head>
<body class="min-h-screen">
<div class="max-w-7xl mx-auto p-5 sm:p-8">
    <div class="flex flex-wrap items-center justify-between gap-3 pb-5 border-b border-white/10">
        <div class="flex items-center gap-3"><div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 via-violet-500 to-fuchsia-500 flex items-center justify-center text-2xl font-extrabold shadow-xl">Q</div>
        <div><h1 id="clinic" class="text-2xl sm:text-3xl font-extrabold tracking-tight">QueueCare</h1><p class="text-xs text-indigo-200 tracking-widest uppercase">Live serial display • please watch your number</p></div></div>
        <div class="text-right"><p id="date" class="text-indigo-200 text-sm"></p><p id="time" class="text-3xl font-mono font-extrabold"></p></div>
    </div>
    <div id="now" class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6"></div>
    <div class="card rounded-2xl mt-6 p-5"><h2 class="text-sm font-bold uppercase tracking-widest text-indigo-200">Up next in queue</h2><div id="upcoming" class="flex flex-wrap gap-3 mt-3"></div></div>
    <div class="overflow-hidden mt-4 text-indigo-200/70 text-sm"><div id="ticker" class="ticker whitespace-nowrap">✦ Please keep your token with you ✦&nbsp;</div></div>
</div>
<script>
let lastSig = '';
function beep(freq=880, t=0.25){ try{ const C = new (window.AudioContext||window.webkitAudioContext)(); const o = C.createOscillator(), g = C.createGain();
    o.connect(g); g.connect(C.destination); o.frequency.value = freq; o.type='sine'; g.gain.value=0.12; o.start(); o.stop(C.currentTime+t);}catch(e){} }
async function load() {
    try {
        const r = await fetch("{{ route('display.api') }}"); const d = await r.json();
        document.getElementById('clinic').textContent = d.clinic || 'QueueCare';
        document.getElementById('date').textContent = d.date; document.getElementById('time').textContent = d.time;
        if (d.ticker) document.getElementById('ticker').textContent = '✦ ' + d.ticker + ' ✦\u00a0';
        const secs = Math.min(30, Math.max(2, parseInt(d.refresh_secs || '4', 10)));
        if (secs * 1000 !== pollMs) { clearInterval(poller); poller = setInterval(load, secs * 1000); pollMs = secs * 1000; }
        const sig = JSON.stringify(d.now);
        if (lastSig && sig !== lastSig) { beep(880); setTimeout(()=>beep(660), 300); }
        lastSig = sig;
        document.getElementById('now').innerHTML = d.now.map(n => `
            <div class="card rounded-2xl p-6 ${n.token_no ? 'ring-2 ring-emerald-300 shadow-[0_0_40px_rgba(52,211,153,.25)]' : ''}">
                <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest">${n.counter}${n.room ? ' • Room '+n.room : ''} — ${n.service}</p>
                <p class="text-6xl font-extrabold my-2 tracking-tight ${n.token_no ? 'flash text-emerald-300' : 'text-white/20'}">${n.token_no ?? '–––'}</p>
                <p class="font-semibold">${n.patient ?? 'Waiting for next token…'}</p><p class="text-sm text-indigo-200">${n.doctor ?? ''} ${n.status ? '• '+n.status : ''}</p>
            </div>`).join('');
        document.getElementById('upcoming').innerHTML = d.upcoming.length
            ? d.upcoming.map(u => `<span class="bg-white/10 border border-white/15 px-5 py-2.5 rounded-xl text-2xl font-mono font-extrabold">${u.token_no}</span>`).join('')
            : '<span class="text-white/40">Queue clear — no waiting tokens 🎉</span>';
    } catch(e) {}
}
let pollMs = 4000, poller = null;
load(); poller = setInterval(load, pollMs);
</script>
</body></html>
