<x-app-layout>
    {{-- BACKGROUND DECORATIVO --}}
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob dark:bg-blue-900/20"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000 dark:bg-purple-900/20"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-emerald-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000 dark:bg-emerald-900/20"></div>
    </div>

    @php
        $totalAulasSemana = collect($gradePorDia)->flatten()->count();
        $hoje = date('N'); 
        $hoje = $hoje > 6 ? 1 : $hoje; 
    @endphp

    <x-slot name="header">
        <div class="flex items-center gap-4 justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="group p-2 -ml-2 rounded-full hover:bg-white/50 dark:hover:bg-gray-800/50 transition lg:hidden">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                        Grade Horária
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Visão completa da sua semana
                    </p>
                </div>
            </div>
            
            {{-- BOTÃO IA NO HEADER --}}
            @if($totalAulasSemana > 0)
                <a href="{{ route('grade.importar.view') }}" class="flex items-center gap-2 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-500 px-3 py-2 rounded-xl font-bold text-xs hover:bg-yellow-100 dark:hover:bg-yellow-900/40 transition border border-yellow-200 dark:border-yellow-800 shadow-sm">
                    <svg class="w-5 h-5 animate-pulse group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                    </svg>
                    <span class="hidden sm:inline">Importar com IA</span>
                    <span class="sm:hidden">IA</span>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6 pb-24" x-data="{ diaAtivo: {{ $hoje }} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 
               ESTADO VAZIO GLOBAL 
               (Aparece para Mobile e Desktop se não houver aulas)
            --}}
            @if($totalAulasSemana === 0)
                <div class="flex flex-col items-center justify-center py-12 sm:py-20 animate-fade-in-up">
                    
                    {{-- Ícone Animado --}}
                    <div class="relative mb-8 group cursor-pointer">
                        <div class="absolute inset-0 bg-yellow-400/30 blur-3xl rounded-full opacity-50 group-hover:opacity-80 transition duration-700"></div>
                        <div class="relative w-28 h-28 bg-gradient-to-br from-yellow-100 to-amber-50 dark:from-yellow-900/40 dark:to-amber-900/20 rounded-[2.5rem] flex items-center justify-center border border-yellow-200/50 dark:border-yellow-700/30 shadow-2xl shadow-yellow-500/10 transform transition group-hover:scale-105 group-hover:rotate-3">
                            <span class="text-6xl filter drop-shadow-sm group-hover:animate-pulse">✨</span>
                        </div>
                    </div>

                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-3 text-center">
                        Sua grade está vazia
                    </h3>
                    
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-10 text-center leading-relaxed">
                        Para começar, precisamos saber seus horários. Você pode cadastrar manualmente ou usar nossa IA para ler sua grade automaticamente.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 w-full max-w-md px-4">
                        {{-- BOTÃO IA PRINCIPAL --}}
                        <a href="{{ route('grade.importar.view') }}" 
                           class="flex-1 flex items-center justify-center gap-3 bg-gradient-to-r from-yellow-500 to-amber-500 hover:from-yellow-600 hover:to-amber-600 text-white font-bold py-4 px-6 rounded-2xl shadow-xl shadow-yellow-500/20 transform transition active:scale-95 group relative overflow-hidden">
                            <div class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                            <svg class="w-6 h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Importar com IA</span>
                        </a>

                        {{-- BOTÃO MANUAL --}}
                        <a href="{{ route('disciplinas.criar') }}" 
                           class="flex-1 flex items-center justify-center gap-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold py-4 px-6 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition active:scale-95 shadow-sm">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Manual</span>
                        </a>
                    </div>
                </div>

            @else
                {{-- VISUALIZAÇÃO DA GRADE (Só aparece se tiver aulas) --}}

                {{-- MENU MOBILE (SNAP SCROLL) --}}
                <div class="md:hidden mb-8">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 ml-1">
                        Dia da Semana
                    </label>

                    <div class="flex gap-3 overflow-x-auto pb-4 no-scrollbar -mx-4 px-4 snap-x">
                        @foreach([1=>'Seg', 2=>'Ter', 3=>'Qua', 4=>'Qui', 5=>'Sex', 6=>'Sáb'] as $num => $nome)
                            <button 
                                @click="diaAtivo = {{ $num }}"
                                class="snap-center px-6 py-3 rounded-2xl text-sm font-bold transition-all duration-300 transform active:scale-95 whitespace-nowrap shadow-sm border"
                                :class="diaAtivo === {{ $num }} 
                                    ? 'bg-gradient-to-tr from-blue-600 to-blue-500 text-white shadow-blue-500/30 border-transparent scale-105' 
                                    : 'bg-white/40 dark:bg-black/20 text-gray-600 dark:text-gray-400 border-white/20 dark:border-gray-700 hover:bg-white/60 dark:hover:bg-gray-800/60'"
                            >
                                {{ $nome }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- LISTA DE AULAS (MOBILE) --}}
                <div class="md:hidden space-y-4 min-h-[300px]">
                    @for($i = 1; $i <= 6; $i++)
                        <div x-show="diaAtivo === {{ $i }}" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             style="display: none;">
                            
                            @if(isset($gradePorDia[$i]) && count($gradePorDia[$i]) > 0)
                                <div class="space-y-4">
                                    @foreach($gradePorDia[$i] as $aula)
                                        <div class="bg-white/70 dark:bg-gray-900/70 backdrop-blur-xl rounded-[2rem] p-5 shadow-sm border border-white/20 dark:border-gray-800 relative overflow-hidden flex items-center justify-between group">
                                            <div class="absolute left-0 top-0 bottom-0 w-2" style="background-color: {{ $aula->disciplina->cor }}"></div>
                                            
                                            <div class="pl-4">
                                                <h3 class="font-bold text-gray-900 dark:text-white text-lg leading-tight">
                                                    {{ $aula->disciplina->nome }}
                                                </h3>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                                        Aula Presencial
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="text-right bg-white/50 dark:bg-black/20 px-4 py-2 rounded-xl border border-gray-100 dark:border-gray-700">
                                                <p class="text-xl font-mono font-bold text-gray-800 dark:text-gray-200 tracking-tight">
                                                    {{ date('H:i', strtotime($aula->horario_inicio)) }}
                                                </p>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">
                                                    Até {{ date('H:i', strtotime($aula->horario_fim)) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-20 text-center bg-white/30 dark:bg-black/10 rounded-[3rem] backdrop-blur-sm border border-dashed border-gray-300/50 dark:border-gray-700/50">
                                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-900 dark:text-white font-bold text-lg">Dia Livre!</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-[200px]">
                                        Aproveite para descansar ou estudar.
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>

                {{-- DESKTOP (GRID) --}}
                <div class="hidden md:grid grid-cols-6 gap-6 items-start">
                    @foreach([1=>'Segunda', 2=>'Terça', 3=>'Quarta', 4=>'Quinta', 5=>'Sexta', 6=>'Sábado'] as $num => $nome)
                        <div class="flex flex-col h-full transition duration-300 {{ $num == $hoje ? 'scale-[1.03] z-10' : 'opacity-60 hover:opacity-100 hover:scale-[1.01]' }}">
                            <div class="flex items-center justify-center mb-6">
                                <span class="px-4 py-1.5 bg-white/80 dark:bg-gray-800/80 backdrop-blur border border-white/40 dark:border-gray-700 rounded-full text-xs font-extrabold text-gray-600 dark:text-gray-300 uppercase tracking-widest shadow-sm">
                                    {{ $nome }}
                                </span>
                            </div>

                            <div class="bg-white/40 dark:bg-gray-900/40 backdrop-blur-md rounded-[2rem] p-4
                                min-h-[240px] border border-white/20 dark:border-gray-800 space-y-4
                                {{ $num == $hoje ? 'ring-2 ring-blue-500/40 bg-white/60 dark:bg-gray-900/60' : '' }}">
                                @if(isset($gradePorDia[$num]))
                                    @foreach($gradePorDia[$num] as $aula)
                                        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm hover:shadow-lg border border-transparent hover:border-blue-200 dark:hover:border-blue-900 transition-all duration-300 group relative overflow-hidden transform hover:-translate-y-1 cursor-default">
                                            <div class="absolute top-4 right-4 w-3 h-3 rounded-full shadow-sm" style="background-color: {{ $aula->disciplina->cor }}"></div>
                                            <p class="font-bold text-gray-900 dark:text-white text-sm pr-6 leading-snug">
                                                {{ $aula->disciplina->nome }}
                                            </p>
                                            <div class="mt-3 flex items-center text-xs font-mono font-bold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-black/30 rounded-lg px-2 py-1.5 w-fit">
                                                <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ date('H:i', strtotime($aula->horario_inicio)) }}
                                                <span class="mx-1 text-gray-300">-</span>
                                                {{ date('H:i', strtotime($aula->horario_fim)) }}
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="h-full flex flex-col items-center justify-center text-center px-4 opacity-60">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Dia Livre</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

    <style>
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>
</x-app-layout>