<x-app-layout>
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
    </div>

    <div class="py-6 sm:py-10 pb-24 md:pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- CABEÇALHO --}}
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
                <div class="flex-1">
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
                        {{ $saudacao }}, {{ explode(' ', Auth::user()->name)[0] }} 👋
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm sm:text-base">
                        {{ $fraseMotivacional }}
                    </p>
                </div>
            </div>

        <div id="gamification-area" class="contents">


            {{-- 1. POP-UP DA OFENSIVA (FOGO) --}}
            @if($streak == 1 && $marcouHoje)
                <div x-data="{ 
                        show: false,
                        init() {
                            const key = 'fire_shown_{{ $dateString }}';
                            if (!localStorage.getItem(key)) {
                                // Pequeno delay inicial para não brigar com outros elementos
                                setTimeout(() => {
                                    this.show = true;
                                    setTimeout(() => {
                                        this.show = false;
                                        localStorage.setItem(key, 'true');
                                    }, 1500);
                                }, 500);
                            }
                        }
                     }"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 scale-50"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-110"
                     class="fixed inset-0 z-[100] flex items-center justify-center pointer-events-none"
                     style="display: none;">
                    
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-md"></div>

                    <div class="relative z-10 flex flex-col items-center justify-center scale-150">
                        <div class="absolute w-full h-full bg-orange-500/40 rounded-full blur-[80px] animate-pulse"></div>
                        <div class="relative drop-shadow-[0_0_50px_rgba(255,100,0,1)]">
                             <svg class="w-40 h-40 text-orange-500 animate-bounce" style="animation-duration: 0.8s;" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12.75 2.255C10.636 3.69 9.098 5.617 8.356 7.822c-.655 1.946.068 4.253 1.058 5.92.358.604.76 1.18 1.157 1.761.278.406.495.962.247 1.41-.33.593-1.127.674-1.685.295-.506-.343-.888-.868-1.157-1.424-.486-1.008-.66-2.148-.5-3.245.093-.634-.73-1.03-1.163-.585C4.244 14.12 4.2 18.23 6.55 20.85c2.19 2.443 6.037 3.018 8.87.973 2.508-1.81 3.554-5.286 2.053-7.974-.833-1.492-2.103-2.73-3.138-4.148-.485-.665-.705-1.52-.395-2.296.342-.857 1.084-1.516 1.458-2.355.197-.442-.315-.903-.748-.795z" />
                             </svg>
                        </div>
                        <div class="mt-4 text-center">
                            <h2 class="text-3xl font-black text-white italic tracking-tighter drop-shadow-xl uppercase animate-pulse">Chama Acesa!</h2>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 2. POP-UP DE MEDALHAS (LOOP COM DELAY) --}}
            @foreach($medalhasHoje as $index => $badge)
                <div x-data="{ 
                        show: false,
                        init() {
                            const key = 'badge_shown_{{ $badge->id }}_{{ $dateString }}';
                            
                            // Se ainda não mostrou hoje
                            if (!localStorage.getItem(key)) {
                                // Calcula o delay baseado na ordem (index).
                                // Se tiver a animação do FOGO (streak == 1), soma +2000ms para não encavalar.
                                let baseDelay = {{ ($streak == 1 && $marcouHoje) ? 2500 : 500 }};
                                let myDelay = baseDelay + ({{ $index }} * 2000); 

                                setTimeout(() => {
                                    this.show = true;
                                    setTimeout(() => {
                                        this.show = false;
                                        localStorage.setItem(key, 'true');
                                    }, 1500); // Duração de 1.5s
                                }, myDelay);
                            }
                        }
                     }"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 scale-50 rotate-180"
                     x-transition:enter-end="opacity-100 scale-100 rotate-0"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-110"
                     class="fixed inset-0 z-[101] flex items-center justify-center pointer-events-none"
                     style="display: none;">
                    
                    {{-- Fundo blur (acumulativo, mas ok pois elas aparecem em sequência) --}}
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-md"></div>

                    <div class="relative z-10 flex flex-col items-center justify-center scale-150 p-6 rounded-3xl">
                        {{-- Luz de fundo com a cor amarela/dourada --}}
                        <div class="absolute w-full h-full bg-yellow-500/30 rounded-full blur-[60px] animate-pulse"></div>

                        {{-- Ícone da Medalha --}}
                        <div class="relative text-6xl mb-4 drop-shadow-[0_0_30px_rgba(234,179,8,0.8)] animate-bounce" style="animation-duration: 1s;">
                            {{ $badge->icon }}
                        </div>

                        <div class="relative z-10 text-center">
                            <p class="text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-1">Nova Conquista!</p>
                            <h2 class="text-2xl font-black text-white tracking-tight drop-shadow-lg leading-none">
                                {{ $badge->name }}
                            </h2>
                        </div>
                        
                        {{-- Confetes simples CSS --}}
                        <div class="absolute -top-10 -left-10 w-3 h-3 bg-blue-400 rotate-12 animate-ping"></div>
                        <div class="absolute top-10 -right-12 w-2 h-2 bg-red-400 -rotate-12 animate-ping" style="animation-delay: 0.2s"></div>
                        <div class="absolute -bottom-5 left-10 w-2 h-2 bg-green-400 rotate-45 animate-ping" style="animation-delay: 0.4s"></div>
                    </div>
                </div>
            @endforeach

            {{-- 3. CARDS DO PAINEL (Lógica Padrão) --}}
            @if($streak > 0 || $badgesCount > 0)
                <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    {{-- Card Ofensiva --}}
                    @php
                        if ($streak == 0) {
                            $cardClasses = "bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm opacity-75";
                            $textClasses = "text-gray-500 dark:text-gray-400";
                            $numClasses = "text-gray-400 dark:text-gray-500";
                            $iconColor = "text-gray-300 dark:text-gray-600";
                            $msg = "Reinicie a chama!";
                            $fireOpacity = "opacity-0";
                        } elseif (!$marcouHoje) {
                            $cardClasses = "bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-transparent shadow-inner";
                            $textClasses = "text-gray-600 dark:text-gray-200";
                            $numClasses = "text-gray-800 dark:text-white opacity-90";
                            $iconColor = "text-gray-400 dark:text-gray-300";
                            $msg = "Salve a ofensiva!";
                            $fireOpacity = "opacity-0";
                        } else {
                            $cardClasses = "bg-gradient-to-br from-orange-500 to-red-600 shadow-lg shadow-orange-500/20";
                            $textClasses = "text-orange-100";
                            $numClasses = "text-white drop-shadow-md";
                            $iconColor = "text-white";
                            $msg = "Em chamas!";
                            $fireOpacity = "opacity-100";
                        }
                    @endphp

                    <div class="relative overflow-hidden rounded-3xl p-4 group flex items-center justify-between h-24 sm:h-28 transition-all duration-300 hover:scale-[1.01] {{ $cardClasses }}">
                        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/20 blur-2xl transition-opacity duration-500 {{ $fireOpacity }}"></div>
                        
                        <div class="relative z-10 flex flex-col justify-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider opacity-80 {{ $textClasses }}">Ofensiva</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-black {{ $numClasses }}">{{ $streak }}</span>
                                <span class="text-sm font-bold opacity-80 {{ $textClasses }}">dias</span>
                            </div>
                            <p class="text-[10px] font-medium mt-0.5 opacity-90 truncate {{ $textClasses }}">{{ $msg }}</p>
                        </div>

                        <div class="relative z-10 mr-2 drop-shadow-lg transform group-hover:scale-110 transition-transform duration-300 {{ $iconColor }}">
                            @if($streak > 0 && !$marcouHoje)
                                <div class="animate-pulse">
                                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                </div>
                            @elseif($streak == 0)
                                    <svg class="w-12 h-12 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            @else
                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12.75 2.255C10.636 3.69 9.098 5.617 8.356 7.822c-.655 1.946.068 4.253 1.058 5.92.358.604.76 1.18 1.157 1.761.278.406.495.962.247 1.41-.33.593-1.127.674-1.685.295-.506-.343-.888-.868-1.157-1.424-.486-1.008-.66-2.148-.5-3.245.093-.634-.73-1.03-1.163-.585C4.244 14.12 4.2 18.23 6.55 20.85c2.19 2.443 6.037 3.018 8.87.973 2.508-1.81 3.554-5.286 2.053-7.974-.833-1.492-2.103-2.73-3.138-4.148-.485-.665-.705-1.52-.395-2.296.342-.857 1.084-1.516 1.458-2.355.197-.442-.315-.903-.748-.795z" /></svg>
                            @endif
                        </div>
                    </div>

                    {{-- Card Medalhas --}}
                    <div x-data 
                            @click="$dispatch('open-modal', 'badges-gallery')"
                            class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-md rounded-3xl border border-white/20 dark:border-gray-800 p-4 shadow-sm flex flex-col justify-center h-24 sm:h-28 transition-transform hover:scale-[1.01] cursor-pointer group hover:bg-white/80 dark:hover:bg-gray-800/80 overflow-hidden relative">
                        
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Conquistas</p>
                            <span class="bg-gray-100 dark:bg-gray-800 text-[10px] font-bold px-1.5 py-0.5 rounded text-gray-500 group-hover:bg-yellow-100 group-hover:text-yellow-700 transition-colors">
                                {{ $badgesCount }}
                            </span>
                        </div>

                        <div class="flex gap-2 items-center">
                            @forelse(Auth::user()->badges->sortByDesc('pivot.earned_at')->take(3) as $badge)
                                <div class="shrink-0 w-10 h-10 bg-gradient-to-b from-yellow-100 to-amber-50 dark:from-yellow-900/30 dark:to-amber-900/10 rounded-xl border border-yellow-200 dark:border-yellow-800/50 flex items-center justify-center text-xl shadow-sm relative group-hover:-translate-y-0.5 transition-transform duration-300">
                                    {{ $badge->icon }}
                                </div>
                            @empty
                                <div class="flex items-center gap-2 text-gray-400 opacity-60">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center grayscale">🏆</div>
                                    <span class="text-[10px] font-medium leading-tight">Sem medalhas<br>ainda...</span>
                                </div>
                            @endforelse

                            @if($badgesCount > 3)
                                <div class="shrink-0 w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center text-xs font-bold text-gray-500 border border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    +{{ $badgesCount - 3 }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Galeria --}}
                    <x-modal name="badges-gallery" focusable>
                        <div class="bg-white dark:bg-gray-900 flex flex-col max-h-[85vh]">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 sticky top-0 z-10 flex justify-between items-center">
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">🏆 Sala de Troféus</h2>
                                    <p class="text-xs text-gray-500">{{ $badgesCount }} desbloqueadas</p>
                                </div>
                                <button x-on:click.stop="$dispatch('close-modal', 'badges-gallery')" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full text-gray-500">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <div class="p-4 overflow-y-auto bg-gray-50 dark:bg-gray-900/50">
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach(Auth::user()->badges->sortByDesc('pivot.earned_at') as $badge)
                                        <div class="flex flex-col items-center p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm text-center">
                                            <div class="text-4xl mb-2 drop-shadow-md">{{ $badge->icon }}</div>
                                            <h3 class="font-bold text-gray-800 dark:text-white text-xs leading-tight mb-1">{{ $badge->name }}</h3>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 line-clamp-2 h-7 mb-2">{{ $badge->description }}</p>
                                            <span class="text-[9px] font-bold text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-md">{{ \Carbon\Carbon::parse($badge->pivot->earned_at)->format('d/m/y') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </x-modal>

                </div>
            @endif
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
                                    chamada: this.aulas.map(a => ({ disciplina_id: a.disciplina_id, presente: a.presente, horario: a.horario }))
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
                        {{-- Header do Card --}}
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-semibold mb-2 border border-white/10 shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ date('d/m') }} <span class="opacity-60">|</span> {{ \Carbon\Carbon::now()->locale('pt_BR')->dayName }}
                                </div>
                                <h2 class="text-2xl sm:text-3xl font-bold leading-tight tracking-tight">Diário de Classe</h2>
                            </div>
                            <div class="bg-white/20 p-2.5 rounded-2xl backdrop-blur-md border border-white/10 shadow-lg hidden sm:block">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- Actions Grid --}}
                        <div class="space-y-3">
                            
                            {{-- PRIMARY ACTION: Main Driver --}}
                            <button @click="abrirModal()" 
                                class="group relative w-full bg-white text-indigo-700 hover:bg-blue-50 font-bold py-4 px-6 rounded-2xl shadow-xl shadow-indigo-900/10 transition-all active:scale-[0.98] flex items-center justify-between border-b-4 border-black/5 hover:border-black/10">
                                <div class="flex flex-col items-start">
                                    @if($temAulaHoje)
                                        <span class="text-lg leading-none">Registrar Presença</span>
                                        <span class="text-xs font-medium text-indigo-500/80 mt-1 uppercase tracking-wide">Aulas de Hoje</span>
                                    @else
                                        <span class="text-lg leading-none">Gerenciar Frequência</span>
                                        <span class="text-xs font-medium text-indigo-500/80 mt-1 uppercase tracking-wide">Histórico & Ajustes</span>
                                    @endif
                                </div>
                                <div class="bg-indigo-100/50 p-2 rounded-xl group-hover:bg-indigo-100 transition-colors">
                                    <svg class="w-6 h-6 transform group-hover:rotate-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                            </button>

                            {{-- SECONDARY ACTIONS: Grid --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                
                                {{-- AI ADVISOR --}}
                                <button @click="consultarIaDia(dataSelecionada)" 
                                        class="relative overflow-hidden w-full bg-purple-600/30 hover:bg-purple-600/40 border border-white/20 text-white font-semibold py-3.5 px-4 rounded-xl transition active:scale-[0.98] flex items-center justify-center gap-2 group backdrop-blur-sm">
                                    
                                    {{-- Glow Effect --}}
                                    <div class="absolute inset-0 bg-gradient-to-tr from-purple-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    
                                    <svg class="w-5 h-5 text-purple-200 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                    <div class="flex flex-col items-start leading-none relative z-10">
                                        <span class="text-sm">Consultar Oráculo</span>
                                        <span class="text-[10px] text-purple-200/80 font-medium">5 créditos</span>
                                    </div>
                                </button>

                                {{-- MARCAR FOLGA --}}
                                <button @click="modalEvento = true" class="w-full bg-white/10 hover:bg-white/20 border border-white/10 text-white font-medium py-3.5 px-4 rounded-xl transition active:scale-[0.98] flex items-center justify-center gap-2 backdrop-blur-sm">
                                    <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm">Marcar Folga</span>
                                </button>
                            </div>

                            {{-- TERTIARY LINK --}}
                            <div class="pt-1 text-center">
                                <a href="{{ route('eventos.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-white/70 hover:text-white transition-colors py-1">
                                    <span>Gerenciar dias sem aula</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
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
                                                        <p class="text-xs text-gray-500 mt-1 font-mono">
                                                            <span x-text="aula.horario.substring(0,5)"></span> - 
                                                            <span x-text="aula.horario_fim.substring(0,5)"></span>
                                                        </p>
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
                        
                        {{-- CARD 1: PRESENÇA TOTAL --}}
                        <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2rem] border border-white/20 dark:border-gray-800 p-6 flex flex-col justify-center shadow-sm">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Presença total acumulada</p>
                            
                            @if($estadoVazio)
                                {{-- ESTADO VAZIO (Neutro) --}}
                                <div class="flex items-center gap-3 opacity-60">
                                    <h3 class="text-4xl font-extrabold text-gray-300 dark:text-gray-600">-</h3>
                                    <span class="text-xs font-medium text-gray-400 leading-tight">Aguardando<br>primeiros dados</span>
                                </div>
                            @else
                                {{-- ESTADO ATIVO (Com Animação Inteligente e Bidirecional) --}}
                                <div class="flex items-baseline gap-1"
                                     x-data="{ 
                                        current: window.lastPercentage ?? 0, 
                                        target: {{ $porcentagemGlobal }} 
                                     }"
                                     x-init="
                                         // Delay para garantir que o DOM renderizou
                                         setTimeout(() => {
                                             const duration = 1500; 
                                             const startValue = current;
                                             const endValue = target;
                                             const change = endValue - startValue; // Calcula a diferença (pode ser negativa)
                                             let start = null;
                                             
                                             // Função de Easing (Suave)
                                             const easeOutQuart = (x) => 1 - Math.pow(1 - x, 4);

                                             const step = (timestamp) => {
                                                 if (!start) start = timestamp;
                                                 const progress = Math.min((timestamp - start) / duration, 1);
                                                 
                                                 // A mágica: soma a diferença (positiva ou negativa) ao valor inicial
                                                 current = Math.floor(startValue + (change * easeOutQuart(progress)));

                                                 if (progress < 1) {
                                                     window.requestAnimationFrame(step);
                                                 } else {
                                                     // Garante que termina no número exato
                                                     current = endValue;
                                                     // SALVA NA MEMÓRIA GLOBAL para a próxima vez
                                                     window.lastPercentage = endValue;
                                                 }
                                             };
                                             
                                             // Só anima se houver mudança
                                             if (startValue !== endValue) {
                                                 window.requestAnimationFrame(step);
                                             } else {
                                                 window.lastPercentage = endValue;
                                             }
                                         }, 200);
                                     ">
                                    <h3 class="text-4xl font-extrabold {{ $corGlobal }}" x-text="current">
                                        {{-- Fallback visual enquanto o Alpine carrega --}}
                                        {{ $porcentagemGlobal }}
                                    </h3>
                                    <span class="text-lg font-medium text-gray-400">%</span>
                                </div>
                            @endif
                        </div>

                        {{-- CARD 2: MATÉRIAS EM RISCO --}}
                        <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2rem] border border-white/20 dark:border-gray-800 p-6 flex flex-col justify-center shadow-sm">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Matérias Em Risco</p>
                            
                            @if($estadoVazio)
                                {{-- ESTADO VAZIO (Neutro) --}}
                                <div class="flex items-center gap-3 opacity-60">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-300 dark:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    </div>
                                    <span class="text-xs font-medium text-gray-400 leading-tight">Sem dados<br>suficientes</span>
                                </div>
                            @elseif($materiasEmRisco > 0)
                                {{-- ESTADO DE ALERTA (Vermelho) --}}
                                <div class="flex items-center gap-3">
                                    <h3 class="text-4xl font-extrabold text-red-500">{{ $materiasEmRisco }}</h3>
                                    <span class="px-2 py-1 rounded-md bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold uppercase">Matérias</span>
                                </div>
                                <p class="text-xs text-red-500 mt-2 font-medium">Atenção necessária!</p>
                            @else
                                {{-- ESTADO DE SUCESSO (Verde) --}}
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
                        Mostrando apenas matérias com frequência abaixo ou igual a 75%
                    </div>
                    @endif

                    <div>
                        <div class="flex items-center justify-between px-1 mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Minhas Matérias</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            {{-- BOTÃO DE ADICIONAR (Só aparece se já existirem matérias) --}}
                            @if($disciplinasFiltradas->isNotEmpty())
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
                            @endif

                            @forelse($disciplinasFiltradas as $disciplina)
                            @php
                                // Dados básicos

                                // 1. Usa os atributos injetados pelo Controller (Zero Queries)
                                $totalRegistros = $disciplina->total_aulas_realizadas; 
                                
                                // O controller já calculou e setou isso via setAttribute
                                $porcentagem = $disciplina->taxa_presenca; 
                                
                                // Usa o count condicional que fizemos no controller
                                $totalFaltas = $disciplina->total_faltas; 
                                
                                // Usa o valor calculado no controller, não o Accessor do Model
                                $totalPrevisto = $disciplina->total_aulas_previstas_cache ?? 0;
                                $limiteFaltas = floor($totalPrevisto * 0.25);
                                $restantes = $limiteFaltas - $totalFaltas;

                                // 2. Lógica de Cores (UX)
                                if ($totalRegistros === 0) {
                                    // ESTADO NEUTRO: Nenhuma aula registrada ainda
                                    $corBarra = 'bg-gray-300 dark:bg-gray-600'; // Cinza
                                    $corTexto = 'text-gray-500 dark:text-gray-400';
                                    $textoPorcentagem = '--'; // Ou 'N/A'
                                    $larguraBarra = '0'; // Barra vazia
                                } else {
                                    // ESTADO ATIVO: Já tem aulas
                                    $textoPorcentagem = $porcentagem . '';
                                    $larguraBarra = $porcentagem . '';

                                    $corBarra = 'bg-emerald-500';
                                    $corTexto = 'text-emerald-600 dark:text-emerald-400';
                                    
                                    if($porcentagem < 75) {
                                        $corBarra = 'bg-red-500';
                                        $corTexto = 'text-red-600 dark:text-red-400';
                                    } elseif($porcentagem < 85) {
                                        $corBarra = 'bg-yellow-500';
                                        $corTexto = 'text-yellow-600 dark:text-yellow-400';
                                    }
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
                                            <span class="text-sm font-bold {{ $corTexto }}">{{ $textoPorcentagem }}%</span>
                                        </div>
                                        {{-- Barra de Progresso Inteligente --}}
                                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2.5 overflow-hidden"
                                            x-data="{ 
                                                id: {{ $disciplina->id }},
                                                width: window.memoriaBarras[{{ $disciplina->id }}] ?? '{{ $larguraBarra }}%' 
                                            }"
                                            x-init="
                                                // Espera um tick para o navegador renderizar o valor inicial
                                                $nextTick(() => { 
                                                    // Define a nova largura alvo
                                                    width = '{{ $larguraBarra }}%'; 
                                                    // Salva na memória global para o próximo reload
                                                    window.memoriaBarras[id] = width;
                                                })
                                            ">
                                            
                                            <div class="h-2.5 rounded-full transition-all duration-1000 ease-in-out {{ $corBarra }}"
                                                :style="'width: ' + width">
                                            </div>
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

                                        <button type="button" onclick="consultarIa({{ $disciplina->id }}, '{{ addslashes($disciplina->nome) }}')" 
                                                class="p-2 text-yellow-600 hover:bg-yellow-100 dark:text-yellow-400 dark:hover:bg-yellow-900/30 rounded-lg transition group relative" 
                                                title="Oráculo">
                                                <svg class="w-5 h-5 animate-pulse group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                                                </svg>
                                        </button>

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
                                <div class="col-span-full flex flex-col items-center justify-center py-12 px-4 text-center animate-fade-in-up">
                                    
                                    @if(request('filtro') == 'hoje')
                                        {{-- CENÁRIO 1: FILTRO 'HOJE' VAZIO (Aparece PC e Mobile) --}}
                                        <div class="relative mb-6">
                                            <div class="absolute inset-0 bg-orange-400/20 rounded-full blur-xl"></div>
                                            <div class="relative w-24 h-24 bg-orange-50 dark:bg-orange-900/20 rounded-full flex items-center justify-center border border-orange-100 dark:border-orange-800">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nada por hoje</h3>
                                        <p class="text-gray-500 dark:text-gray-400 max-w-xs mx-auto mb-6 text-sm">
                                            Nenhuma aula agendada para este dia. Aproveite o descanso! 😌
                                        </p>
                                        <button onclick="aplicarFiltro('')" class="text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                            Ver todas as matérias
                                        </button>

                                    @elseif(request('filtro') == 'risco')
                                        {{-- CENÁRIO 2: FILTRO 'RISCO' VAZIO (Aparece PC e Mobile) --}}
                                        <div class="relative mb-6">
                                            <div class="absolute inset-0 bg-emerald-400/20 rounded-full blur-xl"></div>
                                            <div class="relative w-24 h-24 bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center border border-emerald-100 dark:border-emerald-800">
                                                <svg class="w-10 h-10 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tudo Seguro!</h3>
                                        <p class="text-gray-500 dark:text-gray-400 max-w-xs mx-auto mb-6 text-sm">
                                            Parabéns! Nenhuma matéria está com frequência baixa no momento. 🏆
                                        </p>
                                        <button onclick="aplicarFiltro('')" class="text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                            Ver todas as matérias
                                        </button>

                                    @else
                                        {{-- CENÁRIO 3: ONBOARDING (Aparece SÓ no Mobile) --}}
                                        {{-- Adicionei a div 'md:hidden' envolvendo tudo aqui --}}
                                        <div class="md:hidden flex flex-col items-center">
                                            <div class="relative mb-6 group">
                                                <div class="absolute inset-0 bg-blue-400/20 rounded-full blur-xl"></div>
                                                <div class="relative w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center border border-blue-100 dark:border-blue-800">
                                                    <svg class="w-10 h-10 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                    </svg>
                                                </div>
                                            </div>

                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                                Nenhuma matéria encontrada
                                            </h3>
                                            <p class="text-gray-500 dark:text-gray-400 max-w-xs mx-auto mb-12 text-sm leading-relaxed">
                                                Sua grade está vazia. Adicione sua primeira matéria para começar.
                                            </p>

                                            <div class="flex flex-col items-center gap-2 animate-pulse opacity-80">
                                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide">
                                                    Toque no botão abaixo para adicionar
                                                </span>
                                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        {{-- 🖥️ VERSÃO DESKTOP (Nova - Só aparece em telas médias ou maiores) --}}
                                        <div class="hidden md:flex flex-col items-center max-w-2xl mx-auto">
                                            {{-- Ícone Hero Ilustrativo --}}
                                            <div class="relative mb-10 group cursor-default">
                                                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                                                <div class="relative w-32 h-32 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center border-4 border-blue-50 dark:border-blue-900/30 shadow-2xl">
                                                    <svg class="w-14 h-14 text-blue-600 dark:text-blue-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                </div>
                                            </div>

                                            <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-4 tracking-tight">
                                                Sua grade está vazia
                                            </h3>
                                            
                                            <p class="text-lg text-gray-500 dark:text-gray-400 mb-10 leading-relaxed max-w-lg">
                                                Para o Frequência Certa funcionar, precisamos saber quais matérias você cursa. Cadastre sua grade horária e deixe o resto com a gente.
                                            </p>

                                            {{-- Botão de Ação Principal (CTA) --}}
                                            <a href="{{ route('disciplinas.criar') }}" id= "tour-nova-materia" class="group relative inline-flex items-center justify-center gap-3 bg-blue-600 hover:bg-blue-700 text-white text-lg font-bold py-4 px-10 rounded-2xl shadow-xl shadow-blue-600/20 active:scale-[0.98] transition-all duration-200 transform hover:-translate-y-1">
                                                <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                <span>Adicionar Primeira Matéria</span>
                                                
                                                {{-- Efeito de brilho no botão --}}
                                                <div class="absolute inset-0 rounded-2xl ring-2 ring-white/20 group-hover:ring-white/40 transition-all"></div>
                                            </a>
                                        </div>

                                    @endif
                                    
                                </div>
                            @endforelse
                        </div>

    @if(Auth::user()->has_seen_intro && !Auth::user()->has_completed_tour)
        <script>
            { // <--- 1. ABRE ESCOPO: Isso impede o erro de "variável já declarada" ao voltar na página
                
                // 2. Função encapsulada para iniciar o tour
                const initDashboardTour = () => {
                    
                    // Verifica se a biblioteca driver.js carregou
                    if (!window.driver || !window.driver.js || !window.driver.js.driver) {
                        console.warn('Driver.js não encontrado');
                        return;
                    }

                    const driver = window.driver.js.driver;
                    const isMobile = window.innerWidth < 1024;

                    let tourSteps = [
                        {
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
                        tourSteps.push(
                            {
                                element: '#tour-grade-mobile',
                                popover: { title: 'Sua Grade', description: 'Acesse a visão completa da sua semana.' }
                            }, 
                            {
                                element: '#tour-profile-mobile',
                                popover: { title: 'Seu Perfil', description: 'Gerencie sua conta e outras configurações aqui.' }
                            }, 
                            {
                                element: '#tour-add-mobile',
                                popover: { title: 'Adicione uma Matéria', description: 'Toque no botão central para adicionar suas matérias.' }
                            }
                        );
                    } else {
                        tourSteps.push(
                            {
                                element: '#tour-grade-desktop',
                                popover: { title: 'Grade Horária', description: 'Acesse a visão completa da sua semana.' }
                            }, 
                            {
                                element: '#tour-nova-materia',
                                popover: { title: 'Adicione uma Matéria', description: 'Comece clicando aqui para cadastrar suas matérias.' }
                            }
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
                            }).catch(err => console.error(err));
                            
                            window.dispatchEvent(new CustomEvent('tour-finished'));
                            driverObj.destroy();
                        }
                    });

                    window.dispatchEvent(new CustomEvent('tour-starting'));

                    // Pequeno delay para garantir que a animação da página terminou antes de focar
                    setTimeout(() => driverObj.drive(), 1300);
                };

                // 3. EXECUTA IMEDIATAMENTE (Sem esperar DOMContentLoaded, pois o HTML já está lá)
                initDashboardTour();

            } // <--- FECHA ESCOPO
        </script>
    @endif

        {{-- SCRIPT PARA FUNCIONALIDADES AJAX (FILTROS E DELETE) --}}
    <script>
        // Função principal para recarregar partes da dashboard
        async function reloadDashboardData(targetUrl = null) {
            // Se não passar URL, usa a atual do navegador
            let url = targetUrl || window.location.href;
            
            const contentDiv = document.getElementById('dashboard-content');
            const gamificationDiv = document.getElementById('gamification-area');
            if (contentDiv) contentDiv.style.opacity = '0.6'; // Feedback visual de "carregando"
            if (gamificationDiv) gamificationDiv.style.opacity = '0.6';

            try {
                // 1. Requisição com headers explícitos para salvar cache
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

                // 3. Atualiza o Gamification
                const newGamification = doc.getElementById('gamification-area');
                const currentGamification = document.getElementById('gamification-area');
                if (newGamification && currentGamification) {
                    currentGamification.innerHTML = newGamification.innerHTML;
                    if (window.Alpine) window.Alpine.initTree(currentGamification);
                }

                // 4. Atualiza os Cards de Status
                const newStats = doc.getElementById('dashboard-stats');
                const currentStats = document.getElementById('dashboard-stats');
                if (newStats && currentStats) {
                    currentStats.innerHTML = newStats.innerHTML;

                    //Reinicializa o Alpine nos Status
                    if (window.Alpine) {
                        window.Alpine.initTree(currentStats);
                    }
                }

                // 5. Atualiza a Grade e os Filtros
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
                if (gamificationDiv) gamificationDiv.style.opacity = '1';
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

    <script>
    // Cria um objeto global para lembrar o tamanho das barras
    window.memoriaBarras = window.memoriaBarras || {};
    </script>

    {{-- BANNER DE NOTIFICAÇÕES --}}
    <x-notification-banner />

    {{-- SCRIPT DA I.A. (NOVA VERSÃO CHAT) --}}
    <script>
        window.consultarIa = async function(disciplinaId, disciplinaNome) {
            // 1. Confirmação de Custo (Novo Bloco) 💰
            if (window.swalTailwind) {
                const result = await window.swalTailwind.fire({
                    title: 'Invocar o Oráculo?',
                    html: `O Oráculo analisará sua situação em <strong>${disciplinaNome}</strong>.<br><span class="text-sm text-purple-600 dark:text-purple-400 font-bold">Custo: 10 créditos</span>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, invocar!',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#8b5cf6', // Roxo
                });
                if (!result.isConfirmed) return;
            } else if (!confirm(`Deseja gastar 10 créditos para analisar ${disciplinaNome}?`)) {
                return;
            }

            const chatContainer = document.getElementById('chat-container');
            
            // 2. Abre o Modal
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'oracle-chat' }));
            
            // 3. Limpa o chat anterior
            chatContainer.innerHTML = '';
            
            // 4. Adiciona a mensagem do Usuário
            appendMessage('user', `Ó grande Oráculo, analise minha situação em <strong>${disciplinaNome}</strong>. Posso faltar hoje?`);

            // 5. Mostra o indicador de "Digitando..."
            const typingId = showTyping();

            // Rola para baixo
            scrollToBottom();

            try {
                // 6. Chama a API (Backend)
                const response = await fetch(`/api/ai/analisar/${disciplinaId}`);
                
                // Handle HTTP 402 Insufficient Credits
                if (response.status === 402) {
                    const errorData = await response.json();
                    removeTyping(typingId);
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: 'oracle-chat' }));
                    
                    const resetDate = errorData.reset_at ? new Date(errorData.reset_at).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' }) : 'próximo mês';
                    
                    const exhaustedResult = await window.swalTailwind.fire({
                        title: 'Créditos insuficientes!',
                        html: `
                            <p class="text-gray-600 dark:text-gray-300 mb-4">Você zerou seus créditos de sabedoria este mês.</p>
                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 mb-4">
                                <div class="text-2xl font-black text-purple-700 dark:text-purple-300">${errorData.user_credits}/${errorData.monthly_max}</div><br>
                                <div class="text-xs text-purple-600 dark:text-purple-400 mt-1"><strong>Data de renovação: ${resetDate}<strong></div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Comprar Mais (conceitual)',
                        cancelButtonText: 'Ok',
                        confirmButtonColor: '#8b5cf6',
                    });
                    
                    if (exhaustedResult.isConfirmed) {
                        await window.swalTailwind.fire({
                            title: 'Recurso Conceitual',
                            text: 'Em uma versão comercial, aqui você poderia adquirir mais créditos. Esta é uma demonstração para fins acadêmicos (TCC).',
                            icon: 'info',
                            confirmButtonText: 'Entendi',
                        });
                    }
                    return;
                }
                
                if (!response.ok) throw new Error('Erro na API');
                
                const data = await response.json();

                // 7. Remove "Digitando..."
                removeTyping(typingId);
                
                // Atualiza display de créditos (se você estiver usando listeners globais)
                if (data.user_credits !== undefined) {
                    // Opção A: Se você usa o listener que estava no código colado
                    window.dispatchEvent(new CustomEvent('ai-credits:update', { 
                        detail: { 
                            credits: data.user_credits, 
                            max: {{ $user->getMonthlyMaxCredits() }} 
                        } 
                    }));

                    // Opção B: Atualização direta (caso o listener não esteja configurado)
                    const creditsEl = document.getElementById('credits-display');
                    if (creditsEl) {
                        creditsEl.innerHTML = `${data.user_credits}<span class="text-sm font-medium text-purple-500 dark:text-purple-400">/${{ $user->getMonthlyMaxCredits() }}</span>`;
                    }
                }
                
                // 8. Adiciona a resposta da IA
                setTimeout(() => {
                    let message = data.analise;
                    if (data.cached) {
                        message += '<div class="mt-2 text-xs opacity-70 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg> Grátis (Cache)</div>';
                    }
                    appendMessage('ai', message, data.risco, data.emoji);
                    scrollToBottom();
                }, 400);

            } catch (error) {
                console.error(error);
                removeTyping(typingId);
                appendMessage('ai', 'Meus cristais estão embaçados por uma interferência no servidor. Tente novamente mais tarde.', 'ERRO', '😵');
            }
        };

     window.consultarIaDia = async function(date) {
        
        // 1. Confirmação para economizar Créditos
        if (window.swalTailwind) {
            const result = await window.swalTailwind.fire({
                title: 'Invocar o Oráculo?',
                html: `O Oráculo analisará todas as aulas do dia ${date.split('-').reverse().join('/')}.<br><span class="text-sm text-purple-600 dark:text-purple-400 font-bold">Custo: 5 créditos</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, invocar!',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#8b5cf6',
            });
            if (!result.isConfirmed) return;
        } else if (!confirm('Deseja gastar 5 créditos para analisar o dia?')) {
            return;
        }

        const chatContainer = document.getElementById('chat-container');
        
        // 2. Abre o Modal
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'oracle-chat' }));
        
        // 3. Limpa o chat e adiciona pergunta do usuário
        chatContainer.innerHTML = '';
        
        // Formata a data para o chat ficar bonitinho (YYYY-MM-DD -> DD/MM)
        const dataFormatada = date.split('-').reverse().slice(0, 2).join('/');
        appendMessage('user', `Ó grande Oráculo, analise meu dia (${dataFormatada}). Posso faltar hoje?`);

        // 4. Mostra "Digitando..."
        const typingId = showTyping();
        scrollToBottom();

        try {
            // 5. Chama a API nova (Day Check)
            const response = await fetch(`/ai-advisor/day-check?date=${date}`);
            
            // Handle HTTP 402 Insufficient Credits
            if (response.status === 402) {
                const errorData = await response.json();
                removeTyping(typingId);
                
                // Close oracle modal
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'oracle-chat' }));
                
                // Show exhausted credits modal
                const resetDate = errorData.reset_at ? new Date(errorData.reset_at).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' }) : 'próximo mês';
                
                const exhaustedResult = await window.swalTailwind.fire({
                    title: 'O Oráculo está Exausto!',
                    html: `
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Você zerou seus créditos de sabedoria este mês.</p>
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 mb-4">
                            <div class="text-2xl font-black text-purple-700 dark:text-purple-300">${errorData.user_credits}/${errorData.monthly_max}</div>
                            <div class="text-xs text-purple-600 dark:text-purple-400 mt-1">Renova em ${resetDate}</div>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Comprar Mais (conceitual)',
                    cancelButtonText: 'Ok',
                    confirmButtonColor: '#8b5cf6',
                });
                
                // Show conceptual buy more info
                if (exhaustedResult.isConfirmed) {
                    await window.swalTailwind.fire({
                        title: 'Recurso Conceitual',
                        text: 'Em uma versão comercial, aqui você poderia adquirir mais créditos. Esta é uma demonstração para fins acadêmicos (TCC).',
                        icon: 'info',
                        confirmButtonText: 'Entendi',
                    });
                }
                return;
            }
            
            if (!response.ok) throw new Error('Erro na API');
            
            const data = await response.json();

            // 6. Remove "Digitando..." e exibe resposta
            removeTyping(typingId);
            
            // Update credits display via Global Event
            if (data.user_credits !== undefined) {
                window.dispatchEvent(new CustomEvent('ai-credits:update', { 
                    detail: { 
                        credits: data.user_credits, 
                        max: {{ $user->getMonthlyMaxCredits() }} 
                    } 
                }));
            }
            
            setTimeout(() => {
                // Add cache indicator if cached
                let message = data.message;
                if (data.cached) {
                    message += '<div class="mt-2 text-xs opacity-70 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg> Grátis (Cache)</div>';
                }
                
                appendMessage('ai', message, data.risk, '📅');
                scrollToBottom();
            }, 500);

        } catch (error) {
            console.error(error);
            removeTyping(typingId);
            appendMessage('ai', 'As nuvens cobriram minha visão. Tente novamente mais tarde.', 'ERRO', '☁️');
        }
    };

        // Função auxiliar para criar as bolhas do chat
        function appendMessage(role, text, risk = null, emoji = '') {
            const container = document.getElementById('chat-container');
            const div = document.createElement('div');
            
            if (role === 'user') {
                // Estilo da mensagem do USUÁRIO (Direita, Azul)
                div.className = 'flex justify-end mb-4 pl-8 animate-fade-in-up';
                div.innerHTML = `
                    <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm py-3 px-5 shadow-md text-sm leading-relaxed">
                        ${text}
                    </div>
                `;
            } else {
                // Cores baseadas no risco retornado pela API
                let bgClass = 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100';
                
                if (risk === 'ALTO') {
                    bgClass = 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800 text-red-800 dark:text-red-200';
                } else if (risk === 'MEDIO') {
                    bgClass = 'bg-amber-50 dark:bg-amber-900/20 border-amber-100 dark:border-amber-800 text-amber-800 dark:text-amber-200';
                } else if (risk === 'BAIXO') {
                    bgClass = 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200';
                }

                // Estilo da mensagem da IA (Esquerda)
                div.className = 'flex justify-start mb-4 pr-8 animate-fade-in-up';
                div.innerHTML = `
                    <div class="flex items-end gap-3 max-w-full">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 flex-shrink-0 flex items-center justify-center text-sm shadow-sm ring-2 ring-white dark:ring-gray-800 z-10">
                            ${emoji || '🔮'}
                        </div>
                        <div class="${bgClass} border rounded-2xl rounded-tl-sm py-3.5 px-5 shadow-sm text-sm leading-relaxed relative">
                            <span class="absolute -left-1.5 bottom-2.5 w-3 h-3 ${bgClass.split(' ')[0]} border-l border-b ${bgClass.split(' ')[2]} transform rotate-45"></span>
                            <p class="font-bold text-[10px] opacity-60 mb-1 uppercase tracking-wider flex items-center gap-1">
                                Oráculo diz:
                            </p>
                            ${text}
                        </div>
                    </div>
                `;
            }

            container.appendChild(div);
        }

        // Cria a animação de "3 pontinhos" pulando
        function showTyping() {
            const container = document.getElementById('chat-container');
            const id = 'typing-' + Date.now();
            const div = document.createElement('div');
            div.id = id;
            div.className = 'flex justify-start mb-4 animate-pulse';
            div.innerHTML = `
                <div class="flex items-end gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 flex-shrink-0 flex items-center justify-center text-xs text-white shadow-sm ring-2 ring-white dark:ring-gray-800 opacity-50">
                        🔮
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl rounded-tl-sm py-4 px-4 shadow-sm flex items-center gap-1.5 h-12">
                        <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce"></span>
                        <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                        <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                    </div>
                </div>
            `;
            container.appendChild(div);
            scrollToBottom();
            return id;
        }

        function removeTyping(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        function scrollToBottom() {
            const container = document.getElementById('chat-container');
            if(container) container.scrollTop = container.scrollHeight;
        }
    </script>

    {{-- MODAL CHAT ORÁCULO --}}
<x-modal name="oracle-chat" focusable>
    <div class="bg-white dark:bg-gray-900 flex flex-col h-[500px] max-h-[85vh]">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-white/95 dark:bg-gray-900/95 backdrop-blur z-10 sticky top-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Oráculo Acadêmico</h3>
                    <p class="text-xs text-emerald-500 font-bold flex items-center gap-1">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Online
                    </p>
                </div>
            </div>
            <button x-on:click="$dispatch('close-modal', 'oracle-chat')" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition bg-gray-50 dark:bg-gray-800 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div id="chat-container" class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50 dark:bg-[#0B1220] scroll-smooth">
            {{-- As mensagens serão injetadas aqui pelo JS --}}
        </div>

        <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
            <div class="relative opacity-60">
                <input type="text" disabled placeholder="INDISPONÍVEL..." class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-500 text-sm py-3 px-4 cursor-not-allowed select-none pl-4 pr-10">
                <div class="absolute right-3 top-3 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
            </div>
            <p class="text-[10px] text-center text-gray-400 mt-2">O Oráculo analisa seu histórico para responder.</p>
        </div>
    </div>
</x-modal>

</x-app-layout>