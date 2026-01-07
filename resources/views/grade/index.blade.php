<x-app-layout>
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob dark:bg-blue-900/20"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000 dark:bg-purple-900/20"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-emerald-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000 dark:bg-emerald-900/20"></div>
    </div>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="group p-2 -ml-2 rounded-full hover:bg-white/50 dark:hover:bg-gray-800/50 transition">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            
            <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full shadow-lg ring-2 ring-white/50 dark:ring-white/10" style="background-color: {{ $disciplina->cor }}"></div>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight truncate max-w-[200px]">
                        {{ $disciplina->nome }}
                    </h2>
                    <p class="text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-400 font-bold">Configurar Horários</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 pb-24">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2.5rem] shadow-sm border border-white/20 dark:border-gray-800 p-6 sm:p-8 relative overflow-hidden">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Novo Horário
                </h3>
                
                <form action="{{ route('grade.store', $disciplina->id) }}" method="POST" x-data="{ dia: '1' }">
                    @csrf
                    
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-300 mb-3 ml-1">Dia da Semana</label>
                        
                        {{-- Container Flex com Scroll Horizontal --}}
                        <div class="flex items-center gap-4 overflow-x-auto p-3 pb-4 -mx-2 snap-x scrollbar-hide">
                            
                            @php $dias = [1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sab']; @endphp
                            
                            @foreach($dias as $k => $d)
                                <label class="cursor-pointer snap-center shrink-0 w-16 text-center relative group">
                                    
                                    <input type="radio" name="dia_semana" value="{{ $k }}" class="sr-only" x-model="dia">
                                    
                                    <div class="h-12 w-full rounded-2xl flex items-center justify-center text-sm transition-all duration-300 border"
                                        :class="dia == '{{ $k }}' 
                                            ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-500/40 border-blue-500 scale-110 -translate-y-1' 
                                            : 'bg-white/40 dark:bg-black/20 text-gray-500 dark:text-gray-400 border-white/20 dark:border-gray-700 group-hover:bg-white/60 dark:group-hover:bg-gray-800/60'">
                                        {{ $d }}
                                    </div>
                                </label>
                            @endforeach
                            
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mb-8">
                        <div class="flex-1 relative group">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Início</label>
                            <input type="time" name="horario_inicio" required class="w-full text-center rounded-2xl bg-white/50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:text-white py-4 font-mono text-xl font-bold shadow-sm transition-all group-hover:bg-white/80 dark:group-hover:bg-black/30">
                        </div>
                        <div class="pt-6 text-gray-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg></div>
                        <div class="flex-1 relative group">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Fim</label>
                            <input type="time" name="horario_fim" required class="w-full text-center rounded-2xl bg-white/50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:text-white py-4 font-mono text-xl font-bold shadow-sm transition-all group-hover:bg-white/80 dark:group-hover:bg-black/30">
                        </div>
                    </div>

                    {{-- BOTÃO PRINCIPAL --}}
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-blue-600/20 active:scale-[0.98] transition-all flex justify-center items-center gap-2 group mb-4">
                        <span>Adicionar à Grade</span>
                        <div class="bg-white/20 p-1 rounded-full group-hover:rotate-90 transition-transform"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg></div>
                    </button>

                    {{-- ✨ BOTÃO DE IMPORTAR COM IA (EMBAIXO DO PRINCIPAL) --}}
                    <div class="relative py-2">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="px-2 bg-white/60 dark:bg-gray-900 text-xs text-gray-400 font-bold uppercase tracking-wider backdrop-blur-xl">Ou se preferir</span>
                        </div>
                    </div>

                    <a href="{{ route('grade.importar.view') }}" class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/10 dark:to-amber-900/10 hover:from-yellow-100 hover:to-amber-100 dark:hover:from-yellow-900/20 dark:hover:to-amber-900/20 text-yellow-700 dark:text-yellow-500 font-bold py-3.5 rounded-2xl border border-yellow-200 dark:border-yellow-800 transition-all active:scale-[0.98] group mt-2">
                        <svg class="w-5 h-5 animate-pulse group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                        </svg>
                        <span>Usar IA para montar grade</span>
                    </a>

                </form>
            </div>

            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-5 px-2 flex items-center justify-between">
                    <span>Horários Definidos</span>
                    <span class="text-xs bg-white/50 dark:bg-gray-800/50 px-3 py-1 rounded-full text-gray-500 border border-white/20">{{ $horarios->count() }} aulas</span>
                </h3>
                
                @if($horarios->count() > 0)
                    <div class="space-y-4">
                        @foreach($horarios as $horario)
                            <div class="group bg-white/70 dark:bg-gray-900/70 backdrop-blur-md rounded-3xl p-5 shadow-sm border border-white/40 dark:border-gray-800 flex justify-between items-center transition-all hover:scale-[1.01] hover:shadow-md relative overflow-hidden">
                                
                                <div class="flex items-center gap-5">
                                    <div class="flex flex-col items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl text-blue-600 dark:text-blue-400 border border-blue-200/50 dark:border-gray-700 shadow-inner">
                                        <span class="text-[10px] font-bold uppercase opacity-60">Dia</span>
                                        <span class="text-xl font-extrabold leading-none tracking-tight">
                                            {{ substr($horario->dia_semana_texto, 0, 3) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-0.5">Horário</p>
                                        <p class="font-mono font-bold text-gray-800 dark:text-white text-xl tracking-tight">
                                            {{ \Carbon\Carbon::parse($horario->horario_inicio)->format('H:i') }} 
                                            <span class="text-gray-300 dark:text-gray-600 mx-1">-</span> 
                                            {{ \Carbon\Carbon::parse($horario->horario_fim)->format('H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 z-10">
                                    
                                    <a href="{{ route('grade.edit', $horario->id) }}" class="p-3 text-blue-400 hover:text-blue-600 bg-blue-50/50 dark:bg-blue-900/10 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-2xl transition-colors border border-transparent hover:border-blue-200 dark:hover:border-blue-800" title="Editar Horário">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>

                                    <form action="{{ route('grade.destroy', $horario->id) }}" method="POST" data-confirm="Tem certeza que deseja apagar este horário?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-3 text-red-400 hover:text-red-600 bg-red-50/50 dark:bg-red-900/10 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-2xl transition-colors border border-transparent hover:border-red-200 dark:hover:border-red-800" title="Excluir Horário">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-gray-300/50 dark:border-gray-700/50 rounded-[2.5rem] bg-white/20 dark:bg-black/10 backdrop-blur-sm">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-gray-900 dark:text-white font-bold text-lg">Sem horários</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-[200px]">Adicione os horários das aulas acima.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>