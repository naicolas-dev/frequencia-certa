<x-error-layout>
    <div class="w-full max-w-md bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border border-white/20 dark:border-gray-700 shadow-xl rounded-2xl overflow-hidden relative">
        
        {{-- Faixa de Topo (Cinza/Slate - Representando "Desligado") --}}
        <div class="h-2 bg-gradient-to-r from-gray-400 to-slate-500"></div>

        <div class="p-8 text-center">
            
            {{-- Ícone e Badge --}}
            <div class="relative inline-block mb-6">
                {{-- Glow Sutil --}}
                <div class="absolute inset-0 bg-gray-500/20 blur-xl rounded-full"></div>
                
                <div class="relative bg-gray-100 dark:bg-gray-700/30 w-24 h-24 mx-auto rounded-2xl flex items-center justify-center text-gray-500 dark:text-gray-400">
                    {{-- Ícone de WiFi Cortado / Nuvem Offline --}}
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M12 18h.01M5.8 5.8a14.065 14.065 0 0112.4 0M8.5 8.5a10.034 10.034 0 017 0M10.8 10.8a6.012 6.012 0 012.4 0" />
                    </svg>
                </div>
                
                {{-- Badge "OFF" --}}
                <div class="absolute -bottom-2 -right-2 bg-white dark:bg-gray-800 border-4 border-white dark:border-gray-800 rounded-full px-3 py-1 text-sm font-black text-gray-500 dark:text-gray-300 shadow-sm">
                    OFF
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Você está offline
            </h1>

            <p class="text-gray-500 dark:text-gray-400 mb-8 text-sm leading-relaxed">
                Parece que sua conexão caiu. Verifique seu Wi-Fi ou dados móveis para continuar navegando.
            </p>

            <div class="space-y-3">
                {{-- Botão Principal: Tentar Reconectar --}}
                <button onclick="window.location.reload()" 
                    class="block w-full py-3.5 px-4 bg-gray-700 hover:bg-gray-800 dark:bg-gray-600 dark:hover:bg-gray-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-gray-600/20 active:scale-[0.98] transform hover:-translate-y-0.5">
                    Tentar Reconectar
                </button>
            </div>
        </div>
    </div>
</x-error-layout>