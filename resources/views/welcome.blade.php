<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full motion-safe:scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title>Frequência Certa - Falte Com Segurança</title>
    <meta name="description" content="Posso faltar hoje? Descubra suas vidas por matéria. Cadastre sua grade, registre presença e deixe o app te avisar antes da reprovação.">

    <meta name="theme-color" content="#2563eb">
    <meta name="color-scheme" content="light dark">

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/icons/icon-192x192.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Tema antes do paint (evita flash)
        (function () {
            try {
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = localStorage.theme || (prefersDark ? 'dark' : 'light');
                document.documentElement.classList.toggle('dark', theme === 'dark');
            } catch (_) {}
        })();
    </script>

    <style>
        :root{
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-right: env(safe-area-inset-right, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --safe-left: env(safe-area-inset-left, 0px);
        }

        [data-cloak]{ display:none!important; }
        body{ -webkit-tap-highlight-color: transparent; }

        /* Reveal (leve) */
        .reveal{
            opacity:0;
            transform: translateY(14px);
            transition: opacity 550ms cubic-bezier(.5,0,0,1), transform 550ms cubic-bezier(.5,0,0,1);
            will-change: opacity, transform;
        }
        .reveal.active{ opacity:1; transform: translateY(0); }

        /* Slider (touch-friendly + Firefox) */
        input[type=range]{ -webkit-appearance:none; appearance:none; background:transparent; touch-action: pan-y; }
        input[type=range]::-webkit-slider-thumb{
            -webkit-appearance:none;
            height: 26px; width: 26px;
            border-radius: 999px;
            background: currentColor;
            margin-top: -10px;
            border: 2px solid rgba(255,255,255,0.9);
            box-shadow: 0 6px 14px rgba(0,0,0,0.18);
        }
        input[type=range]::-webkit-slider-runnable-track{
            height: 7px;
            background: rgba(156,163,175,0.32);
            border-radius: 999px;
        }
        input[type=range]::-moz-range-thumb{
            height: 26px; width: 26px;
            border-radius: 999px;
            background: currentColor;
            border: 2px solid rgba(255,255,255,0.9);
            box-shadow: 0 6px 14px rgba(0,0,0,0.18);
        }
        input[type=range]::-moz-range-track{
            height: 7px;
            background: rgba(156,163,175,0.32);
            border-radius: 999px;
        }

        /* Chips horizontal */
        .no-scrollbar::-webkit-scrollbar{ display:none; }
        .no-scrollbar{ -ms-overflow-style:none; scrollbar-width:none; }

        /* iOS toast acima da barra */
        .toast-bottom{
            bottom: calc(16px + var(--safe-bottom) + 70px);
        }

        /* Espaço pra barra fixa mobile */
        @media (max-width: 639px){
            body{ padding-bottom: calc(86px + var(--safe-bottom)); }
        }

        @media (prefers-reduced-motion: reduce){
            .reveal, .animate-blob, .animate-float{ animation:none!important; transition:none!important; transform:none!important; opacity:1!important; }
        }
    </style>
</head>

<body class="antialiased bg-gray-50 dark:bg-black text-gray-900 dark:text-white overflow-x-hidden font-sans selection:bg-blue-500 selection:text-white">

    <!-- Fundo sutil, no mesmo espírito do dashboard -->
    <div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden">
        <div class="absolute -top-24 -right-24 w-[70vw] h-[70vw] bg-blue-500/10 dark:bg-blue-500/15 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-[70vw] h-[70vw] bg-purple-500/10 dark:bg-purple-500/15 rounded-full blur-3xl"></div>
    </div>

    <!-- NAV (minimal, consistente) -->
    <nav
        class="fixed top-0 w-full z-50 border-b border-gray-200/60 dark:border-gray-800/60 backdrop-blur-md bg-white/70 dark:bg-black/70"
        style="padding-top: calc(10px + var(--safe-top)); padding-left: calc(16px + var(--safe-left)); padding-right: calc(16px + var(--safe-right)); padding-bottom: 10px;"
    >
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <x-application-logo class="block h-8 w-auto fill-current text-blue-600 dark:text-blue-500" />
                <div class="leading-tight">
                    <div class="font-extrabold tracking-tight">Frequência Certa</div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 -mt-0.5">sem planilha • sem ansiedade</div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button id="themeToggleBtn" type="button"
                    class="p-2 rounded-full hover:bg-black/5 dark:hover:bg-white/10 transition text-gray-600 dark:text-gray-300"
                    aria-label="Alternar tema">
                    <span class="dark:hidden" aria-hidden="true">🌙</span>
                    <span class="hidden dark:inline" aria-hidden="true">☀️</span>
                </button>

                <!-- instalar discreto (não vira “feature premium”) -->
                <button id="installBtnTop" type="button"
                    class="hidden sm:inline-flex px-4 py-2 rounded-full bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl border border-white/20 dark:border-gray-800 text-sm font-bold text-gray-700 dark:text-gray-200 hover:bg-white/80 dark:hover:bg-gray-800/80 transition"
                    aria-haspopup="dialog" aria-controls="iosInstallToast">
                    Instalar
                </button>

                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-sm font-bold transition shadow-lg shadow-blue-600/20">
                        Ir pro Painel
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">
                        Entrar
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="pt-28 sm:pt-32">

        <!-- HERO (muito consistente com o card “Diário de Classe”) -->
        <section class="px-4" style="padding-left: calc(16px + var(--safe-left)); padding-right: calc(16px + var(--safe-right));">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 items-stretch">
                <div class="reveal active relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-blue-600 to-indigo-700 shadow-2xl shadow-blue-900/20 text-white p-6 sm:p-8">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-purple-500/20 rounded-full blur-2xl"></div>

                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-white/20 backdrop-blur-md text-xs font-semibold mb-4 border border-white/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Você decide com segurança
                            </div>

                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                                “Posso faltar hoje?”
                                <span class="block text-white/90 font-bold mt-2">
                                    O app responde em 2 segundos.
                                </span>
                            </h1>

                            <p class="mt-4 text-white/85 text-base sm:text-lg leading-relaxed max-w-xl">
                                Em vez de porcentagem, você vê quantas <strong>vidas</strong> ainda tem em cada matéria — e o app muda de cor quando fica perigoso.
                            </p>

                            <div class="mt-5 flex flex-wrap gap-2">
                                <span class="px-3 py-1 rounded-full bg-white/15 border border-white/10 text-xs font-bold">Vidas por matéria</span>
                                <span class="px-3 py-1 rounded-full bg-white/15 border border-white/10 text-xs font-bold">Diário de classe</span>
                                <span class="px-3 py-1 rounded-full bg-white/15 border border-white/10 text-xs font-bold">Tudo no mobile</span>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col sm:flex-row gap-3">
                            <a href="#risk"
                               class="w-full sm:w-auto bg-white text-blue-600 hover:bg-blue-50 font-extrabold py-4 px-8 rounded-xl shadow-xl transition active:scale-95 text-center">
                                Testar agora
                            </a>

                            @guest
                                <a href="{{ route('register') }}"
                                   class="w-full sm:w-auto bg-white/20 hover:bg-white/30 text-white font-extrabold py-4 px-8 rounded-xl transition active:scale-95 text-center border border-white/10">
                                    Criar conta grátis
                                </a>
                            @endguest
                        </div>

                        <div class="mt-4 text-xs text-white/70">
                            Sem cartão • instalável • funciona mesmo com internet ruim (quando instalado)
                        </div>
                    </div>
                </div>

                <!-- Prévia “consistente” (sem revelar premium) -->
                <div class="reveal space-y-4">
                    <!-- Cards iguais aos stats do dashboard -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2rem] border border-white/20 dark:border-gray-800 p-6 shadow-sm">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Presença acumulada</p>
                            <div class="flex items-baseline gap-1">
                                <h3 class="text-4xl font-extrabold text-emerald-600 dark:text-emerald-400">--</h3>
                                <span class="text-lg font-medium text-gray-400">%</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Aparece depois do 1º registro</p>
                        </div>

                        <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2rem] border border-white/20 dark:border-gray-800 p-6 shadow-sm">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Matérias em risco</p>
                            <div class="flex items-center gap-3">
                                <h3 class="text-4xl font-extrabold text-gray-400 dark:text-gray-500">0</h3>
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-emerald-500 mt-2 font-medium">Tudo sob controle.</p>
                        </div>
                    </div>

                    <!-- Teaser “premium” estilo lock do seu dashboard (sem poluir) -->
                    <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gradient-to-r dark:from-gray-900 dark:to-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm dark:shadow-lg group transition-colors duration-300">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-24 h-24 bg-blue-500/10 dark:bg-blue-500/20 rounded-full blur-2xl group-hover:bg-blue-500/20 dark:group-hover:bg-blue-500/30 transition-colors"></div>

                        <div class="relative z-10 flex items-center justify-between p-4 sm:p-5">
                            <div class="flex items-center gap-4">
                                <div class="shrink-0 w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 flex items-center justify-center text-2xl shadow-sm text-gray-400 dark:text-gray-500">
                                    🔒
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-800 dark:text-white leading-tight">
                                        IA + Conquistas + Alertas
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-relaxed">
                                        Desbloqueie no painel: Oráculo (IA), Sala de Troféus e alertas anti-reprovação.
                                    </p>
                                </div>
                            </div>
                            @guest
                                <a href="{{ route('register') }}"
                                   class="hidden sm:inline-flex px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-sm shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition active:scale-95">
                                    Desbloquear
                                </a>
                            @endguest
                        </div>

                        <div class="h-1 w-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-full bg-blue-500 w-[8%] shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SEÇÃO PRINCIPAL: POSSO FALTAR HOJE? (única interação antes do login) -->
        <section id="risk" class="px-4 mt-10 sm:mt-12" style="padding-left: calc(16px + var(--safe-left)); padding-right: calc(16px + var(--safe-right));">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 items-start">
                <div class="reveal">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        Teste rápido: quantas vidas você tem?
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm sm:text-base max-w-xl">
                        Ajuste a matéria e veja o veredito (verde/amarelo/vermelho). É exatamente assim que o painel funciona — só que com seus dados reais.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl border border-white/20 dark:border-gray-800 px-3 py-1.5 rounded-full font-bold">Decisão em 2s</span>
                        <span class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl border border-white/20 dark:border-gray-800 px-3 py-1.5 rounded-full font-bold">Sem matemática mental</span>
                        <span class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl border border-white/20 dark:border-gray-800 px-3 py-1.5 rounded-full font-bold">Focado no que importa</span>
                    </div>
                </div>

                <div class="reveal">
                    <div id="riskCard" class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2rem] border border-white/20 dark:border-gray-800 p-6 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-blue-500/10 dark:bg-blue-500/10 rounded-full blur-2xl"></div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between gap-2">
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Posso faltar hoje?</div>
                                <div id="riskPill" class="text-[10px] font-bold px-2 py-1 rounded-md bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">
                                    Seguro
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Matéria</label>
                                    <input id="subjectName" type="text" value="Cálculo II"
                                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0F172A] text-gray-900 dark:text-white py-3 px-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm font-bold">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Faltas usadas</label>
                                    <input id="usedAbsences" type="number" min="0" step="1" value="2"
                                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0F172A] text-gray-900 dark:text-white py-3 px-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm font-extrabold text-right">
                                </div>
                            </div>

                            <div class="mt-5 space-y-4">
                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <label class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total de aulas</label>
                                        <span class="text-sm font-mono font-bold bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded" id="totalOut">80</span>
                                    </div>
                                    <input id="totalClasses" type="range" min="30" max="160" step="2" value="80"
                                        class="w-full text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full">
                                </div>

                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <label class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Limite (%)</label>
                                        <span class="text-sm font-mono font-bold bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded" id="pctOut">25%</span>
                                    </div>
                                    <input id="limitPct" type="range" min="15" max="30" step="5" value="25"
                                        class="w-full text-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500 rounded-full">
                                </div>
                            </div>

                            <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
                                <div class="flex items-end justify-between">
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Vidas restantes</p>
                                        <div class="flex items-baseline gap-2">
                                            <span id="livesRemaining" class="text-5xl font-extrabold tracking-tighter text-emerald-600 dark:text-emerald-400">3</span>
                                            <span class="text-sm font-bold text-gray-400">vidas</span>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Veredito</p>
                                        <div id="canSkip" class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400">SIM</div>
                                    </div>
                                </div>

                                <p id="riskMsg" class="mt-3 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                    Você pode faltar hoje e ainda ficar tranquilo.
                                </p>

                                @guest
                                    <a href="{{ route('register') }}"
                                       class="mt-5 block w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-4 rounded-xl shadow-lg shadow-blue-600/20 active:scale-[0.98] transition text-center">
                                        Salvar isso no meu painel &rarr;
                                    </a>
                                    <p class="text-[10px] text-gray-400 text-center mt-3">Leva menos de 30 segundos.</p>
                                @endguest

                                @auth
                                    <a href="{{ url('/dashboard') }}"
                                       class="mt-5 block w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-4 rounded-xl shadow-lg shadow-blue-600/20 active:scale-[0.98] transition text-center">
                                        Abrir painel &rarr;
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONSISTÊNCIA + “NÃO POLUI”: Features como “desbloqueia depois” -->
        <section class="px-4 mt-10 sm:mt-12 pb-24" style="padding-left: calc(16px + var(--safe-left)); padding-right: calc(16px + var(--safe-right));">
            <div class="max-w-7xl mx-auto">
                <div class="reveal flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-gray-900 dark:text-white">Depois do cadastro, você desbloqueia</h3>
                    </div>
                    @guest
                        <a href="{{ route('register') }}" class="hidden sm:inline-flex px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-600/20 transition active:scale-95">
                            Criar conta
                        </a>
                    @endguest
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- IA (teaser) -->
                    <a href="{{ route('register') }}"
                       class="reveal group bg-white/70 dark:bg-gray-900/70 backdrop-blur-md rounded-3xl border border-white/20 dark:border-gray-800 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden p-6">
                        <div class="absolute top-0 right-0 w-28 h-28 bg-yellow-500/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Oráculo</div>
                                <h4 class="text-lg font-extrabold text-gray-900 dark:text-white mt-1">Conselheiro com IA</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                                    “Vai na aula de hoje.” / “Essa dá pra faltar.” Decisão pronta, sem estresse.
                                </p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 flex items-center justify-center text-2xl text-gray-400 dark:text-gray-500">
                                🔒
                            </div>
                        </div>
                        <div class="mt-5 text-xs font-bold text-blue-600 dark:text-blue-400">Desbloquear no painel →</div>
                    </a>

                    <!-- Gamificação (teaser) -->
                    <a href="{{ route('register') }}"
                       class="reveal group bg-white/70 dark:bg-gray-900/70 backdrop-blur-md rounded-3xl border border-white/20 dark:border-gray-800 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden p-6">
                        <div class="absolute top-0 right-0 w-28 h-28 bg-orange-500/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Conquistas</div>
                                <h4 class="text-lg font-extrabold text-gray-900 dark:text-white mt-1">Ofensiva & Medalhas</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                                    Badges por consistência, presença perfeita e “salvar o semestre”.
                                </p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 flex items-center justify-center text-2xl text-gray-400 dark:text-gray-500">
                                🔒
                            </div>
                        </div>
                        <div class="mt-5 text-xs font-bold text-blue-600 dark:text-blue-400">Desbloquear no painel →</div>
                    </a>

                    <!-- Notificações (teaser) -->
                    <a href="{{ route('register') }}"
                       class="reveal group bg-white/70 dark:bg-gray-900/70 backdrop-blur-md rounded-3xl border border-white/20 dark:border-gray-800 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden p-6">
                        <div class="absolute top-0 right-0 w-28 h-28 bg-blue-500/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Alertas</div>
                                <h4 class="text-lg font-extrabold text-gray-900 dark:text-white mt-1">Anti-reprovação</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                                    O app avisa antes de você faltar quando não pode.
                                </p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 flex items-center justify-center text-2xl text-gray-400 dark:text-gray-500">
                                🔒
                            </div>
                        </div>
                        <div class="mt-5 text-xs font-bold text-blue-600 dark:text-blue-400">Desbloquear no painel →</div>
                    </a>
                </div>

                <!-- CTA final consistente -->
                <div class="reveal mt-8 bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2rem] border border-white/20 dark:border-gray-800 p-6 shadow-sm text-center">
                    <h4 class="text-xl font-extrabold text-gray-900 dark:text-white">Pronto pra parar de contar nos dedos?</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        Cadastre suas matérias e o app cuida do resto.
                    </p>

                    @guest
                        <a href="{{ route('register') }}"
                           class="mt-5 inline-flex w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-4 px-10 rounded-xl shadow-lg shadow-blue-600/20 active:scale-[0.98] transition">
                            Criar conta grátis
                        </a>
                        <div class="mt-3 text-[11px] text-gray-400">Sem cartão • 30s</div>
                    @endguest

                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="mt-5 inline-flex w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-4 px-10 rounded-xl shadow-lg shadow-blue-600/20 active:scale-[0.98] transition">
                            Ir para meu painel
                        </a>
                    @endauth
                </div>

                <footer class="mt-10 opacity-50 text-xs text-center">
                    <p class="mb-2">
                        © {{ date('Y') }} Frequência Certa - Todos os direitos reservados
                    </p>

                    <div class="flex justify-center items-center gap-2 text-current">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" role="img" aria-label="GitHub">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.758-1.333-1.758-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>

                        <span>
                            Feito por 
                            <a href="https://github.com/naicolas-dev" target="_blank" class="underline hover:text-gray-900 dark:hover:text-white transition-colors">
                                Nicolas Alves
                            </a>
                        </span>
                    </div>
                </footer>
            </div>
        </section>
    </main>

    <!-- Bottom bar mobile (aparece só depois do usuário ver o valor) -->
    @guest
    <div id="mobileBar"
         class="sm:hidden fixed inset-x-0 bottom-0 z-50 translate-y-[140%] transition-transform duration-300"
         style="padding-left: calc(12px + var(--safe-left)); padding-right: calc(12px + var(--safe-right)); padding-bottom: calc(10px + var(--safe-bottom));"
         aria-hidden="true">
        <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-3xl border border-white/20 dark:border-gray-800 p-3 shadow-2xl">
            <div class="flex items-center gap-2">
                <a href="{{ route('register') }}"
                   class="flex-1 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-center active:scale-[0.99] transition">
                    Criar conta grátis
                </a>
                <button id="installBtnMobile" type="button"
                        class="hidden w-12 h-12 rounded-2xl bg-white/70 dark:bg-white/5 border border-white/60 dark:border-white/10 flex items-center justify-center active:scale-[0.98] transition"
                        aria-label="Instalar app" aria-haspopup="dialog" aria-controls="iosInstallToast">
                    <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </button>
            </div>
            <div class="mt-2 text-[11px] text-gray-500 dark:text-gray-400 text-center">
                Desbloqueie IA, conquistas e alertas no painel.
            </div>
        </div>
    </div>
    @endguest

    <!-- iOS Install Toast -->
    <div id="iosInstallToast"
         class="hidden fixed left-4 right-4 md:left-auto md:right-6 md:w-80 bg-white/85 dark:bg-gray-900/85 backdrop-blur-xl p-5 rounded-2xl shadow-2xl z-50 border-l-4 border-blue-500 toast-bottom"
         role="dialog" aria-label="Instruções para instalar o app" aria-live="polite">
        <div class="flex flex-col gap-3">
            <div class="flex items-start justify-between">
                <h3 class="font-extrabold text-gray-900 dark:text-white">Instalar app</h3>
                <button id="iosToastCloseBtn" type="button" class="text-gray-400 hover:text-gray-600" aria-label="Fechar">✕</button>
            </div>
            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                No iPhone, a instalação é manual:<br>
                1) Toque em <strong class="text-blue-500">Compartilhar</strong><br>
                2) Escolha <strong class="text-gray-900 dark:text-white">Adicionar à Tela de Início</strong>
            </p>
        </div>
    </div>

    <x-cookie-banner />

    <script>
        (function(){
            const $ = (s) => document.querySelector(s);
            const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            const isIos = () => /iPhone|iPad|iPod/i.test(navigator.userAgent) && !window.MSStream;
            const isStandalone = () =>
                (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) ||
                (typeof navigator.standalone !== 'undefined' && navigator.standalone === true);

            // Theme toggle
            $('#themeToggleBtn')?.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                try { localStorage.theme = isDark ? 'dark' : 'light'; } catch (_) {}
            });

            // Reveal
            const revealEls = Array.from(document.querySelectorAll('.reveal'));
            if (revealEls.length) {
                if (prefersReducedMotion || !('IntersectionObserver' in window)) {
                    revealEls.forEach(el => el.classList.add('active'));
                } else {
                    const io = new IntersectionObserver((entries) => {
                        entries.forEach((e) => {
                            if (e.isIntersecting) {
                                e.target.classList.add('active');
                                io.unobserve(e.target);
                            }
                        });
                    }, { threshold: 0.12, rootMargin: '0px 0px -12% 0px' });
                    revealEls.forEach(el => io.observe(el));
                }
            }

            // Risk calculator ("vidas")
            const totalClasses = $('#totalClasses');
            const limitPct = $('#limitPct');
            const usedAbsences = $('#usedAbsences');

            const totalOut = $('#totalOut');
            const pctOut = $('#pctOut');

            const livesRemaining = $('#livesRemaining');
            const canSkip = $('#canSkip');

            const riskPill = $('#riskPill');
            const riskMsg = $('#riskMsg');

            function setRiskUI(key){
                // reset
                livesRemaining?.classList.remove('text-emerald-600','dark:text-emerald-400','text-yellow-600','dark:text-yellow-400','text-red-600','dark:text-red-400');
                canSkip?.classList.remove('text-emerald-600','dark:text-emerald-400','text-yellow-600','dark:text-yellow-400','text-red-600','dark:text-red-400');
                riskMsg?.classList.remove('text-emerald-600','dark:text-emerald-400','text-yellow-600','dark:text-yellow-400','text-red-600','dark:text-red-400');

                riskPill?.classList.remove('bg-emerald-100','dark:bg-emerald-900/20','text-emerald-700','dark:text-emerald-300');
                riskPill?.classList.remove('bg-yellow-100','dark:bg-yellow-900/20','text-yellow-800','dark:text-yellow-200');
                riskPill?.classList.remove('bg-red-100','dark:bg-red-900/20','text-red-700','dark:text-red-300');

                if (key === 'danger'){
                    livesRemaining?.classList.add('text-red-600','dark:text-red-400');
                    canSkip?.classList.add('text-red-600','dark:text-red-400');
                    riskMsg?.classList.add('text-red-600','dark:text-red-400');
                    riskPill?.classList.add('bg-red-100','dark:bg-red-900/20','text-red-700','dark:text-red-300');
                    riskPill && (riskPill.textContent = 'Perigo');
                    riskMsg && (riskMsg.textContent = 'Perigoso: melhor ir hoje pra não entrar no limite.');
                    return;
                }
                if (key === 'warn'){
                    livesRemaining?.classList.add('text-yellow-600','dark:text-yellow-400');
                    canSkip?.classList.add('text-yellow-600','dark:text-yellow-400');
                    riskMsg?.classList.add('text-yellow-600','dark:text-yellow-400');
                    riskPill?.classList.add('bg-yellow-100','dark:bg-yellow-900/20','text-yellow-800','dark:text-yellow-200');
                    riskPill && (riskPill.textContent = 'Atenção');
                    riskMsg && (riskMsg.textContent = 'Atenção: dá pra faltar, mas você já está gastando suas vidas.');
                    return;
                }
                livesRemaining?.classList.add('text-emerald-600','dark:text-emerald-400');
                canSkip?.classList.add('text-emerald-600','dark:text-emerald-400');
                riskMsg?.classList.add('text-emerald-600','dark:text-emerald-400');
                riskPill?.classList.add('bg-emerald-100','dark:bg-emerald-900/20','text-emerald-700','dark:text-emerald-300');
                riskPill && (riskPill.textContent = 'Seguro');
                riskMsg && (riskMsg.textContent = 'Você pode faltar hoje e ainda ficar tranquilo.');
            }

            function clamp(n, a, b){ return Math.max(a, Math.min(b, n)); }

            function computeRisk(){
                const total = Number(totalClasses?.value || 80);
                const pct = Number(limitPct?.value || 25);
                const used = clamp(Number(usedAbsences?.value || 0), 0, 999);

                const allowed = Math.max(0, Math.floor(total * (pct/100)));
                const remaining = clamp(allowed - used, 0, allowed);

                totalOut && (totalOut.textContent = String(total));
                pctOut && (pctOut.textContent = pct + '%');

                livesRemaining && (livesRemaining.textContent = String(remaining));
                const can = remaining > 0 ? 'SIM' : 'NÃO';
                canSkip && (canSkip.textContent = can);

                const ratio = allowed === 0 ? 0 : (remaining / allowed);
                let key = 'safe';
                if (remaining === 0 || ratio <= 0.15) key = 'danger';
                else if (ratio <= 0.40) key = 'warn';

                setRiskUI(key);
            }

            [totalClasses, limitPct, usedAbsences].forEach(el => {
                el?.addEventListener('input', computeRisk, { passive:true });
                el?.addEventListener('change', computeRisk, { passive:true });
            });
            computeRisk();

            // PWA install (discreto)
            let deferredPrompt = null;
            const installBtnTop = $('#installBtnTop');
            const installBtnMobile = $('#installBtnMobile');
            const iosToast = $('#iosInstallToast');
            const iosToastCloseBtn = $('#iosToastCloseBtn');

            function openIosToast(){
                if (!iosToast) return;
                iosToast.classList.remove('hidden');
                iosToastCloseBtn?.focus();
            }
            function closeIosToast(){ iosToast?.classList.add('hidden'); }
            iosToastCloseBtn?.addEventListener('click', closeIosToast);
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeIosToast(); });

            function hideInstallUI(){
                installBtnTop?.classList.add('hidden');
                installBtnMobile?.classList.add('hidden');
                closeIosToast();
            }
            function updateInstallUI(){
                if (isStandalone()) return hideInstallUI();
                const canShow = !!deferredPrompt || isIos();
                if (installBtnTop) installBtnTop.classList.toggle('hidden', !canShow);
                if (installBtnMobile) installBtnMobile.classList.toggle('hidden', !canShow);
            }

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                updateInstallUI();
            });
            window.addEventListener('appinstalled', () => {
                deferredPrompt = null;
                hideInstallUI();
            });

            async function handleInstallClick(){
                if (isStandalone()) return hideInstallUI();

                if (deferredPrompt){
                    deferredPrompt.prompt();
                    const choice = await deferredPrompt.userChoice;
                    if (choice && choice.outcome === 'accepted') hideInstallUI();
                    deferredPrompt = null;
                    updateInstallUI();
                    return;
                }
                if (isIos()) openIosToast();
            }

            installBtnTop?.addEventListener('click', handleInstallClick);
            installBtnMobile?.addEventListener('click', handleInstallClick);
            updateInstallUI();

            // Mobile bottom bar: só aparece depois de ver o valor (seção #risk)
            const mobileBar = $('#mobileBar');
            const riskSection = $('#risk');

            function showBar(){
                if (!mobileBar) return;
                mobileBar.classList.remove('translate-y-[140%]');
                mobileBar.setAttribute('aria-hidden','false');
            }
            if (mobileBar && riskSection && ('IntersectionObserver' in window)) {
                const ioBar = new IntersectionObserver((entries) => {
                    entries.forEach(e => {
                        if (e.isIntersecting) {
                            showBar();
                            ioBar.disconnect();
                        }
                    });
                }, { threshold: 0.25 });
                ioBar.observe(riskSection);
            }
        })();
    </script>
</body>
</html>
