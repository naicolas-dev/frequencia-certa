<x-app-layout>
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob dark:bg-blue-900/20"></div>
        <div class="absolute -bottom-32 right-1/3 w-96 h-96 bg-purple-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000 dark:bg-purple-900/20"></div>
    </div>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('grade.index', $horario->disciplina_id) }}" class="group p-2 -ml-2 rounded-full hover:bg-white/50 dark:hover:bg-gray-800/50 transition">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                    Editar Horário
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $horario->disciplina->nome }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 pb-24">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2.5rem] shadow-sm border border-white/20 dark:border-gray-800 p-6 sm:p-8 relative overflow-hidden">
                
                <form action="{{ route('grade.update', $horario->id) }}" method="POST" x-data="{ dia: '{{ $horario->dia_semana }}' }">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-300 mb-3 ml-1">Dia da Semana</label>
                        <div class="flex gap-3 overflow-x-auto pb-4 no-scrollbar -mx-2 px-2 snap-x">
                            @php
                                $dias = [1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sab'];
                            @endphp
                            
                            @foreach($dias as $k => $d)
                                <label class="cursor-pointer snap-center shrink-0">
                                    <input type="radio" name="dia_semana" value="{{ $k }}" class="sr-only" x-model="dia">
                                    <div class="px-5 py-3 rounded-2xl text-sm font-bold transition-all duration-300 transform active:scale-95 border"
                                          :class="dia == '{{ $k }}' 
                                            ? 'bg-gradient-to-tr from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/30 border-transparent scale-105' 
                                            : 'bg-white/40 dark:bg-black/20 text-gray-600 dark:text-gray-400 border-white/20 dark:border-gray-700 hover:bg-white/60 dark:hover:bg-gray-800/60'">
                                        {{ $d }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mb-8">
                        <div class="flex-1 relative group">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Início</label>
                            <input type="time" name="horario_inicio" value="{{ $horario->horario_inicio }}" required 
                                class="w-full text-center rounded-2xl bg-white/50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:text-white py-4 font-mono text-xl font-bold shadow-sm transition-all group-hover:bg-white/80 dark:group-hover:bg-black/30">
                        </div>
                        
                        <div class="pt-6 text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                        </div>

                        <div class="flex-1 relative group">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Fim</label>
                            <input type="time" name="horario_fim" value="{{ $horario->horario_fim }}" required 
                                class="w-full text-center rounded-2xl bg-white/50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:text-white py-4 font-mono text-xl font-bold shadow-sm transition-all group-hover:bg-white/80 dark:group-hover:bg-black/30">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-blue-600/20 active:scale-[0.98] transition-all flex justify-center items-center gap-2 group">
                        <span>Salvar Alterações</span>
                        <div class="bg-white/20 p-1 rounded-full group-hover:translate-x-1 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>