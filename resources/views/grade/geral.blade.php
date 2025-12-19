<x-app-layout>
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob dark:bg-blue-900/20"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000 dark:bg-purple-900/20"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-emerald-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000 dark:bg-emerald-900/20"></div>
    </div>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="group p-2 -ml-2 rounded-full hover:bg-white/50 dark:hover:bg-gray-800/50 transition lg:hidden">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    Grade Horária
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Visão completa da sua semana</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 pb-24" x-data="{ diaAtivo: {{ date('N') > 6 ? 1 : date('N') }} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="md:hidden mb-8">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 ml-1">Dia da Semana</label>
                <div class="flex gap-3 overflow-x-auto pb-4 no-scrollbar -mx-4 px-4 snap-x">
                    @foreach([1=>'Seg', 2=>'Ter', 3=>'Qua', 4=>'Qui', 5=>'Sex', 6=>'Sáb'] as $num => $nome)
                        <button @click="diaAtivo = {{ $num }}" 
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

            <div class="md:hidden space-y-4 min-h-[300px]">
                @for($i = 1; $i <= 6; $i++)
                    <div x-show="diaAtivo === {{ $i }}" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         style="display: none;"
                    >
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
                                                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Aula Presencial</p>
                                            </div>
                                        </div>

                                        <div class="text-right bg-white/50 dark:bg-black/20 px-4 py-2 rounded-xl border border-gray-100 dark:border-gray-700">
                                            <p class="text-xl font-mono font-bold text-gray-800 dark:text-gray-200 tracking-tight">
                                                {{ date('H:i', strtotime($aula->horario_inicio)) }}
                                            </p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Até {{ date('H:i', strtotime($aula->horario_fim)) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-20 text-center bg-white/30 dark:bg-black/10 rounded-[3rem] backdrop-blur-sm border border-dashed border-gray-300/50 dark:border-gray-700/50">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-gray-900 dark:text-white font-bold text-lg">Dia Livre!</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-[200px]">Aproveite para descansar ou estudar.</p>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>

            <div class="hidden md:grid grid-cols-5 gap-6 h-full">
                @foreach([1=>'Segunda', 2=>'Terça', 3=>'Quarta', 4=>'Quinta', 5=>'Sexta'] as $num => $nome)
                    <div class="flex flex-col h-full">
                        <div class="flex items-center justify-center mb-6">
                            <span class="px-4 py-1.5 bg-white/80 dark:bg-gray-800/80 backdrop-blur border border-white/40 dark:border-gray-700 rounded-full text-xs font-extrabold text-gray-600 dark:text-gray-300 uppercase tracking-widest shadow-sm">
                                {{ $nome }}
                            </span>
                        </div>

                        <div class="bg-white/40 dark:bg-gray-900/40 backdrop-blur-md rounded-[2rem] p-4 h-full min-h-[500px] border border-white/20 dark:border-gray-800 space-y-4">
                            @if(isset($gradePorDia[$num]))
                                @foreach($gradePorDia[$num] as $aula)
                                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm hover:shadow-lg border border-transparent hover:border-blue-200 dark:hover:border-blue-900 transition-all duration-300 group relative overflow-hidden transform hover:-translate-y-1 cursor-default">
                                        
                                        <div class="absolute top-4 right-4 w-3 h-3 rounded-full shadow-sm" style="background-color: {{ $aula->disciplina->cor }}"></div>
                                        
                                        <p class="font-bold text-gray-900 dark:text-white text-sm pr-6 leading-snug">
                                            {{ $aula->disciplina->nome }}
                                        </p>
                                        
                                        <div class="mt-3 flex items-center text-xs font-mono font-bold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-black/30 rounded-lg px-2 py-1.5 w-fit">
                                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ date('H:i', strtotime($aula->horario_inicio)) }}
                                            <span class="mx-1 text-gray-300">-</span>
                                            {{ date('H:i', strtotime($aula->horario_fim)) }}
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="h-full flex flex-col items-center justify-center opacity-40">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full mb-2"></div>
                                    <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Livre</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>