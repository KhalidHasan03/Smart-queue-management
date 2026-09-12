<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'QueueCare') }} — Sign in</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased" style="font-family:'Plus Jakarta Sans',Figtree,sans-serif;background:radial-gradient(900px 400px at 15% 0%,#4f46e5 0%,transparent 60%),radial-gradient(800px 400px at 90% 10%,#a855f7 0%,transparent 55%),#0b1023;min-height:100vh">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-md">
                <div class="text-center mb-5"><div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 via-violet-500 to-fuchsia-500 items-center justify-center text-2xl font-extrabold text-white shadow-2xl">Q</div>
                <h1 class="text-white text-2xl font-extrabold mt-3">QueueCare</h1><p class="text-indigo-200 text-sm">Smart serial & queue for modern clinics</p></div>
                <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-8">{{ $slot }}</div>
                <p class="text-center text-indigo-200/70 text-xs mt-4">Super: superadmin@ • Admin: admin@ • Reception: reception@ • Operator: operator@ • Staff: staff@ • Display: display@ (all @queuecare.local, pass: password123)</p>
            </div>
        </div>
    </body>
</html>
