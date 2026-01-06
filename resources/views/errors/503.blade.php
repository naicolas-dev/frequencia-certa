<x-error-layout>
    <div class="w-full max-w-md bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border border-white/20 dark:border-gray-700 shadow-xl rounded-2xl overflow-hidden relative">
        
        {{-- Faixa de Topo (Amarelo/Laranja) --}}
        <div class="h-2 bg-gradient-to-r from-yellow-400 to-orange-500"></div>

        <div class="p-8 text-center">
            {{-- Ícone e Número --}}
            <div class="relative inline-block mb-6">
                <div class="absolute inset-0 bg-yellow-500/20 blur-xl rounded-full"></div>
                <div class="relative bg-yellow-50 dark:bg-yellow-900/30 w-20 h-20 mx-auto rounded-2xl flex items-center justify-center text-yellow-600 dark:text-yellow-400 animate-pulse">
                    {{-- Ícone de Cone de Trânsito --}}
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="absolute -bottom-3 -right-3 bg-white dark:bg-gray-800 border-4 border-white dark:border-gray-800 rounded-full px-2 py-1 text-xs font-black text-gray-900 dark:text-white shadow-sm">
                    503
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Em Manutenção
            </h1>

            <p class="text-gray-500 dark:text-gray-400 mb-8 text-sm leading-relaxed">
                Estamos a fazer melhorias rápidas no sistema. Voltaremos ao ar em alguns instantes.
            </p>

            {{-- Botão de Verificar --}}
            <button onclick="window.location.reload()" class="block w-full py-3 px-4 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-xl transition-all shadow-lg shadow-yellow-500/20 active:scale-[0.98]">
                Verificar Agora
            </button>
        </div>
    </div>
</x-error-layout>