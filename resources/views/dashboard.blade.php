<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                Painel do Aluno
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">Ano Letivo 2025</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 px-4 sm:px-0">
                
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border-l-4 border-emerald-500 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Frequência Global</p>
                            <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">--%</h3>
                            <p class="text-xs text-gray-500 mt-2">Aguardando dados...</p>
                        </div>
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900 rounded-full text-emerald-600 dark:text-emerald-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1E3A8A] overflow-hidden shadow-sm rounded-xl p-6 text-white relative">
                    <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 rounded-full bg-blue-700 opacity-50"></div>
                    <p class="text-blue-100 text-sm font-medium z-10 relative">Hoje é {{ \Carbon\Carbon::now()->locale('pt_BR')->dayName }}</p>
                    <h3 class="text-2xl font-bold mt-1 z-10 relative">Aulas de Hoje</h3>
                    <button class="mt-4 w-full bg-white text-blue-900 font-bold py-2 px-4 rounded shadow hover:bg-gray-100 transition z-10 relative text-sm">
                        Registrar Chamada
                    </button>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border-l-4 border-red-500 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Em Risco</p>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mt-1">Análise</h3>
                            <p class="text-xs text-red-500 font-semibold mt-2">Dados insuficientes</p>
                        </div>
                        <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full text-red-600 dark:text-red-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-0 mt-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Minhas Disciplinas</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    
                    @forelse($disciplinas as $disciplina)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition duration-200 overflow-hidden border border-gray-100 dark:border-gray-700">
                            
                            <div class="h-2 w-full" style="background-color: {{ $disciplina->cor ?? '#1E3A8A' }}"></div>
                            
                            <div class="p-5">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ $disciplina->nome }}
                                        </h4>
                                        <p class="text-xs text-gray-500">
                                            {{ $disciplina->horarios->count() }} aulas semanais
                                        </p>
                                    </div>
                                    
                                    <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                        Calculando...
                                    </span>
                                </div>
                                
                                <div class="mt-4">
                                    <div class="flex justify-between mb-1">
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                            Faltas: {{ $disciplina->frequencias->where('presente', 0)->count() }}
                                        </span>
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Limite: --</span>
                                    </div>
                                    
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                        <div class="h-2.5 rounded-full" 
                                             style="width: 100%; background-color: {{ $disciplina->cor ?? '#10B981' }}"></div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-2">Sincronizado.</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 bg-gray-50 dark:bg-gray-700 rounded-lg border-2 border-dashed border-gray-300">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            <p class="mt-2 text-sm text-gray-500">Você ainda não cadastrou nenhuma disciplina.</p>
                        </div>
                    @endforelse

                    <a href="#" class="bg-gray-50 dark:bg-gray-700 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition cursor-pointer group">
                        <div class="text-center">
                            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-600 group-hover:bg-blue-100 dark:group-hover:bg-blue-900 transition">
                                <svg class="h-6 w-6 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                            <span class="mt-2 block text-sm font-medium text-gray-900 dark:text-white">Nova Disciplina</span>
                        </div>
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>