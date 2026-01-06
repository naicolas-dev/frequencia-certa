<x-error-layout>
    <div class="w-full max-w-md bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border border-white/20 dark:border-gray-700 shadow-xl rounded-2xl overflow-hidden relative">
        
        {{-- Faixa de Topo (Indigo para Ciano - Gradiente Frio) --}}
        <div class="h-2 bg-gradient-to-r from-indigo-500 to-cyan-400"></div>

        <div class="p-8 text-center">
            
            {{-- Ícone e Número --}}
            <div class="relative inline-block mb-6">
                {{-- Blur de fundo para dar o efeito de "Glow" --}}
                <div class="absolute inset-0 bg-indigo-500/30 blur-2xl rounded-full"></div>
                
                {{-- Container do Ícone (Mais claro no dark mode para contraste) --}}
                <div class="relative bg-indigo-50 dark:bg-indigo-500/20 w-24 h-24 mx-auto rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-300">
                    
                    {{-- Ícone de Digital (Fingerprint) - Traço mais grosso (stroke-2) --}}
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.2-2.858.591-4.161m6.111 5.09c.883-2.89 4.51-2.865 5.41-.045a2.5 2.5 0 01-1 3.5m-6 3.5c0-1.933 1.343-3.5 3-3.5 1.577 0 2.862 1.42 2.986 3.238" />
                    </svg>

                </div>
                
                {{-- Badge do Número --}}
                <div class="absolute -bottom-2 -right-2 bg-white dark:bg-gray-800 border-4 border-white dark:border-gray-800 rounded-full px-3 py-1 text-sm font-black text-gray-900 dark:text-white shadow-sm">
                    401
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Autenticação Necessária
            </h1>

            <p class="text-gray-500 dark:text-gray-400 mb-8 text-sm leading-relaxed">
                Não conseguimos identificar quem é você. <br class="hidden sm:block">Por favor, conecte-se para continuar.
            </p>

            <div class="space-y-3">
                {{-- Botão Principal: Login --}}
                <a href="{{ route('login') }}" class="block w-full py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-indigo-600/20 active:scale-[0.98] transform hover:-translate-y-0.5">
                    Fazer Login
                </a>
                
                {{-- Botão Secundário: Voltar --}}
                <a href="{{ url('/') }}" class="block w-full py-3 px-4 bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700/50 text-gray-600 dark:text-gray-300 font-semibold rounded-xl transition-colors">
                    Voltar ao Início
                </a>
            </div>
        </div>
    </div>
</x-error-layout>