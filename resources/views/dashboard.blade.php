<x-app-layout>
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob dark:bg-blue-900/20"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000 dark:bg-purple-900/20"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-emerald-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000 dark:bg-emerald-900/20"></div>
    </div>

    @php
        $todasFrequencias = $disciplinas->pluck('frequencias')->collapse();
        $totalAulasGeral = $todasFrequencias->count();
        $totalFaltasGeral = $todasFrequencias->where('presente', false)->count();
        
        $porcentagemGlobal = 100;
        if ($totalAulasGeral > 0) {
            $porcentagemGlobal = round((($totalAulasGeral - $totalFaltasGeral) / $totalAulasGeral) * 100);
        }

        $corGlobal = 'text-emerald-600 dark:text-emerald-400';
        if($porcentagemGlobal < 75) $corGlobal = 'text-red-600 dark:text-red-400';
        elseif($porcentagemGlobal < 85) $corGlobal = 'text-yellow-600 dark:text-yellow-400';

        $materiasEmRisco = 0;
        foreach($disciplinas as $d) {
            $t = $d->frequencias->count();
            $f = $d->frequencias->where('presente', false)->count();
            if($t > 0 && ((($t - $f) / $t) * 100) < 75) {
                $materiasEmRisco++;
            }
        }
    @endphp

    <div class="py-6 sm:py-10 pb-24 md:pb-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Olá, {{ explode(' ', Auth::user()->name)[0] }} 👋
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm sm:text-base">
                        Vamos manter o foco nos estudos hoje?
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div id="tour-chamada" class="lg:col-span-2 relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-blue-600 to-indigo-700 shadow-2xl shadow-blue-900/20 text-white p-6 sm:p-8"
                     x-data="{
                        modalOpen: false, 
                        modalEvento: false,
                        diaLivre: null,
                        aulas: [], 
                        loading: false, 
                        enviando: false, 
                        sucesso: false, 
                        dataSelecionada: new Date(new Date().getTime() - (new Date().getTimezoneOffset() * 60000)).toISOString().slice(0, 10),
                        
                        async abrirModal() {
                            this.modalOpen = true;
                            this.sucesso = false;
                            this.dataSelecionada = new Date(new Date().getTime() - (new Date().getTimezoneOffset() * 60000)).toISOString().slice(0, 10);
                            await this.buscarAulas();
                        },

                        async buscarAulas() {
                            this.loading = true;
                            this.aulas = [];
                            this.diaLivre = null;
                            try {
                                let res = await fetch(`/api/buscar-aulas?data=${this.dataSelecionada}`);
                                let data = await res.json();
                                if (data.dia_livre) {
                                    this.aulas = [];
                                    this.diaLivre = data.motivo;
                                    this.loading = false;
                                    return;
                                    }
                                this.aulas = data;
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
                    
                >
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-purple-500/20 rounded-full blur-2xl"></div>

                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/20 backdrop-blur-md text-xs font-semibold mb-3 border border-white/10">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ date('d/m/y -') }} {{ \Carbon\Carbon::now()->locale('pt_BR')->dayName }} 
                                </div>
                                <h2 class="text-2xl sm:text-3xl font-bold leading-tight">Registrar<br>Presença Diária</h2>
                            </div>
                            <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-md border border-white/10 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>

                        <button @click="abrirModal()" class="mt-8 w-full sm:w-auto bg-white text-blue-600 hover:bg-blue-50 font-bold py-4 px-8 rounded-xl shadow-xl transition-transform active:scale-95 flex items-center justify-center gap-2 group">
                            <span>Abrir Diário de Classe</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </button>
                        <button @click="modalEvento = true" class="mt-3 w-full sm:w-auto bg-white/20 text-white hover:bg-white/30 font-semibold py-3 px-6 rounded-xl transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Marcar Dia Livre
                        </button>
                    </div>

                    <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
                        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="modalOpen = false"></div>
                        
                        <div class="relative bg-white dark:bg-gray-900 w-full max-w-md rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-white/50 dark:bg-gray-900/50 backdrop-blur-xl z-10">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Diário de Classe</h3>
                                <button @click="modalOpen = false" class="p-2 -mr-2 text-gray-400 hover:text-gray-600 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                            </div>
                            
                            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Selecione a Data</label>
                                    <input type="date" x-model="dataSelecionada" @change="buscarAulas()" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 shadow-sm">
                                </div>

                                <div x-show="diaLivre"
     class="flex items-center gap-2 px-4 py-3 rounded-xl bg-red-50 text-red-700 border border-red-200 text-sm font-semibold">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>

    <span>
        Dia livre:
        <strong x-text="diaLivre"></strong>
    </span>
</div>


                                <div class="min-h-[200px]">
                                    <div x-show="loading" class="flex flex-col items-center justify-center h-40 text-gray-400"><svg class="animate-spin h-8 w-8 text-blue-500 mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="text-sm font-medium">Sincronizando grade...</span></div>
                                    <div x-show="sucesso" class="flex flex-col items-center justify-center h-40 text-emerald-500"><div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-3"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div><span class="font-bold text-lg">Salvo com Sucesso!</span></div>
                                    <div x-show="!loading && !sucesso">
                                        <div x-show="aulas.length === 0 && !loading" class="text-center py-10 text-gray-400 text-sm">
                                            <!-- Dia livre -->
                                             <p x-show="diaLivre">
                                                Dia livre — Nenhuma aula neste dia 🎉
                                            </p>
                                            <p x-show="!diaLivre">
                                                Nenhuma aula nesta grade horária.
                                            </p>
                                        </div>
                                        <div class="space-y-3">
                                            <template x-for="(aula, index) in aulas" :key="index">
                                                <div class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                                                    <div>
                                                        <div class="flex items-center gap-2"><h4 class="font-bold text-gray-800 dark:text-gray-100" x-text="aula.nome"></h4><span x-show="aula.ja_registrado" class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">Editando</span></div>
                                                        <p class="text-xs text-gray-500 mt-1 font-mono" x-text="aula.horario.substring(0,5)"></p>
                                                    </div>
                                                    <button @click="aula.presente = !aula.presente" class="w-12 h-12 rounded-xl flex items-center justify-center transition-all active:scale-90 shadow-sm border" :class="aula.presente ? 'bg-emerald-50 border-emerald-200 text-emerald-600' : 'bg-red-50 border-red-200 text-red-600'"><span class="font-bold text-lg" x-text="aula.presente ? 'P' : 'F'"></span></button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-black/20">
                                <button x-show="aulas.length > 0 && !loading && !sucesso" @click="confirmarChamada()" :disabled="enviando" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-500/30 active:scale-[0.98] transition-all disabled:opacity-50"><span x-show="!enviando">Confirmar Chamada</span><span x-show="enviando">Salvando...</span></button>
                                <button x-show="aulas.length === 0 || sucesso" @click="modalOpen = false" class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold py-4 rounded-xl shadow-sm">Fechar</button>
                            </div>
                        </div>
                    </div>

                    <div x-show="modalEvento" style="display: none"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
         @click="modalEvento = false"></div>

    <div class="relative bg-white dark:bg-gray-900 w-full max-w-md rounded-3xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            Marcar Dia Livre
        </h3>

        <form method="POST" action="{{ route('eventos.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-1">Título</label>
                <input type="text" name="titulo" required
                       placeholder="Ex: Feriado, Recesso, Falta Justificada"
                       class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Data</label>
                <input type="date" name="data" required
                       class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Tipo</label>
                <select name="tipo" required
                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="sem_aula">Sem Aula</option>
                    <option value="feriado">Feriado Municipal</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Descrição (opcional)</label>
                <textarea name="descricao" rows="3"
                          class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl">
                    Salvar
                </button>
                <button type="button"
                        @click="modalEvento = false"
                        class="flex-1 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold py-3 rounded-xl">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

                </div>

                <div id="tour-status" class="grid grid-cols-2 lg:grid-cols-1 gap-4 lg:gap-6">
                    
                    <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2rem] border border-white/20 dark:border-gray-800 p-6 flex flex-col justify-center shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Global</p>
                        <div class="flex items-baseline gap-1">
                            <h3 class="text-4xl font-extrabold {{ $corGlobal }}">{{ $porcentagemGlobal }}</h3>
                            <span class="text-lg font-medium text-gray-400">%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 font-medium">Presença total acumulada</p>
                    </div>

                    <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2rem] border border-white/20 dark:border-gray-800 p-6 flex flex-col justify-center shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Em Risco</p>
                        @if($materiasEmRisco > 0)
                            <div class="flex items-center gap-3">
                                <h3 class="text-4xl font-extrabold text-red-500">{{ $materiasEmRisco }}</h3>
                                <span class="px-2 py-1 rounded-md bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold uppercase">Matérias</span>
                            </div>
                            <p class="text-xs text-red-500 mt-2 font-medium">Atenção necessária!</p>
                        @else
                            <div class="flex items-center gap-3">
                                <h3 class="text-4xl font-extrabold text-emerald-500">0</h3>
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-xs text-emerald-500 mt-2 font-medium">Tudo sob controle.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between px-1 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Minhas Matérias</h3>
                    </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    
                    <a href="{{ route('disciplinas.criar') }}" 
                       id="tour-nova-materia"
                       class="hidden md:flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-3xl hover:border-blue-500 dark:hover:border-blue-500 hover:bg-blue-50/50 dark:hover:bg-blue-900/10 transition-all duration-300 group min-h-[200px]">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Nova Matéria</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-1">Adicionar à grade</p>
                    </a>

                    @forelse($disciplinas as $disciplina)
                        @php
                            $totalRegistros = $disciplina->frequencias->count();
                            $totalFaltas = $disciplina->frequencias->where('presente', false)->count();
                            $porcentagem = 100;
                            if ($totalRegistros > 0) {
                                $porcentagem = round((($totalRegistros - $totalFaltas) / $totalRegistros) * 100);
                            }
                            $corBarra = 'bg-emerald-500';
                            $corTexto = 'text-emerald-600 dark:text-emerald-400';
                            if($porcentagem < 75) {
                                $corBarra = 'bg-red-500';
                                $corTexto = 'text-red-600 dark:text-red-400';
                            } elseif($porcentagem < 85) {
                                $corBarra = 'bg-yellow-500';
                                $corTexto = 'text-yellow-600 dark:text-yellow-400';
                            }
                        @endphp

                        <div class="group bg-white/70 dark:bg-gray-900/70 backdrop-blur-md rounded-3xl border border-white/20 dark:border-gray-800 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
                            <div class="h-2 w-full absolute top-0 left-0" style="background-color: {{ $disciplina->cor ?? '#3B82F6' }}"></div>
                            
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-6">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white truncate pr-4 leading-tight">
                                        {{ $disciplina->nome }}
                                    </h4>
                                    <div class="flex flex-col items-end">
                                        <span class="text-3xl font-extrabold text-gray-900 dark:text-white leading-none tracking-tighter">
                                            {{ $totalFaltas }}
                                        </span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Faltas</span>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex justify-between items-end">
                                        <span class="text-xs font-medium text-gray-400">Frequência</span>
                                        <span class="text-sm font-bold {{ $corTexto }}">{{ $porcentagem }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-1000 {{ $corBarra }}" style="width: {{ $porcentagem }}%"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-black/20 flex justify-between items-center">
                                <a href="{{ route('grade.index', $disciplina->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition flex items-center gap-1 bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 rounded-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Grade
                                </a>

                                <div class="flex items-center gap-1">
                                    <a href="{{ route('disciplinas.edit', $disciplina->id) }}" class="p-2 text-gray-400 hover:text-blue-500 hover:bg-white dark:hover:bg-gray-800 rounded-lg transition" title="Editar Matéria">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>

                                    <form action="{{ route('disciplinas.destroy', $disciplina->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta matéria? Todas as faltas serão apagadas.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-white dark:hover:bg-gray-800 rounded-lg transition" title="Excluir Matéria">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center md:hidden">
                            <p class="text-gray-500 font-medium">Nenhuma disciplina cadastrada.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    @if(Auth::user()->has_seen_intro && !Auth::user()->has_completed_tour)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const driver = window.driver.js.driver;
    const isMobile = window.innerWidth < 1024;

    let tourSteps = [
        {
            element: '#tour-chamada',
            popover: {
                title: 'Chamada Rápida',
                description: 'Registre sua presença do dia com um clique aqui. O sistema identifica as aulas automaticamente.'
            }
        },
        {
            element: '#tour-status',
            popover: {
                title: 'Seu Painel',
                description: 'Acompanhe sua frequência global e veja alertas de matérias em risco.'
            }
        },
        {
            element: '#tour-theme-toggle',
            popover: {
                title: 'Modo Noturno',
                description: 'Prefere estudar à noite? Troque o tema aqui.'
            }
        }
    ];

    if (isMobile) {
        tourSteps.push(
            { element: '#tour-add-mobile', popover: { title: 'Nova Matéria', description: 'Toque no botão central para adicionar suas disciplinas.' } },
            { element: '#tour-grade-mobile', popover: { title: 'Sua Grade', description: 'Veja e configure seus horários nesta aba.' } },
            { element: '#tour-profile-mobile', popover: { title: 'Seu Perfil', description: 'Gerencie sua conta e configurações aqui.' } }
        );
    } else {
        tourSteps.push(
            { element: '#tour-nova-materia', popover: { title: 'Nova Matéria', description: 'Comece clicando aqui para cadastrar disciplinas.' } },
            { element: '#tour-grade-desktop', popover: { title: 'Grade Horária', description: 'Acesse a visão completa da sua semana.' } }
        );
    }

    const driverObj = driver({
        showProgress: true,
        allowClose: true,
        animate: true,
        nextBtnText: 'Próximo',
        prevBtnText: 'Voltar',
        doneBtnText: 'Concluir',
        steps: tourSteps,
        onDestroyStarted: () => {
            fetch('{{ route("tour.finish") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
            driverObj.destroy();
        }
    });

    setTimeout(() => driverObj.drive(), 1000);
});
</script>
@endif


</x-app-layout>