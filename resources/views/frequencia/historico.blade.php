<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}"
               class="p-2 -ml-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                Histórico
            </h2>
        </div>
    </x-slot>

    <div class="py-6 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ÁREA DE FILTROS (Responsiva) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm"
                 x-data="{ open: false }">
                
                {{-- Toggle Mobile (Só aparece em telas pequenas 'md:hidden') --}}
                <button @click="open = !open" 
                        class="md:hidden w-full flex items-center justify-between p-4 bg-gray-50/50 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition rounded-t-2xl">
                    <div class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filtrar Registros
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Formulário --}}
                {{-- CORREÇÃO AQUI: Adicionei 'md:block' junto com a lógica do Alpine --}}
                {{-- Isso garante que no Desktop (md) ele seja sempre 'block', ignorando o 'hidden' do Alpine --}}
                <div class="p-5 border-t border-gray-100 dark:border-gray-700 md:border-0 md:block"
                     :class="{'block': open, 'hidden': !open}">
                    
                    <form method="GET" action="{{ route('frequencia.historico') }}" 
                          class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4 items-end">
                        
                        {{-- Disciplina --}}
                        <div class="md:col-span-2 lg:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase ml-1 mb-1">Disciplina</label>
                            <select name="disciplina_id" class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todas</option>
                                @foreach($disciplinas as $d)
                                    <option value="{{ $d->id }}" {{ request('disciplina_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase ml-1 mb-1">Status</label>
                            <select name="status" class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white text-sm">
                                <option value="">Todos</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Presença</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Falta</option>
                            </select>
                        </div>

                        {{-- Datas --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase ml-1 mb-1">De</label>
                            <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase ml-1 mb-1">Até</label>
                            <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white text-sm">
                        </div>

                        {{-- Botões --}}
                        <div class="flex gap-2">
                            @if(request()->anyFilled(['disciplina_id', 'status', 'data_inicio', 'data_fim']))
                                <a href="{{ route('frequencia.historico') }}" class="px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center justify-center" title="Limpar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Limpar
                                </a>
                            @endif
                            <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 transition">
                                <span class="md:hidden">Aplicar Filtros</span>
                                <span class="hidden md:inline">Filtrar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ==============================
                 VISUALIZAÇÃO 1: MOBILE (Cards)
                 ============================== --}}
            <div class="space-y-3 md:hidden">
                @forelse($historico as $registro)
                    @php $data = \Carbon\Carbon::parse($registro->data); @endphp
                    <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 {{ $registro->presente ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                        
                        <div class="flex flex-col items-center justify-center w-12 h-12 bg-gray-100 dark:bg-gray-700/50 rounded-xl mr-4 shrink-0">
                            <span class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400 leading-none">{{ $data->translatedFormat('M') }}</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white leading-none mt-0.5">{{ $data->format('d') }}</span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <div class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $registro->disciplina->cor ?? '#ccc' }}"></div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                    {{ $registro->disciplina->nome }}
                                </h4>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">
                                {{ $data->translatedFormat('l') }}
                            </p>
                            <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ \Carbon\Carbon::parse($registro->horario)->format('H:i') }}
                            </span>
                        </div>

                        <div class="ml-2">
                            @if($registro->presente)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-500">Nenhum registo encontrado.</div>
                @endforelse
            </div>

            {{-- ================================
                 VISUALIZAÇÃO 2: DESKTOP (Table)
                 ================================ --}}
            <div class="hidden md:block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-300">
                        <tr>
                            <th class="px-6 py-4">Data</th>
                            <th class="px-6 py-4">Disciplina</th>
                            <th class="px-6 py-4">Horário</th>
                            <th class="px-6 py-4">Dia da Semana</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historico as $registro)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($registro->data)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full shadow-sm" style="background-color: {{ $registro->disciplina->cor }}"></div>
                                        <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $registro->disciplina->nome }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($registro->horario)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 capitalize">
                                    {{ \Carbon\Carbon::parse($registro->data)->translatedFormat('l') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($registro->presente)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            Presente
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 border border-red-200 dark:border-red-800">
                                            Falta
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-900 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <p class="text-gray-500 dark:text-gray-400 text-base">Nenhum registo encontrado com estes filtros.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINAÇÃO --}}
            @if($historico->hasPages())
                <div class="pb-6">
                    {{ $historico->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>