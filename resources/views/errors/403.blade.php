<x-error-layout>
    <div class="w-full max-w-md bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border border-white/20 dark:border-gray-700 shadow-xl rounded-2xl overflow-hidden relative">
        
        <div class="h-2 bg-gradient-to-r from-red-500 to-orange-500"></div>

        <div class="p-8 text-center">
            <div class="relative inline-block mb-6">
                <div class="absolute inset-0 bg-red-500/20 blur-xl rounded-full"></div>
                <div class="relative bg-red-50 dark:bg-red-900/30 w-20 h-20 mx-auto rounded-2xl flex items-center justify-center text-red-600 dark:text-red-400">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div class="absolute -bottom-3 -right-3 bg-white dark:bg-gray-800 border-4 border-white dark:border-gray-800 rounded-full px-2 py-1 text-xs font-black text-gray-900 dark:text-white shadow-sm">
                    403
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Acesso Restrito
            </h1>

            <p class="text-gray-500 dark:text-gray-400 mb-8 text-sm leading-relaxed">
                Você não tem permissão para visualizar esta página. Se acredita que isso é um erro, contate o administrador.
            </p>

            <a href="{{ route('dashboard') }}" class="block w-full py-3 px-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-semibold rounded-xl transition-all hover:opacity-90 active:scale-[0.98]">
                Ir para o Dashboard
            </a>
        </div>
    </div>
</x-error-layout>