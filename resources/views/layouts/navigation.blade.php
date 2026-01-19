<nav x-data="{ 
    show: true,
    scrolled: false,
    init() {
        // Monitora o scroll para o comportamento 'auto-hide' do Desktop
        window.addEventListener('scroll', () => {
            this.scrolled = window.scrollY > 20;
            if (!this.scrolled) {
                this.show = true;
            } else {
                this.show = false;
            }
        }, { passive: true });
    },
    // AI Credits Logic
    aiCredits: {
        current: {{ Auth::user()->ai_credits }},
        max: {{ Auth::user()->getMonthlyMaxCredits() }},
        delta: 0,
        showDelta: false,
        deltaColor: 'text-red-500',
        
        init() {
            window.addEventListener('ai-credits:update', (e) => {
                this.update(e.detail.credits, e.detail.max);
            });
        },

        update(newCredits, newMax) {
            const diff = newCredits - this.current;
            if (diff === 0) return;

            this.delta = diff > 0 ? '+' + diff : diff;
            this.deltaColor = diff > 0 ? 'text-green-500' : 'text-red-500';
            this.showDelta = true;
            this.max = newMax;

            // Animate counting
            const start = this.current;
            const end = newCredits;
            const duration = 1000;
            const startTime = performance.now();

            const animate = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Ease out quart
                const ease = 1 - Math.pow(1 - progress, 4);
                
                this.current = Math.round(start + (diff * ease));

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    this.current = end;
                    setTimeout(() => { this.showDelta = false; }, 1000);
                }
            };

            requestAnimationFrame(animate);
        }
    }
}" x-init="aiCredits.init()">

    {{-- 1. ÁREA DE GATILHO + DICA VISUAL (DESKTOP) --}}
    <div 
        class="fixed top-0 left-0 w-full h-8 z-50 flex justify-center items-start group hidden sm:flex cursor-pointer"
        @mouseenter="if(scrolled) show = true"
    >
        <div 
            x-show="!show && scrolled"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-full"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-full"
            class="mt-1"
        >
            <div class="h-1.5 w-24 bg-gray-300/50 dark:bg-gray-600/50 backdrop-blur-md rounded-full shadow-sm ring-1 ring-black/5 transition-all duration-300 group-hover:bg-blue-500/80 group-hover:w-32 group-hover:h-2 group-hover:shadow-[0_0_15px_rgba(59,130,246,0.5)]"></div>
        </div>
    </div>

    {{-- 2. NAVBAR FLUTUANTE (DESKTOP) --}}
    <div 
        x-show="show"
        @mouseleave="if(scrolled) show = false"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="-translate-y-[150%] opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-[150%] opacity-0"
        class="fixed top-6 left-1/2 -translate-x-1/2 z-40 hidden sm:flex"
    >
        <div class="flex items-center gap-6 px-6 py-3 rounded-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-2xl border border-white/20 dark:border-gray-700/50 shadow-2xl shadow-black/5 dark:shadow-black/20 ring-1 ring-black/5">
            
            {{-- Logo --}}
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="hover:scale-105 transition-transform">
                    <x-application-logo class="block h-8 w-auto fill-current text-blue-600 dark:text-blue-500" />
                </a>
            </div>

            {{-- Links --}}
            <div class="flex space-x-1">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="px-4 py-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 border-0 text-sm font-medium transition-colors">
                    {{ __('Painel') }}
                </x-nav-link>

                <x-nav-link id="tour-grade-desktop" :href="route('grade.geral')" :active="request()->routeIs('grade.geral')" class="px-4 py-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 border-0 text-sm font-medium transition-colors">
                    {{ __('Grade') }}
                </x-nav-link>

                <x-nav-link :href="route('frequencia.historico')" :active="request()->routeIs('frequencia.historico')" class="px-4 py-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 border-0 text-sm font-medium transition-colors">
                    {{ __('Histórico') }}
                </x-nav-link>
            </div>

            {{-- Separador --}}
            <div class="h-5 w-px bg-gray-200 dark:bg-gray-700"></div>

            {{-- Ações Direita --}}
            <div class="flex items-center gap-3">
                
                {{-- AI CREDITS PILL (DESKTOP) --}}
                <div class="relative group flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-full border border-purple-200 dark:border-purple-800/50 shadow-sm hover:shadow-md transition-all cursor-help"
                     title="Créditos de Inteligência Artificial">
                    
                    {{-- Floating Delta --}}
                    <div x-show="aiCredits.showDelta" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 -translate-y-6"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 -translate-y-6"
                         x-transition:leave-end="opacity-0 -translate-y-10"
                         class="absolute -top-4 right-0 font-bold text-lg pointer-events-none z-50"
                         :class="aiCredits.deltaColor"
                         x-text="aiCredits.delta">
                    </div>

                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    
                    <div class="flex flex-col leading-none">
                        <span class="text-[8px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider hidden lg:block">Créditos</span>
                        <div class="flex items-baseline gap-0.5">
                            <span class="text-sm font-black text-purple-700 dark:text-purple-300 tabular-nums" x-text="aiCredits.current"></span>
                            <span class="text-[10px] font-medium text-purple-500 dark:text-purple-400">/</span>
                            <span class="text-[10px] font-medium text-purple-500 dark:text-purple-400 tabular-nums" x-text="aiCredits.max"></span>
                        </div>
                    </div>
                </div>

                {{-- Theme Toggle (Desktop) --}}
                <button 
                    id="tour-theme-toggle"
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
                    class="p-2 text-gray-500 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                >
                    <svg class="w-5 h-5 hidden dark:block text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <svg class="w-5 h-5 block dark:hidden text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z"></path></svg>
                </button>

                {{-- User Dropdown --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:bg-white dark:hover:bg-gray-700 transition-colors">
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-200 px-2 max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                        <div class="h-6 w-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                            <svg class="w-3 h-3 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </div>
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg ring-1 ring-black/5 py-1 z-50 origin-top-right" style="display: none;">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Perfil') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" 
                                class="group text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                
                                <div class="flex items-center gap-2">
                                    {{-- Ícone de Logout --}}
                                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    
                                    <span>{{ __('Sair') }}</span>
                                </div>

                            </x-dropdown-link>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. SPACER INVISÍVEL --}}
    <div class="hidden sm:block h-24 w-full" aria-hidden="true"></div>

    {{-- 4. NAVBAR MOBILE (Com Toggle de Tema) --}}
    {{-- 'relative' permite posicionar o botão absolute na direita --}}
    <div class="sm:hidden flex items-center justify-center h-16 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md sticky top-0 z-30 border-b border-gray-100 dark:border-gray-800 relative">
         
         {{-- Logo Centralizado --}}
         <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-8 w-auto fill-current text-blue-600 dark:text-blue-500" />
         </a>

         {{-- AI CREDITS (MOBILE) --}}
         <div class="absolute left-4 top-1/2 -translate-y-1/2">
             <div class="relative flex items-center gap-1.5 px-2 py-1 bg-purple-50/80 dark:bg-purple-900/10 rounded-lg border border-purple-100 dark:border-purple-800/30">
                 
                 {{-- Floating Delta Mobile --}}
                 <div x-show="aiCredits.showDelta" 
                      x-transition:enter="transition ease-out duration-300"
                      x-transition:enter-start="opacity-0 translate-y-2"
                      x-transition:enter-end="opacity-100 -translate-y-4"
                      x-transition:leave="transition ease-in duration-300"
                      x-transition:leave-start="opacity-100 -translate-y-4"
                      x-transition:leave-end="opacity-0 -translate-y-8"
                      class="absolute -top-2 left-0 font-bold text-xs pointer-events-none z-50 whitespace-nowrap"
                      :class="aiCredits.deltaColor"
                      x-text="aiCredits.delta">
                 </div>

                 <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                 </svg>
                 <span class="text-xs font-black text-purple-700 dark:text-purple-300 tabular-nums" x-text="aiCredits.current"></span>
             </div>
         </div>

         {{-- Toggle de Tema (Posicionado na direita) --}}
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
            class="absolute right-4 p-2 text-gray-500 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
            {{-- Ícone Lua (mostra no dark) --}}
            <svg class="w-5 h-5 hidden dark:block text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            {{-- Ícone Sol (mostra no light) --}}
            <svg class="w-5 h-5 block dark:hidden text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z"></path>
            </svg>
        </button>
    </div>
