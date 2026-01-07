<x-app-layout>
    {{-- HEADER (Padrão das outras telas) --}}
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}"
               class="p-2 -ml-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-600 dark:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                Importar Grade via IA
            </h2>
        </div>
    </x-slot>

    <div class="py-6 pb-24" 
         x-data="gradeImporter()">
        
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ETAPA 1: INPUT (Texto ou Foto) --}}
            <div x-show="step === 1" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-xl overflow-hidden relative">
                
                {{-- Efeito decorativo de fundo --}}
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-yellow-400/20 rounded-full blur-2xl"></div>

                {{-- ABAS --}}
                <div class="flex border-b border-gray-100 dark:border-gray-800">
                    <button @click="mode = 'texto'" 
                            class="flex-1 py-4 text-sm font-bold transition-colors flex items-center justify-center gap-2 relative"
                            :class="mode === 'texto' ? 'text-yellow-600 dark:text-yellow-500 bg-yellow-50 dark:bg-yellow-900/10' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'">
                        <span class="text-lg">📝</span> Texto
                        <div x-show="mode === 'texto'" class="absolute bottom-0 left-0 w-full h-1 bg-yellow-500"></div>
                    </button>
                    <button @click="mode = 'foto'" 
                            class="flex-1 py-4 text-sm font-bold transition-colors flex items-center justify-center gap-2 relative"
                            :class="mode === 'foto' ? 'text-yellow-600 dark:text-yellow-500 bg-yellow-50 dark:bg-yellow-900/10' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'">
                        <span class="text-lg">📸</span> Foto
                        <div x-show="mode === 'foto'" class="absolute bottom-0 left-0 w-full h-1 bg-yellow-500"></div>
                    </button>
                </div>

                <div class="p-6 sm:p-8">
                    {{-- MODO TEXTO --}}
                    <div x-show="mode === 'texto'" class="space-y-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Cole sua grade aqui
                        </label>
                        <textarea 
                            x-model="textoInput"
                            rows="8" 
                            class="w-full rounded-2xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 dark:text-white text-sm font-mono leading-relaxed focus:border-yellow-500 focus:ring-yellow-500 shadow-sm transition-all"
                            placeholder="Exemplo:&#10;SEGUNDA&#10;1º Português&#10;2º Matemática&#10;..."></textarea>
                        <p class="text-xs text-gray-400">
                            Dica: Copie do WhatsApp ou bloco de notas e cole aqui.
                        </p>
                    </div>

                    {{-- MODO FOTO --}}
                    <div x-show="mode === 'foto'" class="space-y-4">
                        <div class="relative w-full h-64 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl flex flex-col items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-800/50 transition cursor-pointer group bg-gray-50 dark:bg-gray-800 overflow-hidden">
                            
                            <input type="file" x-ref="fileInput" accept="image/*" 
                                   @change="handleFileUpload($event)"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            {{-- Placeholder --}}
                            <div x-show="!previewUrl" class="flex flex-col items-center pointer-events-none p-4 text-center">
                                <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-600 dark:text-gray-300">Toque para enviar foto</span>
                                <span class="text-xs text-gray-400 mt-1">Formatos: JPG, PNG</span>
                            </div>

                            {{-- Preview --}}
                            <img x-show="previewUrl" :src="previewUrl" class="absolute inset-0 w-full h-full object-contain p-2 z-0" />
                            
                            {{-- Botão remover foto --}}
                            <button x-show="previewUrl" @click.stop="limparFoto()" class="absolute top-2 right-2 z-20 bg-red-500 text-white p-2 rounded-full shadow-lg hover:bg-red-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    {{-- BOTÃO PROCESSAR --}}
                    <div class="mt-8">
                        <button @click="processarIA()" 
                                :disabled="isLoading"
                                class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold text-lg py-4 rounded-2xl shadow-lg shadow-yellow-500/20 transform transition active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-3">
                            
                            <template x-if="!isLoading">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 animate-pulse group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                                    </svg>
                                    <span>Confirmar</span>
                                </div>
                            </template>

                            <template x-if="isLoading">
                                <div class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Analisando...</span>
                                </div>
                            </template>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ETAPA 2: RESULTADO E CONFIGURAÇÃO --}}
            <div x-show="step === 2" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-10"
                 x-transition:enter-end="opacity-100 translate-y-0">
                
                {{-- BOX DE CONFIGURAÇÃO DE HORÁRIOS --}}
                <div class="bg-white/50 dark:bg-gray-900/50 backdrop-blur-md border border-yellow-200 dark:border-yellow-900/30 rounded-3xl p-6 mb-6 shadow-sm">
                    <h3 class="text-sm font-bold text-yellow-700 dark:text-yellow-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Ajuste os Horários
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Início Aula 1</label>
                            <input type="time" x-model="config.inicio" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:border-yellow-500 focus:ring-yellow-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Duração (min)</label>
                            <input type="number" x-model="config.duracao" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:border-yellow-500 focus:ring-yellow-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Intervalo (min)</label>
                            <input type="number" x-model="config.intervaloTempo" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:border-yellow-500 focus:ring-yellow-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Após aula nº</label>
                            <input type="number" x-model="config.intervaloApos" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:border-yellow-500 focus:ring-yellow-500">
                        </div>
                    </div>
                </div>

                {{-- LISTA DE AULAS (Gerada pelo Alpine) --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2 px-1">
                        <span>🤖</span> Resultado da IA:
                    </h3>

                    <template x-for="(dia, index) in dadosExtraidos" :key="index">
                        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition">
                            {{-- Cabeçalho do Dia --}}
                            <div class="bg-gray-50 dark:bg-gray-900/50 px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                <span class="font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                    📅 <span x-text="dia.nome_dia"></span>
                                </span>
                                <span class="text-xs bg-white dark:bg-gray-800 px-2 py-1 rounded-md border border-gray-200 dark:border-gray-600 text-gray-500" x-text="dia.aulas.length + ' aulas'"></span>
                            </div>
                            
                            {{-- Lista de Matérias --}}
                            <div class="p-4 space-y-2">
                                <template x-for="aula in dia.aulas" :key="aula.ordem">
                                    <div class="flex items-center gap-4 p-2 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold shrink-0" x-text="aula.ordem + 'º'"></div>
                                        <div class="font-medium text-gray-700 dark:text-gray-300" x-text="aula.disciplina"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- AÇÕES FINAIS (Sticky Bottom no Mobile) --}}
                <div class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-t border-gray-100 dark:border-gray-800 z-50 md:relative md:bg-transparent md:border-none md:p-0 md:mt-8">
                    <div class="max-w-2xl mx-auto flex gap-3">
                        <button @click="resetar()" 
                                class="flex-1 py-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            Tentar de Novo
                        </button>
                        <button @click="salvarTudo()" 
                                :disabled="isLoading"
                                class="flex-[2] py-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-500/20 transition transform active:scale-95 disabled:opacity-70 flex justify-center items-center gap-2">
                            <span x-show="!isLoading">Confirmar e Salvar ✅</span>
                            <span x-show="isLoading" class="animate-pulse">Salvando...</span>
                        </button>
                    </div>
                </div>
                {{-- Spacer para não cobrir conteúdo no mobile --}}
                <div class="h-24 md:hidden"></div>

            </div>

        </div>
    </div>

    {{-- LÓGICA ALPINE.JS --}}
    <script>
        function gradeImporter() {
            return {
                step: 1,
                mode: 'texto',
                textoInput: '',
                previewUrl: null,
                file: null,
                isLoading: false,
                dadosExtraidos: [],
                config: {
                    inicio: '07:00',
                    duracao: 50,
                    intervaloTempo: 15,
                    intervaloApos: 3
                },

                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    this.file = file;
                    
                    // Cria Preview
                    const reader = new FileReader();
                    reader.onload = (e) => this.previewUrl = e.target.result;
                    reader.readAsDataURL(file);
                },

                limparFoto() {
                    this.file = null;
                    this.previewUrl = null;
                    this.$refs.fileInput.value = ''; // Limpa o input
                },

                async processarIA() {
                    // Validações
                    if (this.mode === 'texto' && !this.textoInput.trim()) {
                        return this.alert('Atenção', 'Digite ou cole sua grade primeiro.', 'warning');
                    }
                    if (this.mode === 'foto' && !this.file) {
                        return this.alert('Atenção', 'Selecione uma foto da grade.', 'warning');
                    }

                    this.isLoading = true;
                    const formData = new FormData();

                    if (this.mode === 'texto') {
                        formData.append('texto_grade', this.textoInput);
                    } else {
                        formData.append('foto_grade', this.file);
                    }

                    try {
                        const response = await fetch("{{ route('grade.importar') }}", {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                            body: formData
                        });

                        const result = await response.json();

                        if (!response.ok) throw new Error(result.error || 'Erro ao processar');

                        this.dadosExtraidos = result.data;
                        this.step = 2; // Avança para o próximo passo
                        window.scrollTo({ top: 0, behavior: 'smooth' });

                    } catch (error) {
                        console.error(error);
                        this.alert('Erro', 'A IA não conseguiu entender. Tente melhorar o texto ou a foto.', 'error');
                    } finally {
                        this.isLoading = false;
                    }
                },

                async salvarTudo() {
                    if (!this.dadosExtraidos.length) return;

                    this.isLoading = true;

                    try {
                        const response = await fetch("{{ route('grade.salvar.lote') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                dados: this.dadosExtraidos,
                                configuracao: this.config
                            })
                        });

                        const result = await response.json();

                        if (!response.ok) throw new Error(result.error || 'Erro ao salvar');

                        await this.alert('Sucesso! 🎉', 'Grade importada. Redirecionando...', 'success');
                        window.location.href = "{{ route('dashboard') }}";

                    } catch (error) {
                        this.alert('Erro', error.message, 'error');
                        this.isLoading = false;
                    }
                },

                resetar() {
                    this.step = 1;
                    this.dadosExtraidos = [];
                    this.limparFoto();
                    this.textoInput = '';
                },

                // Helper de alerta (Usa SweetAlert se disponível ou nativo)
                alert(title, text, icon) {
                    if (window.swalTailwind) {
                        return window.swalTailwind.fire({ title, text, icon, confirmButtonColor: '#3b82f6' });
                    } else if (window.Swal) {
                        return window.Swal.fire({ title, text, icon });
                    } else {
                        alert(`${title}: ${text}`);
                        return Promise.resolve();
                    }
                }
            }
        }
    </script>
</x-app-layout>