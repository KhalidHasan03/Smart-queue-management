<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME', 'Queue-Pro') }} — Hospital Queue Management</title>
    <meta name="description" content="Queue-Pro — Smart queue management for hospitals. Reduce wait times, improve patient experience.">
    <meta property="og:title" content="Queue-Pro — Hospital Queue Management">
    <meta property="og:description" content="Smart queue management for modern hospitals.">
    <meta name="robots" content="index, follow">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased" style="font-family:'Plus Jakarta Sans',Figtree,sans-serif" x-data="{ mobileOpen: false }">
    @include('landing.components.landing-nav')

    <main>
        @yield('content')
    </main>

    @include('landing.components.landing-footer')

    <script>
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const t = document.querySelector(a.getAttribute('href'));
                if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    </script>
</body>
</html>
