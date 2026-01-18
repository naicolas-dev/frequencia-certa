<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="group p-2 -ml-2 rounded-full hover:bg-white/50 dark:hover:bg-gray-800/50 transition">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            
            <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full shadow-lg ring-2 ring-white/50 dark:ring-white/10" style="background-color: {{ $disciplina->cor }}"></div>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight truncate max-w-[200px]">
                        {{ $disciplina->nome }}
                    </h2>
                    <p class="text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-400 font-bold">Configurar Horários</p>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- INÍCIO DO CONTEXTO ALPINE --}}
    <div class="py-6 pb-24" x-data="gradeForm()">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- FORMULÁRIO DE CADASTRO / EDIÇÃO --}}
            <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2.5rem] shadow-sm border border-white/20 dark:border-gray-800 p-6 sm:p-8 relative overflow-hidden transition-all duration-300"
                 :class="isEditing ? 'ring-2 ring-blue-500/50' : ''">
                
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span x-text="isEditing ? 'Editando Horário' : 'Novo Horário'"></span>
                    </span>
                    
                    {{-- Botão Cancelar Edição --}}
                    <button x-show="isEditing" @click="resetForm()" x-transition class="text-red-500 hover:text-red-700 text-[10px]">
                        CANCELAR
                    </button>
                </h3>
                
                <form @submit.prevent="save()">
                    
                    {{-- Seleção de Dia --}}
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-300 mb-3 ml-1">Dia da Semana</label>
                        <div class="flex items-center gap-4 overflow-x-auto p-3 pb-4 -mx-2 snap-x scrollbar-hide">
                            @php $dias = [1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sab']; @endphp
                            @foreach($dias as $k => $d)
                                <label class="cursor-pointer snap-center shrink-0 w-16 text-center relative group">
                                    <input type="radio" name="dia_semana" value="{{ $k }}" class="sr-only" x-model="form.dia_semana">
                                    <div class="h-12 w-full rounded-2xl flex items-center justify-center text-sm transition-all duration-300 border"
                                         :class="form.dia_semana == '{{ $k }}' 
                                            ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-500/40 border-blue-500 scale-110 -translate-y-1' 
                                            : 'bg-white/40 dark:bg-black/20 text-gray-500 dark:text-gray-400 border-white/20 dark:border-gray-700 group-hover:bg-white/60 dark:group-hover:bg-gray-800/60'">
                                        {{ $d }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Seleção de Horários --}}
                    <div class="flex items-center gap-4 mb-8">
                        <div class="flex-1 relative group">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Início</label>
                            <input type="time" x-model="form.horario_inicio" required class="w-full text-center rounded-2xl bg-white/50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:text-white py-4 font-mono text-xl font-bold shadow-sm transition-all group-hover:bg-white/80 dark:group-hover:bg-black/30">
                        </div>
                        <div class="pt-6 text-gray-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg></div>
                        <div class="flex-1 relative group">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Fim</label>
                            <input type="time" x-model="form.horario_fim" required class="w-full text-center rounded-2xl bg-white/50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:text-white py-4 font-mono text-xl font-bold shadow-sm transition-all group-hover:bg-white/80 dark:group-hover:bg-black/30">
                        </div>
                    </div>

                    {{-- BOTÃO SALVAR --}}
                    <button type="submit" 
                        :disabled="loading"
                        class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-70 disabled:cursor-not-allowed text-white font-bold py-4 rounded-2xl shadow-xl shadow-blue-600/20 active:scale-[0.98] transition-all flex justify-center items-center gap-2 group mb-4">
                        
                        <span x-show="!loading" x-text="isEditing ? 'Salvar Alterações' : 'Adicionar à Grade'"></span>
                        <span x-show="!loading && !isEditing">
                            <div class="bg-white/20 p-1 rounded-full group-hover:rotate-90 transition-transform"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg></div>
                        </span>
                        
                        {{-- Loading Spinner --}}
                        <svg x-cloak x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>

                    {{-- Link IA (Só mostra se não estiver editando) --}}
                    <div x-show="!isEditing">
                        <div class="relative py-2">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="px-2 bg-white/60 dark:bg-gray-900 text-xs text-gray-400 font-bold uppercase tracking-wider backdrop-blur-xl">Ou se preferir</span>
                            </div>
                        </div>

                        <a href="{{ route('grade.importar.view') }}" class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/10 dark:to-amber-900/10 hover:from-yellow-100 hover:to-amber-100 dark:hover:from-yellow-900/20 dark:hover:to-amber-900/20 text-yellow-700 dark:text-yellow-500 font-bold py-3.5 rounded-2xl border border-yellow-200 dark:border-yellow-800 transition-all active:scale-[0.98] group mt-2">
                            <svg class="w-5 h-5 animate-pulse group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                            </svg>
                            <span>Usar IA para montar grade</span>
                        </a>
                    </div>
                </form>
            </div>

            {{-- LISTAGEM DE HORÁRIOS (ID 'grade-list' É O ALVO DO REFRESH) --}}
            <div id="grade-list">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-5 px-2 flex items-center justify-between">
                    <span>Horários Definidos</span>
                    <span class="text-xs bg-white/50 dark:bg-gray-800/50 px-3 py-1 rounded-full text-gray-500 border border-white/20">{{ $horarios->count() }} aulas</span>
                </h3>
                
                @if($horarios->count() > 0)
                    <div class="space-y-4">
                        @foreach($horarios as $horario)
                            <div class="group bg-white/70 dark:bg-gray-900/70 backdrop-blur-md rounded-3xl p-5 shadow-sm border border-white/40 dark:border-gray-800 flex justify-between items-center transition-all hover:scale-[1.01] hover:shadow-md relative overflow-hidden">
                                
                                <div class="flex items-center gap-5">
                                    <div class="flex flex-col items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl text-blue-600 dark:text-blue-400 border border-blue-200/50 dark:border-gray-700 shadow-inner">
                                        <span class="text-[10px] font-bold uppercase opacity-60">Dia</span>
                                        <span class="text-xl font-extrabold leading-none tracking-tight">
                                            {{ substr($horario->dia_semana_texto, 0, 3) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-0.5">Horário</p>
                                        <p class="font-mono font-bold text-gray-800 dark:text-white text-xl tracking-tight">
                                            {{ \Carbon\Carbon::parse($horario->horario_inicio)->format('H:i') }} 
                                            <span class="text-gray-300 dark:text-gray-600 mx-1">-</span> 
                                            {{ \Carbon\Carbon::parse($horario->horario_fim)->format('H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 z-10">
                                    {{-- Botão Editar (Preenche o form) --}}
                                    <button @click="editItem({{ $horario->toJson() }})" class="p-3 text-blue-400 hover:text-blue-600 bg-blue-50/50 dark:bg-blue-900/10 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-2xl transition-colors border border-transparent hover:border-blue-200 dark:hover:border-blue-800" title="Editar Horário">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>

                                    {{-- Botão Excluir (AJAX) --}}
                                    <button @click="deleteItem({{ $horario->id }})" class="p-3 text-red-400 hover:text-red-600 bg-red-50/50 dark:bg-red-900/10 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-2xl transition-colors border border-transparent hover:border-red-200 dark:hover:border-red-800" title="Excluir Horário">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-gray-300/50 dark:border-gray-700/50 rounded-[2.5rem] bg-white/20 dark:bg-black/10 backdrop-blur-sm">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-gray-900 dark:text-white font-bold text-lg">Sem horários</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-[200px]">Adicione os horários das aulas acima.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- SCRIPTS ALPINE.JS --}}
    <script>
        function gradeForm() {
            return {
                loading: false,
                isEditing: false,
                editId: null,
                form: {
                    disciplina_id: '{{ $disciplina->id }}', // Garante que o ID vai no JSON
                    dia_semana: '1',
                    horario_inicio: '',
                    horario_fim: ''
                },

                // Preenche o formulário com dados existentes
                editItem(data) {
                    this.isEditing = true;
                    this.editId = data.id;
                    this.form.dia_semana = String(data.dia_semana);
                    this.form.horario_inicio = data.horario_inicio.substring(0, 5); // Corta os segundos HH:MM:SS -> HH:MM
                    this.form.horario_fim = data.horario_fim.substring(0, 5);
                    
                    // Rola suavemente até o topo do formulário
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                resetForm() {
                    this.isEditing = false;
                    this.editId = null;
                    this.form.dia_semana = '1';
                    this.form.horario_inicio = '';
                    this.form.horario_fim = '';
                },

                // Salvar (Criar ou Atualizar) via AJAX
                async save() {
                    this.loading = true;
                    
                    // Define a URL e o Método dependendo se é Edição ou Criação
                    // Obs: A rota de store usa a estrutura do controller atualizada
                    const url = this.isEditing 
                        ? `/grade/${this.editId}` 
                        : "{{ route('grade.store', $disciplina->id) }}"; // Fallback seguro
                        
                    const method = this.isEditing ? 'PUT' : 'POST';

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json', // Importante para o Controller retornar JSON
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();

                        if (response.ok) {
                            window.toastSuccess(data.message || 'Salvo com sucesso!');
                            this.resetForm();
                            await this.refreshGrid(); // Mágica: Atualiza a lista sem reload
                        } else {
                            // Erro de validação ou conflito (status 422)
                            window.toastError(data.message || 'Erro ao salvar.');
                        }
                    } catch (error) {
                        console.error(error);
                        window.toastError('Erro de conexão. Tente novamente.');
                    } finally {
                        this.loading = false;
                    }
                },

                // Excluir via AJAX
                deleteItem(id) {
                    window.swalTailwind.fire({
                        title: 'Apagar Horário?',
                        text: "Essa ação não pode ser desfeita.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, apagar',
                        cancelButtonText: 'Cancelar'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            try {
                                const response = await fetch(`/grade/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });

                                const data = await response.json();

                                if (response.ok) {
                                    window.toastSuccess(data.message || 'Removido com sucesso.');
                                    await this.refreshGrid();
                                } else {
                                    window.toastError('Erro ao remover.');
                                }
                            } catch (e) {
                                window.toastError('Erro de conexão.');
                            }
                        }
                    });
                },

                // FETCH & SWAP: Atualiza a tabela pegando o HTML novo do servidor
                async refreshGrid() {
                    try {
                        // Busca a mesma página em background
                        const res = await fetch(window.location.href);
                        const html = await res.text();
                        
                        // Transforma o texto em HTML manipulável
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Troca o conteúdo da div #grade-list atual pelo novo vindo do servidor
                        const newList = doc.getElementById('grade-list');
                        if (newList) {
                            document.getElementById('grade-list').innerHTML = newList.innerHTML;
                        }
                    } catch (e) {
                        console.error("Erro ao atualizar grid", e);
                    }
                }
            }
        }
    </script>
</x-app-layout>