<div id="notification-banner" 
     x-data="{ 
        show: false,
        checkAndShow() {
            // 1. Verifica se cookies JÁ foram aceitos
            const cookiesAceitos = localStorage.getItem('cookie_seen');
            // 2. Verifica se este banner JÁ foi dispensado ou aceito
            const bannerVisto = localStorage.getItem('notification_banner_seen');
            // 3. Verifica se o navegador suporta e se ainda está 'default' (não perguntado)
            const suportaPush = ('Notification' in window) && Notification.permission === 'default';
            // 4. Verifica se o Tour está ativo
            const tourAtivo = document.body.classList.contains('driver-active');

            if (cookiesAceitos && !bannerVisto && suportaPush && !tourAtivo) {
                this.show = true;
            }
        },
        init() {
            // Checa 2 segundos depois de carregar
            setTimeout(() => this.checkAndShow(), 2000);

            // Fica vigiando caso o usuário aceite os cookies agora
            setInterval(() => {
                if (!this.show) this.checkAndShow();
            }, 800);
        },
        async accept() {
            // Chama a função global do app.js
            const sucesso = await window.pedirPermissaoNotificacao();
            if(sucesso) {
                localStorage.setItem('notification_banner_seen', 'true');
                this.show = false;
            } else {
                // Se o usuário bloqueou no navegador, não mostramos mais
                if(Notification.permission === 'denied') {
                    localStorage.setItem('notification_banner_seen', 'true');
                    this.show = false;
                }
            }
        },
        dismiss() {
            localStorage.setItem('notification_banner_seen', 'true');
            this.show = false;
        }
     }"
     @tour-finished.window="setTimeout(() => checkAndShow(), 1000)"
     x-init="init()"
     style="display: none;"
     x-show="show"
     class="relative z-[90]"> {{-- Z-Index menor que o Cookie (100) --}}

    {{-- Container Flutuante na Base --}}
    <div class="fixed inset-0 flex items-end justify-center px-4 pb-6 md:pb-6 pointer-events-none">
        
        <div x-show="show"
             x-transition:enter="transition ease-out duration-700 delay-150"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="pointer-events-auto w-full md:max-w-xl bg-white/90 dark:bg-gray-900/90 backdrop-blur-2xl border border-white/40 dark:border-gray-700 p-5 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.12)] dark:shadow-black/50 ring-1 ring-black/5 flex flex-col md:flex-row items-center gap-5">
            
            <div class="flex items-center gap-5 flex-1 text-center md:text-left">
                
                {{-- ÍCONE (Sino / Roxo) --}}
                <div class="shrink-0 p-3 bg-indigo-100/50 dark:bg-indigo-900/20 text-indigo-500 rounded-2xl hidden md:block animate-pulse">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-1 flex items-center justify-center md:justify-start gap-2">
                        {{-- Ícone Mobile --}}
                        <svg class="w-6 h-6 md:hidden text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span>Fique por dentro!</span>
                    </h3>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-snug">
                        Ative as notificações para receber lembretes de chamadas e alertas.
                    </p>
                </div>
            </div>

            {{-- BOTÕES DE AÇÃO --}}
            <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto shrink-0">
                <button @click="accept()" 
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/20 active:scale-[0.98] transition-all transform whitespace-nowrap">
                    Ativar
                </button>
                <button @click="dismiss()" 
                        class="px-4 py-3 bg-transparent hover:bg-gray-100 dark:hover:bg-white/5 text-gray-500 dark:text-gray-400 font-semibold rounded-xl transition-colors whitespace-nowrap">
                    Agora não
                </button>
            </div>

        </div>
    </div>
</div>