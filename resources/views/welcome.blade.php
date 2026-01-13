<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Frequência Certa - A Jornada do Estudante</title>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/icons/icon-192x192.png') }}">
    <meta name="theme-color" content="#000000">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>

        console.log(
        "%c" +
        "███╗   ██╗██╗ ██████╗ ██████╗ ██╗      █████╗ ███████╗\n" +
        "████╗  ██║██║██╔════╝██╔═══██╗██║     ██╔══██╗██╔════╝\n" +
        "██╔██╗ ██║██║██║     ██║   ██║██║     ███████║███████╗\n" +
        "██║╚██╗██║██║██║     ██║   ██║██║     ██╔══██║╚════██║\n" +
        "██║ ╚████║██║╚██████╗╚██████╔╝███████╗██║  ██║███████║\n" +
        "╚═╝  ╚═══╝╚═╝ ╚═════╝ ╚═════╝ ╚══════╝╚═╝  ╚═╝╚══════╝\n" +
        "                                                      \n" +
        " █████╗ ██╗     ██╗   ██╗███████╗███████╗             \n" +
        "██╔══██╗██║     ██║   ██║██╔════╝██╔════╝             \n" +
        "███████║██║     ██║   ██║█████╗  ███████╗             \n" +
        "██╔══██║██║     ╚██╗ ██╔╝██╔══╝  ╚════██║             \n" +
        "██║  ██║███████╗ ╚████╔╝ ███████╗███████║             \n" +
        "╚═╝  ╚═╝╚══════╝  ╚═══╝  ╚══════╝╚══════╝             ",
        "color: #3b82f6; font-weight: bold; font-family: monospace;" // Azul da sua marca
        );

        console.log(
                "%cDesenvolvido por Nicolas Alves %c🚀 \nConfira o código em: https://github.com/naicolas-dev/frequencia-certa",
                "color: #9ca3af; font-family: sans-serif; font-size: 11px;", 
                "font-size: 16px;"
        );
        // TRAVA PWA: Se estiver rodando como aplicativo, proíbe a Landing Page
        (function () {
            const isStandalone =
            (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) ||
            window.navigator.standalone === true; // iOS antigo

            if (isStandalone && window.location.pathname === '/') {
            window.location.replace(`${window.location.origin}/dashboard`);
            }
        })();
    </script>

    <!-- GSAP (defer pra não bloquear) -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

    <!-- Evita "flash" do modo escuro -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        body { font-family: 'Instrument Sans', sans-serif; overflow-x: hidden; }
        ::-webkit-scrollbar { width: 0px; background: transparent; }

        .phone-mockup {
            box-shadow: 0 0 0 10px #1f2937, 0 20px 50px -10px rgba(0, 0, 0, 0.5);
        }
        .notch {
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }
        .text-gradient {
            background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-image: linear-gradient(to right, #3b82f6, #8b5cf6, #ec4899);
        }
        .h-screen-ios { height: 100vh; height: 100dvh; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-black text-gray-900 dark:text-white antialiased selection:bg-blue-500 selection:text-white transition-colors duration-300">

    <div id="page-loader"
     class="fixed inset-0 z-[9999] bg-[#2D6AE6] flex items-center justify-center flex-col transition-colors duration-500 dark:bg-[#1F4CC7]">
        <div class="loader-content text-white text-4xl md:text-5xl font-black tracking-tighter opacity-0 translate-y-4"
            style="font-family: 'Instrument Sans', sans-serif;">
            Frequência Certa
        </div>
    </div>

    <nav class="fixed top-0 w-full z-50 p-6 flex justify-between items-center backdrop-blur-xl bg-white/80 dark:bg-black/90 border-b border-gray-200/50 dark:border-white/10 transition-colors duration-300">
        <div class="flex items-center gap-2 font-bold text-xl tracking-tighter text-gray-900 dark:text-white">
            <x-application-logo class="block h-8 w-auto fill-current text-blue-600 dark:text-blue-500" />
            <span class="hidden sm:inline">Frequência Certa</span>
        </div>
        <div class="flex items-center gap-4">
            <button id="theme-toggle" class="p-2 rounded-full text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/10 transition-colors">
                <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            </button>
            @auth
                <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full font-bold hover:scale-105 transition shadow-lg">Painel</a>
            @else
                <a href="{{ route('login') }}" class="font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition mr-2">Entrar</a>
                <a href="{{ route('register') }}" class="px-5 py-2 bg-blue-600 text-white rounded-full font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">Começar</a>
            @endauth
        </div>
    </nav>

    <section class="h-screen-ios w-full flex flex-col items-center justify-center relative overflow-hidden bg-gray-50 dark:bg-black transition-colors duration-300">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-500/10 dark:bg-blue-600/20 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="z-10 text-center px-4 space-y-6 max-w-2xl mt-16 sm:mt-0">
            <div class="inline-block px-3 py-1 rounded-full border border-gray-200 dark:border-white/20 bg-white/50 dark:bg-white/5 text-xs font-medium tracking-wide mb-2 text-gray-600 dark:text-gray-300">
                <strong>INTEGRAÇÃO COM IA</strong> ✨
            </div>
            <h1 class="text-5xl md:text-8xl font-black tracking-tight leading-tight text-gray-900 dark:text-white">
                Posso faltar <br />
                <span class="text-gradient">hoje?</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 mx-auto leading-relaxed px-4">
                Pare de fazer contas mentais. Deixe o <strong>Frequência Certa</strong> calcular suas <strong>Faltas</strong>, gerenciar seus riscos e te salvar.
            </p>
            <div class="pt-8 animate-bounce">
                <svg class="w-6 h-6 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </div>
        </div>
    </section>

    <div id="scrolly-wrapper" class="relative w-full h-[600vh] bg-gray-50 dark:bg-black transition-colors duration-300">
        <div class="scrolly-stage h-screen-ios w-full sticky top-0 flex flex-col md:flex-row overflow-hidden">

            <div class="order-2 md:order-1 w-full md:w-1/2 h-[40vh] md:h-full flex items-center md:items-center justify-center relative md:pl-20 z-30 pointer-events-none md:pb-0 bg-transparent">

                <div id="text-1" class="absolute max-w-md opacity-0 translate-y-20 w-full px-6 text-center md:text-left">
                    <div class="md:bg-transparent bg-white/90 dark:bg-black/80 backdrop-blur-xl md:backdrop-blur-none p-6 rounded-3xl md:p-0 border border-gray-200 dark:border-white/10 md:border-none shadow-xl md:shadow-none">
                        <h2 class="text-2xl md:text-5xl font-bold mb-3 md:mb-4 text-gray-900 dark:text-white">Organização <br>Instantânea.</h2>
                        <p class="text-base md:text-xl text-gray-600 dark:text-gray-400">Esqueça as planilhas. Cadastre suas matérias de forma automatizada com a IA.</p>
                    </div>
                </div>

                <div id="text-2" class="absolute max-w-md opacity-0 translate-y-20 w-full px-6 text-center md:text-left">
                    <div class="md:bg-transparent bg-white/90 dark:bg-black/80 backdrop-blur-xl md:backdrop-blur-none p-6 rounded-3xl md:p-0 border border-gray-200 dark:border-white/10 md:border-none shadow-xl md:shadow-none">
                        <h2 class="text-2xl md:text-5xl font-bold mb-3 md:mb-4 text-blue-600 dark:text-blue-500">Matemática <br>Automática.</h2>
                        <p class="text-base md:text-xl text-gray-600 dark:text-gray-400">O Frequência Certa projeta suas faltas com base no Calendário do seu Estado.</p>
                    </div>
                </div>

                <div id="text-3" class="absolute max-w-md opacity-0 translate-y-20 w-full px-6 text-center md:text-left">
                     <div class="md:bg-transparent bg-white/90 dark:bg-black/80 backdrop-blur-xl md:backdrop-blur-none p-6 rounded-3xl md:p-0 border border-gray-200 dark:border-white/10 md:border-none shadow-xl md:shadow-none">
                        <h2 class="text-2xl md:text-5xl font-bold mb-3 md:mb-4 text-purple-600 dark:text-white">IA que aconselha você.</h2>
                        <p class="text-base md:text-xl text-gray-600 dark:text-gray-400">Na dúvida? O Oráculo analisa o calendário, suas faltas e o peso da matéria.</p>
                    </div>
                </div>

                <div id="text-4" class="absolute max-w-md opacity-0 translate-y-20 w-full px-6 text-center md:text-left">
                    <div class="md:bg-transparent bg-white/90 dark:bg-black/80 backdrop-blur-xl md:backdrop-blur-none p-6 rounded-3xl md:p-0 border border-gray-200 dark:border-white/10 md:border-none shadow-xl md:shadow-none">
                       <h2 class="text-2xl md:text-5xl font-bold mb-3 md:mb-4 text-red-600 dark:text-red-500">Alertas Anti<br>Reprovação.</h2>
                       <p class="text-base md:text-xl text-gray-600 dark:text-gray-400">Esqueceu de registrar a frequência? Está em perigo de estourar as faltas? O app te manda um puxão de orelha na hora certa se você estiver em perigo.</p>
                   </div>
               </div>

                <div id="text-5" class="absolute max-w-md opacity-0 translate-y-20 w-full px-6 text-center md:text-left">
                     <div class="md:bg-transparent bg-white/90 dark:bg-black/80 backdrop-blur-xl md:backdrop-blur-none p-6 rounded-3xl md:p-0 border border-gray-200 dark:border-white/10 md:border-none shadow-xl md:shadow-none">
                        <h2 class="text-2xl md:text-5xl font-bold mb-3 md:mb-4 text-orange-500">Vicie em não reprovar.</h2>
                        <p class="text-base md:text-xl text-gray-600 dark:text-gray-400">Transformamos registrar presença em jogo. Mantenha sua ofensiva, desbloqueie medalhas e suba de nível.</p>
                    </div>
                </div>
            </div>

            <div class="order-1 md:order-2 w-full md:w-1/2 h-[60vh] md:h-full flex items-end pb-4 md:items-center justify-center relative z-20">
                <div id="phone-container" class="relative md:static w-full h-full flex items-end md:items-center justify-center">

                    <div id="phone" class="phone-mockup relative w-[300px] h-[600px] bg-gray-800 rounded-[40px] z-20 overflow-hidden transform-gpu scale-[0.55] sm:scale-[0.6] md:scale-100 opacity-0 md:opacity-100 shadow-2xl">
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-7 bg-[#1f2937] notch z-30"></div>

                        <div class="relative w-full h-full bg-gray-50 dark:bg-[#030712] flex flex-col transition-colors duration-300">

                            <div class="pt-10 px-5 pb-3 flex justify-between items-center bg-white dark:bg-[#111827] border-b border-gray-100 dark:border-gray-800 transition-colors">
                                <div class="flex items-center gap-2">
                                    <x-application-logo />
                                    <span class="font-bold text-gray-900 dark:text-white tracking-tight text-sm">Frequência Certa</span>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden border border-gray-200 dark:border-gray-700">
                                    <img src="https://ui-avatars.com/api/?name=User&background=random" alt="Avatar" class="w-full h-full opacity-80">
                                </div>
                            </div>

                            <div class="relative flex-1 px-4 overflow-hidden bg-gray-50 dark:bg-[#030712] transition-colors">

                                <div id="scene-1" class="absolute inset-0 px-1 pt-6 transition-opacity duration-300">

                                    <div class="mb-5 p-4 rounded-xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 shadow-sm">
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Presença Global</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-2xl font-black text-gray-900 dark:text-white">92%</h3>
                                            <span class="text-xs text-emerald-500 font-bold bg-emerald-50 dark:bg-emerald-900/20 px-2 py-1 rounded-md">Excelente</span>
                                        </div>
                                    </div>

                                    <h2 class="text-sm font-bold text-gray-900 dark:text-white mb-3 px-1">Minhas Matérias</h2>
                                    <div class="space-y-3">
                                        <div class="p-4 rounded-xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 flex justify-between items-center shadow-sm">
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white text-sm">Matemática</div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                                    <span class="text-[10px] text-gray-500 dark:text-gray-400">Terça e Quinta</span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-black text-red-500">1</div>
                                                <div class="text-[9px] uppercase text-gray-400 font-bold">Falta restante</div>
                                            </div>
                                        </div>

                                        <div class="p-4 rounded-xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 flex justify-between items-center shadow-sm opacity-80">
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white text-sm">História</div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                    <span class="text-[10px] text-gray-500 dark:text-gray-400">Segunda</span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-black text-emerald-500">5</div>
                                                <div class="text-[9px] uppercase text-gray-400 font-bold">Faltas restantes</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="scene-2" class="absolute inset-0 px-2 flex flex-col justify-center translate-y-full opacity-0">
                                    <div class="bg-white dark:bg-[#111827] p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl">
                                        <div class="flex justify-between mb-2">
                                            <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase">Aulas Totais</span>
                                            <span class="text-blue-600 dark:text-blue-500 font-mono font-bold">80</span>
                                        </div>
                                        <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full mb-6 overflow-hidden">
                                            <div id="calc-bar" class="h-full bg-blue-600 w-[0%]"></div>
                                        </div>
                                        <div class="flex justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-4">
                                            <div class="text-[10px] font-bold text-gray-500 uppercase">Limite (25%)</div>
                                            <div class="text-xl font-black text-gray-900 dark:text-white">20 <span class="text-xs font-normal text-gray-400">faltas</span></div>
                                        </div>
                                    </div>
                                </div>

                                <div id="scene-3" class="absolute inset-0 px-2 flex flex-col justify-end pb-8 translate-y-full opacity-0">
                                    <div class="space-y-3">
                                        <div class="self-end bg-blue-600 text-white text-xs p-3 rounded-2xl rounded-tr-none ml-auto max-w-[80%] shadow-lg transform translate-x-10 opacity-0 chat-bubble-1">
                                            Posso faltar hoje?
                                        </div>
                                        <div class="self-start bg-white dark:bg-[#1f2937] border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-xs p-3 rounded-2xl rounded-tl-none max-w-[90%] shadow-lg transform -translate-x-10 opacity-0 chat-bubble-2">
                                            <div class="flex items-center gap-1 mb-1 text-purple-600 dark:text-purple-400 text-[9px] font-bold uppercase tracking-wider">
                                                🤖 Oráculo
                                            </div>
                                            Melhor não. Ainda é o começo do ano, guarde suas faltas.
                                        </div>
                                    </div>
                                </div>

                                <div id="scene-4" class="absolute inset-0 px-2 flex flex-col items-center pt-8 opacity-0">
                                    <div id="push-notification" class="w-full bg-white/95 dark:bg-[#1f2937]/95 backdrop-blur-md p-3 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl transform -translate-y-20">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-500 flex items-center justify-center text-xs font-bold">⚠️</div>
                                            <div class="flex-1">
                                                <div class="flex justify-between">
                                                    <span class="text-xs font-bold text-gray-900 dark:text-white">Alerta Crítico</span>
                                                    <span class="text-[9px] text-gray-500 dark:text-gray-400">1 min atrás</span>
                                                </div>
                                                <p class="text-[10px] text-gray-600 dark:text-gray-300 leading-tight mt-0.5">
                                                    Você usou 19 de 20 faltas em Física.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="scene-5" class="absolute inset-0 flex items-center justify-center scale-50 opacity-0">
                                    <div class="text-center relative">
                                        <div class="absolute inset-0 bg-orange-500 blur-3xl opacity-20 animate-pulse"></div>
                                        <div class="text-7xl mb-2 relative z-10 drop-shadow-2xl">🏆</div>
                                        <h3 class="text-2xl font-black text-gray-900 dark:text-white relative z-10">NOVA<br>CONQUISTA</h3>
                                        <p class="text-xs text-orange-500 dark:text-orange-400 font-bold uppercase tracking-widest mt-2 relative z-10">Inimigo da Reprovação</p>
                                    </div>
                                </div>
                            </div>

                            <div class="h-14 bg-white dark:bg-[#111827] border-t border-gray-200 dark:border-gray-800 flex justify-around items-center px-4 transition-colors">
                                <div class="flex flex-col items-center gap-0.5 text-blue-600 dark:text-blue-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    <span class="text-[9px] font-bold">Início</span>
                                </div>
                                <div class="flex flex-col items-center gap-0.5 text-gray-400 dark:text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-[9px] font-medium">Grade</span>
                                </div>
                                <div class="flex flex-col items-center gap-0.5 text-gray-400 dark:text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="text-[9px] font-medium">Perfil</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <section class="min-h-screen bg-white dark:bg-zinc-900 flex items-center justify-center py-24 px-4 relative transition-colors duration-300">
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-blue-500 to-transparent opacity-50"></div>

        <div class="max-w-4xl w-full bg-gray-50 dark:bg-black border border-gray-200 dark:border-white/10 rounded-[3rem] p-8 md:p-16 text-center relative overflow-hidden transition-colors shadow-xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 dark:bg-blue-600/20 blur-[100px] rounded-full"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/10 dark:bg-purple-600/20 blur-[100px] rounded-full"></div>

            <div class="relative z-10">
                <h2 class="text-4xl md:text-6xl font-bold mb-6 tracking-tight text-gray-900 dark:text-white">Pare de arriscar.</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400 mb-10 max-w-2xl mx-auto">
                    Junte-se a outros estudantes que estão faltando com inteligência.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    @guest
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-gray-900 dark:bg-white text-white dark:text-black text-lg font-bold rounded-full hover:scale-105 transition shadow-lg">
                        Criar Conta Grátis
                    </a>
                    @endguest
                    @auth
                    <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-4 bg-blue-600 text-white text-lg font-bold rounded-full hover:bg-blue-500 transition shadow-lg">
                        Abrir Meu Painel
                    </a>
                    @endauth
                </div>

                <div class="mt-8 flex flex-col items-center justify-center">
                    <button id="pwaInstallBtn" class="hidden px-6 py-3 border border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full font-semibold transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Instalar App
                    </button>
                    <p id="iosHint" class="hidden text-sm text-gray-500 mt-4 flex items-center justify-center gap-1 flex-wrap">
                        <span>Para instalar o aplicativo no iPhone: Toque em</span>

                        <svg class="w-5 h-5 text-blue-500 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span class="font-bold text-blue-500">Compartilhar</span>

                        <span>e</span>

                        <svg class="w-5 h-5 text-gray-900 dark:text-white inline-block bg-gray-200 dark:bg-gray-700 rounded-md p-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="font-bold text-gray-900 dark:text-white">Tela de Início</span>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-10 text-center text-gray-500 text-sm bg-white dark:bg-black transition-colors">
        <p>&copy; {{ date('Y') }} Frequência Certa. Feito por <a href="https://github.com/naicolas-dev" class="underline hover:text-blue-500">Nicolas Alves</a>.</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Garante que GSAP carregou (por causa do defer)
            if (!window.gsap || !window.ScrollTrigger) return;

            gsap.registerPlugin(ScrollTrigger);

            // --- THEME LOGIC ---
            const themeBtn = document.getElementById('theme-toggle');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');

            function updateIcons() {
                if (document.documentElement.classList.contains('dark')) {
                    darkIcon.classList.remove('hidden');
                    lightIcon.classList.add('hidden');
                } else {
                    lightIcon.classList.remove('hidden');
                    darkIcon.classList.add('hidden');
                }
            }

            themeBtn.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                if (document.documentElement.classList.contains('dark')) {
                    localStorage.theme = 'dark';
                } else {
                    localStorage.theme = 'light';
                }
                updateIcons();
            });

            updateIcons();

            // --- SCROLLYTELLING ANIMATION ---
            // Importante: não animar blur (pesado). Aplica blur com .set() no momento certo.
            function createTimeline() {
                let tl = gsap.timeline();

                // START
                tl.to("#phone", { opacity: 1, duration: 1 })
                  .to("#text-1", { opacity: 1, y: 0, duration: 1 }, "<")
                  .addLabel("scene1");

                // CENA 1 -> 2
                tl.to("#text-1", { opacity: 0, y: -50, duration: 1 })
                  .to("#scene-1", { opacity: 0.3, duration: 1 }, "<")
                  .set("#scene-1", { filter: "blur(4px)" }, "<")     // <-- aplica blur sem animar
                  .to("#scene-2", { y: "0%", opacity: 1, duration: 1 })
                  .to("#calc-bar", { width: "80%", duration: 1, ease: "power2.out" })
                  .to("#text-2", { opacity: 1, y: 0, duration: 1 }, "<")
                  .addLabel("scene2");

                // CENA 2 -> 3
                tl.to("#text-2", { opacity: 0, y: -50, duration: 1 })
                  .to("#scene-2", { opacity: 0, y: "20%", duration: 1 }, "<")
                  .to("#scene-3", { y: "0%", opacity: 1, duration: 1 })
                  .to(".chat-bubble-1", { x: 0, opacity: 1, duration: 0.5 })
                  .to(".chat-bubble-2", { x: 0, opacity: 1, duration: 0.5 })
                  .to("#text-3", { opacity: 1, y: 0, duration: 1 }, "<")
                  .addLabel("scene3");

                // CENA 3 -> 4
                tl.to("#text-3", { opacity: 0, y: -50, duration: 1 })
                  .to("#scene-3", { opacity: 0, duration: 1 }, "<")
                  .to("#scene-4", { opacity: 1, duration: 0.5 })
                  .to("#push-notification", { y: 0, duration: 0.8, ease: "back.out(1.7)" })
                  .to("#text-4", { opacity: 1, y: 0, duration: 1 }, "<")
                  .addLabel("scene4");

                // CENA 4 -> 5
                tl.to("#text-4", { opacity: 0, y: -50, duration: 1 })
                  .to("#scene-4", { opacity: 0, duration: 1 }, "<")
                  .to("#scene-5", { scale: 1, opacity: 1, duration: 1.5, ease: "elastic.out(1, 0.5)" })
                  .to("#text-5", { opacity: 1, y: 0, duration: 1 }, "<")
                  .addLabel("scene5");

                // END (blur aplicado sem animar)
                tl.set("#phone", { filter: "blur(10px)" })
                  .to("#phone", { scale: 0.8, opacity: 0, duration: 1 })
                  .to("#text-5", { opacity: 0, scale: 0.8, duration: 1 }, "<")
                  .set("#phone", { filter: "none" });

                return tl;
            }

            // --- Responsive + Reduced Motion (gsap.matchMedia) ---
            const mm = gsap.matchMedia();

            mm.add(
                {
                    desktop: "(min-width: 768px)",
                    mobile: "(max-width: 767px)",
                    reduce: "(prefers-reduced-motion: reduce)"
                },
                (ctx) => {
                    const { desktop, mobile, reduce } = ctx.conditions;

                    // Se usuário prefere reduzir movimento: deixa a primeira cena visível (sem pin/scrub)
                    if (reduce) {
                        gsap.set("#phone", { opacity: 1, clearProps: "transform,filter" });
                        gsap.set("#text-1", { opacity: 1, y: 0 });
                        gsap.set(["#text-2", "#text-3", "#text-4", "#text-5"], { opacity: 0, clearProps: "transform" });

                        gsap.set("#scene-1", { opacity: 1, clearProps: "filter" });
                        gsap.set("#scene-2", { opacity: 0, y: "100%" });
                        gsap.set("#scene-3", { opacity: 0, y: "100%" });
                        gsap.set("#scene-4", { opacity: 0 });
                        gsap.set("#scene-5", { opacity: 0, scale: 0.5 });

                        gsap.set(".chat-bubble-1", { opacity: 0, x: 40 });
                        gsap.set(".chat-bubble-2", { opacity: 0, x: -40 });
                        gsap.set("#push-notification", { y: -80 });

                        return;
                    }

                    // Mantém o mesmo visual/escala que você já tinha
                    if (mobile) {
                        gsap.set("#phone", { scale: 0.55, transformOrigin: "center bottom" });
                    } else {
                        gsap.set("#phone", { scale: 1, transformOrigin: "center center" });
                    }

                    const tl = createTimeline();

                    // 600vh => distância de scroll efetiva (height - viewport) = 500vh = 5 telas.
                    // End dinâmico mantém a mesma sensação, mas fica robusto em qualquer viewport.
                    const st = ScrollTrigger.create({
                        animation: tl,
                        trigger: "#scrolly-wrapper",
                        start: "top top",
                        end: () => "+=" + (window.innerHeight * 5),
                        scrub: 1,
                        pin: ".scrolly-stage",
                        invalidateOnRefresh: true,
                        snap: desktop
                            ? { snapTo: "labels", duration: { min: 0.2, max: 0.8 }, delay: 0.1, ease: "power1.inOut" }
                            : false
                    });

                    // Ajuda a acertar medidas após assets (fonts/images) carregarem
                    const onLoadRefresh = () => ScrollTrigger.refresh();
                    window.addEventListener("load", onLoadRefresh, { once: true });

                    return () => {
                        window.removeEventListener("load", onLoadRefresh);
                        st.kill();
                        tl.kill();
                    };
                }
            );

            // --- PWA Logic ---
            let deferredPrompt;
            const installBtn = document.getElementById('pwaInstallBtn');
            const iosHint = document.getElementById('iosHint');
            const isIos = /iPhone|iPad|iPod/i.test(navigator.userAgent);
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches;

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                installBtn.classList.remove('hidden');
            });

            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') installBtn.classList.add('hidden');
                    deferredPrompt = null;
                }
            });

            if (isIos && !isStandalone) {
                iosHint.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
