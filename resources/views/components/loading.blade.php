<div id="page-loader"
    {{ $attributes->merge(['class' => 'fixed inset-0 z-[9999] bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-[#407aef] to-[#2D6AE6] flex items-center justify-center flex-col transition-opacity duration-500 dark:from-[#3572e6] dark:to-[#1a4db3]']) }}
    @if($errors->any()) style="display: none;" @endif>

    <div class="loader-content flex flex-col items-center gap-4">
        
        <div class="h-1.5 w-64 bg-black/20 rounded-full overflow-hidden relative backdrop-blur-sm shadow-inner">
            
            <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-transparent via-white to-transparent animate-infinite-flow"></div>
            
            <div class="absolute inset-0 h-full w-full bg-white/10"></div>
        </div>
        
    </div>

    <style>
        /* Animação de fluxo contínuo */
        .animate-infinite-flow {
            transform: translateX(-100%);
            animation: flow 1.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            width: 50%; /* O feixe de luz tem 50% da largura da barra */
            filter: drop-shadow(0 0 5px rgba(255,255,255,0.8)); /* Brilho neon */
        }

        @keyframes flow {
            0% { 
                transform: translateX(-200%); /* Começa bem antes da esquerda */
                opacity: 0;
            }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { 
                transform: translateX(400%); /* Termina bem depois da direita */
                opacity: 0;
            }
        }
    </style>
</div>