<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="icon" type="image/png" href="/favicon.ico">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css" />


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    {{-- GLOBAL TOAST --}}
    @if(session('toast'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toast = @json(session('toast'));

            switch (toast.type) {
                case 'success':
                    toastSuccess(toast.message);
                    break;
                case 'error':
                    toastError(toast.message);
                    break;
                case 'warning':
                    toastWarning(toast.message);
                    break;
                default:
                    toastInfo(toast.message);
            }
        });
    </script>
    @endif

    @if(session('swal'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const swal = @json(session('swal'));

            swalTailwind.fire({
                icon: swal.icon ?? 'info',
                title: swal.title ?? '',
                text: swal.text ?? '',
                confirmButtonText: 'Entendi'
            });
        });
    </script>
    @endif

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('PWA: Service Worker registrado com sucesso:', registration.scope);
                    })
                    .catch((error) => {
                        console.error('PWA: Falha ao registrar Service Worker:', error);
                    });
            });
        }
    </script>
    <x-cookie-banner />
</body>

</html>