<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'QueueCare') }} — Smart Serial & Queue</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ sidebarOpen: false }" @toggle-sidebar.window="sidebarOpen = !sidebarOpen">
<div class="min-h-screen flex">
    <div x-show="sidebarOpen" @click="sidebarOpen=false" class="fixed inset-0 bg-slate-900/50 z-20 lg:hidden" x-cloak></div>
    <aside class="fixed lg:static inset-y-0 left-0 z-30 w-[270px] shrink-0 bg-[#0c1222] text-slate-200 flex flex-col transition-transform duration-300" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="px-5 h-[72px] flex items-center gap-3 border-b border-white/10">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-500 via-violet-500 to-fuchsia-500 flex items-center justify-center text-xl font-extrabold text-white shadow-lg shadow-violet-900/50">Q</div>
            <div><p class="font-extrabold text-white tracking-tight leading-tight">QueueCare</p><p class="text-[11px] text-slate-400 capitalize">{{ \App\Support\Rbac::roleLabel(auth()->user()?->role ?? '') }} • premium</p></div>
        </div>
        @php $u = auth()->user(); @endphp
        <nav class="flex-1 overflow-y-auto p-4 space-y-1.5">
            <a href="{{ route('dashboard') }}" class="qc-navlink {{ request()->routeIs('dashboard') ? 'qc-navlink-active' : '' }}"><span>◈</span> Dashboard</a>
            @if($u?->hasPermission('serials.view'))
            <a href="{{ route('tokens.index') }}" class="qc-navlink {{ request()->routeIs('tokens.index') ? 'qc-navlink-active' : '' }}"><span>🎫</span> Tokens</a>
            @endif
            @if($u?->hasPermission('serials.create'))
            <a href="{{ route('tokens.create') }}" class="qc-navlink {{ request()->routeIs('tokens.create') ? 'qc-navlink-active' : '' }}"><span>＋</span> New Token</a>
            @endif
            @if($u?->hasPermission('queue.view') && ! $u?->hasPermission('queue.next') && $u?->role === 'staff')
            <a href="{{ route('staff.index') }}" class="qc-navlink {{ request()->routeIs('staff.*') ? 'qc-navlink-active' : '' }}"><span>🩺</span> My Patients</a>
            @endif
            @if($u?->hasPermission('queue.next'))
            <a href="{{ route('queue.index') }}" class="qc-navlink {{ request()->routeIs('queue.*') ? 'qc-navlink-active' : '' }}"><span>⚡</span> My Queue</a>
            @endif
            <a href="{{ route('display') }}" target="_blank" class="qc-navlink"><span>📺</span> Live Display <span class="ms-auto text-[10px] bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded-full">LIVE</span></a>
            @if($u?->hasPermission('display.manage'))
            <a href="{{ route('display.manage') }}" class="qc-navlink {{ request()->routeIs('display.manage') ? 'qc-navlink-active' : '' }}"><span>🖥️</span> Display Setup</a>
            @endif
            @if($u?->hasPermission('services.manage') || $u?->hasPermission('counters.manage') || $u?->hasPermission('users.view') || $u?->hasPermission('reports.view') || $u?->hasPermission('settings.manage') || $u?->hasPermission('roles.manage'))
            <p class="px-3 pt-5 pb-1 text-[11px] font-bold uppercase tracking-widest text-slate-500">Manage</p>
            @endif
            @if($u?->hasPermission('services.manage'))
            <a href="{{ route('admin.services.index') }}" class="qc-navlink {{ request()->routeIs('admin.services.*') ? 'qc-navlink-active' : '' }}"><span>🩺</span> Services</a>
            <a href="{{ route('admin.doctors.index') }}" class="qc-navlink {{ request()->routeIs('admin.doctors.*') ? 'qc-navlink-active' : '' }}"><span>👨‍⚕️</span> Doctors</a>
            @endif
            @if($u?->hasPermission('counters.manage'))
            <a href="{{ route('admin.counters.index') }}" class="qc-navlink {{ request()->routeIs('admin.counters.*') ? 'qc-navlink-active' : '' }}"><span>🏢</span> Counters</a>
            @endif
            @if($u?->hasPermission('users.view'))
            <a href="{{ route('admin.users.index') }}" class="qc-navlink {{ request()->routeIs('admin.users.*') ? 'qc-navlink-active' : '' }}"><span>👥</span> Users</a>
            @endif
            @if($u?->hasPermission('roles.manage'))
            <a href="{{ route('admin.roles.index') }}" class="qc-navlink {{ request()->routeIs('admin.roles.*') ? 'qc-navlink-active' : '' }}"><span>🔑</span> Roles & Access</a>
            @endif
            @if($u?->hasPermission('reports.view'))
            <a href="{{ route('reports.index') }}" class="qc-navlink {{ request()->routeIs('reports.*') ? 'qc-navlink-active' : '' }}"><span>📊</span> Reports</a>
            @endif
            @if($u?->hasPermission('settings.manage'))
            <a href="{{ route('admin.settings.edit') }}" class="qc-navlink {{ request()->routeIs('admin.settings.*') ? 'qc-navlink-active' : '' }}"><span>⚙️</span> Settings</a>
            @endif
        </nav>
        @if($u?->hasPermission('serials.create'))
        <div class="p-4"><div class="rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 p-4 text-white">
            <p class="text-sm font-bold">Today's flow</p><p class="text-xs text-indigo-100">Register → Call → Serve → Done</p>
            <a href="{{ route('tokens.create') }}" class="mt-3 block text-center bg-white/20 hover:bg-white/30 rounded-xl py-2 text-sm font-bold">+ Quick token</a>
        </div></div>
        @endif
    </aside>
    <div class="flex-1 min-w-0 flex flex-col">
        @include('layouts.navigation')
        @isset($header)
        <header class="px-4 sm:px-6 pt-5"><div class="max-w-7xl mx-auto">{{ $header }}</div></header>
        @endisset
        <main class="flex-1 px-4 sm:px-6 py-5"><div class="max-w-7xl mx-auto fade-in">
            <div id="qc-toasts" class="space-y-2 mb-4">
                @if(session('success'))<div class="qc-toast qc-card !border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm font-semibold text-emerald-700">✅ {{ session('success') }}</div>@endif
                @if(session('error'))<div class="qc-toast qc-card !border-red-200 bg-red-50/80 px-4 py-3 text-sm font-semibold text-red-600">⚠️ {{ session('error') }}</div>@endif
                @if($errors->any())<div class="qc-card !border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-700"><ul class="list-disc ms-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            </div>
            {{ $slot }}
            <p class="text-center text-[11px] text-slate-400 mt-8">QueueCare premium • crafted for fast front-desk & counter flow</p>
        </div></main>
    </div>
</div>
<style>[x-cloak]{display:none!important}</style>
<script>setTimeout(()=>document.querySelectorAll('.qc-toast').forEach(t=>{t.style.transition='all .5s';t.style.opacity='0';setTimeout(()=>t.remove(),500)}),4200);</script>
</body>
</html>
