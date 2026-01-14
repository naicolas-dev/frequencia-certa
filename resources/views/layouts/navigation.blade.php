<nav x-data="{ open: false }" class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 sticky top-0 z-40 transition-colors duration-300">
    
    {{-- MENU DESKTOP --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-8 w-auto fill-current text-blue-600 dark:text-blue-500" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-base font-medium">
                        {{ __('Painel') }}
                    </x-nav-link>

                    <x-nav-link id="tour-grade-desktop" :href="route('grade.geral')" :active="request()->routeIs('grade.geral')" class="text-base font-medium">
                        {{ __('Grade Horária') }}
                    </x-nav-link>

                    {{-- NOVO: Link Histórico --}}
                    <x-nav-link :href="route('frequencia.historico')" :active="request()->routeIs('frequencia.historico')" class="text-base font-medium">
                        {{ __('Histórico') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="flex items-center sm:ms-6">
                
                {{-- Toggle Tema --}}
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
                    class="p-2 mr-2 text-gray-500 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition-colors"
                >
                    <svg class="w-5 h-5 hidden dark:block text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg class="w-5 h-5 block dark:hidden text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z"></path>
                    </svg>
                </button>

                <div class="hidden sm:flex sm:items-center sm:ms-2">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-500 dark:text-gray-300 bg-transparent hover:text-gray-700 dark:hover:text-white">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Perfil') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" data-no-spa onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Sair') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- MENU MOBILE INFERIOR --}}
<div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] sm:hidden pb-safe">
    {{-- Mantido grid-cols-5: Início, Grade, Add, Histórico, Perfil --}}
    <div class="grid h-full max-w-lg grid-cols-5 mx-auto font-medium">
        
        {{-- 1. INÍCIO --}}
        <a href="{{ route('dashboard') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group transition-colors">
            <svg class="w-6 h-6 mb-1 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500' }}"
                 fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[9px] {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-500 font-bold' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500' }}">Início</span>
        </a>

        {{-- 2. GRADE --}}
        <a id="tour-grade-mobile" href="{{ route('grade.geral') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group transition-colors">
            <svg class="w-6 h-6 mb-1 {{ request()->routeIs('grade.*') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500' }}"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0" />
            </svg>
            <span class="text-[9px] {{ request()->routeIs('grade.*') ? 'text-blue-600 dark:text-blue-500 font-bold' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500' }}">Grade</span>
        </a>

        {{-- 3. ADICIONAR (BOTÃO FLUTUANTE CENTRAL) --}}
        <div class="flex items-center justify-center relative">
            <a id="tour-add-mobile" href="{{ route('disciplinas.criar') }}" class="absolute -top-5 inline-flex items-center justify-center w-12 h-12 font-medium bg-blue-600 rounded-full hover:bg-blue-700 shadow-lg shadow-blue-500/40 transform active:scale-95 transition-all border-4 border-white dark:border-gray-900">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4" />
                </svg>
            </a>
        </div>

        {{-- 4. HISTÓRICO (NOVO) --}}
        <a href="{{ route('frequencia.historico') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group transition-colors">
            <svg class="w-6 h-6 mb-1 {{ request()->routeIs('frequencia.historico') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500' }}" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span class="text-[9px] {{ request()->routeIs('frequencia.historico') ? 'text-blue-600 dark:text-blue-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Histórico</span>
        </a>

        {{-- 5. PERFIL --}}
        <a id="tour-profile-mobile" href="{{ route('profile.edit') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group transition-colors">
            <svg class="w-6 h-6 mb-1 {{ request()->routeIs('profile.*') ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500' }}" fill="{{ request()->routeIs('profile.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="text-[9px] {{ request()->routeIs('profile.*') ? 'text-blue-600 dark:text-blue-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Perfil</span>
        </a>

    </div>
</div>