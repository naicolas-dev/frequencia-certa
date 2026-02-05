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

    <div class="py-10 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- ESTATÍSTICAS --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Card Total -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition">
                        <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] md:text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total de Aulas</p>
                        <h3 class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white mt-1">{{ $totalRegistros }}</h3>
                    </div>
                     <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-blue-600 dark:text-blue-400">
                        <span class="bg-blue-100 dark:bg-blue-900/30 px-2 py-0.5 rounded text-blue-700 dark:text-blue-300">100%</span>
                        <span class="text-gray-400 font-normal ml-1">do período</span>
                    </div>
                </div>

                <!-- Card Presenças -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between relative overflow-hidden group">
                     <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition">
                        <svg class="w-16 h-16 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] md:text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Presenças</p>
                        <h3 class="text-2xl md:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $totalPresencas }}</h3>
                    </div>
                    <div class="mt-4 text-xs text-gray-400">
                        Registros positivos
                    </div>
                </div>

                <!-- Card Faltas -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between relative overflow-hidden group">
                     <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition">
                        <svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] md:text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Faltas</p>
                        <h3 class="text-2xl md:text-3xl font-black text-red-600 dark:text-red-400 mt-1">{{ $totalFaltas }}</h3>
                    </div>
                    <div class="mt-4 text-xs text-gray-400">
                        Ausências
                    </div>
                </div>

                <!-- Card Frequência -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between relative overflow-hidden">
                    <div>
                        <p class="text-[10px] md:text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Frequência</p>
                        <h3 class="text-2xl md:text-3xl font-black {{ $porcentagemPresenca >= 75 ? 'text-emerald-500' : 'text-amber-500' }} mt-1">
                            {{ $porcentagemPresenca }}%
                        </h3>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-4 overflow-hidden">
                        <div class="{{ $porcentagemPresenca >= 75 ? 'bg-emerald-500' : 'bg-amber-500' }} h-1.5 rounded-full" style="width: {{ $porcentagemPresenca }}%"></div>
                    </div>
                </div>
            </div>

            {{-- ÁREA DE FILTROS (Modernizada) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm"
                 x-data="{ open: false }">
                
                {{-- Toggle Mobile --}}
                <button @click="open = !open" 
                        class="md:hidden w-full flex items-center justify-between p-4 bg-gray-50/50 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition rounded-t-2xl">
                    <div class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filtros de Busca
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div class="p-6 border-t border-gray-100 dark:border-gray-700 md:border-0 md:block" :class="{'block': open, 'hidden': !open}">
                    <form method="GET" action="{{ route('frequencia.historico') }}" 
                          class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-12 gap-6 items-end">
                        
                        {{-- Disciplina --}}
                        <div class="md:col-span-2 lg:col-span-4">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase ml-1 mb-2">Disciplina</label>
                            <div class="relative">
                                <select name="disciplina_id" class="appearance-none w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 pl-3 pr-10 shadow-sm">
                                    <option value="">Todas as disciplinas</option>
                                    @foreach($disciplinas as $d)
                                        <option value="{{ $d->id }}" {{ request('disciplina_id') == $d->id ? 'selected' : '' }}>
                                            {{ $d->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="md:col-span-2 lg:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase ml-1 mb-2">Status</label>
                            <div class="relative">
                                <select name="status" class="appearance-none w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white text-sm py-2.5 pl-3 pr-10 shadow-sm">
                                    <option value="">Todos</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>✅ Presença</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>❌ Falta</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Datas --}}
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase ml-1 mb-2">De</label>
                            <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white text-sm py-2.5 shadow-sm">
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase ml-1 mb-2">Até</label>
                            <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white text-sm py-2.5 shadow-sm">
                        </div>

                        {{-- Botões --}}
                        <div class="md:col-span-4 lg:col-span-2 flex gap-2">
                            @if(request()->anyFilled(['disciplina_id', 'status', 'data_inicio', 'data_fim']))
                                <a href="{{ route('frequencia.historico') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center justify-center shadow-sm" title="Limpar Filtros">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                            <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 transition hover:shadow-blue-500/40 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- LISTA DE REGISTROS --}}
            <div class="space-y-4">
                {{-- MOBILE (Cards Clean) --}}
                <div class="space-y-3 md:hidden">
                    @forelse($historico as $registro)
                        @php $data = \Carbon\Carbon::parse($registro->data); @endphp
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4 relative overflow-hidden active:scale-[0.98] transition duration-150">
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $registro->presente ? 'bg-emerald-500' : 'bg-red-500' }}"></div>

                            <div class="flex flex-col items-center justify-center w-12 h-12 bg-gray-50 dark:bg-gray-700/50 rounded-xl shrink-0">
                                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500">{{ $data->shortMonthName }}</span>
                                <span class="text-xl font-bold text-gray-800 dark:text-gray-200 -mt-1">{{ $data->format('d') }}</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                     <div class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $registro->disciplina->cor ?? '#ccc' }}"></div>
                                    <h4 class="font-bold text-gray-900 dark:text-white truncate text-sm">{{ $registro->disciplina->nome }}</h4>
                                </div>
                                <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="capitalize">{{ $data->translatedFormat('l') }}</span>
                                    <span>•</span>
                                    <span>{{ \Carbon\Carbon::parse($registro->horario)->format('H:i') }}</span>
                                </div>
                            </div>
                            
                            <div class="shrink-0">
                                @if($registro->presente)
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                         <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Nenhum registro encontrado</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Tente ajustar seus filtros de busca.</p>
                        </div>
                    @endforelse
                </div>

                {{-- DESKTOP (Table Modern) --}}
                <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Data</th>
                                <th class="px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Disciplina</th>
                                <th class="px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Horário</th>
                                <th class="px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($historico as $registro)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition duration-150">
                                    <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">
                                        {{ \Carbon\Carbon::parse($registro->data)->format('d/m/Y') }}
                                        <span class="text-xs text-gray-500 dark:text-gray-400 font-normal ml-1 border-l border-gray-300 dark:border-gray-600 pl-2">
                                            {{ \Carbon\Carbon::parse($registro->data)->translatedFormat('l') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="w-2.5 h-2.5 rounded-full ring-2 ring-white dark:ring-gray-800" style="background-color: {{ $registro->disciplina->cor }}"></span>
                                            <span class="font-medium text-gray-700 dark:text-gray-200">{{ $registro->disciplina->nome }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300 font-mono text-xs">
                                        {{ \Carbon\Carbon::parse($registro->horario)->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($registro->presente)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                Presente
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400 border border-red-100 dark:border-red-500/20">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                Falta
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mb-4 text-gray-400">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <p class="text-gray-800 dark:text-white font-medium">Nenhum registro encontrado</p>
                                            <p class="text-gray-500 text-sm">Tente limpar os filtros ou selecionar outra data</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if($historico->hasPages())
                    <div class="pt-4">
                        {{ $historico->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>