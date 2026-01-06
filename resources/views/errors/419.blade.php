<x-error-layout>
    <div class="w-full max-w-md bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border border-white/20 dark:border-gray-700 shadow-xl rounded-2xl overflow-hidden relative">
        
        <div class="h-2 bg-gradient-to-r from-amber-400 to-orange-500"></div>

        <div class="p-8 text-center">
            <div class="relative inline-block mb-6">
                <div class="absolute inset-0 bg-amber-500/20 blur-xl rounded-full"></div>
                <div class="relative bg-amber-50 dark:bg-amber-900/30 w-20 h-20 mx-auto rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="absolute -bottom-3 -right-3 bg-white dark:bg-gray-800 border-4 border-white dark:border-gray-800 rounded-full px-2 py-1 text-xs font-black text-gray-900 dark:text-white shadow-sm">
                    419
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Sessão Expirada
            </h1>

            <p class="text-gray-500 dark:text-gray-400 mb-8 text-sm leading-relaxed">
                Você ficou inativo por muito tempo. Por segurança, por favor recarregue a página e tente novamente.
            </p>

            <button onclick="window.location.reload()" class="block w-full py-3 px-4 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl transition-all shadow-lg shadow-amber-500/20 active:scale-[0.98]">
                Recarregar Página
            </button>
        </div>
    </div>
</x-error-layout>