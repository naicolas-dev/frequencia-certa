<div class="mb-12">

    <!-- Ícone de sucesso -->
    <div class="relative w-20 h-20 mx-auto mb-8">
        <div class="absolute inset-0 rounded-full 
            bg-emerald-500/20 dark:bg-emerald-500/30 
            blur-xl"></div>

        <div class="relative w-full h-full rounded-full 
            bg-emerald-500/10 dark:bg-emerald-500/20 
            flex items-center justify-center
            ring-8 ring-emerald-500/5">
            
            <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
    </div>

    <!-- Título -->
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-3">
        Tudo pronto!
    </h2>

    <p class="text-gray-500 dark:text-gray-400 text-sm max-w-xs mx-auto mb-8">
        Revisamos suas informações. Agora é só salvar para começar a usar o sistema.
    </p>

    <!-- Resumo -->
    <div class="space-y-3 mb-6">

        <!-- Estado -->
        <div class="flex items-center justify-between 
            rounded-2xl px-4 py-3
            bg-white/60 dark:bg-white/5 
            border border-white/20 dark:border-white/10
            backdrop-blur-md">

            <span class="text-xs uppercase tracking-wider font-semibold 
                text-gray-500 dark:text-gray-400">
                Estado
            </span>

            <span class="text-sm font-semibold text-gray-800 dark:text-white"
                  x-text="form.estado"></span>
        </div>

        <!-- Ano letivo -->
        <div class="rounded-2xl px-4 py-3
            bg-white/60 dark:bg-white/5 
            border border-white/20 dark:border-white/10
            backdrop-blur-md text-left">

            <span class="block text-xs uppercase tracking-wider font-semibold 
                text-gray-500 dark:text-gray-400 mb-2">
                Ano letivo
            </span>

            <div class="flex items-center justify-between text-sm font-medium 
                text-gray-800 dark:text-white">
                
                <span x-text="new Date(form.ano_letivo_inicio)
                    .toLocaleDateString('pt-BR', { timeZone: 'UTC' })"></span>

                <span class="text-gray-400 mx-2">—</span>

                <span x-text="new Date(form.ano_letivo_fim)
                    .toLocaleDateString('pt-BR', { timeZone: 'UTC' })"></span>
            </div>
        </div>

    </div>

    <!-- Texto de ação -->
    <p class="text-xs text-gray-400 dark:text-gray-500">
        Você pode alterar essas informações depois nas configurações.
    </p>
</div>

<!-- Ações -->
<div class="flex gap-3">

    <!-- Voltar -->
    <button type="button"
            @click="prevStep()"
            :disabled="loading"
            class="w-1/3 py-4 rounded-2xl font-semibold
                bg-gray-100/80 dark:bg-gray-800/80
                text-gray-600 dark:text-gray-300
                hover:bg-gray-200 dark:hover:bg-gray-700
                transition-all active:scale-95
                disabled:opacity-50">
        Voltar
    </button>

    <!-- Confirmar -->
    <button type="submit"
            :disabled="loading"
            class="w-2/3 py-4 rounded-2xl font-semibold
                bg-emerald-600 hover:bg-emerald-700
                text-white
                shadow-xl shadow-emerald-600/20
                transition-all active:scale-95
                disabled:bg-emerald-600/50
                disabled:cursor-not-allowed
                flex items-center justify-center gap-2">

        <!-- Normal -->
        <span x-show="!loading" class="flex items-center gap-2">
            Ir para o painel
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
        </span>

        <!-- Loading -->
        <span x-show="loading" class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5 text-white"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Salvando...
        </span>

    </button>

</div>
