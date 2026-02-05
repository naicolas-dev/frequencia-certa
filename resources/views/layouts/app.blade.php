<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1E3A8A">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/png" href="/favicon.ico">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800|inter:400,500,600,700"
        rel="stylesheet" />

    <script>
        window.VAPID_PUBLIC_KEY = "{{ config('webpush.vapid.public_key') }}";
    </script>

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- Aplicando as cores exatas da Landing Page e a fonte Instrument Sans --}}

<body
    class="font-['Instrument_Sans'] antialiased bg-[#F8FAFC] dark:bg-[#0F172A] text-gray-900 dark:text-gray-100 selection:bg-[#06B6D4] selection:text-white transition-colors duration-300">

    <div class="min-h-screen flex flex-col pt-16 sm:pt-24">
        @include('layouts.navigation')

        @isset($header)
            <header
                class="bg-white/80 dark:bg-[#0F172A]/90 backdrop-blur-md shadow-sm border-b border-gray-200/50 dark:border-white/5 sticky top-0 z-20">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="flex-1 w-full">
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

    {{-- SWEETALERT --}}
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

    {{-- PWA --}}
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
    <x-ai-sidebar />
    <x-cookie-banner />
</body>

</html>