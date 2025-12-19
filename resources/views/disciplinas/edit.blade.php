<x-app-layout>
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob dark:bg-blue-900/20"></div>
        <div class="absolute -bottom-32 right-1/3 w-96 h-96 bg-purple-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000 dark:bg-purple-900/20"></div>
    </div>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="group p-2 -ml-2 rounded-full hover:bg-white/50 dark:hover:bg-gray-800/50 transition">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    Editar Matéria
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6 pb-32">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            
            <form action="{{ route('disciplinas.update', $disciplina->id) }}" method="POST" x-data="{ color: '{{ $disciplina->cor }}' }">
                @csrf
                @method('PUT')

                <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2rem] border border-white/20 dark:border-gray-800 p-6 sm:p-8 shadow-sm space-y-8">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 ml-1">Nome da Matéria</label>
                        <div class="relative">
                            <input type="text" name="nome" value="{{ $disciplina->nome }}" required autofocus
                                class="w-full text-lg font-semibold bg-white/50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 px-5 py-4 placeholder-gray-400 dark:text-white transition-all shadow-sm"
                            >
                            <div class="absolute right-4 top-4 text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 ml-1">Cor da Etiqueta</label>
                        <div class="grid grid-cols-5 gap-4">
                            @php
                                $cores = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#6366F1', '#14B8A6', '#F97316', '#64748B'];
                            @endphp
                            @foreach($cores as $c)
                                <label class="relative flex items-center justify-center cursor-pointer group">
                                    <input type="radio" name="cor" value="{{ $c }}" class="sr-only" x-model="color">
                                    <div class="w-12 h-12 rounded-2xl shadow-sm transition-all duration-300 flex items-center justify-center" 
                                         style="background-color: {{ $c }}"
                                         :class="color === '{{ $c }}' ? 'ring-4 ring-offset-2 ring-gray-300 dark:ring-gray-600 scale-110 shadow-lg' : 'hover:scale-105 opacity-80 hover:opacity-100'"
                                    >
                                        <svg x-show="color === '{{ $c }}'" class="w-6 h-6 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="fixed bottom-0 left-0 right-0 p-4 pb-6 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border-t border-gray-100 dark:border-gray-800 z-30 flex justify-center">
                    <div class="w-full max-w-md">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-blue-600/30 transition transform active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>Atualizar</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>