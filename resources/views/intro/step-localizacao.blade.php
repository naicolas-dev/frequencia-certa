<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Selecione o Estado da sua instituição</h2>
    <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm">
        Usaremos isso apenas para configurar seu calendário acadêmico.
    </p>

    <div class="space-y-4 text-left">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase ml-1 mb-1">Estado</label>
            <select x-model="form.estado" @change="error = ''"
                class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white py-3 px-4 focus:ring-blue-500 focus:border-blue-500">
                <option value="" disabled selected>Selecione...</option>
                @foreach(config('estados') as $sigla => $nome)
                    <option value="{{ $sigla }}">{{ $nome }}</option>
                @endforeach
            </select>
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