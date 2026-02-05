<nav x-data="navbar()" x-init="init()" class="fixed top-0 inset-x-0 z-50 pointer-events-none font-['Inter']">

    {{-- TRIGGER HANDLE (Visible cue when navbar is hidden) --}}
    <div class="hidden sm:block fixed top-0 left-1/2 -translate-x-1/2 z-50 pointer-events-auto transition-all duration-500 ease-out"
        :class="(scrolled && !forceShow) ? 'translate-y-0 opacity-100 delay-200' : '-translate-y-full opacity-0'"
        @mouseenter="showNavbar()">
        <div
            class="w-32 h-1.5 mx-auto bg-gray-300/50 dark:bg-gray-600/50 backdrop-blur-md rounded-b-xl shadow-sm hover:bg-blue-500 dark:hover:bg-blue-400 transition-colors cursor-pointer group">
            <div class="w-8 h-0.5 bg-white/50 rounded-full mx-auto mt-0.5 group-hover:bg-white/80 transition-colors">
            </div>
        </div>
    </div>

    {{-- EDGE TRIGGER (Thin hidden strip for easy access) --}}
    <div class="hidden sm:block fixed top-0 inset-x-0 h-1.5 z-50 pointer-events-auto" x-show="scrolled && !forceShow"
        @mouseenter="showNavbar()"></div>

    {{-- DESKTOP / TABLET FLOATING HUD --}}
    <div class="pointer-events-auto w-full flex justify-center pt-6 px-4 transition-transform duration-500 hidden sm:flex"
        :class="(scrolled && !forceShow) ? '-translate-y-[150%]' : 'translate-y-0'" @mouseenter="showNavbar()"
        @mouseleave="hideNavbar()">

        <div class="relative group">

            {{-- PILL CONTAINER --}}
            <div class="relative flex items-center gap-1 p-2 bg-white/90 dark:bg-[#0F172A]/90 backdrop-blur-2xl border border-white/20 dark:border-white/10 rounded-full shadow-2xl shadow-blue-900/10 transition-all duration-300"
                :class="isCompact ? 'px-3' : 'px-4 pr-2'">

                {{-- LOGO --}}
                <a href="{{ route('dashboard') }}"
                    class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:scale-110 hover:rotate-3 transition-transform duration-300">
                    <x-application-logo class="w-6 h-6 fill-current" />
                </a>

                {{-- NAV LINKS (DESKTOP) --}}
                <div
                    class="hidden md:flex items-center bg-gray-100/50 dark:bg-gray-800/50 rounded-full px-1 py-1 mx-2 gap-1 border border-gray-200/50 dark:border-white/5">
                    <x-nav-link-modern href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
                        icon="home">
                        Painel
                    </x-nav-link-modern>
                    <x-nav-link-modern id="tour-grade-desktop" href="{{ route('grade.geral') }}"
                        :active="request()->routeIs('grade.geral')" icon="calendar">
                        Grade
                    </x-nav-link-modern>
                    <x-nav-link-modern href="{{ route('frequencia.historico') }}"
                        :active="request()->routeIs('frequencia.historico')" icon="clock">
                        Histórico
                    </x-nav-link-modern>
                </div>

                {{-- DIVIDER --}}
                <div class="hidden md:block w-px h-6 bg-gray-200 dark:bg-gray-700 mx-1"></div>

                {{-- ACTIONS --}}
                <div class="flex items-center gap-2">

                    {{-- CREDITS --}}
                    <button id="tour-credits" @click="$dispatch('open-modal', 'ai-credits-info')"
                        class="group/credits relative flex items-center gap-2 px-3 py-1.5 rounded-full bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/30 dark:to-indigo-900/30 border border-purple-100 dark:border-purple-500/20 hover:border-purple-300 dark:hover:border-purple-500/50 transition-all overflow-hidden"
                        title="Créditos IA">
                        <div
                            class="absolute inset-0 bg-purple-500/10 translate-y-full group-hover/credits:translate-y-0 transition-transform duration-300">
                        </div>

                        {{-- Icon --}}
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>

                        {{-- Counter --}}
                        <div class="flex flex-col items-start leading-none relative z-10">
                            <span
                                class="text-[9px] font-bold uppercase text-purple-400 dark:text-purple-500 tracking-widest"
                                :class="isCompact ? 'hidden' : 'block'">Créditos</span>
                            <div
                                class="flex items-baseline gap-0.5 text-purple-700 dark:text-purple-200 font-mono font-bold text-xs">
                                <span x-text="aiCredits.current">0</span>
                                <span class="opacity-50 text-[10px]">/</span>
                                <span class="opacity-50 text-[10px]" x-text="aiCredits.max">100</span>
                            </div>
                        </div>

                        {{-- Delta Animation --}}
                        <div x-show="aiCredits.showDelta" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="translate-y-full opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="-translate-y-full opacity-0"
                            class="absolute inset-0 flex items-center justify-center font-bold font-mono bg-purple-100 dark:bg-purple-900 z-20"
                            :class="aiCredits.delta > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                            <span x-text="aiCredits.delta > 0 ? '+' + aiCredits.delta : aiCredits.delta"></span>
                        </div>
                    </button>

                    {{-- THEME TOGGLE --}}
                    <button id="tour-theme-toggle" @click="toggleTheme()"
                        class="w-9 h-9 flex items-center justify-center rounded-full text-gray-500 hover:text-yellow-500 hover:bg-yellow-50 dark:hover:bg-gray-800 dark:text-gray-400 dark:hover:text-yellow-400 transition-colors">
                        {{-- Sun --}}
                        <svg class="w-5 h-5 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        {{-- Moon --}}
                        <svg class="w-5 h-5 block dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z">
                            </path>
                        </svg>
                    </button>

                    {{-- USER MENU --}}
                    <div class="relative" x-data="{ 
                        open: false, 
                        timer: null,
                        closeWithDelay() {
                            this.timer = setTimeout(() => { this.open = false }, 400);
                        },
                        cancelClose() {
                            clearTimeout(this.timer);
                        }
                    }" @click.outside="open = false" @mouseleave="closeWithDelay()" @mouseenter="cancelClose()">
                        <button @click="open = !open"
                            class="flex items-center gap-1 pl-1 pr-1 lg:gap-2 lg:pl-2 py-1 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:bg-white dark:hover:bg-gray-700 transition-colors">
                            <div
                                class="w-7 h-7 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                {{ substr(Auth::user()->name, 0, 2) }}
                            </div>
                            <span
                                class="text-xs font-medium text-gray-700 dark:text-gray-200 lg:block truncate max-w-[60px] lg:max-w-[100px]"
                                :class="isCompact ? 'hidden' : 'block'">
                                {{ Auth::user()->name }}
                            </span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- DROPDOWN --}}
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                            class="absolute right-0 mt-3 w-56 transform origin-top-right bg-white dark:bg-[#1E293B] rounded-2xl shadow-2xl ring-1 ring-black/5 dark:ring-white/10 p-2 overflow-hidden z-50">

                            <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700 mb-1">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Logado como</span>
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                    {{ Auth::user()->name }}
                                </p>
                            </div>

                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Editar Perfil
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Encerrar Sessão
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- 4. NAVBAR MOBILE (LEGACY - RESTORED) --}}
    <div
        class="sm:hidden flex items-center justify-center h-16 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md sticky top-0 z-30 border-b border-gray-100 dark:border-gray-800 relative">

        {{-- Logo Centralizado --}}
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-8 w-auto fill-current text-blue-600 dark:text-blue-500" />
        </a>

        {{-- AI CREDITS (MOBILE) --}}
        <div class="absolute left-4 top-1/2 -translate-y-1/2">
            <button id="tour-credits-mobile" type="button" @click="$dispatch('open-modal', 'ai-credits-info')"
                class="relative flex items-center gap-1.5 px-2 py-1 bg-purple-50/80 dark:bg-purple-900/10 rounded-lg border border-purple-100 dark:border-purple-800/30 active:scale-95 transition-transform">

                {{-- Floating Delta Mobile --}}
                <div x-show="aiCredits.showDelta" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 -translate-y-4"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 -translate-y-4"
                    x-transition:leave-end="opacity-0 -translate-y-8"
                    class="absolute -top-2 left-0 font-bold text-xs pointer-events-none z-50 whitespace-nowrap"
                    :class="aiCredits.delta > 0 ? 'text-green-500' : 'text-red-500'"
                    x-text="aiCredits.delta > 0 ? '+' + aiCredits.delta : aiCredits.delta">
                </div>

                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                </svg>
                <span class="text-xs font-black text-purple-700 dark:text-purple-300 tabular-nums"
                    x-text="aiCredits.current"></span>
            </button>
        </div>

        {{-- Toggle de Tema (Posicionado na direita) --}}
        <button type="button" @click="toggleTheme()"
            class="absolute right-4 p-2 text-gray-500 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
            {{-- Ícone Lua (mostra no dark) --}}
            <svg class="w-5 h-5 hidden dark:block text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            {{-- Ícone Sol (mostra no light) --}}
            <svg class="w-5 h-5 block dark:hidden text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z">
                </path>
            </svg>
        </button>
    </div>

    {{-- MENU INFERIOR MOBILE (LEGACY - RESTORED) --}}
    <div
        class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 shadow-lg sm:hidden pb-safe">
        <div class="grid h-full max-w-lg grid-cols-5 mx-auto font-medium">
            <a href="{{ route('dashboard') }}"
                class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group transition-colors">
                <svg class="w-6 h-6 mb-1 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400' }}"
                    fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span
                    class="text-[9px] {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Início</span>
            </a>
            <a id="tour-grade-mobile" href="{{ route('grade.geral') }}"
                class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group transition-colors">
                <svg class="w-6 h-6 mb-1 {{ request()->routeIs('grade.*') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0" />
                </svg>
                <span
                    class="text-[9px] {{ request()->routeIs('grade.*') ? 'text-blue-600 dark:text-blue-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Grade</span>
            </a>
            <div class="flex items-center justify-center relative">
                <a id="tour-add-mobile" href="{{ route('disciplinas.criar') }}"
                    class="absolute -top-5 inline-flex items-center justify-center w-12 h-12 font-medium bg-blue-600 rounded-full hover:bg-blue-700 shadow-lg shadow-blue-500/40 transform active:scale-95 transition-all border-4 border-white dark:border-gray-900">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </a>
            </div>
            <a href="{{ route('frequencia.historico') }}"
                class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group transition-colors">
                <svg class="w-6 h-6 mb-1 {{ request()->routeIs('frequencia.historico') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span
                    class="text-[9px] {{ request()->routeIs('frequencia.historico') ? 'text-blue-600 dark:text-blue-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Histórico</span>
            </a>
            <a id="tour-profile-mobile" href="{{ route('profile.edit') }}"
                class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group transition-colors">
                <svg class="w-6 h-6 mb-1 {{ request()->routeIs('profile.*') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400' }}"
                    fill="{{ request()->routeIs('profile.*') ? 'currentColor' : 'none' }}" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span
                    class="text-[9px] {{ request()->routeIs('profile.*') ? 'text-blue-600 dark:text-blue-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Perfil</span>
            </a>
        </div>
    </div>

