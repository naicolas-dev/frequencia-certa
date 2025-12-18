<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Minha Grade Horária') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="{ diaAtivo: {{ date('N') > 5 ? 1 : date('N') }} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="md:hidden mb-6 overflow-x-auto pb-2 px-4 scrollbar-hide">
                <div class="flex space-x-2">
                    @foreach([1=>'Seg', 2=>'Ter', 3=>'Qua', 4=>'Qui', 5=>'Sex', 6=>'Sáb'] as $num => $nome)
                        <button @click="diaAtivo = {{ $num }}" 
                            :class="diaAtivo === {{ $num }} ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-gray-300 dark:border-gray-700'"
                            class="px-4 py-2 rounded-full border text-sm font-bold shadow-sm transition whitespace-nowrap">
                            {{ $nome }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="md:hidden px-4 space-y-4">
                @for($i = 1; $i <= 6; $i++)
                    <div x-show="diaAtivo === {{ $i }}" style="display: none;">
                        @if(isset($gradePorDia[$i]))
                            @foreach($gradePorDia[$i] as $aula)
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 p-4 flex justify-between items-center"
                                     style="border-left-color: {{ $aula->disciplina->cor }};">
                                    <div>
                                        <h3 class="font-bold text-gray-800 dark:text-gray-100 text-lg">
                                            {{ $aula->disciplina->nome }}
                                        </h3>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                                            {{ date('H:i', strtotime($aula->horario_inicio)) }} - {{ date('H:i', strtotime($aula->horario_fim)) }}
                                        </p>
                                    </div>
                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $aula->disciplina->cor }}"></div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-10 text-gray-400">
                                <p>Sem aulas neste dia. 💤</p>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>

            <div class="hidden md:grid grid-cols-5 gap-4">
                @foreach([1=>'Segunda', 2=>'Terça', 3=>'Quarta', 4=>'Quinta', 5=>'Sexta'] as $num => $nome)
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 h-full">
                        <h3 class="text-center font-bold text-gray-700 dark:text-gray-300 mb-4 uppercase text-sm tracking-wider border-b pb-2 border-gray-200 dark:border-gray-700">
                            {{ $nome }}
                        </h3>

                        <div class="space-y-3">
                            @if(isset($gradePorDia[$num]))
                                @foreach($gradePorDia[$num] as $aula)
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded shadow-sm border-l-4 hover:shadow-md transition"
                                         style="border-left-color: {{ $aula->disciplina->cor }}">
                                        <p class="font-bold text-gray-800 dark:text-gray-200 text-sm">
                                            {{ $aula->disciplina->nome }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ date('H:i', strtotime($aula->horario_inicio)) }} - {{ date('H:i', strtotime($aula->horario_fim)) }}
                                        </p>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-center text-xs text-gray-400 italic py-4">Livre</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>