<div x-data="aiSidebar" @open-ai-sidebar.window="openSidebar($event.detail)"
    @ai-new-message.window="addMessage($event.detail)" @keydown.escape.window="open = false" class="relative z-50"
    style="display: none;" x-show="open" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>

    {{-- Sidebar --}}
    <div class="fixed inset-y-0 right-0 w-full md:max-w-md bg-white dark:bg-[#0B1220] shadow-2xl transform transition-transform duration-300 ease-in-out flex flex-col"
        x-show="open" x-transition:enter="translate-x-full" x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="translate-x-0"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">

        {{-- Header --}}
        <div
            class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-white/95 dark:bg-[#0B1220]/95 backdrop-blur z-10 flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-purple-500/30 ring-2 ring-purple-100 dark:ring-purple-900/50">
                    ✨
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-base leading-tight">Oráculo Acadêmico</h3>
                    <p class="text-[10px] text-emerald-500 font-bold flex items-center gap-1 uppercase tracking-wide">
                        <span class="relative flex h-1.5 w-1.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                        </span>
                        Online
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{-- Credits Pill --}}
                <button @click="$dispatch('open-modal', 'ai-credits-info')"
                    class="flex group/credits relative items-center gap-2 px-3 py-1.5 rounded-full bg-purple-50 dark:bg-purple-900/30 border border-purple-100 dark:border-purple-500/20 hover:border-purple-300 dark:hover:border-purple-500/50 transition-all overflow-hidden"
                    title="Créditos Frequência Certa">

                    {{-- Icon --}}
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>

                    {{-- Counter --}}
                    <div class="flex flex-col items-start leading-none relative z-10">
                        <span
                            class="text-[9px] font-bold uppercase text-purple-400 dark:text-purple-500 tracking-widest">Créditos</span>
                        <div
                            class="flex items-baseline gap-0.5 text-purple-700 dark:text-purple-200 font-mono font-bold text-xs">
                            <span x-text="credits">0</span>
                            <span class="opacity-50 text-[10px]">/</span>
                            <span class="opacity-50 text-[10px]" x-text="maxCredits">100</span>
                        </div>
                    </div>

                    {{-- Delta Animation --}}
                    <div x-show="showCreditsDelta" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="translate-y-full opacity-0"
                        x-transition:enter-end="translate-y-0 opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="translate-y-0 opacity-100"
                        x-transition:leave-end="-translate-y-full opacity-0"
                        class="absolute inset-0 flex items-center justify-center font-bold font-mono bg-purple-100 dark:bg-purple-900 z-20"
                        :class="creditsDelta > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                        <span x-text="creditsDelta > 0 ? '+' + creditsDelta : creditsDelta"></span>
                    </div>
                </button>

                <button @click="open = false"
                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition bg-gray-50 dark:bg-gray-800/50 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Chat Area --}}
        <div id="ai-sidebar-chat"
            class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50 dark:bg-[#0F172A] scroll-smooth overscroll-contain">

            {{-- Loading Skeletons for History --}}
            <template x-if="loadingHistory">
                <div class="space-y-4 opacity-50">
                    <div class="flex justify-end">
                        <div class="h-10 w-2/3 bg-gray-200 dark:bg-gray-800 rounded-2xl rounded-tr-sm"></div>
                    </div>
                    <div class="flex justify-start">
                        <div class="h-20 w-3/4 bg-gray-200 dark:bg-gray-800 rounded-2xl rounded-tl-sm"></div>
                    </div>
                </div>
            </template>

            {{-- Messages Loop --}}
            <template x-for="msg in messages" :key="msg.id || msg.tempId">
                <div>
                    {{-- USER MESSAGE --}}
                    <div x-show="msg.role === 'user'" class="flex justify-end pl-8 animate-fade-in-up">
                        <div
                            class="bg-blue-600 text-white rounded-2xl rounded-tr-sm py-3 px-5 shadow-md text-sm leading-relaxed max-w-full break-words">
                            <span x-html="msg.content"></span>
                        </div>
                    </div>

                    {{-- AI MESSAGE --}}
                    <div x-show="msg.role === 'ai'" class="flex justify-start pr-8 animate-fade-in-up">
                        <div class="flex items-end gap-3 max-w-full">
                            {{-- Avatar --}}
                            <div
                                class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 flex-shrink-0 flex items-center justify-center text-sm shadow-sm ring-2 ring-white dark:ring-gray-800 z-10 select-none">
                                <span x-text="msg.meta?.emoji || '🔮'"></span>
                            </div>

                            {{-- Bubble --}}
                            <div :class="{
                                'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800 text-red-800 dark:text-red-200': msg.meta?.risk === 'HIGH' || msg.meta?.risk === 'ALTO',
                                'bg-amber-50 dark:bg-amber-900/20 border-amber-100 dark:border-amber-800 text-amber-800 dark:text-amber-200': msg.meta?.risk === 'MEDIUM' || msg.meta?.risk === 'MEDIO',
                                'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200': msg.meta?.risk === 'LOW' || msg.meta?.risk === 'BAIXO',
                                'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100': !msg.meta?.risk
                            }"
                                class="border rounded-2xl rounded-tl-sm py-3.5 px-5 shadow-sm text-sm leading-relaxed relative w-full break-words">

                                {{-- Speech Triangle --}}
                                <span
                                    class="absolute -left-1.5 bottom-2.5 w-3 h-3 transform rotate-45 border-l border-b"
                                    :class="{
                                        'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800': msg.meta?.risk === 'HIGH' || msg.meta?.risk === 'ALTO',
                                        'bg-amber-50 dark:bg-amber-900/20 border-amber-100 dark:border-amber-800': msg.meta?.risk === 'MEDIUM' || msg.meta?.risk === 'MEDIO',
                                        'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-800': msg.meta?.risk === 'LOW' || msg.meta?.risk === 'BAIXO',
                                        'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700': !msg.meta?.risk
                                      }"></span>

                                <p
                                    class="font-bold text-[10px] opacity-60 mb-1 uppercase tracking-wider flex items-center gap-1 select-none">
                                    Oráculo diz:
                                </p>
                                <div x-html="msg.content"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Typing Indicator --}}
            <div x-show="typing" class="flex justify-start mb-4 animate-pulse" style="display: none;">
                <div class="flex items-end gap-3">
                    <div
                        class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 flex-shrink-0 flex items-center justify-center text-xs text-white shadow-sm ring-2 ring-white dark:ring-gray-800 opacity-50">
                        🔮
                    </div>
                    <div
                        class="bg-gray-100 dark:bg-gray-800 rounded-2xl rounded-tl-sm py-4 px-4 shadow-sm flex items-center gap-1.5 h-12">
                        <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce"></span>
                        <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce"
                            style="animation-delay: 0.1s"></span>
                        <span class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce"
                            style="animation-delay: 0.2s"></span>
                    </div>
                </div>
            </div>

            {{-- Bottom Spacer --}}
            <div x-ref="bottom" class="h-4"></div>
        </div>

        {{-- Footer / Input Area (ACTIONS) --}}
        <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-[#0B1220] flex-shrink-0">
            <template x-if="!showSubjects">
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="consultarIaDia()" :disabled="typing"
                            class="flex flex-col items-center justify-center p-3 rounded-xl bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 text-purple-700 dark:text-purple-300 transition border border-purple-100 dark:border-purple-800 group disabled:opacity-50 disabled:cursor-not-allowed">
                            <div
                                class="w-8 h-8 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-sm mb-2 group-hover:scale-110 transition">
                                📅
                            </div>
                            <span class="text-xs font-bold">Posso faltar hoje?</span>
                        </button>

                        <button @click="openSubjectSelector()" :disabled="typing"
                            class="flex flex-col items-center justify-center p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 text-blue-700 dark:text-blue-300 transition border border-blue-100 dark:border-blue-800 group disabled:opacity-50 disabled:cursor-not-allowed">
                            <div
                                class="w-8 h-8 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-sm mb-2 group-hover:scale-110 transition">
                                📚
                            </div>
                            <span class="text-xs font-bold">Analisar Matéria</span>
                        </button>
                    </div>

                    <a href="{{ route('grade.importar.view') }}"
                        class="flex items-center gap-3 p-3.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white transition shadow-lg shadow-indigo-500/20 group">
                        <div
                            class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-xl group-hover:scale-110 transition">
                            🤖
                        </div>
                        <div class="flex flex-col items-start leading-tight">
                            <span class="text-sm font-bold">Importar Horários com IA</span>
                            <span class="text-[10px] opacity-80 font-medium">Extraia dados da sua grade
                                automaticamente</span>
                        </div>
                        <svg class="w-5 h-5 ml-auto opacity-50 group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </template>

            {{-- Subject Selector Mode --}}
            <template x-if="showSubjects">
                <div class="flex flex-col h-full animate-fade-in-up">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-xs font-bold uppercase text-gray-400 tracking-wider">Escolha a Matéria</h4>
                        <button @click="showSubjects = false"
                            class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">Cancelar</button>
                    </div>

                    <div class="max-h-40 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                        <template x-if="loadingSubjects">
                            <div class="text-center py-4 text-gray-400 text-xs">Carregando matérias...</div>
                        </template>

                        <template x-for="sub in subjects" :key="sub.id">
                            <button @click="consultarIaMateria(sub)"
                                class="w-full text-left p-3 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-100 dark:border-gray-700 flex justify-between items-center transition">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                    x-text="sub.nome"></span>
                                <span class="text-xs text-purple-500">Selecionar &rarr;</span>
                            </button>
                        </template>

                        <template x-if="!loadingSubjects && subjects.length === 0">
                            <div class="text-center py-4 text-gray-400 text-xs">Nenhuma matéria encontrada.</div>
                        </template>
                    </div>
                </div>
            </template>

            <p x-show="!showSubjects" class="text-[10px] text-center text-gray-400 mt-3 font-medium">O Oráculo utiliza
                seus créditos de sabedoria.</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('aiSidebar', () => ({
            open: false,
            loadingHistory: false,
            messages: [],
            typing: false,
            initialized: false,

            // New State for Selectors
            showSubjects: false,
            loadingSubjects: false,
            subjects: [],

            // Credits State
            credits: {{ Auth::user()->ai_credits }},
            maxCredits: {{ Auth::user()->getMonthlyMaxCredits() }},
            creditsDelta: 0,
            showCreditsDelta: false,

            init() {
                // Listen for global credit updates
                window.addEventListener('ai-credits:update', (e) => this.handleCreditsUpdate(e.detail));

                // Pre-load history if needed, or wait for first open
                this.$watch('open', value => {
                    if (value && !this.initialized) {
                        this.fetchHistory();
                        this.initialized = true;
                    }
                    if (value) {
                        setTimeout(() => this.scrollToBottom(), 100);
                    }
                });
            },

            async fetchHistory() {
                this.loadingHistory = true;
                try {
                    const res = await fetch('/api/ai-advisor/history');
                    const data = await res.json();
                    this.messages = data;
                    this.$nextTick(() => this.scrollToBottom());
                } catch (e) {
                    console.error('Failed to load history', e);
                }
                this.loadingHistory = false;
            },

            openSidebar() {
                this.open = true;
            },

            addMessage(detail) {
                // detail can have: role, content, risk, emoji, typing (bool)
                if (detail.typing !== undefined) {
                    this.typing = detail.typing;
                    this.$nextTick(() => this.scrollToBottom());
                    return;
                }

                this.messages.push({
                    id: Date.now(),
                    role: detail.role,
                    content: detail.content,
                    meta: {
                        risk: detail.risk,
                        emoji: detail.emoji
                    }
                });

                this.typing = false;
                this.$nextTick(() => this.scrollToBottom());
            },

            handleCreditsUpdate(detail) {
                const diff = detail.credits - this.credits;
                if (diff === 0) return;
                this.creditsDelta = diff;
                this.credits = detail.credits;
                this.maxCredits = detail.max || this.maxCredits;
                this.showCreditsDelta = true;
                setTimeout(() => this.showCreditsDelta = false, 2000);
            },

            // --- LOGIC MOVED FROM DASHBOARD ---

            async openSubjectSelector() {
                this.showSubjects = true;
                if (this.subjects.length === 0) {
                    this.loadingSubjects = true;
                    try {
                        const res = await fetch('/api/disciplinas/list');
                        this.subjects = await res.json();
                    } catch (e) { console.error(e); }
                    this.loadingSubjects = false;
                }
            },

            async consultarIaDia() {
                const date = new Date().toISOString().split('T')[0]; // Hoje
                const dataFormatada = new Date().toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });

                // 1. Confirmação
                if (window.swalTailwind) {
                    const result = await window.swalTailwind.fire({
                        html: `O Oráculo analisará todas as aulas de HOJE (${dataFormatada}).<br><span class="text-sm text-purple-600 dark:text-purple-400 font-bold">Custo: 10 créditos</span>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, invocar!',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#8b5cf6',
                    });
                    if (!result.isConfirmed) return;
                } else if (!confirm('Deseja gastar 5 créditos?')) {
                    return;
                }

                // 2. Adiciona msg usuario
                this.addMessage({
                    role: 'user',
                    content: `Ó grande Oráculo, analise meu dia (${dataFormatada}). Posso faltar hoje?`
                });

                // 3. Typing
                this.typing = true;
                this.$nextTick(() => this.scrollToBottom());

                try {
                    const response = await fetch(`/ai-advisor/day-check?date=${date}`);

                    if (response.status === 402) {
                        const errorData = await response.json();
                        this.typing = false;
                        window.swalTailwind.fire({ icon: 'warning', title: 'Créditos Esgotados', text: 'Você zerou seus créditos.' });
                        return;
                    }

                    if (!response.ok) throw new Error('Erro na API');
                    const data = await response.json();

                    // Update credits global
                    if (data.user_credits !== undefined) {
                        window.dispatchEvent(new CustomEvent('ai-credits:update', {
                            detail: { credits: data.user_credits }  // Simplified event, assuming receiver handles null max
                        }));
                    }

                    setTimeout(() => {
                        let message = data.message;
                        if (data.cached) {
                            message += '<div class="mt-2 text-xs opacity-70 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg> Grátis (Cache)</div>';
                        }
                        this.addMessage({
                            role: 'ai',
                            content: message,
                            risk: data.risk,
                            emoji: '✨'
                        });
                    }, 500);

                } catch (e) {
                    console.error(e);
                    this.typing = false;
                    this.addMessage({ role: 'ai', content: 'Erro ao consultar o Oráculo.', risk: 'ERRO', emoji: '☁️' });
                }
            },

            async consultarIaMateria(subject) {
                this.showSubjects = false; // Fecha selector

                // 1. Confirmação
                if (window.swalTailwind) {
                    const result = await window.swalTailwind.fire({
                        html: `O Oráculo analisará <strong>${subject.nome}</strong>.<br><span class="text-sm text-purple-600 dark:text-purple-400 font-bold">Custo: 5 créditos</span>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, invocar!',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#8b5cf6',
                    });
                    if (!result.isConfirmed) return;
                }

                // 2. User Message
                this.addMessage({
                    role: 'user',
                    content: `Ó grande Oráculo, analise minha situação em <strong>${subject.nome}</strong>. Posso faltar hoje?`
                });

                // 3. Typing
                this.typing = true;
                this.$nextTick(() => this.scrollToBottom());

                try {
                    const response = await fetch(`/api/ai/analisar/${subject.id}`);

                    if (response.status === 402) {
                        const errorData = await response.json();
                        this.typing = false;
                        window.swalTailwind.fire({ icon: 'warning', title: 'Créditos Esgotados', text: 'Você zerou seus créditos.' });
                        return;
                    }

                    if (!response.ok) throw new Error('Erro na API');
                    const data = await response.json();

                    // Update credits global
                    if (data.user_credits !== undefined) {
                        window.dispatchEvent(new CustomEvent('ai-credits:update', {
                            detail: { credits: data.user_credits }
                        }));
                    }

                    setTimeout(() => {
                        let message = data.analise;
                        if (data.cached) {
                            message += '<div class="mt-2 text-xs opacity-70 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg> Grátis (Cache)</div>';
                        }
                        this.addMessage({
                            role: 'ai',
                            content: message,
                            risk: data.risco,
                            emoji: data.emoji
                        });
                    }, 400);

                } catch (e) {
                    console.error(e);
                    this.typing = false;
                    this.addMessage({ role: 'ai', content: 'Erro ao consultar o Oráculo.', risk: 'ERRO', emoji: '☁️' });
                }
            },

            scrollToBottom() {
                const chat = document.getElementById('ai-sidebar-chat');
                if (chat) chat.scrollTop = chat.scrollHeight;
            }
        }));
    });
</script>