<div id="cookie-banner" x-data="{ 
        show: false,
        init() {
            setTimeout(() => {
                // ADICIONADO: Verifica se o tour NÃO está ativo antes de mostrar
                if (!localStorage.getItem('cookie_seen') && !document.body.classList.contains('driver-active')) {
                    this.show = true;
                    // TRAVA A ROLAGEM DA TELA (Congela o fundo)
                    document.body.style.overflow = 'hidden'; 
                }
            }, 200);
        },
        accept() {
            localStorage.setItem('cookie_seen', 'true'); 
            this.show = false;
            // DESTRAVA A TELA
            document.body.style.overflow = ''; 
        }
     }"
     {{-- 👇 EVENTOS NECESSÁRIOS ADICIONADOS AQUI 👇 --}}
     @tour-starting.window="show = false; document.body.style.overflow = '';" 
     @tour-finished.window="if(!localStorage.getItem('cookie_seen')) { setTimeout(() => { show = true; document.body.style.overflow = 'hidden'; }, 800) }"
     
     x-init="init()"
     style="display: none;"
     x-show="show"
     class="relative z-[100]"> {{-- Z-Index altíssimo para ficar acima de tudo --}}

    <div x-show="show"
         x-transition:enter="transition ease-out duration-700"
         x-transition:enter-start="opacity-0 backdrop-blur-none"
         x-transition:enter-end="opacity-100 backdrop-blur-sm"
         x-transition:leave="transition ease-in duration-500"
         x-transition:leave-start="opacity-100 backdrop-blur-sm"
         x-transition:leave-end="opacity-0 backdrop-blur-none"
         class="fixed inset-0 bg-gray-900/60 backdrop-blur-[4px]">
    </div>

    <div class="fixed inset-0 flex items-end justify-center px-4 pb-6 md:pb-10 pointer-events-none">
        
        <div x-show="show"
             x-transition:enter="transition ease-out duration-700 delay-150" {{-- Delay para o fundo aparecer antes --}}
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="pointer-events-auto w-full md:max-w-2xl bg-white/90 dark:bg-gray-900/90 backdrop-blur-2xl border border-white/40 dark:border-gray-700 p-6 md:p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.12)] dark:shadow-black/50 ring-1 ring-black/5 flex flex-col md:flex-row items-center gap-6 md:gap-8">
            
            <div class="flex items-center gap-5 flex-1 text-center md:text-left">
                
                {{-- ÍCONE DESKTOP (Cookie) --}}
                <div class="shrink-0 p-4 bg-amber-100/50 dark:bg-amber-900/20 text-amber-500 rounded-2xl hidden md:block animate-bounce-slow">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5" />
                        <path d="M8.5 8.5h.01" />
                        <path d="M16 15h.01" />
                        <path d="M9 16h.01" />
                        <path d="M12 11.5h.01" />
                    </svg>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-1 flex items-center justify-center md:justify-start gap-2">
                        
                        {{-- ÍCONE MOBILE (Cookie) --}}
                        <svg class="w-6 h-6 md:hidden text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5" />
                            <path d="M8.5 8.5h.01" />
                            <path d="M16 15h.01" />
                            <path d="M9 16h.01" />
                            <path d="M12 11.5h.01" />
                        </svg>

                        <span>Privacidade e Cookies</span>
                    </h3>
                    
                    <p class="text-sm md:text-base text-gray-600 dark:text-gray-400 leading-snug">
                        Para que você não precise fazer login toda hora e seus dados fiquem seguros, precisamos usar alguns cookies essenciais.
                    </p>
                </div>
            </div>

            {{-- BOTÃO DE AÇÃO --}}
            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto shrink-0">
                <button @click="accept()" 
                        class="w-full md:w-auto px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-600/20 active:scale-[0.98] transition-all transform hover:-translate-y-0.5 whitespace-nowrap">
                    Entendi e Aceito
                </button>
            </div>

        </div>
    </div>
</div>