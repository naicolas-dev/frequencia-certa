<x-app-layout>
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob dark:bg-blue-900/20"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000 dark:bg-purple-900/20"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-emerald-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000 dark:bg-emerald-900/20"></div>
    </div>

    <div class="py-6 sm:py-10 pb-24 md:pb-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- CABEÇALHO --}}
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
                        @php
                        $hora = now()->hour;
                        if ($hora < 12) { $saudacao='Bom dia' ; } elseif ($hora < 18) { $saudacao='Boa tarde' ; } else { $saudacao='Boa noite' ; }
                        @endphp
                        {{ $saudacao }}, {{ explode(' ', Auth::user()->name)[0] }} 👋
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm sm:text-base">
                        @php
                            $hora = now()->hour;
                            $diaHoje = now()->dayOfWeek;
                            $mensagensPorHora = [
                                0 => 'Já deu por hoje 🙂 — descansar também é produtividade.',
                                1 => 'Hora de desligar um pouco. Um bom sono melhora seu rendimento.',
                                2 => 'Sono é parte do progresso. Seu eu de amanhã agradece.',
                                3 => 'Tá bem tarde… cuida de você. Amanhã é um novo dia.',
                                4 => 'Quase amanhecendo. Que tal se preparar pra não correr depois?',
                                5 => 'Um novo começo chegando 🌅 Ajuste o ritmo e vai com calma.',
                                6 => 'Bom começo de dia! Presença hoje faz diferença no final do semestre.',
                                7 => 'Organiza o dia rapidinho e evita correria mais tarde.',
                                8 => 'Primeiras aulas, primeira chance de mandar bem. Bora marcar presença?',
                                9 => 'Mantém o ritmo: consistência é o que dá resultado.',
                                10 => 'Cada aula conta. Confere sua presença e segue firme.',
                                11 => 'Último gás da manhã 💪 Foco no que importa.',
                                12 => 'Pausa merecida! Já aproveita e confirma sua presença.',
                                13 => 'De volta aos estudos: calma, atenção e presença.',
                                14 => 'Ainda dá tempo de virar o jogo hoje. Bora manter a frequência?',
                                15 => 'Vai no constante: consistência vence a pressa.',
                                16 => 'Olho na frequência 👀 O que você garante hoje evita dor de cabeça depois.',
                                17 => 'Final da tarde chegando. Fecha o dia com presença em dia.',
                                18 => 'Encerrando? Dá uma olhada na chamada antes de sair.',
                                19 => 'Se organizar agora poupa estresse amanhã.',
                                20 => 'Revisar hoje é se agradecer amanhã. 😉',
                                21 => 'Última checagem do dia: tudo certo na frequência?',
                                22 => 'Fechando o dia com responsabilidade. Boa!',
                                23 => 'Hora de descansar 🌙 Amanhã continua — com mais uma presença.'
                            ];
                            $temAulaHoje = $todasDisciplinas->contains(function($d) use ($diaHoje) {
                                return $d->horarios->contains('dia_semana', $diaHoje);
                            });
                            
                            if ($todasDisciplinas->isEmpty()) {
                                echo 'Comece adicionando suas matérias para montar a grade 🚀';
                            } elseif (!$temAulaHoje) {
                                echo 'Hoje não há aulas programadas. Aproveite o descanso 😌';
                            } elseif ($materiasEmRisco > 0) {
                                echo '⚠️ Atenção: você tem matérias com frequência baixa. Foco total!';
                            } else {
                                echo $mensagensPorHora[$hora];
                            }
                        @endphp
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- CARD DO DIÁRIO DE CLASSE --}}
                <div id="tour-chamada" class="lg:col-span-2 relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-blue-600 to-indigo-700 shadow-2xl shadow-blue-900/20 text-white p-6 sm:p-8"
                    x-data="{
                        modalOpen: false, 
                        modalEvento: false,
                        diaLivre: null,
                        aulas: [], 
                        loading: false, 
                        enviando: false, 
                        sucesso: false, 
                        dataSelecionada: '{{ date('Y-m-d') }}',
                        
                        async abrirModal() {
                            this.modalOpen = true;
                            this.sucesso = false;
                            this.dataSelecionada = '{{ date('Y-m-d') }}';
                            await this.buscarAulas();
                        },

                        async validarDataEBuscar() {
                            const hoje = '{{ date('Y-m-d') }}';
                            const inicioAno = '{{ date('Y') }}-01-01';
                            
                            // 1. Validação: FUTURO
                            if (this.dataSelecionada > hoje) {
                                window.swalTailwind.fire({
                                    icon: 'warning',
                                    title: 'Data Inválida',
                                    text: 'Você não pode registrar presença em datas futuras.',
                                    confirmButtonText: 'Entendi'
                                });
                                this.dataSelecionada = hoje;
                                return;
                            }

                            // 2. Validação: ANO PASSADO
                            if (this.dataSelecionada < inicioAno) {
                                window.swalTailwind.fire({
                                    icon: 'info',
                                    title: 'Ano Letivo',
                                    text: 'Você só pode visualizar frequências deste ano letivo.',
                                    confirmButtonText: 'Ok'
                                });
                                this.dataSelecionada = hoje;
                                return;
                            }

                            // 3. Se passou, busca
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
                                this.diaLivre = null;
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
                                    // ATUALIZAÇÃO ASSÍNCRONA DA DASHBOARD AQUI
                                    if (typeof window.reloadDashboardData === 'function') {
                                        await window.reloadDashboardData();
                                    }
                                    
                                    setTimeout(() => { 
                                        this.sucesso = false;
                                        this.modalOpen = false;
                                    }, 1500);
                                }
                            } catch (e) { 
                                window.swalTailwind.fire({
                                    icon: 'error',
                                    title: 'Erro',
                                    text: 'Falha na conexão com o servidor.'
                                });
                            }
                            this.enviando = false;
                        }
                    }">

                    {{-- ELEMENTOS DE FUNDO DO CARD --}}
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-purple-500/20 rounded-full blur-2xl"></div>

                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/20 backdrop-blur-md text-xs font-semibold mb-3 border border-white/10">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ date('d/m/y -') }} {{ \Carbon\Carbon::now()->locale('pt_BR')->dayName }}
                                </div>
                                <h2 class="text-2xl sm:text-3xl font-bold leading-tight">Diário de Classe</h2>
                            </div>
                            <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-md border border-white/10 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <button @click="abrirModal()" class="mt-8 w-full sm:w-auto bg-white text-blue-600 hover:bg-blue-50 font-bold py-4 px-8 rounded-xl shadow-xl transition-transform active:scale-95 flex items-center justify-center gap-2 group">
                            <span>Registrar Presença</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </button>
                        <button @click="modalEvento = true" class="mt-3 w-full sm:w-auto bg-white/20 text-white hover:bg-white/30 font-semibold py-3 px-6 rounded-xl transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Marcar Dia Livre
                        </button>

                        <a href="{{ route('eventos.index') }}" class="mt-2 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white/90 hover:text-white underline underline-offset-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Gerenciar dias livres
                        </a>
                    </div>

                    {{-- MODAL DE CHAMADA --}}
                    <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
                        <div class="absolute inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm" @click="modalOpen = false"></div>

                        <div class="relative w-full max-w-md rounded-3xl bg-white dark:bg-[#0B1220] shadow-2xl shadow-black/20 dark:shadow-black/60 border border-gray-100 dark:border-white/10 overflow-hidden flex flex-col max-h-[90vh]">
                            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-white/50 dark:bg-gray-900/50 backdrop-blur-xl z-10">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Diário de Classe</h3>
                                <button @click="modalOpen = false" class="p-2 -mr-2 text-gray-400 hover:text-gray-600 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg></button>
                            </div>

                            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Selecione a Data</label>
                                    <input 
                                        type="date" 
                                        x-model="dataSelecionada" 
                                        @change="validarDataEBuscar()" 
                                        min="{{ date('Y') }}-01-01" 
                                        max="{{ date('Y-m-d') }}" 
                                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0F172A] text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                                    >
                                </div>

                                <div x-show="diaLivre" class="flex items-center gap-2 px-4 py-3 rounded-xl bg-red-50 text-red-700 border border-red-200 text-sm font-semibold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Dia livre: <strong x-text="diaLivre"></strong></span>
                                </div>

                                <div class="min-h-[200px]">
                                    <div x-show="loading" class="flex flex-col items-center justify-center h-40 text-gray-400">
                                        <svg class="animate-spin h-8 w-8 text-blue-500 mb-3" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Sincronizando grade...</span>
                                    </div>
                                    <div x-show="sucesso" class="flex flex-col items-center justify-center h-40 text-emerald-500">
                                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-3">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <span class="font-bold text-lg">Salvo com Sucesso!</span>
                                    </div>
                                    <div x-show="!loading && !sucesso">
                                        <div x-show="aulas.length === 0 && !loading" class="text-center py-10 text-gray-500 dark:text-gray-400 text-sm">
                                            <p x-show="diaLivre">Dia livre — Nenhuma aula neste dia 🎉</p>
                                            <p x-show="!diaLivre">Nenhuma aula nesta grade horária.</p>
                                        </div>
                                        <div class="space-y-3">
                                            <template x-for="(aula, index) in aulas" :key="index">
                                                <div class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <h4 class="font-bold text-gray-800 dark:text-gray-100" x-text="aula.nome"></h4>
                                                            <span x-show="aula.ja_registrado" class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">Editando</span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1 font-mono" x-text="aula.horario.substring(0,5)"></p>
                                                    </div>
                                                    <button @click="aula.presente = !aula.presente" class="w-12 h-12 rounded-xl flex items-center justify-center transition-all active:scale-90 shadow-sm border" :class="aula.presente ? 'bg-emerald-50 border-emerald-200 text-emerald-600' : 'bg-red-50 border-red-200 text-red-600'">
                                                        <span class="font-bold text-lg" x-text="aula.presente ? 'P' : 'F'"></span>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-black/20">
                                <button x-show="aulas.length > 0 && !loading && !sucesso" @click="confirmarChamada()" :disabled="enviando" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-500/30 active:scale-[0.98] transition-all disabled:opacity-50"><span x-show="!enviando">Confirmar Chamada</span><span x-show="enviando">Salvando...</span></button>
                                <button x-show="aulas.length === 0 || sucesso" @click="modalOpen = false" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/10 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold py-4 rounded-xl shadow-sm">Fechar</button>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL DE EVENTO --}}
                    <div x-show="modalEvento" style="display: none" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
                        <div class="absolute inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm" @click="modalEvento = false"></div>

                        <div class="relative w-full max-w-md rounded-3xl p-6 bg-white dark:bg-[#0B1220] border border-gray-100 dark:border-white/10 shadow-2xl shadow-black/20 dark:shadow-black/60">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Marcar Dia Livre</h3>
                            <form method="POST" action="{{ route('eventos.store') }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Título</label>
                                    <input type="text" name="titulo" required placeholder="Ex: Feriado, Recesso, Falta Justificada" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0F172A] text-gray-900 dark:text-white py-3 px-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Data</label>
                                    <input type="date" name="data" required class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0F172A] text-gray-900 dark:text-white py-3 px-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Tipo</label>
                                    <select name="tipo" required class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0F172A] text-gray-900 dark:text-white py-3 px-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                        <option value="sem_aula">Sem Aula</option>
                                        <option value="feriado">Feriado Municipal</option>
                                    </select>
                                </div>
                                <div class="flex gap-3 pt-2">
                                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-600/30 active:scale-[0.98] transition">Salvar</button>
                                    <button type="button" @click="modalEvento = false" class="flex-1 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 font-bold py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-white/10 transition">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- WRAPPER PARA ATUALIZAR STATUS VIA AJAX --}}
                <div id="tour-status" class="grid grid-cols-2 lg:grid-cols-1 gap-4 lg:gap-6">
                    <div id="dashboard-stats" class="contents">
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
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-emerald-500 mt-2 font-medium">Tudo sob controle.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- WRAPPER PARA ATUALIZAR O CONTEÚDO PRINCIPAL VIA AJAX --}}
            <div id="dashboard-content">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 px-1">Filtros</h3>
                    {{-- BARRA DE FILTROS --}}
                    <div class="flex gap-3 mb-6 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-hide sm:mx-0 sm:px-0">

                        {{-- 1. TODAS --}}
                        <a href="javascript:void(0)" onclick="aplicarFiltro('')"
                            class="shrink-0 px-5 py-2.5 rounded-full text-sm font-bold transition shadow-sm border
                                {{ !request('filtro') 
                                ? 'bg-blue-600 text-white border-blue-600' 
                                : 'bg-white text-gray-600 border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            Todas
                        </a>

                        {{-- 2. HOJE --}}
                        <a href="javascript:void(0)" onclick="aplicarFiltro('hoje')"
                            class="shrink-0 px-5 py-2.5 rounded-full text-sm font-bold transition shadow-sm border flex items-center gap-2
                                {{ request('filtro') == 'hoje' 
                                ? 'bg-blue-600 text-white border-blue-600' 
                                : 'bg-white text-gray-600 border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <span>📅</span> Hoje
                        </a>

                        {{-- 3. EM RISCO --}}
                        <a href="javascript:void(0)" onclick="aplicarFiltro('risco')"
                            class="shrink-0 px-5 py-2.5 rounded-full text-sm font-bold transition shadow-sm border flex items-center gap-2
                                {{ request('filtro') == 'risco' 
                                ? 'bg-red-500 text-white border-red-500' 
                                : 'bg-white text-gray-600 border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <span>⚠️</span> Em Risco
                        </a>
                    </div>

                    {{-- FEEDBACK DO FILTRO --}}
                    @if(request('filtro') == 'hoje')
                    <div class="mb-4 flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 font-bold bg-blue-50 dark:bg-blue-900/20 px-3 py-2 rounded-lg inline-flex">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mostrando apenas aulas desta {{ now()->translatedFormat('l') }}
                    </div>
                    @elseif(request('filtro') == 'risco')
                    <div class="mb-4 flex items-center gap-2 text-sm text-red-600 dark:text-red-400 font-bold bg-red-50 dark:bg-red-900/20 px-3 py-2 rounded-lg inline-flex">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Matérias com frequência abaixo ou igual a 75%
                    </div>
                    @endif

                    <div>
                        <div class="flex items-center justify-between px-1 mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Minhas Matérias</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                            <a href="{{ route('disciplinas.criar') }}"
                                id="tour-nova-materia"
                                class="hidden md:flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-3xl hover:border-blue-500 dark:hover:border-blue-500 hover:bg-blue-50/50 dark:hover:bg-blue-900/10 transition-all duration-300 group min-h-[200px]">
                                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-sm">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                                <h4 class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Nova Matéria</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-1">Adicionar à grade</p>
                            </a>

                            @forelse($disciplinasFiltradas as $disciplina)
                            @php
                            // Lógica centralizada: Pergunta ao Model a taxa correta
                            $porcentagem = $disciplina->taxa_presenca;

                            // Dados auxiliares para exibir no HTML
                            $totalFaltas = $disciplina->frequencias->where('presente', false)->count();
                            $totalPrevisto = $disciplina->total_aulas_previstas;
                            $limiteFaltas = floor($totalPrevisto * 0.25);
                            $restantes = $limiteFaltas - $totalFaltas;

                            // Definição visual (Cores)
                            $corBarra = 'bg-emerald-500';
                            $corTexto = 'text-emerald-600 dark:text-emerald-400';
                            
                            if($porcentagem < 75) {
                                $corBarra='bg-red-500';
                                $corTexto='text-red-600 dark:text-red-400';
                            } elseif($porcentagem < 85) {
                                $corBarra='bg-yellow-500';
                                $corTexto='text-yellow-600 dark:text-yellow-400';
                            }
                            @endphp

                                <div class="group bg-white/70 dark:bg-gray-900/70 backdrop-blur-md rounded-3xl border border-white/20 dark:border-gray-800 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
                                <div class="h-2 w-full absolute top-0 left-0" style="background-color: {{ $disciplina->cor ?? '#3B82F6' }}"></div>

                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-6">
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white truncate pr-4 leading-tight">
                                            {{ $disciplina->nome }}
                                        </h4>

                                        {{-- EXIBIÇÃO DE FALTAS E PROJEÇÃO --}}
                                        <div class="flex flex-col items-end">
                                            {{-- Número Grande das Faltas --}}
                                            <span class="text-3xl font-extrabold text-gray-900 dark:text-white leading-none tracking-tighter">
                                                {{ $totalFaltas }}

                                                {{-- Mostra o Limite (ex: "/ 20") se houver previsão --}}
                                                @if($totalPrevisto > 0)
                                                <span class="text-sm text-gray-400 font-normal" title="Limite de faltas (25% de {{ $totalPrevisto }} aulas)">
                                                    /{{ $limiteFaltas }}
                                                </span>
                                                @endif
                                            </span>

                                            {{-- Texto Pequeno abaixo do número --}}
                                            @if($totalPrevisto > 0)
                                            {{-- Mostra quantas faltas restam --}}
                                            <span class="text-[10px] font-bold {{ $restantes < 3 ? 'text-red-500 animate-pulse' : 'text-gray-400' }} uppercase tracking-wide">
                                                {{ $restantes >= 0 ? 'Faltas restantes: ' . $restantes : 'Reprovado' }}
                                            </span>
                                            @else
                                            {{-- Fallback: Se não tiver datas cadastradas, mostra só "Faltas" --}}
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Faltas</span>
                                            @endif
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
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Grade
                                    </a>

                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('disciplinas.edit', $disciplina->id) }}" class="p-2 text-gray-400 hover:text-blue-500 hover:bg-white dark:hover:bg-gray-800 rounded-lg transition" title="Editar Matéria">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </a>

                                        <button type="button" onclick="deleteDisciplina({{ $disciplina->id }})" class="p-2 text-gray-400 hover:text-red-500 hover:bg-white dark:hover:bg-gray-800 rounded-lg transition" title="Excluir Matéria">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
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
        </div>
    </div>

    @if(Auth::user()->has_seen_intro && !Auth::user()->has_completed_tour)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const driver = window.driver.js.driver;
            const isMobile = window.innerWidth < 1024;

            let tourSteps = [{
                    element: '#tour-chamada',
                    popover: {
                        title: 'Diário de Classe',
                        description: 'Registre sua presença do dia com um clique. Ou, marque um dia livre.'
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
                tourSteps.push({
                    element: '#tour-add-mobile',
                    popover: {
                        title: 'Adicione uma Matéria',
                        description: 'Toque no botão central para adicionar suas disciplinas.'
                    }
                }, {
                    element: '#tour-grade-mobile',
                    popover: {
                        title: 'Sua Grade',
                        description: 'Veja seus horários nesta aba.'
                    }
                }, {
                    element: '#tour-profile-mobile',
                    popover: {
                        title: 'Seu Perfil',
                        description: 'Gerencie sua conta e outras configurações aqui.'
                    }
                });
            } else {
                tourSteps.push({
                    element: '#tour-nova-materia',
                    popover: {
                        title: 'Adicione uma Matéria',
                        description: 'Comece clicando aqui para cadastrar disciplinas.'
                    }
                }, {
                    element: '#tour-grade-desktop',
                    popover: {
                        title: 'Grade Horária',
                        description: 'Acesse a visão completa da sua semana.'
                    }
                });
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

        {{-- SCRIPT PARA FUNCIONALIDADES AJAX (FILTROS E DELETE) --}}
    <script>
        // Função principal para recarregar partes da dashboard
        async function reloadDashboardData(targetUrl = null) {
            // Se não passar URL, usa a atual do navegador
            let url = targetUrl || window.location.href;
            
            const contentDiv = document.getElementById('dashboard-content');
            if (contentDiv) contentDiv.style.opacity = '0.6'; // Feedback visual de "carregando"

            try {
                // 1. TRUQUE DO CACHE: Adiciona um timestamp na URL
                // Isso engana o navegador e obriga ele a buscar os dados novos no servidor
                const fetchUrl = new URL(url, window.location.origin);
                fetchUrl.searchParams.set('t', new Date().getTime()); 

                // 2. Requisição com headers explícitos para não salvar cache
                const response = await fetch(fetchUrl, {
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Pragma': 'no-cache',
                        'Cache-Control': 'no-cache, no-store, must-revalidate'
                    }
                });

                if (!response.ok) throw new Error('Falha de conexão');

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 3. Atualiza os Cards de Status
                const newStats = doc.getElementById('dashboard-stats');
                const currentStats = document.getElementById('dashboard-stats');
                if (newStats && currentStats) {
                    currentStats.innerHTML = newStats.innerHTML;

                    //Reinicializa o Alpine nos Status
                    if (window.Alpine) {
                        window.Alpine.initTree(currentStats);
                    }
                }

                // 4. Atualiza a Grade e os Filtros
                const newContent = doc.getElementById('dashboard-content');
                if (newContent && contentDiv) {
                    contentDiv.innerHTML = newContent.innerHTML;

                    if (window.Alpine) {
                        window.Alpine.initTree(contentDiv);
                    }
                }

                // Atualiza a URL na barra de endereços (sem o timestamp feio)
                if(targetUrl) {
                    window.history.pushState({}, '', targetUrl);
                }

            } catch (error) {
                console.error('Erro ao atualizar dashboard:', error);
            } finally {
                if (contentDiv) contentDiv.style.opacity = '1';
            }
        }

        // Função para os Filtros
        function aplicarFiltro(tipo) {
            let url = new URL('{{ route('dashboard') }}');
            if (tipo) {
                url.searchParams.set('filtro', tipo);
            }
            reloadDashboardData(url.toString());
        }

        // Função para Excluir Disciplina com SweetAlert
        async function deleteDisciplina(id) {
            // 1. Confirmação (Estilizada ou Nativa)
            if (window.swalTailwind) {
                const result = await window.swalTailwind.fire({
                    title: 'Tem certeza?',
                    text: "Todas as faltas dessa matéria serão apagadas.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir',
                    confirmButtonColor: '#ef4444',
                    cancelButtonText: 'Cancelar'
                });
                if (!result.isConfirmed) return;
            } else if (!confirm('Tem certeza?')) {
                return;
            }

            try {
                // 2. Requisição AJAX pedindo JSON (Accept: application/json)
                const response = await fetch(`/disciplinas/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json' // Importante para o controller saber que queremos JSON
                    }
                });

                // 3. Verifica sucesso
                if (response.ok) {
                    // Atualiza a tela (reloadDashboardData ainda é necessário para redesenhar a grade)
                    await reloadDashboardData(); 
                    
                    if (window.toastSuccess) {
                        window.toastSuccess('Matéria removida com sucesso!');
                    }
                } else {
                    alert('Erro ao excluir.');
                }
            } catch (error) {
                console.error('Erro:', error);
            }
        }
    </script>
</x-app-layout>