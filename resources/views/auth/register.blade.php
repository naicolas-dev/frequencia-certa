<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Criar Conta - {{ config('app.name', 'Frequência') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
      if (
        localStorage.theme === "dark" ||
        (!("theme" in localStorage) &&
          window.matchMedia("(prefers-color-scheme: dark)").matches)
      ) {
        document.documentElement.classList.add("dark");
      } else {
        document.documentElement.classList.remove("dark");
      }
    </script>

    <style>
      [x-cloak] { display: none !important; }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
  </head>

  <body
    class="antialiased bg-gray-50 dark:bg-black text-gray-900 dark:text-white min-h-screen flex items-center justify-center relative overflow-x-hidden font-sans selection:bg-blue-500 selection:text-white"
    x-data="registerPage()"
    x-init="init()"
  >
  <div id="page-loader"
      @if($errors->any()) style="display: none;" @endif
      class="fixed inset-0 z-[9999] bg-[#2D6AE6] flex items-center justify-center flex-col transition-colors duration-500 dark:bg-[#2D6AE6]">
      <div class="loader-content text-white text-4xl md:text-5xl font-black tracking-tighter opacity-0 translate-y-4"
          style="font-family: 'Instrument Sans', sans-serif;">
          Frequência Certa
      </div>
  </div>

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
      <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob dark:bg-blue-900/20"></div>
      <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000 dark:bg-purple-900/20"></div>
      <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-emerald-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000 dark:bg-emerald-900/20"></div>
    </div>

    <!-- Toast simples -->
    <div
      x-cloak
      x-show="toast.open"
      x-transition
      class="fixed top-6 left-1/2 -translate-x-1/2 z-[60] w-[min(92vw,520px)]"
      role="status"
      aria-live="polite"
    >
      <div
        class="rounded-2xl border shadow-lg backdrop-blur-md px-4 py-3"
        :class="toast.type === 'error'
          ? 'bg-red-50/90 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-700 dark:text-red-300'
          : 'bg-emerald-50/90 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300'"
      >
        <div class="flex items-start justify-between gap-3">
          <p class="text-sm font-semibold" x-text="toast.message"></p>
          <button type="button" class="text-xs font-bold opacity-70 hover:opacity-100" @click="toast.open = false">
            Fechar
          </button>
        </div>
      </div>
    </div>

    <div class="absolute top-6 right-6 z-50">
      <button
        type="button"
        @click="toggleTheme()"
        class="p-3 text-gray-600 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md rounded-full hover:bg-white dark:hover:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-800 transition-all hover:scale-110 active:scale-95 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 outline-none"
        aria-label="Alternar tema"
      >
        <svg class="w-6 h-6 hidden dark:block text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
        <svg class="w-6 h-6 block dark:hidden text-gray-600" fill="currentColor" viewBox="0 0 20 20">
          <path
            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z"
            fill-rule="evenodd"
            clip-rule="evenodd"
          ></path>
        </svg>
      </button>
    </div>

    <main class="relative z-10 w-full max-w-[420px] px-4 py-10">
      <div class="bg-white/70 dark:bg-gray-900/70 backdrop-blur-xl rounded-[2.5rem] shadow-2xl border border-white/20 dark:border-gray-800 p-8 sm:p-10 relative overflow-hidden transition-all duration-500">
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-500 text-white shadow-lg shadow-blue-500/30 mb-5 transform rotate-6 hover:rotate-0 transition-transform duration-300">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Criar nova conta</h1>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Junte-se a nós para organizar seus estudos.</p>
        </div>

        <div class="space-y-3 mb-6">
          <button
            type="button"
            @click="handleSocial('google')"
            :disabled="loading"
            :class="{ 'opacity-50 pointer-events-none cursor-not-allowed': loading }"
            class="relative w-full flex items-center justify-center gap-3 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 p-3.5 rounded-2xl transition-all duration-200 hover:scale-[1.01] active:scale-[0.98] shadow-sm group"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M23.766 12.2764C23.766 11.4607 23.6999 10.6406 23.5588 9.83807H12.24V14.4591H18.7217C18.4528 15.9494 17.5885 17.2678 16.323 18.1056V21.1039H20.19C22.4608 19.0139 23.766 15.9274 23.766 12.2764Z" fill="#4285F4"/>
              <path d="M12.2401 24.0008C15.4766 24.0008 18.2059 22.9382 20.1945 21.1039L16.3275 18.1055C15.2517 18.8375 13.8627 19.252 12.2445 19.252C9.11388 19.252 6.45946 17.1399 5.50705 14.3003H1.5166V17.3912C3.55371 21.4434 7.7029 24.0008 12.2401 24.0008Z" fill="#34A853"/>
              <path d="M5.50253 14.3003C5.00236 12.8099 5.00236 11.1961 5.50253 9.70575V6.61481H1.51649C-0.18551 10.0056 -0.18551 14.0004 1.51649 17.3912L5.50253 14.3003Z" fill="#FBBC05"/>
              <path d="M12.2401 4.74966C13.9509 4.7232 15.6044 5.36697 16.8434 6.54867L20.2695 3.12262C18.1001 1.0855 15.2208 -0.034466 12.2401 0.000808666C7.7029 0.000808666 3.55371 2.55822 1.5166 6.61481L5.50264 9.70575C6.45064 6.86173 9.10947 4.74966 12.2401 4.74966Z" fill="#EA4335"/>
            </svg>
            <span class="font-bold text-sm sm:text-base">Continuar com Google</span>
          </button>

          <button
            type="button"
            @click="handleSocial('github')"
            :disabled="loading"
            :class="{ 'opacity-50 pointer-events-none cursor-not-allowed': loading }"
            class="relative w-full flex items-center justify-center gap-3 bg-[#24292e] dark:bg-white text-white dark:text-black hover:bg-[#2b3137] dark:hover:bg-gray-200 p-3.5 rounded-2xl transition-all duration-200 hover:scale-[1.01] active:scale-[0.98] shadow-sm"
          >
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
              <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
            </svg>
            <span class="font-bold text-sm sm:text-base">Continuar com GitHub</span>
          </button>
        </div>

        <div class="flex items-center gap-4 my-6">
          <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
          <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">ou use email</span>
          <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5" @submit="onSubmit($event)">
          @csrf

          <div>
            <label for="name" class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2 ml-1">Nome Completo</label>
            <input
              id="name"
              class="w-full text-base font-medium bg-white/50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-4 placeholder-gray-400 dark:text-white transition-all duration-300 shadow-sm focus:scale-[1.01] focus:bg-white dark:focus:bg-black/40 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 @error('name') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
              type="text"
              name="name"
              value="{{ old('name') }}"
              required
              autofocus
              autocomplete="name"
              placeholder="Seu nome"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm font-medium text-red-600 dark:text-red-400 ml-1 animate-pulse" />
          </div>

          <div>
            <label for="email" class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2 ml-1">Email</label>
            <input
              id="email"
              class="w-full text-base font-medium bg-white/50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-4 placeholder-gray-400 dark:text-white transition-all duration-300 shadow-sm focus:scale-[1.01] focus:bg-white dark:focus:bg-black/40 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 @error('email') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
              type="email"
              name="email"
              value="{{ old('email') }}"
              required
              autocomplete="username"
              placeholder="Seu email"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm font-medium text-red-600 dark:text-red-400 ml-1 animate-pulse" />
          </div>

          <div>
            <label for="password" class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2 ml-1">Senha</label>

            <div class="relative">
              <input
                id="password"
                x-model="password"
                x-bind:type="showPassword ? 'text' : 'password'"
                minlength="8"
                class="w-full text-base font-medium bg-white/50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-4 pr-12 placeholder-gray-400 dark:text-white transition-all duration-300 shadow-sm focus:scale-[1.01] focus:bg-white dark:focus:bg-black/40 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 @error('password') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Mínimo 8 caracteres"
                aria-describedby="password-help password-strength"
              />

              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none transition-colors"
                :aria-pressed="showPassword.toString()"
                aria-label="Mostrar/ocultar senha"
              >
                <svg x-cloak x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <svg x-cloak x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.059 10.059 0 011.517-2.925m2.766-2.541A9.996 9.996 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.057 10.057 0 01-1.127 2.18m-4.545 3.328A3 3 0 0112 15a3 3 0 01-3-3 3 3 0 01.373-1.46m-1.795-3.076A3.385 3.385 0 0112 9a3.385 3.385 0 012.352 1.056m-5.322-2.31L17.5 17.5"></path>
                </svg>
              </button>
            </div>

            <div
              x-cloak
              x-show="password.length > 0"
              x-transition
              class="h-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full mt-2 overflow-hidden"
              role="progressbar"
              aria-label="Força da senha"
              :aria-valuenow="strengthScore"
              aria-valuemin="0"
              aria-valuemax="4"
              id="password-strength"
            >
              <div class="h-full transition-all duration-500 ease-out rounded-full" :class="strengthBarClass"></div>
            </div>

            <p id="password-help" class="text-[10px] text-gray-400 mt-1 ml-1" x-cloak x-show="password.length > 0">
              <span x-text="strengthLabel"></span>
              <span x-show="strengthScore < 4"> — Dica: use números e símbolos.</span>
            </p>

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm font-medium text-red-600 dark:text-red-400 ml-1 animate-pulse" />
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2 ml-1">Confirmar Senha</label>
            <input
              id="password_confirmation"
              x-model="passwordConfirmation"
              x-bind:type="showPassword ? 'text' : 'password'"
              class="w-full text-base font-medium bg-white/50 dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-4 placeholder-gray-400 dark:text-white transition-all duration-300 shadow-sm focus:scale-[1.01] focus:bg-white dark:focus:bg-black/40 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
              name="password_confirmation"
              required
              autocomplete="new-password"
              placeholder="Repita a senha"
              :class="passwordMismatch ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : ''"
              aria-describedby="password-confirm-help"
            />
            <p id="password-confirm-help" class="mt-2 text-sm font-medium text-red-600 dark:text-red-400 ml-1" x-cloak x-show="passwordMismatch">
              As senhas não coincidem.
            </p>
          </div>

          <button
            type="submit"
            :disabled="loading || passwordMismatch"
            class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-70 disabled:cursor-not-allowed text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-blue-600/30 transition-all duration-200 transform active:scale-[0.98] hover:shadow-2xl flex items-center justify-center gap-2 mt-4 outline-none focus:ring-4 focus:ring-blue-600/30"
          >
            <span x-cloak x-show="!loading" class="flex items-center gap-2">
              Cadastrar
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </span>
            <span x-cloak x-show="loading" class="flex items-center gap-2">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              Criando conta...
            </span>
          </button>
        </form>

        <div class="mt-8 text-center pt-6 border-t border-gray-200/60 dark:border-gray-800">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Já possui uma conta?
            <a href="{{ route('login') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:underline p-2">
              Fazer Login
            </a>
          </p>
        </div>
      </div>
    </main>

    <script>
      function registerPage() {
        const silentCodes = new Set([
          "auth/popup-closed-by-user",
          "auth/cancelled-popup-request",
          "auth/popup-blocked",
          "client/throttled",
        ]);

        return {
          loading: false,
          showPassword: false,
          password: "",
          passwordConfirmation: "",
          toast: { open: false, type: "error", message: "" },

          _attempt: 0,

          init() {},

          toggleTheme() {
            const isDark = document.documentElement.classList.contains("dark");
            if (isDark) {
              localStorage.theme = "light";
              document.documentElement.classList.remove("dark");
            } else {
              localStorage.theme = "dark";
              document.documentElement.classList.add("dark");
            }
          },

          showToast(message, type = "error") {
            this.toast = { open: true, type, message };
            window.clearTimeout(this._toastTimer);
            this._toastTimer = window.setTimeout(() => {
              this.toast.open = false;
            }, 4500);
          },

          get strengthScore() {
            let s = 0;
            if (this.password.length >= 6) s++;
            if (this.password.length >= 9) s++;
            if (/[0-9]/.test(this.password)) s++;
            if (/[^a-zA-Z0-9]/.test(this.password)) s++;
            return s;
          },

          get strengthLabel() {
            const s = this.strengthScore;
            if (this.password.length === 0) return "";
            if (s <= 1) return "Força: fraca";
            if (s === 2) return "Força: razoável";
            if (s === 3) return "Força: boa";
            return "Força: forte";
          },

          get strengthBarClass() {
            const s = this.strengthScore;
            return {
              "w-1/4 bg-red-500": s <= 1,
              "w-2/4 bg-yellow-500": s === 2,
              "w-3/4 bg-blue-500": s === 3,
              "w-full bg-emerald-500": s >= 4,
            };
          },

          get passwordMismatch() {
            if (!this.password || !this.passwordConfirmation) return false;
            return this.password !== this.passwordConfirmation;
          },

          onSubmit(e) {
            if (this.loading || this.passwordMismatch) {
              e.preventDefault();
              return;
            }
            this.loading = true;
          },

          async handleSocial(provider) {
            if (this.loading) return;

            const attemptId = ++this._attempt;
            this.loading = true;

            const unlock = () => {
              if (this._attempt === attemptId) this.loading = false;
            };

            // ✅ fecha popup -> volta foco -> destrava rápido
            const onFocus = () => setTimeout(unlock, 150);
            window.addEventListener("focus", onFocus, { once: true });

            try {
              const res = await window.socialLogin(provider);

              if (res?.ignored) return;
              if (res?.redirected) return;
            } catch (e) {
              if (!silentCodes.has(e?.code)) {
                this.showToast(e?.message || "Erro ao criar conta. Tente novamente.", "error");
              }
            } finally {
              window.removeEventListener("focus", onFocus);
              unlock();
            }
          },
        };
      }
    </script>
  </body>
</html>
