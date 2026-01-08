<x-app-layout>
    {{-- CSS OTIMIZADO (Sem linhas, sem bugs) --}}
    <style>
        /* Bloqueia tudo enquanto arrasta */
        body.is-dragging {
            overflow: hidden !important;
            touch-action: none !important;
            user-select: none !important;
            -webkit-user-select: none !important;
        }
        
        /* O item fantasma (limpo, só sombra) */
        .ghost-item {
            position: fixed;
            pointer-events: none;
            z-index: 10000;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.15);
            border-radius: 0.75rem; /* rounded-xl */
            transform: translate3d(0, 0, 0); /* Força GPU limpa */
            will-change: top, left;
        }
        .dark .ghost-item {
            background: rgba(31, 41, 55, 0.98);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.3);
        }

        /* Otimização da lista */
        .drag-list {
            transform: translate3d(0, 0, 0);
        }
    </style>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}"
               class="p-2 -ml-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-600 dark:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">Importar Grade via IA</h2>
        </div>
    </x-slot>

    <div class="py-6 pb-24" x-data="gradeImporter()">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ETAPA 1: INPUT --}}
            <div x-show="step === 1" 
                 x-transition:enter="transition ease-out duration-300"
                 class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-xl overflow-hidden relative">
                
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-yellow-400/20 rounded-full blur-2xl"></div>

                <div class="flex border-b border-gray-100 dark:border-gray-800">
                    <button @click="mode = 'texto'" class="flex-1 py-4 text-sm font-bold transition-colors flex items-center justify-center gap-2 relative" :class="mode === 'texto' ? 'text-yellow-600 dark:text-yellow-500 bg-yellow-50 dark:bg-yellow-900/10' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'">
                        <span class="text-lg">📝</span> Texto
                        <div x-show="mode === 'texto'" class="absolute bottom-0 left-0 w-full h-1 bg-yellow-500"></div>
                    </button>
                    <button @click="mode = 'foto'" class="flex-1 py-4 text-sm font-bold transition-colors flex items-center justify-center gap-2 relative" :class="mode === 'foto' ? 'text-yellow-600 dark:text-yellow-500 bg-yellow-50 dark:bg-yellow-900/10' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'">
                        <span class="text-lg">📸</span> Foto
                        <div x-show="mode === 'foto'" class="absolute bottom-0 left-0 w-full h-1 bg-yellow-500"></div>
                    </button>
                </div>

                <div class="p-6 sm:p-8">
                    <div x-show="mode === 'texto'" class="space-y-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Cole sua grade aqui</label>
                        <textarea x-model="textoInput" rows="8" class="w-full rounded-2xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 dark:text-white text-sm font-mono focus:border-yellow-500 focus:ring-yellow-500 shadow-sm transition-all" placeholder="Exemplo:&#10;SEGUNDA&#10;1º Português..."></textarea>
                    </div>
                    <div x-show="mode === 'foto'" class="space-y-4">
                        <div class="relative w-full h-64 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl flex flex-col items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-800/50 transition cursor-pointer group bg-gray-50 dark:bg-gray-800 overflow-hidden">
                            <input type="file" x-ref="fileInput" accept="image/*" @change="handleFileUpload($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div x-show="!previewUrl" class="flex flex-col items-center pointer-events-none p-4 text-center">
                                <span class="text-sm font-bold text-gray-600 dark:text-gray-300">Toque para enviar foto</span>
                            </div>
                            <img x-show="previewUrl" :src="previewUrl" class="absolute inset-0 w-full h-full object-contain p-2 z-0" />
                            <button x-show="previewUrl" @click.stop="limparFoto()" class="absolute top-2 right-2 z-20 bg-red-500 text-white p-2 rounded-full shadow-lg hover:bg-red-600 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>
                    </div>
                    <div class="mt-8">
                        <button @click="processarIA()" :disabled="isLoading" class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold text-lg py-4 rounded-2xl shadow-lg shadow-yellow-500/20 transform transition active:scale-[0.98] disabled:opacity-70 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5 animate-pulse group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                            </svg>
                            <span x-show="!isLoading">Confirmar</span><span x-show="isLoading">Analisando...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ETAPA 2: RESULTADO --}}
            <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-500">
                
                {{-- BOX CONFIG --}}
                <div class="bg-white dark:bg-gray-900 border border-yellow-200 dark:border-yellow-900/30 rounded-3xl p-6 mb-6 shadow-sm">
                    <h3 class="text-sm font-bold text-yellow-700 dark:text-yellow-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Ajuste os Horários
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div><label class="block text-[10px] font-bold text-gray-500 uppercase">Início</label><input type="time" x-model="config.inicio" class="w-full rounded-xl border-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-700 text-sm"></div>
                        <div><label class="block text-[10px] font-bold text-gray-500 uppercase">Duração</label><input type="number" x-model="config.duracao" class="w-full rounded-xl border-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-700 text-sm"></div>
                        <div><label class="block text-[10px] font-bold text-gray-500 uppercase">Intervalo</label><input type="number" x-model="config.intervaloTempo" class="w-full rounded-xl border-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-700 text-sm"></div>
                        <div><label class="block text-[10px] font-bold text-gray-500 uppercase">Após aula</label><input type="number" x-model="config.intervaloApos" class="w-full rounded-xl border-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-700 text-sm"></div>
                    </div>
                </div>

                {{-- LISTA DE AULAS --}}
                <div class="space-y-4 drag-list">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2 px-1"><span>🤖</span> Resultado da IA:</h3>
                    
                    <template x-for="(dia, diaIndex) in dadosExtraidos" :key="diaIndex">
                        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm transition">
                            <div class="bg-gray-50 dark:bg-gray-900/50 px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center select-none">
                                <span class="font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">📅 <span x-text="dia.nome_dia"></span></span>
                                <span class="text-xs bg-white dark:bg-gray-800 px-2 py-1 rounded-md border border-gray-200 dark:border-gray-600 text-gray-500" x-text="dia.aulas.length + ' aulas'"></span>
                            </div>
                            
                            {{-- LISTA DE ITENS --}}
                            <div class="p-4 space-y-2 relative">
                                <template x-for="(aula, aulaIndex) in dia.aulas" :key="aulaIndex">
                                    <div class="flex items-center gap-4 p-2 rounded-xl border border-transparent transition-none select-none bg-white dark:bg-gray-800"
                                         :class="{ 
                                            'opacity-20 bg-gray-100 dark:bg-gray-700': dragging && dragging.diaIndex === diaIndex && dragging.aulaIndex === aulaIndex 
                                         }"
                                         style="touch-action: none;"
                                         :data-dia="diaIndex" :data-aula="aulaIndex">
                                        
                                        {{-- HANDLE (Toque limpo) --}}
                                        <div class="text-gray-300 cursor-grab active:cursor-grabbing p-2 -ml-2 touch-none"
                                             @mousedown.prevent="startDrag($event, diaIndex, aulaIndex)"
                                             @touchstart.prevent="startDrag($event, diaIndex, aulaIndex)">
                                            <svg class="w-6 h-6 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                                        </div>

                                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold shrink-0 pointer-events-none" x-text="(aulaIndex + 1) + 'º'"></div>
                                        <input type="text" x-model="aula.disciplina" class="flex-1 bg-transparent border-none p-0 text-gray-700 dark:text-gray-300 font-medium focus:ring-0">
                                        <div class="relative shrink-0"><input type="color" x-model="aula.cor" class="w-8 h-8 rounded-full overflow-hidden cursor-pointer border-2 border-white dark:border-gray-600 shadow-sm p-0 bg-transparent"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- AÇÕES FIXAS --}}
                <div class="fixed bottom-0 left-0 right-0 p-4 bg-white/60 dark:bg-gray-900/60 backdrop-blur-lg border-t border-gray-200/50 dark:border-gray-700/50 z-50 md:relative md:bg-transparent md:border-none md:p-0 md:mt-8 shadow-[0_-8px_30px_rgba(0,0,0,0.04)]">
                    <div class="max-w-2xl mx-auto flex gap-3">
                        
                        {{-- BOTÃO RESETAR --}}
                        <button @click="resetar()" 
                                class="flex-1 py-4 rounded-xl border border-gray-200 dark:border-gray-700 font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200 transition-all duration-200 flex items-center justify-center gap-2 group">
                            {{-- Ícone Refresh --}}
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="text-sm sm:text-base">Refazer</span>
                        </button>

                        {{-- BOTÃO SALVAR --}}
                        <button @click="salvarTudo()" 
                                :disabled="isLoading" 
                                class="flex-[2] py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 transition-all transform active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex justify-center items-center gap-2">
                            
                            {{-- Estado Normal --}}
                            <template x-if="!isLoading">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm sm:text-base">Salvar Grade</span>
                                </div>
                            </template>

                            {{-- Estado Carregando --}}
                            <template x-if="isLoading">
                                <div class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-sm sm:text-base">Salvando...</span>
                                </div>
                            </template>
                        </button>
                    </div>
                </div>
                {{-- Spacer para não cobrir conteúdo no mobile --}}
                <div class="h-24 md:hidden"></div>

            {{-- GHOST ELEMENT (LIMPO) --}}
            <div x-show="dragging" x-cloak class="ghost-item flex items-center gap-4 w-64 p-2"
                 :style="`left: ${ghostPos.x}px; top: ${ghostPos.y}px; transform: translate3d(-50%, -50%, 0);`">
                 <div class="text-gray-300"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg></div>
                 <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-600"><span x-text="dragging ? (dragging.aulaIndex + 1) + 'º' : ''"></span></div>
                 <span class="font-bold text-gray-800 dark:text-white truncate" x-text="dragging ? dragging.disciplina : ''"></span>
            </div>

        </div>
    </div>

    <script>
        function gradeImporter() {
            return {
                step: 1, mode: 'texto', textoInput: '', previewUrl: null, file: null, isLoading: false, dadosExtraidos: [],
                config: { inicio: '07:00', duracao: 50, intervaloTempo: 15, intervaloApos: 3 },
                dragging: null, ghostPos: { x: 0, y: 0 }, moveHandler: null, endHandler: null,

                startDrag(e, diaIndex, aulaIndex) {
                    document.body.classList.add('is-dragging');
                    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                    this.dragging = { diaIndex, aulaIndex, ...this.dadosExtraidos[diaIndex].aulas[aulaIndex] };
                    this.ghostPos = { x: clientX, y: clientY };
                    this.moveHandler = (event) => this.onMove(event);
                    this.endHandler = (event) => this.endDrag(event);
                    window.addEventListener('mousemove', this.moveHandler);
                    window.addEventListener('touchmove', this.moveHandler, { passive: false });
                    window.addEventListener('mouseup', this.endHandler);
                    window.addEventListener('touchend', this.endHandler);
                },

                onMove(e) {
                    if (!this.dragging) return;
                    if (e.cancelable) e.preventDefault();
                    
                    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                    
                    // Atualização direta para performance máxima
                    this.ghostPos = { x: clientX, y: clientY };

                    const targetEl = document.elementFromPoint(clientX, clientY);
                    if (!targetEl) return;
                    const row = targetEl.closest('[data-aula]');
                    if (!row) return;

                    const targetDia = parseInt(row.dataset.dia);
                    const targetAula = parseInt(row.dataset.aula);
                    
                    if (targetDia === this.dragging.diaIndex && targetAula !== this.dragging.aulaIndex) {
                        const aulas = this.dadosExtraidos[this.dragging.diaIndex].aulas;
                        const itemMovido = aulas.splice(this.dragging.aulaIndex, 1)[0];
                        aulas.splice(targetAula, 0, itemMovido);
                        this.dragging.aulaIndex = targetAula;
                        aulas.forEach((a, i) => a.ordem = i + 1);
                    }
                },

                endDrag() {
                    this.dragging = null;
                    document.body.classList.remove('is-dragging');
                    window.removeEventListener('mousemove', this.moveHandler);
                    window.removeEventListener('touchmove', this.moveHandler);
                    window.removeEventListener('mouseup', this.endHandler);
                    window.removeEventListener('touchend', this.endHandler);
                },

                handleFileUpload(event) { const file = event.target.files[0]; if (!file) return; this.file = file; const reader = new FileReader(); reader.onload = (e) => this.previewUrl = e.target.result; reader.readAsDataURL(file); },
                limparFoto() { this.file = null; this.previewUrl = null; this.$refs.fileInput.value = ''; },
                async processarIA() { 
                    if (this.mode === 'texto' && !this.textoInput.trim()) return this.alert('Atenção', 'Digite ou cole sua grade.', 'warning');
                    if (this.mode === 'foto' && !this.file) return this.alert('Atenção', 'Selecione uma foto.', 'warning');
                    this.isLoading = true; const formData = new FormData(); this.mode === 'texto' ? formData.append('texto_grade', this.textoInput) : formData.append('foto_grade', this.file);
                    try { const response = await fetch("{{ route('grade.importar') }}", { method: 'POST', headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" }, body: formData }); const result = await response.json(); if (!response.ok) throw new Error(result.error); this.dadosExtraidos = result.data; this.step = 2; window.scrollTo({ top: 0, behavior: 'smooth' }); } catch (error) { this.alert('Erro', 'Não consegui ler a grade.', 'error'); } finally { this.isLoading = false; }
                },
                async salvarTudo() {
                    if (!this.dadosExtraidos.length) return; this.isLoading = true;
                    try { const response = await fetch("{{ route('grade.salvar.lote') }}", { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" }, body: JSON.stringify({ dados: this.dadosExtraidos, configuracao: this.config }) }); const result = await response.json(); if (!response.ok) throw new Error(result.error); await this.alert('Sucesso! 🎉', 'Grade salva com sucesso!', 'success'); window.location.href = "{{ route('dashboard') }}"; } catch (error) { this.alert('Erro', error.message, 'error'); this.isLoading = false; }
                },
                resetar() { this.step = 1; this.dadosExtraidos = []; this.limparFoto(); this.textoInput = ''; },
                alert(title, text, icon) { if (window.swalTailwind) return window.swalTailwind.fire({ title, text, icon, confirmButtonColor: '#3b82f6' }); if (window.Swal) return window.Swal.fire({ title, text, icon }); alert(`${title}: ${text}`); }
            }
        }
    </script>
</x-app-layout>