</nav>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('navbar', () => ({
            scrolled: false,
            isDark: localStorage.theme === 'dark',
            isCompact: false,
            forceShow: false,
            hoverTimer: null,

            showNavbar() {
                clearTimeout(this.hoverTimer);
                this.forceShow = true;
            },

            hideNavbar() {
                clearTimeout(this.hoverTimer);
                this.hoverTimer = setTimeout(() => {
                    this.forceShow = false;
                }, 800);
            },

            aiCredits: {
                current: {{ Auth::user()->ai_credits }},
                max: {{ Auth::user()->getMonthlyMaxCredits() }},
                delta: 0,
                showDelta: false,
                init() {
                    window.addEventListener('ai-credits:update', (e) => this.handleUpdate(e.detail));
                    // Check responsive state
                    const resizeObserver = new ResizeObserver(entries => {
                        this.isCompact = window.innerWidth < 1100; // Trigger compact mode earlier for zoom safety
                    });
                    resizeObserver.observe(document.body);
                },
                handleUpdate(detail) {
                    const diff = detail.credits - this.current;
                    if (diff === 0) return;
                    this.delta = diff;
                    this.current = detail.credits;
                    this.max = detail.max || this.max;
                    this.showDelta = true;
                    setTimeout(() => this.showDelta = false, 2000);
                }
            },

            init() {
                this.aiCredits.init();
                window.addEventListener('scroll', () => {
                    this.scrolled = window.scrollY > 20;
                }, { passive: true });

                // Força a navbar visível durante o tour
                window.addEventListener('tour-starting', () => {
                    this.forceShow = true;
                    this.scrolled = false; // Reset scroll state
                });
                window.addEventListener('tour-finished', () => {
                    this.forceShow = false;
                });

                // Set initial dark state properly
                if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                    this.isDark = true;
                } else {
                    document.documentElement.classList.remove('dark');
                    this.isDark = false;
                }
            },

            toggleTheme() {
                this.isDark = !this.isDark;
                if (this.isDark) {
                    localStorage.theme = 'dark';
                    document.documentElement.classList.add('dark');
                } else {
                    localStorage.theme = 'light';
                    document.documentElement.classList.remove('dark');
                }
            }
        }));
    });
