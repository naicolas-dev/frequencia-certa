<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Botão Voltar (Mobile) --}}
            <a href="{{ route('dashboard') }}"
                class="p-2 -ml-2 rounded-full hover:bg-white/50 dark:hover:bg-gray-800/50 transition lg:hidden">
                <svg class="w-6 h-6 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                Meu Perfil
            </h2>
        </div>
    </x-slot>

    <div class="py-6 pb-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- 1. Card do Usuário --}}
            <div
                class="flex items-center gap-5 p-6 rounded-3xl bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl border border-white/20 dark:border-gray-800 shadow-sm">
                <div
                    class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg shadow-blue-500/20">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                </div>
            </div>

            {{-- 2. Relatórios --}}
            <div
                class="p-6 sm:p-8 bg-white/70 dark:bg-gray-900/70 backdrop-blur-md shadow-sm rounded-[2rem] border border-white/20 dark:border-gray-800">
                <section>
                    <header class="mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Relatório da Frequência</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Baixe o relatório da sua presença
                            informada em PDF</p>
                    </header>

                    <div class="flex items-center">
                        <a href="{{ route('relatorio.baixar') }}" download="relatorio-frequencia.pdf"
                            class="w-full sm:w-auto px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-xl shadow-indigo-500/30 transition transform active:scale-[0.98] text-sm flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Baixar
                        </a>
                    </div>
                </section>
            </div>

            {{-- 3. Informações Pessoais --}}
            <div
                class="p-6 sm:p-8 bg-white/70 dark:bg-gray-900/70 backdrop-blur-md shadow-sm rounded-[2rem] border border-white/20 dark:border-gray-800">
                <section>
                    <header class="mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Informações Pessoais</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atualize seu nome e email de contato.
                        </p>
                    </header>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('patch')

                        {{-- Nome --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Nome
                                Completo</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-black/20 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 py-4 px-5 transition-all">
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        {{-- Email --}}
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-black/20 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 py-4 px-5 transition-all">
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        {{-- Estado (UF) --}}
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Estado
                                (UF)</label>
                            <div class="relative">
                                <select name="estado"
                                    class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-black/20 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 py-4 px-5 transition-all appearance-none">
                                    @foreach($estados as $sigla => $nome)
                                        <option value="{{ $sigla }}" {{ old('estado', $user->estado) === $sigla ? 'selected' : '' }}>
                                            {{ $nome }} - {{ $sigla }}
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                        </div>

                        {{-- Datas --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Início
                                    das Aulas</label>
                                <input type="date" name="ano_letivo_inicio"
                                    value="{{ old('ano_letivo_inicio', optional($user->ano_letivo_inicio)->format('Y-m-d')) }}"
                                    required
                                    class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-black/20 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 py-4 px-5 transition-all">
                                <x-input-error class="mt-2" :messages="$errors->get('ano_letivo_inicio')" />
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Término
                                    das Aulas</label>
                                <input type="date" name="ano_letivo_fim"
                                    value="{{ old('ano_letivo_fim', optional($user->ano_letivo_fim)->format('Y-m-d')) }}"
                                    required
                                    class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-black/20 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 py-4 px-5 transition-all">
                                <x-input-error class="mt-2" :messages="$errors->get('ano_letivo_fim')" />
                            </div>
                        </div>

                        {{-- Botão Salvar --}}
                        <div class="flex items-center gap-4 pt-2">
                            <button type="submit"
                                class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-xl shadow-blue-500/30 transition transform active:scale-[0.98] text-sm">
                                Salvar Alterações
                            </button>
                            @if (session('status') === 'profile-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition
                                    x-init="setTimeout(() => show = false, 2000)"
                                    class="text-sm text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Salvo!
                                </p>
                            @endif
                        </div>
                    </form>
                </section>
            </div>

            {{-- 4. Segurança --}}
            <div
                class="p-6 sm:p-8 bg-white/70 dark:bg-gray-900/70 backdrop-blur-md shadow-sm rounded-[2rem] border border-white/20 dark:border-gray-800">
                <section>
                    <header class="mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Segurança</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atualize sua senha periodicamente.</p>
                    </header>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                        @csrf
                        @method('put')

                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Senha
                                Atual</label>
                            <input type="password" name="current_password"
                                class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-black/20 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 py-4 px-5">
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Nova
                                    Senha</label>
                                <input type="password" name="password"
                                    class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-black/20 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 py-4 px-5">
                                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Confirmar
                                    Senha</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-black/20 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 py-4 px-5">
                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')"
                                    class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-2">
                            <button type="submit"
                                class="px-8 py-4 bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 text-white font-bold rounded-2xl shadow-lg transition transform active:scale-[0.98] text-sm">
                                Atualizar Senha
                            </button>
                            @if (session('status') === 'password-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition
                                    x-init="setTimeout(() => show = false, 2000)"
                                    class="text-sm text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Senha alterada!
                                </p>
                            @endif
                        </div>
                    </form>
                </section>
            </div>

            {{-- 5. Onboarding --}}
            <div
                class="p-6 sm:p-8 bg-white/70 dark:bg-gray-900/70 backdrop-blur-md shadow-sm rounded-[2rem] border border-white/20 dark:border-gray-800">
                <section>
                    <header class="mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Tour Guiado</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Deseja rever como o aplicativo
                            funciona?</p>
                    </header>

                    <div class="flex items-center gap-4">
                        <form method="POST" action="{{ route('tour.reset') }}">
                            @csrf
                            <button type="submit"
                                class="px-8 py-4 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-bold rounded-2xl hover:bg-blue-200 dark:hover:bg-blue-900/50 transition transform active:scale-[0.98] text-sm flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reiniciar Tour
                            </button>
                        </form>

                        @if (session('status') === 'tour-reset')
                            <p x-data="{ show: true }" x-show="show" x-transition
                                x-init="setTimeout(() => show = false, 3000)"
                                class="text-sm text-blue-600 dark:text-blue-400 font-bold">
                                Pronto! Vá para o Painel para ver o tour.
                            </p>
                        @endif
                    </div>
                </section>
            </div>

            {{-- 5. BOTÃO DE SAIR (MOBILE) --}}
            <div class="lg:hidden">
                <form method="POST" data-confirm="Tem certeza que deseja sair?" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full p-5 rounded-[2rem] bg-white/70 dark:bg-gray-900/70 backdrop-blur-md border border-gray-200 dark:border-gray-800 shadow-sm flex items-center justify-center gap-3 text-gray-700 dark:text-gray-200 font-bold hover:bg-gray-50 dark:hover:bg-gray-800 transition transform active:scale-[0.98]">
                        <div
                            class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-red-500 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <span>Sair da Conta</span>
                    </button>
                </form>
            </div>

            {{-- 6. Excluir Conta (LÓGICA NATIVA ATUALIZADA) --}}
            <div class="p-6 sm:p-8 bg-red-50/80 dark:bg-red-900/20 backdrop-blur-md shadow-sm rounded-[2rem] border border-red-100 dark:border-red-900/30"
                x-data="{ 
                    confirmDeletion: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }},
                    
                    // Variáveis Swipe
                    swipeDistance: 0, 
                    touchStartY: 0,
                    isDragging: false,

                    init() {
                        // Scroll Lock do Body
                        $watch('confirmDeletion', value => {
                            if(value) {
                                document.body.classList.add('overflow-hidden');
                                this.swipeDistance = 0;
                            } else {
                                document.body.classList.remove('overflow-hidden');
                            }
                        });
                    },

                    // Touch Handlers
                    handleTouchStart(e) {
                        if (window.innerWidth >= 640) return; // Bloqueia PC
                        this.touchStartY = e.changedTouches[0].screenY;
                        this.isDragging = true;
                    },

                    handleTouchMove(e) {
                        if (!this.isDragging) return;
                        let currentY = e.changedTouches[0].screenY;
                        let distance = currentY - this.touchStartY;
                        if (distance > 0) this.swipeDistance = distance;
                    },

                    handleTouchEnd() {
                        if (!this.isDragging) return;
                        this.isDragging = false;

                        if (this.swipeDistance > 100) {
                            // Swipe Out Physics
                            this.swipeDistance = window.innerHeight; 
                            setTimeout(() => { 
                                this.confirmDeletion = false; 
                            }, 200);
                        } else {
                            // Snap Back
                            this.swipeDistance = 0;
                        }
                    },

                    // Close Padrão
                    closeModal() {
                        this.swipeDistance = 0;
                        this.confirmDeletion = false;
                    }
                 }">

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-red-700 dark:text-red-400">Excluir Conta</h2>
                        <p class="text-sm text-red-600/70 dark:text-red-300/70 mt-1 font-medium">Ação irreversível.
                            Todos os dados serão perdidos.</p>
                    </div>

                    <button @click="confirmDeletion = true"
                        class="w-full sm:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-500/20 transition transform active:scale-95 text-sm whitespace-nowrap">
                        Excluir minha conta
                    </button>
                </div>

                <template x-teleport="body">
                    <div x-show="confirmDeletion" style="display: none;"
                        class="fixed inset-0 z-[99] flex items-end md:items-center justify-center p-0 md:p-4">

                        <div x-show="confirmDeletion" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" @click="closeModal()"
                            class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm">
                        </div>

                        <div x-show="confirmDeletion" @touchstart="handleTouchStart($event)"
                            @touchmove="handleTouchMove($event)" @touchend="handleTouchEnd()"
                            :style="swipeDistance !== 0 ? 'transform: translateY(' + swipeDistance + 'px); transition: ' + (isDragging ? 'none' : 'transform 0.3s ease-out') : ''"
                            x-transition:enter="transition ease-out duration-300 transform"
                            x-transition:enter-start="translate-y-full md:opacity-0 md:scale-95 md:translate-y-0"
                            x-transition:enter-end="translate-y-0 md:opacity-100 md:scale-100"
                            x-transition:leave="transition ease-in duration-200 transform"
                            x-transition:leave-start="translate-y-0 md:opacity-100 md:scale-100"
                            x-transition:leave-end="translate-y-full md:opacity-0 md:scale-95 md:translate-y-0"
                            class="relative w-full md:max-w-lg bg-white dark:bg-gray-900 rounded-t-[2.5rem] md:rounded-[2.5rem] shadow-2xl border-t border-white/20 dark:border-gray-700 max-h-[85vh] overflow-y-auto">

                            <div class="sticky top-0 bg-white dark:bg-gray-900 pt-4 pb-2 z-10 flex justify-center md:hidden"
                                @click="closeModal()">
                                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-700 rounded-full"></div>
                            </div>

                            <div class="p-8 pt-2 md:p-10 md:pt-10">
                                <div class="text-center md:text-left mb-8">
                                    <div
                                        class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-2xl flex items-center justify-center mx-auto md:mx-0 mb-5 shadow-inner">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">Excluir
                                        conta permanentemente?</h2>
                                    <p class="mt-2 text-base text-gray-500 dark:text-gray-400 leading-relaxed">
                                        Esta ação é <strong>irreversível</strong>. Todos os seus dados, frequências e
                                        histórico serão apagados para sempre.
                                    </p>
                                </div>

                                <form method="post" action="{{ route('profile.destroy') }}" class="space-y-6">
                                    @csrf
                                    @method('delete')

                                    <div>
                                        <label for="password_del"
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Confirme
                                            sua senha</label>
                                        <input id="password_del" name="password" type="password"
                                            placeholder="Senha atual"
                                            class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-black/30 dark:text-white py-4 px-5 focus:ring-4 focus:ring-red-500/20 focus:border-red-500 text-lg transition-all"
                                            autofocus />
                                        <x-input-error :messages="$errors->userDeletion->get('password')"
                                            class="mt-2" />
                                    </div>

                                    <div class="flex flex-col gap-3 pt-2">
                                        <button type="submit"
                                            class="w-full py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-2xl shadow-xl shadow-red-500/20 transition transform active:scale-[0.98] text-lg flex items-center justify-center gap-2">
                                            <span>Sim, excluir permanentemente</span>
                                        </button>

                                        <button type="button" @click="closeModal()"
                                            class="w-full py-4 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-bold rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-700 transition active:scale-[0.98]">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

        </div>
    </div>
</x-app-layout>