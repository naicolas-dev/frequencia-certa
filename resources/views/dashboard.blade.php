<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Painel
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bem-vindo, {{ Auth::user()->name }}</p>
            </div>
            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full text-xs font-semibold">
                {{ date('Y') }}
            </span>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Frequência Global</p>
                        <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">--%</h3>
                        <p class="text-[10px] text-gray-400 mt-1">Calculando...</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <div 
                    x-data="{ 
                        modalOpen: false, 
                        aulas: [], 
                        loading: false, 
                        enviando: false, 
                        sucesso: false, 
                        dataSelecionada: new Date(new Date().getTime() - (new Date().getTimezoneOffset() * 60000)).toISOString().slice(0, 10),
                        
                        async abrirModal() {
                            this.modalOpen = true;
                            this.sucesso = false;
                            await this.buscarAulas();
                        },

                        async buscarAulas() {
                            this.loading = true;
                            this.aulas = [];
                            try {
                                let res = await fetch(`/api/buscar-aulas?data=${this.dataSelecionada}`);
                                this.aulas = await res.json();
                            } catch(e) { console.error(e); }
                            this.loading = false;
                        },

                        async confirmarChamada() {
                            this.enviando = true;
                            try {
                                let payload = {
                                    data: this.dataSelecionada,
                                    chamada: this.aulas.map(a => ({ disciplina_id: a.disciplina_id, presente: a.presente }))
                                };
                                let response = await fetch('/api/registrar-chamada', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify(payload)
                                });
                                if (response.ok) {
                                    this.sucesso = true;
                                    setTimeout(() => { window.location.reload(); }, 1000);
                                }
                            } catch (e) { alert('Erro de conexão'); }
                            this.enviando = false;
                        }
                    }"
                    class="bg-blue-600 rounded-2xl shadow-lg shadow-blue-500/30 p-5 text-white relative overflow-hidden"
                >
                    <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-xs font-medium opacity-80">Hoje é {{ \Carbon\Carbon::now()->locale('pt_BR')->dayName }}</p>
                                <h3 class="text-xl font-bold mt-1">Registrar Chamada</h3>
                            </div>
                            <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        
                        <button @click="abrirModal()" class="mt-4 w-full bg-white text-blue-600 font-bold py-3 px-4 rounded-xl shadow-sm active:scale-95 transition-transform flex items-center justify-center gap-2 text-sm">
                            <span>Abrir Diário</span>
                        </button>
                    </div>

                    <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-0 sm:p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="modalOpen = false"></div>

                        <div 
                             x-show="modalOpen"
                             x-transition:enter="transform transition ease-out duration-300"
                             x-transition:enter-start="translate-y-full sm:translate-y-10 sm:opacity-0"
                             x-transition:enter-end="translate-y-0 sm:translate-y-0 sm:opacity-100"
                             class="relative bg-white dark:bg-gray-900 w-full h-full sm:h-auto sm:max-w-md sm:rounded-2xl shadow-2xl flex flex-col z-10"
                        >
                            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-white dark:bg-gray-900 z-10 sm:rounded-t-2xl">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Frequência</h3>
                                <button @click="modalOpen = false" class="p-2 -mr-2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <div class="flex-1 overflow-y-auto p-5 space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Data da Aula</label>
                                    <input type="date" x-model="dataSelecionada" @change="buscarAulas()" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="min-h-[200px]">
                                    <div x-show="loading" class="flex flex-col items-center justify-center h-40 text-gray-400">
                                        <svg class="animate-spin h-8 w-8 text-blue-500 mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span class="text-sm">Buscando grade...</span>
                                    </div>

                                    <div x-show="sucesso" class="flex flex-col items-center justify-center h-40 text-emerald-500">
                                        <span class="font-bold text-lg">Salvo com Sucesso!</span>
                                    </div>

                                    <div x-show="!loading && !sucesso">
                                        <div x-show="aulas.length === 0" class="text-center py-10 text-gray-400">
                                            <p>Sem aulas neste dia.</p>
                                        </div>

                                        <div class="space-y-3">
                                            <template x-for="(aula, index) in aulas" :key="index">
                                                <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <h4 class="font-bold text-gray-800 dark:text-gray-100" x-text="aula.nome"></h4>
                                                            <span x-show="aula.ja_registrado" class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold">Editando</span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-0.5" x-text="'Início: ' + aula.horario.substring(0,5)"></p>
                                                    </div>
                                                    
                                                    <button @click="aula.presente = !aula.presente" class="w-12 h-12 rounded-full flex items-center justify-center transition-all active:scale-90 shadow-sm" :class="aula.presente ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'">
                                                        <span class="font-bold text-lg" x-text="aula.presente ? 'P' : 'F'"></span>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-5 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 sm:rounded-b-2xl">
                                <button x-show="aulas.length > 0 && !loading && !sucesso" @click="confirmarChamada()" :disabled="enviando" class="w-full bg-blue-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 active:scale-[0.98] transition-all disabled:opacity-50">
                                    <span x-show="!enviando">Salvar Chamada</span>
                                    <span x-show="enviando">Salvando...</span>
                                </button>
                                <button x-show="aulas.length === 0 || sucesso" @click="modalOpen = false" class="w-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold py-3.5 rounded-xl">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Em Risco</p>
                        <h3 class="text-3xl font-extrabold text-gray-800 dark:text-gray-200 mt-1">OK</h3>
                        <p class="text-[10px] text-emerald-500 mt-1 font-bold">Nenhuma reprovação</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between px-1 mt-8">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Matérias</h3>
                <a href="{{ route('disciplinas.criar') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-500">+ Nova</a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($disciplinas as $disciplina)
                    @php
                        // CÁLCULO DA PORCENTAGEM DE PRESENÇA
                        $totalRegistros = $disciplina->frequencias->count();
                        $totalFaltas = $disciplina->frequencias->where('presente', false)->count();
                        
                        $porcentagem = 100; // Padrão se não tiver registros
                        if ($totalRegistros > 0) {
                            $porcentagem = round((($totalRegistros - $totalFaltas) / $totalRegistros) * 100);
                        }

                        // Lógica de Cor da Barra
                        $corBarra = 'bg-emerald-500'; // Verde
                        $corTexto = 'text-emerald-600 dark:text-emerald-400';
                        
                        if($porcentagem < 75) {
                            $corBarra = 'bg-red-500'; // Reprovando
                            $corTexto = 'text-red-600 dark:text-red-400';
                        } elseif($porcentagem < 85) {
                            $corBarra = 'bg-yellow-500'; // Atenção
                            $corTexto = 'text-yellow-600 dark:text-yellow-400';
                        }
                    @endphp

                    <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-900 transition-all duration-300 overflow-hidden relative flex flex-col justify-between">
                        
                        <div class="absolute left-0 top-0 bottom-0 w-1.5" style="background-color: {{ $disciplina->cor ?? '#3B82F6' }}"></div>
                        
                        <div class="p-5 pl-7 pb-2">
                            <div class="flex justify-between items-start">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white truncate pr-4">
                                    {{ $disciplina->nome }}
                                </h4>
                                <div class="flex flex-col items-end">
                                    <span class="text-2xl font-extrabold text-gray-800 dark:text-white leading-none">
                                        {{ $totalFaltas }}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Faltas</span>
                                </div>
                            </div>
                        </div>

                        <div class="px-5 pl-7 pb-5">
                            <div class="flex justify-between items-end mb-1">
                                <span class="text-xs text-gray-400">Frequência</span>
                                <span class="text-sm font-bold {{ $corTexto }}">{{ $porcentagem }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000 {{ $corBarra }}" style="width: {{ $porcentagem }}%"></div>
                            </div>
                            
                            <div class="mt-4 flex items-center justify-between border-t border-gray-50 dark:border-gray-700/50 pt-3">
                                <a href="{{ route('grade.index', $disciplina->id) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                    Configurar Grade Horária
                                </a>
                                <span class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $disciplina->horarios->count() }} aulas/sem
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-12 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl">
                        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <h3 class="text-gray-900 dark:text-white font-bold">Tudo vazio por aqui</h3>
                        <p class="text-sm text-gray-500 mt-1">Adicione sua primeira matéria para começar.</p>
                        <a href="{{ route('disciplinas.criar') }}" class="mt-4 text-blue-600 font-bold text-sm hover:underline">Criar Disciplina</a>
                    </div>
                @endforelse

                <a href="{{ route('disciplinas.criar') }}" class="hidden md:flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-gray-800/50 transition cursor-pointer group h-full min-h-[140px]">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <span class="mt-2 font-bold text-sm text-gray-600 dark:text-gray-400 group-hover:text-blue-600">Adicionar Matéria</span>
                </a>
            </div>
        </div>
    </div>

    <a href="{{ route('disciplinas.criar') }}" class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-blue-600 text-white rounded-full shadow-xl shadow-blue-600/40 flex items-center justify-center active:scale-90 transition-transform z-40">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </a>
</x-app-layout>