</nav>

{{-- MENU INFERIOR MOBILE --}}
<div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 shadow-lg sm:hidden pb-safe">
    <div class="grid h-full max-w-lg grid-cols-5 mx-auto font-medium">
        <a href="{{ route('dashboard') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group transition-colors">
            <svg class="w-6 h-6 mb-1 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400' }}" fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            <span class="text-[9px] {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Início</span>
        </a>
        <a id="tour-grade-mobile" href="{{ route('grade.geral') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group transition-colors">
            <svg class="w-6 h-6 mb-1 {{ request()->routeIs('grade.*') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0" /></svg>
            <span class="text-[9px] {{ request()->routeIs('grade.*') ? 'text-blue-600 dark:text-blue-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Grade</span>
        </a>
        <div class="flex items-center justify-center relative">
            <a id="tour-add-mobile" href="{{ route('disciplinas.criar') }}" class="absolute -top-5 inline-flex items-center justify-center w-12 h-12 font-medium bg-blue-600 rounded-full hover:bg-blue-700 shadow-lg shadow-blue-500/40 transform active:scale-95 transition-all border-4 border-white dark:border-gray-900">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            </a>
        </div>
        <a href="{{ route('frequencia.historico') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group transition-colors">
            <svg class="w-6 h-6 mb-1 {{ request()->routeIs('frequencia.historico') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
            <span class="text-[9px] {{ request()->routeIs('frequencia.historico') ? 'text-blue-600 dark:text-blue-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Histórico</span>
        </a>
        <a id="tour-profile-mobile" href="{{ route('profile.edit') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group transition-colors">
            <svg class="w-6 h-6 mb-1 {{ request()->routeIs('profile.*') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400' }}" fill="{{ request()->routeIs('profile.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            <span class="text-[9px] {{ request()->routeIs('profile.*') ? 'text-blue-600 dark:text-blue-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Perfil</span>
        </a>
    </div>
</div>