</script>
{{-- MODAL DE INFORMAÇÕES DOS CRÉDITOS (GLOBAL) --}}
<x-modal name="ai-credits-info" focusable>
    <div class="p-6 bg-white dark:bg-gray-800">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                ✨
            </div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                {{ __('Sobre os Créditos IA') }}
            </h2>
        </div>

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            {{ __('Os créditos são utilizados para realizar ações inteligentes no sistema. Eles são renovados mensalmente. Veja abaixo quanto custa cada ação:') }}
        </p>

        <div class="relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 font-medium">{{ __('Ação') }}</th>
                        <th class="px-4 py-3 font-medium text-right">{{ __('Custo') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr class="bg-white dark:bg-gray-800">
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">Posso faltar hoje?</td>
                        <td class="px-4 py-3 text-right font-bold text-purple-600 dark:text-purple-400">
                            {{ \App\Helpers\AiCredits::COST_DAY_CHECK }} créditos
                        </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-800">
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">Análise de Disciplina</td>
                        <td class="px-4 py-3 text-right font-bold text-purple-600 dark:text-purple-400">
                            {{ \App\Helpers\AiCredits::COST_SUBJECT_ANALYSIS }} créditos
                        </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-800">
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">Importação de Grade</td>
                        <td class="px-4 py-3 text-right font-bold text-purple-600 dark:text-purple-400">
                            {{ \App\Helpers\AiCredits::COST_IMPORT_SCHEDULE }} créditos
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            class="flex items-center justify-between p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-100 dark:border-purple-800/30">
            <span class="text-sm font-medium text-purple-800 dark:text-purple-200">
                {{ __('Limite Mensal') }}
            </span>
            <span class="text-lg font-black text-purple-700 dark:text-purple-300">
                {{ \App\Helpers\AiCredits::MONTHLY_MAX }}
            </span>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Entendi') }}
            </x-secondary-button>
        </div>
    </div>
</x-modal>