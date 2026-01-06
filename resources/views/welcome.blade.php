<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Frequência') }}</title>

        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#2563eb">
        <link rel="apple-touch-icon" href="{{ asset('img/icons/icon-192x192.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>
    </head>
    <body class="antialiased bg-gray-50 dark:bg-black text-gray-900 dark:text-white min-h-screen flex items-center justify-center relative overflow-hidden font-sans selection:bg-blue-500 selection:text-white">

        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full max-w-4xl opacity-30 pointer-events-none">
            <div class="absolute top-0 left-0 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
            <div class="absolute top-0 right-0 w-72 h-72 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>

        <div class="absolute top-6 right-6 z-50">
            <button 
                type="button" 
                x-data 
                @click="
                    if (localStorage.theme === 'dark') {
                        localStorage.theme = 'light';
                        document.documentElement.classList.remove('dark');
                    } else {
                        localStorage.theme = 'dark';
                        document.documentElement.classList.add('dark');
                    }
                "
                class="p-3 text-gray-500 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md rounded-full hover:bg-white dark:hover:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-800 transition-all hover:scale-110 active:scale-95"
            >
                <svg class="w-6 h-6 hidden dark:block text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                <svg class="w-6 h-6 block dark:hidden text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
            </button>
        </div>

        <main class="relative z-10 w-full max-w-[380px] px-4">
            <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-800 p-8 text-center">
                
                <div class="w-20 h-20 bg-gradient-to-tr from-blue-600 to-cyan-500 rounded-2xl mx-auto flex items-center justify-center shadow-lg shadow-blue-500/30 mb-6 transform rotate-3 hover:rotate-0 transition-transform duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 tracking-tight">
                    Organize sua<br>rotina acadêmica
                </h1>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                    Controle suas faltas, organize seus horários e acompanhe seu desempenho escolar em um só lugar.
                </p>

                <div class="space-y-3">
                    {{-- BOTÃO PWA PERSONALIZADO (Começa oculto "hidden") --}}
                    <button id="installAppBtn" class="hidden md:hidden w-full py-3.5 px-6 text-center text-blue-600 bg-blue-50 hover:bg-blue-100 border-2 border-blue-100 dark:bg-gray-800 dark:border-gray-700 dark:text-blue-400 rounded-xl font-bold shadow-sm transition transform active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Instalar Aplicativo
                    </button>

                    {{-- TUTORIAL DE INSTALAÇÃO IOS (Oculto por padrão, aparece ao clicar no botão) --}}
                    <div id="iosInstallToast" class="hidden fixed bottom-6 left-4 right-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl p-4 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 animate-fade-in-up">
                        <div class="flex items-start gap-4">
                            {{-- Botão Fechar --}}
                            <button onclick="document.getElementById('iosInstallToast').classList.add('hidden')" class="absolute top-2 right-2 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center shrink-0">
                                <img src="{{ asset('img/icons/icon-192x192.png') }}" class="w-10 h-10 rounded-lg shadow-sm">
                            </div>

                            <div class="flex-1 pr-6">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Instalar no iPhone</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                                    1. Toque no botão <strong class="text-blue-600">Compartilhar</strong> 
                                    <svg class="w-4 h-4 inline-block align-text-bottom text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7a3.07 3.07 0 000-1.39l7.02-4.11A3 3 0 0018 7.91a3 3 0 10-3-3c0 .23.03.45.08.67l-7.02 4.11a3 3 0 100 4.62l7.02 4.11c-.05.21-.08.43-.08.66a3 3 0 103-3z"/></svg>
                                    abaixo.<br>
                                    2. Selecione <strong class="text-gray-900 dark:text-white">Adicionar à Tela de Início</strong> 
                                    <svg class="w-4 h-4 inline-block align-text-bottom text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>.
                                </p>
                            </div>
                        </div>
                        {{-- Seta apontando para baixo --}}
                        <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-white dark:bg-gray-800 transform rotate-45 border-r border-b border-gray-200 dark:border-gray-700"></div>
                    </div>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block w-full py-3.5 px-6 text-center text-white bg-blue-600 hover:bg-blue-700 rounded-xl font-bold shadow-lg shadow-blue-600/30 transition transform active:scale-95">
                                Acessar Painel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="block w-full py-3.5 px-6 text-center text-white bg-blue-600 hover:bg-blue-700 rounded-xl font-bold shadow-lg shadow-blue-600/30 transition transform active:scale-95">
                                Entrar
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block w-full py-3.5 px-6 text-center text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl font-bold transition transform active:scale-95">
                                    Criar conta
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">
                        © {{ date('Y') }} Frequência Certa
                    </p>
                </div>

            </div>
        </main>

        {{-- SCRIPT DE INSTALAÇÃO PWA --}}
        <script>
            let deferredPrompt;
            const installBtn = document.getElementById('installAppBtn');
            const iosToast = document.getElementById('iosInstallToast');

            // --- LÓGICA ANDROID / CHROME ---
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                installBtn.classList.remove('hidden'); // Mostra botão no Android
            });

            // --- LÓGICA IPHONE (iOS) ---
            const isIos = /iPhone|iPad|iPod/.test(navigator.userAgent) && !window.MSStream;
            const isInStandaloneMode = ('standalone' in window.navigator) && (window.navigator.standalone);

            if (isIos && !isInStandaloneMode) {
                installBtn.classList.remove('hidden'); // Mostra botão no iOS também
            }

            // --- CLICK HANDLER
            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    // Se for Android/PC, dispara a instalação nativa
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        installBtn.classList.add('hidden');
                    }
                    deferredPrompt = null;
                } else if (isIos) {
                    // Se for iOS, mostra o Toast de instruções
                    iosToast.classList.remove('hidden');
                }
            });

            // --- LIMPEZA PÓS-INSTALAÇÃO ---
            window.addEventListener('appinstalled', () => {
                installBtn.classList.add('hidden');
                iosToast.classList.add('hidden');
                deferredPrompt = null;
            });

            if (window.matchMedia('(display-mode: standalone)').matches) {
                installBtn.classList.add('hidden');
            }
        </script>

        <x-cookie-banner />
    </body>
</html>