<x-app-layout>
    {{-- CABEÇALHO (Padrão idêntico ao Nova Matéria) --}}
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}"
               class="p-2 -ml-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                Dias Livres
            </h2>
        </div>
    </x-slot>

    {{-- CONTAINER PRINCIPAL --}}
    <div class="py-6 pb-28"> {{-- pb-28 garante espaço para a barra fixa --}}
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">

            {{-- EMPTY STATE --}}
            @if($eventos->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 text-center opacity-60">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        Nenhum dia livre
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Seus dias de folga aparecerão aqui.
                    </p>
                </div>
            @else
                {{-- LISTAGEM (Mantendo os cards, mas limpando o visual) --}}
                <div class="space-y-4">
                    @foreach($eventos as $evento)
                        @php
                            $isFeriado = $evento->tipo === 'feriado';
                            $dataCarbon = \Carbon\Carbon::parse($evento->data);
                        @endphp

                        <div class="group relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden transition hover:shadow-md">
                            
                            {{-- Barra lateral de cor --}}
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $isFeriado ? 'bg-red-500' : 'bg-blue-500' }}"></div>

                            <div class="p-5 pl-6 flex items-center justify-between gap-4">
                                {{-- Informações --}}
                                <div>
                                    <span class="text-xs font-bold uppercase tracking-wider {{ $isFeriado ? 'text-red-500' : 'text-blue-500' }}">
                                        {{ $isFeriado ? 'Feriado' : 'Sem Aula' }}
                                    </span>
                                    
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-white leading-tight mt-0.5">
                                        {{ $evento->titulo }}
                                    </h3>
                                    
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-medium">
                                        {{ $dataCarbon->format('d/m') }} 
                                        <span class="opacity-60 font-normal">- {{ $dataCarbon->translatedFormat('l') }}</span>
                                    </p>
                                </div>

                                {{-- Ações (Simplificadas para ícones para limpar a UI) --}}
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('eventos.edit', $evento->id) }}" 
                                       class="p-2 rounded-xl text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('eventos.destroy', $evento->id) }}" method="POST"
                                        data-confirm="Tem certeza que deseja excluir este dia livre?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</x-app-layout>