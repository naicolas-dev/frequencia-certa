<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuração Inicial - {{ config('app.name') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body class="antialiased bg-gray-50 dark:bg-black text-gray-900 dark:text-white min-h-screen flex items-center justify-center relative overflow-hidden font-sans"
    x-data="{ 
        step: 1,
        form: { estado: '', ano_letivo_inicio: '{{ date('Y') }}-01-01', ano_letivo_fim: '{{ date('Y') }}-12-31' },
        error: '',      
        loading: false, 
        
        nextStep() {
            this.error = ''; 
            if (this.step === 2) {
                if (!this.form.estado) {
                    this.error = 'Por favor, selecione o estado.'; 
                    return;
                }
            }
            
            if (this.step === 3) {
                if (!this.form.ano_letivo_inicio || !this.form.ano_letivo_fim) {
                    this.error = 'Por favor, preencha as datas.'; 
                    return;
                }
                
                if (this.form.ano_letivo_fim <= this.form.ano_letivo_inicio) {
                    this.error = 'Por favor, preencha as datas corretamente.'; 
                    return;
                }
            }

            this.step++;
        },

        prevStep() {
            this.error = '';
            if (this.step > 1) {
                this.step--;
            }
        }
    }"
>
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob dark:bg-blue-900/20"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000 dark:bg-purple-900/20"></div>
    </div>

    <div class="absolute top-6 right-6 z-50">
        <button type="button" x-data @click="localStorage.theme === 'dark' ? (localStorage.theme='light', document.documentElement.classList.remove('dark')) : (localStorage.theme='dark', document.documentElement.classList.add('dark'))" 
                class="p-3 text-gray-600 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md rounded-full hover:bg-white dark:hover:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-800 transition-all hover:scale-110 active:scale-95 focus:outline-none">
            <svg class="w-5 h-5 hidden dark:block text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
            <svg class="w-5 h-5 block dark:hidden text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
        </button>
    </div>

    <main class="relative z-10 w-full max-w-md px-6 text-center">
        
        <form action="{{ route('intro.store') }}" method="POST" @submit="loading = true" class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl rounded-[2.5rem] shadow-2xl border border-white/20 dark:border-gray-800 p-8 sm:p-12 transition-all duration-500">
            
            @csrf
            <input type="hidden" name="estado" :value="form.estado">

            <input type="hidden" name="ano_letivo_inicio" :value="form.ano_letivo_inicio">
            <input type="hidden" name="ano_letivo_fim" :value="form.ano_letivo_fim">

            <div class="flex justify-center gap-2 mb-10">
                <template x-for="i in 4">
                    <div class="h-1.5 rounded-full transition-all duration-500"
                        :class="step >= i ? 'w-8 bg-blue-600' : 'w-2 bg-gray-300 dark:bg-gray-700'"></div>
                </template>
            </div>

            <template x-if="step === 1">
                <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    @include('intro.step-intro')
                </div>
            </template>

            <template x-if="step === 2">
                <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    @include('intro.step-localizacao')
                </div>
            </template>

            <template x-if="step === 3">
                <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    @include('intro.step-datas')
                </div>
            </template>

            <template x-if="step === 4">
                <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    @include('intro.step-final')
                </div>
            </template>

        </form>
    </main>

</body>
</html>