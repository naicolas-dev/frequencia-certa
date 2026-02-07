<div class="mb-6">
    <div
        class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mb-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>

    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Defina seu Ano Letivo</h2>
    <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm">
        Informe quando as aulas começam e terminam.
    </p>

    <div class="space-y-4 text-left">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase ml-1 mb-1">Início das Aulas</label>
            <input type="date" x-model="form.ano_letivo_inicio" @input="error = ''"
                class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white py-3 px-4 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase ml-1 mb-1">Término das Aulas</label>
            <input type="date" x-model="form.ano_letivo_fim" @input="error = ''"
                class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white py-3 px-4 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <p x-show="error" x-text="error" x-transition
            class="text-red-500 text-sm font-bold mt-2 text-center animate-pulse"></p>
    </div>
</div>

<div class="flex gap-3 mt-6">
    <button type="button" @click="prevStep()"
        class="w-1/3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold text-lg py-4 rounded-2xl transition active:scale-95">
        Voltar
    </button>

    <button type="button" @click="nextStep()"
        class="w-2/3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-blue-600/20 transition active:scale-95">
        Próximo
    </button>
</div>