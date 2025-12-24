<div class="mb-8">
    <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-600 dark:text-emerald-400">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    </div>
    
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Tudo Pronto! 🎉</h2>
    
    <div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-2xl p-4 mb-2">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold tracking-wider mb-1">Localização Definida</p>
        <p class="text-lg font-medium text-gray-800 dark:text-white">
            <span x-text="form.estado"></span>
        </p>
    </div>
    
    <p class="text-gray-500 dark:text-gray-400 text-sm">
        Clique abaixo para salvar e acessar seu painel.
    </p>
</div>

<div class="flex gap-3">
    
    <button type="button" 
            @click="prevStep()" 
            class="w-1/3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold text-lg py-4 rounded-2xl transition active:scale-95 disabled:opacity-50" 
            :disabled="loading">
        Voltar
    </button>

    <button type="submit" 
        :disabled="loading" 
        class="w-2/3 bg-emerald-600 hover:bg-emerald-700 disabled:bg-emerald-600/50 disabled:cursor-not-allowed text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-emerald-600/20 transition active:scale-95 flex items-center justify-center gap-2">
        
        <span x-show="!loading" class="flex items-center gap-2">
            Ir para o Painel
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </span>

        <span x-show="loading" class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Salvando...
        </span>
    </button>
</div>