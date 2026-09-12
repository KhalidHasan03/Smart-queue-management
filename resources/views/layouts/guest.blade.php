<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'QueueCare') }} — Sign in</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased" style="font-family:'Plus Jakarta Sans',Figtree,sans-serif">
<div class="min-h-screen flex">
    <div class="hidden lg:flex w-[46%] xl:w-[50%] flex-col justify-between relative overflow-hidden text-white p-10 xl:p-14" style="background:radial-gradient(700px 380px at 15% 0%,#6366f1 0%,transparent 60%),radial-gradient(650px 420px at 95% 90%,#a855f7 0%,transparent 55%),linear-gradient(160deg,#0b1023 0%,#171442 55%,#2b1b5b 100%)">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full opacity-20" style="background:conic-gradient(from 90deg,#818cf8,#e879f9,#818cf8)"></div>
        <div class="absolute -bottom-32 -left-20 w-[28rem] h-[28rem] rounded-full opacity-10 bg-white"></div>
        <div class="relative flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-400 via-violet-500 to-fuchsia-500 flex items-center justify-center text-2xl font-extrabold shadow-2xl">Q</div>
            <div><p class="text-xl font-extrabold tracking-tight">QueueCare</p><p class="text-indigo-200/80 text-xs tracking-[0.2em] uppercase">Serial & Queue Suite</p></div>
        </div>
        <div class="relative">
            <p class="text-indigo-200/80 text-xs font-bold uppercase tracking-[0.25em]">Clinic front-desk, perfected</p>
            <h1 class="text-4xl xl:text-5xl font-extrabold leading-[1.08] mt-3">Every patient.<br>Every token.<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 via-violet-300 to-fuchsia-300">Flowing perfectly.</span></h1>
            <div class="mt-8 space-y-4 text-sm">
                <div class="flex items-start gap-3"><span class="w-9 h-9 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center shrink-0">🎫</span><div><p class="font-bold">Instant serials & thermal print</p><p class="text-indigo-200/70 text-[13px]">Auto daily numbering per service, 80mm print-ready.</p></div></div>
                <div class="flex items-start gap-3"><span class="w-9 h-9 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center shrink-0">⚡</span><div><p class="font-bold">Counter console that keeps pace</p><p class="text-indigo-200/70 text-[13px]">Next, recall, skip and complete in one click.</p></div></div>
                <div class="flex items-start gap-3"><span class="w-9 h-9 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center shrink-0">📺</span><div><p class="font-bold">Live TV display wall</p><p class="text-indigo-200/70 text-[13px]">Now-serving flashes with sound, zero setup.</p></div></div>
            </div>
        </div>
        <p class="relative text-indigo-200/50 text-xs">Trusted by busy clinics • Secure role-based access • v2.0</p>
    </div>
    <div class="flex-1 flex items-center justify-center bg-[#eef2f7] p-4 sm:p-8" style="background-image:radial-gradient(#c7d2fe 1px,transparent 1px);background-size:22px 22px">
        <div class="w-full max-w-md">
            <div class="lg:hidden flex items-center justify-center gap-2 mb-5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 via-violet-500 to-fuchsia-500 flex items-center justify-center text-lg font-extrabold text-white">Q</div>
                <span class="font-extrabold text-lg">QueueCare</span>
            </div>
            <div class="bg-white rounded-3xl shadow-[0_24px_70px_rgb(30,27,75,0.16)] border border-white p-6 sm:p-9 relative overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500"></div>
                {{ $slot }}
            </div>
            <p class="text-center text-slate-400 text-[11px] mt-4">Protected sign-in • Your session stays secure on shared counters</p>
        </div>
    </div>
</div>
</body>
</html>
