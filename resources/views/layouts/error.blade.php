<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="font-sans antialiased h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    
    {{-- Background Sutil (Mesma vibe do Dashboard) --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full max-w-7xl opacity-40 dark:opacity-20">
             <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-400/30 rounded-full blur-[100px]"></div>
             <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-400/30 rounded-full blur-[100px]"></div>
        </div>
    </div>

    {{-- Wrapper Centralizado --}}
    <div class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">
        {{ $slot }}
    </div>

</body>
</html>