<x-app-layout>
    {{-- 1. HEADER (Igual ao Nova Matéria) --}}
    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- O botão de voltar age como "Cancelar" --}}
            <a href="{{ route('eventos.index') }}"
               class="p-2 -ml-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                Editar Evento
            </h2>
        </div>
    </x-slot>

    {{-- 2. CONTAINER PRINCIPAL --}}
    <div class="py-6 pb-24">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">

            <form action="{{ route('eventos.update', $evento->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-8">

                    {{-- TÍTULO (Estilo Minimalista / Underline) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">
                            Título
                        </label>
                        <input type="text" name="titulo" required
                               value="{{ old('titulo', $evento->titulo) }}"
                               placeholder="Ex: Feriado Nacional"
                               class="w-full text-lg font-semibold bg-transparent border-0 border-b-2
                                      border-gray-300 dark:border-gray-600 focus:border-blue-500
                                      focus:ring-0 px-1 py-3 placeholder-gray-300
                                      dark:placeholder-gray-600 dark:text-white transition-colors">
                        @error('titulo')
                            <p class="text-red-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- GRID DE DATA E TIPO --}}
                    <div class="grid grid-cols-1 gap-6">
                        
                        {{-- Data --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">
                                Data
                            </label>
                            <div class="relative">
                                <input type="date" name="data" required
                                       value="{{ old('data', $evento->data->format('Y-m-d')) }}"
                                       class="w-full bg-gray-50 dark:bg-gray-800 border-0 rounded-2xl
                                              px-4 py-4 font-semibold text-gray-800 dark:text-white
                                              focus:ring-2 focus:ring-blue-500 transition-shadow">
                                {{-- Ícone decorativo --}}
                                <div class="absolute right-4 top-4 pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Tipo --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">
                                Tipo
                            </label>
                            <div class="relative">
                                <select name="tipo" required
                                        class="w-full bg-gray-50 dark:bg-gray-800 border-0 rounded-2xl
                                               pl-4 pr-10 py-4 font-semibold text-gray-800 dark:text-white
                                               focus:ring-2 focus:ring-blue-500 transition-shadow appearance-none cursor-pointer">
                                    <option value="sem_aula" @selected($evento->tipo === 'sem_aula')>Sem Aula (Folga)</option>
                                    <option value="feriado" @selected($evento->tipo === 'feriado')>Feriado Municipal</option>
                                </select>
                                <div class="absolute right-4 top-4 pointer-events-none text-gray-500">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- 3. BOTÃO FLUTUANTE DE SALVAR (Igual ao Nova Matéria) --}}
                <div class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 dark:bg-gray-900/80
                            backdrop-blur-md border-t border-gray-100 dark:border-gray-800 z-50">
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold
                                   text-lg py-4 rounded-2xl shadow-xl shadow-blue-600/20
                                   transition transform active:scale-[0.98]
                                   flex items-center justify-center gap-2">
                        <span>Salvar Alterações</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>