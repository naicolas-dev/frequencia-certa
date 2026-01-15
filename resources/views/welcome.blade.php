<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    
    <title>Controle de Faltas Escolares | Saiba Quantas Faltas Pode Ter - Frequência Certa</title>
    <meta name="description" content="Descubra exatamente quantas faltas você pode ter sem reprovar ou perder o Pé-de-Meia. O Frequência Certa calcula sua frequência escolar, cruza com o calendário letivo e te avisa antes de rodar. Grátis.">
    <meta name="author" content="Frequência Certa">
    <link rel="canonical" href="https://frequenciacerta.app.br/">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://frequenciacerta.app.br/">
    <meta property="og:title" content="Controle de Faltas Escolares – Frequência Certa">
    <meta property="og:description" content="A ferramenta que calcula quantas vezes você pode faltar na escola sem reprovar. Simples, rápido e grátis.">
    <meta property="og:image" content="{{ asset('img/og-image.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta property="og:title" content="Controle de Faltas Escolares – Frequência Certa">
    <meta name="twitter:description" content="Evite a reprovação e garanta seu benefício. Calcule suas faltas agora.">
    <meta name="twitter:image" content="{{ asset('img/og-image.png') }}">

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/icons/icon-192x192.png') }}">
    <meta name="theme-color" content="#1E3A8A">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "SoftwareApplication",
      "name": "Frequência Certa",
      "applicationCategory": "EducationalApplication",
      "operatingSystem": "Web, iOS, Android",
      "offers": {
        "@@type": "Offer",
        "price": "0",
        "priceCurrency": "BRL"
      },
      "description": "Aplicativo para controle de faltas escolares, cálculo de frequência e alertas de reprovação cruzados com o calendário letivo.",
    }
    </script>

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    
    <script>
    // EASTER EGG MANTIDO - NÃO ALTERAR
    console.log(
        "%c👀 Curioso? Clique em qualquer lugar da página para desbloquear o áudio.",
        "color:#94a3b8;font-family:monospace;font-size:11px"
        );
        console.log(
        "%c👀 Curious? Click anywhere on the page to unlock the audio.",
        "color:#94a3b8;font-family:monospace;font-size:11px"
        );

        (() => {
        let devtoolsOpen = false;
        let userInteracted = false;
        let easterEggFired = false;

        const tryFire = () => {
            if (devtoolsOpen && userInteracted && !easterEggFired) {
            easterEggFired = true;
            runEasterEgg();
            }
        };

        (() => {
            const threshold = 160;
            const check = () => {
            const w = window.outerWidth - window.innerWidth;
            const h = window.outerHeight - window.innerHeight;
            if (w > threshold || h > threshold) {
                devtoolsOpen = true;
                tryFire();
                window.removeEventListener("resize", check);
            }
            };
            window.addEventListener("resize", check);
            setTimeout(check, 500);
        })();

        (() => {
            const onGesture = () => {
            userInteracted = true;
            tryFire();
            ["click", "keydown", "pointerdown", "touchstart"].forEach(evt =>
                window.removeEventListener(evt, onGesture)
            );
            };
            ["click", "keydown", "pointerdown", "touchstart"].forEach(evt =>
            window.addEventListener(evt, onGesture)
            );
        })();

        function runEasterEgg() {
            console.clear();
            const sfx = (() => {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return {};
            const ctx = new AudioCtx();
            const beep = ({ freq = 700, duration = 0.06, type = "sine", volume = 0.03, delay = 0 } = {}) => {
                const o = ctx.createOscillator();
                const g = ctx.createGain();
                const t0 = ctx.currentTime + delay;
                o.type = type;
                o.frequency.value = freq;
                g.gain.setValueAtTime(0.0001, t0);
                g.gain.exponentialRampToValueAtTime(volume, t0 + 0.01);
                g.gain.exponentialRampToValueAtTime(0.0001, t0 + duration);
                o.connect(g);
                g.connect(ctx.destination);
                o.start(t0);
                o.stop(t0 + duration + 0.02);
            };
            const ambient = () => {
                const now = ctx.currentTime;
                const master = ctx.createGain();
                master.gain.value = 0.18;
                master.connect(ctx.destination);
                const dry = ctx.createGain();
                dry.gain.value = 1.0;
                const wet = ctx.createGain();
                wet.gain.value = 0.45;
                const makeImpulse = (duration = 0.22, decay = 10) => {
                    const rate = ctx.sampleRate;
                    const length = Math.floor(rate * duration);
                    const buffer = ctx.createBuffer(2, length, rate);
                    for (let ch = 0; ch < 2; ch++) {
                    const data = buffer.getChannelData(ch);
                    for (let i = 0; i < length; i++) {
                        const t = i / length;
                        data[i] = (Math.random() * 2 - 1) * Math.pow(1 - t, decay);
                    }
                    }
                    return buffer;
                };
                const convolver = ctx.createConvolver();
                convolver.buffer = makeImpulse(0.22, 10);
                const reverbHP = ctx.createBiquadFilter();
                reverbHP.type = "highpass";
                reverbHP.frequency.value = 700;
                const reverbLP = ctx.createBiquadFilter();
                reverbLP.type = "lowpass";
                reverbLP.frequency.value = 9000;
                const delay = ctx.createDelay(0.3);
                delay.delayTime.value = 0.095;
                const fb = ctx.createGain();
                fb.gain.value = 0.22;
                const fbLP = ctx.createBiquadFilter();
                fbLP.type = "lowpass";
                fbLP.frequency.value = 6500;
                delay.connect(fbLP);
                fbLP.connect(fb);
                fb.connect(delay);
                const wetBus = ctx.createGain();
                wetBus.gain.value = 1.0;
                wetBus.connect(delay);
                wetBus.connect(convolver);
                delay.connect(wet);
                convolver.connect(reverbHP);
                reverbHP.connect(reverbLP);
                reverbLP.connect(wet);
                dry.connect(master);
                wet.connect(master);
                const bell = (freq, t, dur = 0.55, amp = 1, pan = 0) => {
                    const out = ctx.createGain();
                    out.gain.setValueAtTime(0.0001, t);
                    out.gain.exponentialRampToValueAtTime(0.22 * amp, t + 0.02);
                    out.gain.exponentialRampToValueAtTime(0.0001, t + dur);
                    const p = ctx.createStereoPanner();
                    p.pan.setValueAtTime(pan, t);
                    const carrier = ctx.createOscillator();
                    carrier.type = "sine";
                    carrier.frequency.setValueAtTime(freq, t);
                    const mod = ctx.createOscillator();
                    mod.type = "sine";
                    mod.frequency.setValueAtTime(freq * 2.4, t);
                    const modGain = ctx.createGain();
                    modGain.gain.setValueAtTime(freq * 0.06, t);
                    mod.connect(modGain);
                    modGain.connect(carrier.frequency);
                    const harm = ctx.createOscillator();
                    harm.type = "triangle";
                    harm.frequency.setValueAtTime(freq * 2, t);
                    carrier.connect(out);
                    harm.connect(out);
                    out.connect(p);
                    p.connect(dry);
                    p.connect(wetBus);
                    carrier.start(t);
                    harm.start(t);
                    mod.start(t);
                    carrier.stop(t + dur + 0.05);
                    harm.stop(t + dur + 0.05);
                    mod.stop(t + dur + 0.05);
                };
                const sparkle = (t, pan = 0) => {
                    const length = Math.floor(ctx.sampleRate * 0.09);
                    const buffer = ctx.createBuffer(1, length, ctx.sampleRate);
                    const data = buffer.getChannelData(0);
                    for (let i = 0; i < length; i++) data[i] = (Math.random() * 2 - 1);
                    const src = ctx.createBufferSource();
                    src.buffer = buffer;
                    const bp = ctx.createBiquadFilter();
                    bp.type = "bandpass";
                    bp.frequency.value = 7200;
                    bp.Q.value = 10;
                    const g = ctx.createGain();
                    g.gain.setValueAtTime(0.0001, t);
                    g.gain.exponentialRampToValueAtTime(0.02, t + 0.01);
                    g.gain.exponentialRampToValueAtTime(0.0001, t + 0.09);
                    const p = ctx.createStereoPanner();
                    p.pan.setValueAtTime(pan, t);
                    src.connect(bp);
                    bp.connect(g);
                    g.connect(p);
                    p.connect(wetBus);
                    src.start(t);
                    src.stop(t + 0.11);
                };
                const B4  = 493.88;
                const Fs5 = 739.99;
                const D6  = 1174.66;
                bell(B4,  now + 0.00, 0.55, 1.0, -0.15);
                bell(Fs5, now + 0.02, 0.55, 0.95,  0.10);
                bell(D6,  now + 0.04, 0.52, 0.85,  0.18);
                sparkle(now + 0.03,  0.25);
                sparkle(now + 0.14, -0.15);
                setTimeout(() => {
                    [delay, fb, fbLP, convolver, reverbHP, reverbLP, wetBus, dry, wet, master].forEach(n => {
                    try { n.disconnect(); } catch {}
                    });
                }, 1600);
            };
            return {
                step: () => beep({ freq: 520, duration: 0.045, type: "triangle", volume: 0.02 }),
                ok: () => beep({ freq: 880, duration: 0.11 }),
                achievement: () => {
                beep({ freq: 784, duration: 0.09, type: "square", volume: 0.02 });
                beep({ freq: 1175, duration: 0.12, type: "square", volume: 0.02, delay: 0.12 });
                },
                ambient
            };
            })();

            const cssTitle = "font-family:monospace;font-weight:900;font-size:14px;color:#e2e8f0;background:#0b1220;padding:6px 10px;border-radius:10px;border:1px solid rgba(148,163,184,.25)";
            const cssLine = "font-family:monospace;font-weight:600;font-size:12px;color:#94a3b8";
            const cssAccent = "font-family:monospace;font-weight:800;font-size:12px;color:#60a5fa";
            const cssOk = "font-family:monospace;font-weight:800;font-size:12px;color:#22c55e";
            const cssAchievement = "font-family:monospace;font-weight:900;color:#0b1220;background:#e2e8f0;padding:10px 12px;border-radius:12px";
            const cssSub = "font-family:monospace;font-weight:700;color:#94a3b8";

            const steps = [
            { t: "%c[ SYSTEM BOOT ]", s: cssTitle, d: 0 },
            { t: "%c> Initializing modules...", s: cssLine, d: 180 },
            { t: "%c> Loading UI...", s: cssLine, d: 180 },
            { t: "%c> Injecting easter eggs...", s: cssLine, d: 180 },
            { t: "%c> Author: %cNAICOLAS", s: cssLine, s2: cssAccent, d: 220 },
            { t: "%c> Status: %cOK ✔", s: cssLine, s2: cssOk, d: 240, ok: true }
            ];

            let timeline = 0;
            for (const step of steps) {
            timeline += step.d;
            setTimeout(() => {
                sfx.step();
                step.s2 ? console.log(step.t, step.s, step.s2) : console.log(step.t, step.s);
                if (step.ok) sfx.ok();
            }, timeline);
            }

            setTimeout(() => {
            sfx.achievement();
            console.log("%c🏆 ACHIEVEMENT UNLOCKED", cssAchievement);
            console.log("%cSystem booted successfully  •  +50 curiosity", cssSub);
            console.log("%cHint: try typing %cnaicolas()%c", cssSub, "color:#60a5fa;font-weight:900;font-family:monospace", cssSub);
            console.log("%cDica: digite %cnaicolas()%c", cssSub, "color:#60a5fa;font-weight:900;font-family:monospace", cssSub);

            window.naicolas = () => {
                sfx.ambient();
                console.log("%cDeveloped by Nicolas Viana Alves %c🤓\n%cCheck the source at: https://github.com/naicolas-dev/frequencia-certa", "color:#9ca3af;font-family:sans-serif;font-size:11px;", "font-size:14px;", "color:#60a5fa;font-family:sans-serif;font-size:11px;");
                console.log("%cCheck my other projects at: https://github.com/naicolas-dev", "color:#60a5fa;font-family:sans-serif;font-size:11px;");
            };

            const lines = [
                " ",
                "███╗   ██╗  █████╗ ██╗ ██████╗  ██████╗ ██╗      █████╗ ███████╗",
                "████╗  ██║ ██╔══██╗██║██╔════╝ ██╔═══██╗██║     ██╔══██╗██╔════╝",
                "██╔██╗ ██║ ███████║██║██║      ██║   ██║██║     ███████║███████╗",
                "██║╚██╗██║ ██╔══██║██║██║      ██║   ██║██║     ██╔══██║╚════██║",
                "██║ ╚████║ ██║  ██║██║╚██████╗ ╚██████╔╝███████╗██║  ██║███████║",
                "╚═╝  ╚═══╝ ╚═╝  ╚═╝╚═╝ ╚═════╝  ╚═════╝ ╚══════╝╚═╝  ╚═╝╚══════╝"
            ];
            const gradient = ["#3b82f6", "#4f7cf6", "#6376f6", "#776ff6", "#8b69f6", "#9f63f6", "#b35df6", "#c757f6", "#db51f6", "#ef4bf6"];
            let fmt = "";
            let styles = [];
            lines.forEach((line, i) => {
                const color = gradient[Math.floor((i / (lines.length - 1)) * (gradient.length - 1))];
                fmt += `%c${line}\n`;
                styles.push(`color:${color};font-weight:bold;font-family:monospace;`);
            });
            console.log(fmt, ...styles);
            }, timeline + 400);
        }
        })();

        // TRAVA PWA
        (function () {
            const isStandalone = (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) || window.navigator.standalone === true;
            if (isStandalone && window.location.pathname === '/') {
            window.location.replace(`${window.location.origin}/dashboard`);
            }
        })();
    </script>

    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

    <style>
        body { font-family: 'Instrument Sans', sans-serif; overflow-x: hidden; }
        ::-webkit-scrollbar { width: 0px; background: transparent; }

        .phone-mockup {
            box-shadow: 0 0 0 10px #1f2937, 0 20px 50px -10px rgba(0, 0, 0, 0.5);
        }
        .notch { border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; }
        .text-gradient {
            background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-image: linear-gradient(to right, #06B6D4, #1D4ED8, #06B6D4);
        }
        .h-screen-ios { height: 100vh; height: 100dvh; }
        
        @keyframes pulse-soft {
            0%, 100% { box-shadow: 0 0 0 0 rgba(29, 78, 216, 0.4); }
            50% { box-shadow: 0 0 0 15px rgba(29, 78, 216, 0); }
        }
        .cta-pulse { animation: pulse-soft 2s infinite; }
    </style>
</head>
<body class="bg-[#F8FAFC] dark:bg-[#0F172A] text-gray-900 dark:text-white antialiased selection:bg-[#06B6D4] selection:text-white transition-colors duration-300">

    <nav class="fixed top-0 w-full z-50 p-6 flex justify-between items-center backdrop-blur-xl bg-white/80 dark:bg-[#0F172A]/90 border-b border-gray-200/50 dark:border-white/10 transition-colors duration-300">
        <div class="flex items-center gap-2 font-bold text-xl tracking-tighter text-[#1E3A8A] dark:text-white">
            <x-application-logo class="block h-8 w-auto fill-current text-[#1D4ED8] dark:text-[#06B6D4]" width="32" height="32" />
            <span class="hidden sm:inline">Frequência Certa</span>
        </div>
        <div class="flex items-center gap-4">
            <button id="theme-toggle" class="p-2 rounded-full text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/10 transition-colors" aria-label="Alternar tema">
                <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            </button>
            @auth
                <a href="{{ url('/dashboard') }}" data-no-spa class="px-5 py-2 bg-[#1E3A8A] dark:bg-white text-white dark:text-[#0F172A] rounded-full font-bold hover:scale-105 transition shadow-lg">Voltar ao App</a>
            @else
                <a href="{{ route('login') }}" data-no-spa class="hidden sm:inline font-medium text-gray-600 dark:text-gray-400 hover:text-[#1D4ED8] dark:hover:text-[#06B6D4] transition mr-2 text-sm">Já tenho conta</a>
                <a href="{{ route('register') }}" data-no-spa class="px-5 py-2 bg-[#1D4ED8] text-white rounded-full font-bold hover:bg-[#1E40AF] transition shadow-lg shadow-blue-600/20 text-sm">Criar Grátis</a>
            @endauth
        </div>
    </nav>

    <section class="h-screen-ios w-full flex flex-col items-center justify-center relative overflow-hidden bg-[#F8FAFC] dark:bg-[#0F172A] transition-colors duration-300">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[#1D4ED8]/10 dark:bg-[#1D4ED8]/20 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="z-10 text-center px-4 space-y-8 max-w-3xl mt-16 sm:mt-0">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-blue-100 dark:border-blue-900/50 bg-blue-50 dark:bg-blue-900/20 text-xs font-semibold tracking-wide mb-2 text-[#1D4ED8] dark:text-[#60A5FA] animate-fade-in-up">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                IA DE CONTROLE DE FREQUÊNCIA 🤖
            </div>
            
            <h1 class="text-5xl md:text-8xl font-black tracking-tight leading-[0.9] text-[#1E3A8A] dark:text-white">
                Evite reprovar por <br />
                <span class="text-gradient">Faltas Escolares</span>
            </h1>
            
            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 mx-auto leading-relaxed px-4 max-w-xl">
                O <strong class="text-[#1E3A8A] dark:text-white">controle de faltas</strong> definitivo. Calcule se você ainda pode faltar sem perder o <strong>Pé-de-Meia</strong> ou rodar de ano.
            </p>

            <div class="flex flex-col items-center gap-3 pt-2">
                @auth
                    <a href="{{ url('/dashboard') }}" class="cta-pulse px-8 py-4 bg-[#1E3A8A] dark:bg-white text-white dark:text-[#0F172A] text-lg font-bold rounded-full hover:scale-105 transition shadow-xl">
                        Acessar Painel de Frequência
                    </a>
                @else
                    <a href="{{ route('register') }}" class="cta-pulse group relative inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white transition-all duration-200 bg-[#1D4ED8] font-pj rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1D4ED8] hover:bg-[#1E40AF] hover:scale-105">
                        Calcular minhas Faltas
                        <svg class="w-5 h-5 ml-2 -mr-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                        ✨ 100% Grátis • Leva menos de 1 minuto
                    </p>
                @endauth
            </div>

            <div class="pt-8 animate-bounce opacity-50">
                <svg class="w-6 h-6 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </div>
        </div>
    </section>

    <div id="scrolly-wrapper" class="relative w-full h-[600vh] bg-[#F8FAFC] dark:bg-[#0F172A] transition-colors duration-300">
        <div class="scrolly-stage h-screen-ios w-full sticky top-0 flex flex-col md:flex-row overflow-hidden">

            <div class="order-2 md:order-1 w-full md:w-1/2 h-[40vh] md:h-full flex items-center md:items-center justify-center relative md:pl-20 z-30 pointer-events-none md:pb-0 bg-transparent">

                <div id="text-1" class="absolute max-w-md opacity-0 translate-y-20 w-full px-6 text-center md:text-left">
                    <div class="md:bg-transparent bg-white/90 dark:bg-[#0F172A]/90 backdrop-blur-xl md:backdrop-blur-none p-6 rounded-3xl md:p-0 border border-gray-200 dark:border-white/10 md:border-none shadow-xl md:shadow-none">
                        <h2 class="text-2xl md:text-5xl font-bold mb-3 md:mb-4 text-[#1E3A8A] dark:text-white">Frequência Escolar<br>no piloto automático.</h2>
                        <p class="text-base md:text-xl text-gray-600 dark:text-gray-400">Esqueça a planilha. Nossa IA monta sua grade, identifica feriados e automatiza seu controle. Só marque "Fui" ou "Faltei".</p>
                    </div>
                </div>

                <div id="text-2" class="absolute max-w-md opacity-0 translate-y-20 w-full px-6 text-center md:text-left">
                    <div class="md:bg-transparent bg-white/90 dark:bg-[#0F172A]/90 backdrop-blur-xl md:backdrop-blur-none p-6 rounded-3xl md:p-0 border border-gray-200 dark:border-white/10 md:border-none shadow-xl md:shadow-none">
                        <h2 class="text-2xl md:text-5xl font-bold mb-3 md:mb-4 text-[#1D4ED8] dark:text-[#60A5FA]">Cálculo de Faltas<br>inteligente.</h2>
                        <p class="text-base md:text-xl text-gray-600 dark:text-gray-400">Cruzamos o <strong>calendário letivo</strong> com suas aulas. Saiba exatamente quantas faltas você tem "no bolso" antes de arriscar o ano.</p>
                        <div class="mt-4 md:hidden">
                            <span class="text-xs font-bold text-[#1D4ED8] dark:text-[#60A5FA]">Ver como funciona ↓</span>
                        </div>
                    </div>
                </div>

                <div id="text-3" class="absolute max-w-md opacity-0 translate-y-20 w-full px-6 text-center md:text-left">
                      <div class="md:bg-transparent bg-white/90 dark:bg-[#0F172A]/90 backdrop-blur-xl md:backdrop-blur-none p-6 rounded-3xl md:p-0 border border-gray-200 dark:border-white/10 md:border-none shadow-xl md:shadow-none">
                        <h2 class="text-2xl md:text-5xl font-bold mb-3 md:mb-4 text-[#7C3AED] dark:text-[#A78BFA]">O Oráculo da<br>Aprovação.</h2>
                        <p class="text-base md:text-xl text-gray-600 dark:text-gray-400">Na dúvida? Pergunte: <i>"Posso faltar amanhã?"</i>. Ele analisa provas, feriados e seu limite de faltas para te dar o veredito.</p>
                    </div>
                </div>

                <div id="text-4" class="absolute max-w-md opacity-0 translate-y-20 w-full px-6 text-center md:text-left">
                    <div class="md:bg-transparent bg-white/90 dark:bg-[#0F172A]/90 backdrop-blur-xl md:backdrop-blur-none p-6 rounded-3xl md:p-0 border border-gray-200 dark:border-white/10 md:border-none shadow-xl md:shadow-none">
                        <h2 class="text-2xl md:text-5xl font-bold mb-3 md:mb-4 text-[#06B6D4] dark:text-[#22D3EE]">Alerta Anti<br>Reprovação.</h2>
                       <p class="text-base md:text-xl text-gray-600 dark:text-gray-400">Esqueceu de registrar presença? O app manda um alerta antes que a situação fique crítica e você corra risco de reprovar por falta.</p>
                   </div>
               </div>

                <div id="text-5" class="absolute max-w-md opacity-0 translate-y-20 w-full px-6 text-center md:text-left">
                      <div class="md:bg-transparent bg-white/90 dark:bg-[#0F172A]/90 backdrop-blur-xl md:backdrop-blur-none p-6 rounded-3xl md:p-0 border border-gray-200 dark:border-white/10 md:border-none shadow-xl md:shadow-none">
                        <h2 class="text-2xl md:text-5xl font-bold mb-3 md:mb-4 text-[#F59E0B]">Gamifique sua<br>Escola.</h2>
                        <p class="text-base md:text-xl text-gray-600 dark:text-gray-400">Transformamos o "ir para a aula" num jogo. Mantenha sua ofensiva de presença, ganhe medalhas e mostre que você domina o sistema.</p>
                    </div>
                </div>
            </div>

            <div class="order-1 md:order-2 w-full md:w-1/2 h-[60vh] md:h-full flex items-end pb-4 md:items-center justify-center relative z-20">
                <div id="phone-container" class="relative md:static w-full h-full flex items-end md:items-center justify-center">
                    <div id="phone" class="phone-mockup relative w-[300px] h-[600px] bg-gray-800 rounded-[40px] z-20 overflow-hidden transform-gpu scale-[0.55] sm:scale-[0.6] md:scale-100 opacity-0 md:opacity-100 shadow-2xl">
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-7 bg-[#1f2937] notch z-30"></div>
                        <div class="relative w-full h-full bg-[#F8FAFC] dark:bg-[#030712] flex flex-col transition-colors duration-300">
                             <div class="pt-10 px-5 pb-3 flex justify-between items-center bg-white dark:bg-[#111827] border-b border-gray-100 dark:border-gray-800 transition-colors">
                                <div class="flex items-center gap-2">
                                    <x-application-logo class="w-6 h-6 text-[#1D4ED8] dark:text-[#06B6D4]" />
                                    <span class="font-bold text-[#1E3A8A] dark:text-white tracking-tight text-sm">Frequência Certa</span>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden border border-gray-200 dark:border-gray-700">
                                    <img src="https://ui-avatars.com/api/?name=User&background=random" alt="Avatar Usuário" class="w-full h-full opacity-80" width="32" height="32">
                                </div>
                            </div>
                            
                            <div class="relative flex-1 px-4 overflow-hidden bg-[#F8FAFC] dark:bg-[#030712] transition-colors">
                                <div id="scene-1" class="absolute inset-0 px-1 pt-6 transition-opacity duration-300">
                                    <div class="mb-5 p-4 rounded-xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 shadow-sm">
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Presença Global</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-2xl font-black text-[#1E3A8A] dark:text-white">92%</h3>
                                            <span class="text-xs text-[#22C55E] font-bold bg-emerald-50 dark:bg-emerald-900/20 px-2 py-1 rounded-md">Pé-de-Meia Garantido</span>
                                        </div>
                                    </div>
                                    <h2 class="text-sm font-bold text-[#1E3A8A] dark:text-white mb-3 px-1">Minhas Matérias</h2>
                                    <div class="space-y-3">
                                        <div class="p-4 rounded-xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 flex justify-between items-center shadow-sm">
                                            <div>
                                                <div class="font-bold text-[#1E3A8A] dark:text-white text-sm">Matemática</div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                                                    <span class="text-[10px] text-gray-500 dark:text-gray-400">Terça e Quinta</span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-black text-[#DC2626]">1</div>
                                                <div class="text-[9px] uppercase text-gray-400 font-bold">Falta restante</div>
                                            </div>
                                        </div>
                                        <div class="p-4 rounded-xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 flex justify-between items-center shadow-sm opacity-80">
                                            <div>
                                                <div class="font-bold text-[#1E3A8A] dark:text-white text-sm">História</div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
                                                    <span class="text-[10px] text-gray-500 dark:text-gray-400">Segunda</span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-black text-[#22C55E]">10</div>
                                                <div class="text-[9px] uppercase text-gray-400 font-bold">Faltas restantes</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="scene-2" class="absolute inset-0 px-2 flex flex-col justify-center translate-y-full opacity-0">
                                    <div class="font-bold bg-white dark:bg-[#111827] p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl">
                                        História
                                        <div class="flex justify-between mb-2">
                                            <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase">Frequência</span>
                                            <span class="text-[#22C55E] font-mono font-bold">100%</span>
                                        </div>
                                        <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full mb-6 overflow-hidden">
                                            <div id="calc-bar" class="h-full bg-[#22C55E] w-[0%]"></div>
                                        </div>
                                        <div class="flex justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-4">
                                            <div class="text-[10px] font-bold text-gray-500 uppercase">Faltas restantes: 10</div>
                                            <div class="text-xl font-black text-[#1E3A8A] dark:text-white">0/10 <span class="text-xs font-normal text-gray-400">faltas</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div id="scene-3" class="absolute inset-0 px-2 flex flex-col justify-end pb-8 translate-y-full opacity-0">
                                    <div class="space-y-3">
                                        <div class="self-end bg-[#1D4ED8] text-white text-xs p-3 rounded-2xl rounded-tr-none ml-auto max-w-[80%] shadow-lg transform translate-x-10 opacity-0 chat-bubble-1">
                                            Posso faltar em História hoje?
                                        </div>
                                        <div class="self-start bg-white dark:bg-[#1f2937] border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-xs p-3 rounded-2xl rounded-tl-none max-w-[90%] shadow-lg transform -translate-x-10 opacity-0 chat-bubble-2">
                                            <div class="flex items-center gap-1 mb-1 text-[#7C3AED] dark:text-[#A78BFA] text-[9px] font-bold uppercase tracking-wider">
                                                🤖 Oráculo
                                            </div>
                                            Pode sim. Você tem 10 faltas sobrando e não tem prova agendada. Aproveite o sono! 😴
                                        </div>
                                    </div>
                                </div>
                                <div id="scene-4" class="absolute inset-0 px-2 flex flex-col items-center pt-8 opacity-0">
                                    <div id="push-notification" class="w-full bg-white/95 dark:bg-[#1f2937]/95 backdrop-blur-md p-3 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl transform -translate-y-20">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-[#06B6D4]/10 text-[#06B6D4] flex items-center justify-center">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" stroke="currentColor" stroke-width="2"/><path d="M12 10.5v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 7.5h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between">
                                                    <span class="text-xs font-bold text-[#1E3A8A] dark:text-white">Chamada! 📢</span>
                                                    <span class="text-[9px] text-gray-500 dark:text-gray-400">há 1 min</span>
                                                </div>
                                                <p class="text-[10px] text-gray-600 dark:text-gray-300 leading-tight mt-0.5">Você tem aulas hoje! Não se esqueça de registrar sua frequência para manter a média.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="scene-5" class="absolute inset-0 flex items-center justify-center scale-50 opacity-0">
                                    <div class="text-center relative">
                                        <div class="absolute inset-0 bg-[#EAB308] blur-3xl opacity-20 animate-pulse"></div>
                                        <div class="text-7xl mb-2 relative z-10 drop-shadow-2xl">🏆</div>
                                        <h3 class="text-2xl font-black text-[#1E3A8A] dark:text-white relative z-10">NOVA<br>CONQUISTA</h3>
                                        <p class="text-xs text-[#EAB308] font-bold uppercase tracking-widest mt-2 relative z-10">Inimigo da Reprovação</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="h-14 bg-white dark:bg-[#111827] border-t border-gray-200 dark:border-gray-800 flex justify-around items-center px-4 transition-colors">
                                <div class="flex flex-col items-center gap-0.5 text-[#1D4ED8] dark:text-[#06B6D4]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    <span class="text-[9px] font-bold">Início</span>
                                </div>
                                <div class="flex flex-col items-center gap-0.5 text-gray-400 dark:text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-[9px] font-medium">Grade</span>
                                </div>
                                <div class="flex flex-col items-center gap-0.5 text-gray-400 dark:text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="text-[9px] font-medium">Perfil</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <section class="min-h-screen bg-white dark:bg-zinc-900 flex items-center justify-center py-24 px-4 relative transition-colors duration-300">
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#1D4ED8] to-transparent opacity-50"></div>

        <div class="max-w-4xl w-full bg-[#F8FAFC] dark:bg-black border border-gray-200 dark:border-white/10 rounded-[3rem] p-8 md:p-16 text-center relative overflow-hidden transition-colors shadow-xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-[#1D4ED8]/10 dark:bg-[#1D4ED8]/20 blur-[100px] rounded-full"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-[#06B6D4]/10 dark:bg-[#06B6D4]/20 blur-[100px] rounded-full"></div>

            <div class="relative z-10">
                <h2 class="text-4xl md:text-6xl font-bold mb-6 tracking-tight text-[#1E3A8A] dark:text-white">Não deixe para a recuperação.</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400 mb-10 max-w-2xl mx-auto">
                    Grátis. Leve. Instala em segundos.<br>
                    <span class="text-sm text-gray-400 dark:text-gray-600 mt-2 block">(O teu "eu" do futuro vai agradecer).</span>
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    @auth
                    <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-4 bg-[#1D4ED8] text-white text-lg font-bold rounded-full hover:bg-[#1E40AF] transition shadow-lg">
                        Abrir App
                    </a>
                    @else
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-[#1E3A8A] dark:bg-white text-white dark:text-[#0F172A] text-lg font-bold rounded-full hover:scale-105 transition shadow-lg flex items-center justify-center gap-2">
                        Entrar no ritmo agora
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    @endauth
                </div>

                <div class="mt-8 flex flex-col items-center justify-center">
                    <button id="pwaInstallBtn" class="hidden px-6 py-3 border border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full font-semibold transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Instalar na tela inicial
                    </button>
                    <p id="iosHint" class="hidden text-sm text-gray-500 mt-4 flex items-center justify-center gap-1 flex-wrap">
                        <span>iPhone? Toque em</span>
                        <svg class="w-5 h-5 text-[#1D4ED8] inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        <span class="font-bold text-[#1D4ED8]">Partilhar</span>
                        <span>e</span>
                        <span class="font-bold text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded px-1">Tela de Início</span>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-10 text-center text-gray-500 text-sm bg-white dark:bg-black transition-colors border-t border-gray-100 dark:border-gray-800">
        <p class="mb-2">Feito para estudantes, por estudantes 🎓</p>
        <p>&copy; {{ date('Y') }} Frequência Certa. Desenvolvido por <a href="https://github.com/naicolas-dev" class="underline hover:text-[#1D4ED8]">Nicolas Alves</a>.</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (!window.gsap || !window.ScrollTrigger) return;
            gsap.registerPlugin(ScrollTrigger);

            const themeBtn = document.getElementById('theme-toggle');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            function updateIcons() { if (document.documentElement.classList.contains('dark')) { darkIcon.classList.remove('hidden'); lightIcon.classList.add('hidden'); } else { lightIcon.classList.remove('hidden'); darkIcon.classList.add('hidden'); } }
            themeBtn.addEventListener('click', () => { document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'; updateIcons(); });
            updateIcons();

            function createTimeline() {
                let tl = gsap.timeline();
                tl.to("#phone", { opacity: 1, duration: 1 }).to("#text-1", { opacity: 1, y: 0, duration: 1 }, "<").addLabel("scene1");
                tl.to("#text-1", { opacity: 0, y: -50, duration: 1 }).to("#scene-1", { opacity: 0.3, duration: 1 }, "<").set("#scene-1", { filter: "blur(4px)" }, "<").to("#scene-2", { y: "0%", opacity: 1, duration: 1 }).to("#calc-bar", { width: "100%", duration: 1, ease: "power2.out" }).to("#text-2", { opacity: 1, y: 0, duration: 1 }, "<").addLabel("scene2");
                tl.to("#text-2", { opacity: 0, y: -50, duration: 1 }).to("#scene-2", { opacity: 0, y: "20%", duration: 1 }, "<").to("#scene-3", { y: "0%", opacity: 1, duration: 1 }).to(".chat-bubble-1", { x: 0, opacity: 1, duration: 0.5 }).to(".chat-bubble-2", { x: 0, opacity: 1, duration: 0.5 }).to("#text-3", { opacity: 1, y: 0, duration: 1 }, "<").addLabel("scene3");
                tl.to("#text-3", { opacity: 0, y: -50, duration: 1 }).to("#scene-3", { opacity: 0, duration: 1 }, "<").to("#scene-4", { opacity: 1, duration: 0.5 }).to("#push-notification", { y: 0, duration: 0.8, ease: "back.out(1.7)" }).to("#text-4", { opacity: 1, y: 0, duration: 1 }, "<").addLabel("scene4");
                tl.to("#text-4", { opacity: 0, y: -50, duration: 1 }).to("#scene-4", { opacity: 0, duration: 1 }, "<").to("#scene-5", { scale: 1, opacity: 1, duration: 1.5, ease: "elastic.out(1, 0.5)" }).to("#text-5", { opacity: 1, y: 0, duration: 1 }, "<").addLabel("scene5");
                tl.set("#phone", { filter: "blur(10px)" }).to("#phone", { scale: 0.8, opacity: 0, duration: 1 }).to("#text-5", { opacity: 0, scale: 0.8, duration: 1 }, "<").set("#phone", { filter: "none" });
                return tl;
            }

            const mm = gsap.matchMedia();
            mm.add({ desktop: "(min-width: 768px)", mobile: "(max-width: 767px)", reduce: "(prefers-reduced-motion: reduce)" }, (ctx) => {
                const { desktop, mobile, reduce } = ctx.conditions;
                if (reduce) { gsap.set("#phone", { opacity: 1, clearProps: "transform,filter" }); gsap.set("#text-1", { opacity: 1, y: 0 }); return; }
                if (mobile) { gsap.set("#phone", { scale: 0.55, transformOrigin: "center bottom" }); } else { gsap.set("#phone", { scale: 1, transformOrigin: "center center" }); }
                const tl = createTimeline();
                ScrollTrigger.create({ animation: tl, trigger: "#scrolly-wrapper", start: "top top", end: () => "+=" + (window.innerHeight * 5), scrub: 1, pin: ".scrolly-stage", invalidateOnRefresh: true, snap: desktop ? { snapTo: "labels", duration: { min: 0.2, max: 0.8 }, delay: 0.1, ease: "power1.inOut" } : false });
            });

            let deferredPrompt; const installBtn = document.getElementById('pwaInstallBtn'); const iosHint = document.getElementById('iosHint'); const isIos = /iPhone|iPad|iPod/i.test(navigator.userAgent); const isStandalone = window.matchMedia('(display-mode: standalone)').matches;
            window.addEventListener('beforeinstallprompt', (e) => { e.preventDefault(); deferredPrompt = e; installBtn.classList.remove('hidden'); });
            installBtn.addEventListener('click', async () => { if (deferredPrompt) { deferredPrompt.prompt(); const { outcome } = await deferredPrompt.userChoice; if (outcome === 'accepted') installBtn.classList.add('hidden'); deferredPrompt = null; } });
            if (isIos && !isStandalone) { iosHint.classList.remove('hidden'); }
        });
    </script>
</body>
</html>