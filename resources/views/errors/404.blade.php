<x-error-layout>
    <div class="w-full max-w-md bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border border-white/20 dark:border-gray-700 shadow-xl rounded-2xl overflow-hidden relative">
        
        {{-- Faixa de Topo (Visual) --}}
        <div class="h-2 bg-gradient-to-r from-blue-500 to-purple-500"></div>

        <div class="p-8 text-center">
            {{-- Ícone e Número Integrados --}}
            <div class="relative inline-block mb-6">
                <div class="absolute inset-0 bg-blue-500/20 blur-xl rounded-full"></div>
                <div class="relative bg-blue-50 dark:bg-blue-900/30 w-20 h-20 mx-auto rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                </div>
                <div class="absolute -bottom-3 -right-3 bg-white dark:bg-gray-800 border-4 border-white dark:border-gray-800 rounded-full px-2 py-1 text-xs font-black text-gray-900 dark:text-white shadow-sm">
                    404
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Página não encontrada
            </h1>

            <p class="text-gray-500 dark:text-gray-400 mb-8 text-sm leading-relaxed">
                O endereço que você digitou não existe ou foi movido. Verifique a URL ou volte para o início.
            </p>

            <div class="space-y-3">
                <a href="{{ route('dashboard') }}" class="block w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-600/20 active:scale-[0.98]">
                    Voltar ao Dashboard
                </a>
                
                <button onclick="history.back()" class="block w-full py-3 px-4 bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700/50 text-gray-600 dark:text-gray-300 font-semibold rounded-xl transition-colors">
                    Voltar página anterior
                </button>
            </div>
        </div>
    </div>
</x-error-layout>