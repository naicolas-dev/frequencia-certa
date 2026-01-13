import './bootstrap';
import gsap from 'gsap';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

import { socialLogin, consumeRedirectResult } from './firebase-auth';

window.Alpine = Alpine;
window.Swal = Swal;

// --- CONFIGURAÇÃO FIREBASE AUTH ---
window.firebaseAuth = window.firebaseAuth || {};
window.firebaseAuth.socialLogin = socialLogin;

// Compatibilidade com os Blades atuais
window.socialLogin = socialLogin;

if (typeof consumeRedirectResult === 'function') {
    consumeRedirectResult().catch((err) => {
        console.error('Erro ao finalizar login via redirect (Firebase):', err?.code || err);
    });
}

Alpine.start();


/* =========================
   SWEETALERT CONFIRM GLOBAL
========================= */

const swalTailwind = Swal.mixin({
    customClass: {
        popup: `
            rounded-3xl 
            bg-white dark:bg-gray-900 
            text-gray-800 dark:text-gray-100 
            shadow-2xl 
            border border-gray-100 dark:border-gray-800
        `,
        title: 'text-lg font-bold',
        htmlContainer: 'text-sm text-gray-500 dark:text-gray-400',
        actions: 'gap-3',
        confirmButton: `
            bg-blue-600 hover:bg-blue-700 
            text-white font-bold 
            px-6 py-3 rounded-xl 
            shadow-lg shadow-blue-500/30 
            transition active:scale-95
        `,
        cancelButton: `
            bg-gray-200 dark:bg-gray-800 
            text-gray-700 dark:text-gray-300 
            font-bold 
            px-6 py-3 rounded-xl 
            transition active:scale-95
        `
    },
    buttonsStyling: false,
    backdrop: `
        bg-black/50 
        backdrop-blur-sm
    `
});

window.swalTailwind = swalTailwind;

/* =========================
   TOAST GLOBAL
========================= */
const toast = Swal.mixin({
    toast: true,
    position: window.innerWidth > 768 ? 'bottom' : 'top',
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
    customClass: {
        popup: `
            bg-white dark:bg-gray-900
            text-gray-800 dark:text-gray-100
            rounded-2xl
            shadow-xl
            border border-gray-100 dark:border-gray-800
            px-4 py-3
            md:mb-6
        `,
        title: 'text-sm font-semibold',
        icon: 'scale-90'
    },
    showClass: {
        popup: `
            animate__animated 
            ${window.innerWidth > 768 ? 'animate__fadeInUp' : 'animate__fadeInDown'}
            animate__faster
        `
    }
});

// SUCESSO (Verde / Emerald)
window.toastSuccess = (msg) =>
    toast.fire({
        icon: 'success',
        title: msg,
        iconColor: '#10b981',
        background: '#ecfdf5', 
        color: '#064e3b',     
    });

// ERRO (Vermelho / Red)
window.toastError = (msg) =>
    toast.fire({
        icon: 'error',
        title: msg,
        iconColor: '#ef4444', 
        background: '#fef2f2',
        color: '#7f1d1d',      
    });

// AVISO (Amarelo / Amber)
window.toastWarning = (msg) =>
    toast.fire({
        icon: 'warning',
        title: msg,
        iconColor: '#f59e0b', 
        background: '#fffbeb',
        color: '#78350f',      
    });

// INFO (Azul / Blue)
window.toastInfo = (msg) =>
    toast.fire({
        icon: 'info',
        title: msg,
        iconColor: '#3b82f6',
        background: '#eff6ff',
        color: '#1e3a8a',     
    });

/* =========================
   CONFIRM SUBMIT LISTENER
========================= */

document.addEventListener('submit', function (e) {
    const form = e.target;
    const message = form.getAttribute('data-confirm');

    if (!message) return;

    e.preventDefault();

    swalTailwind.fire({
        title: 'Confirmação',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});

/* =========================
   SERVICE WORKER REGISTRATION
========================= */

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')           
    });
}

/* =========================
   NOTIFICAÇÕES PUSH
========================= */

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

window.pedirPermissaoNotificacao = async () => {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        console.error('Push notifications não suportadas.');
        return false;
    }

    // 1. Pede permissão ao navegador
    const permission = await Notification.requestPermission();
    
    if (permission === 'granted') {
        try {
            // 2. Obtém o SW e a chave pública
            const registration = await navigator.serviceWorker.ready;
            
            const vapidPublicKey = import.meta.env.VITE_VAPID_PUBLIC_KEY; 

            if (!vapidPublicKey) {
                console.error('Chave VAPID não configurada no .env');
                return false;
            }

            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
            });

            // 3. Envia para o Laravel
            await fetch('/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(subscription)
            });

            window.toastSuccess('Notificações ativadas!');
            return true;
        } catch (error) {
            console.error('Erro ao inscrever push:', error);
            window.toastError('Erro ao ativar notificações.');
            return false;
        }
    }
    return false;
};

/* =========================
   LÓGICA DO LOADER (GSAP) 
========================= */

// Função separada para esconder o loader
function hideLoader() {
    const loader = document.querySelector('#page-loader');
    if (!loader) return;

    const tl = gsap.timeline();

    // 1. Mostra o logo/texto
    tl.to('.loader-content', {
        opacity: 1,
        y: 0,
        duration: 0.5,
        ease: 'power2.out'
    })
    // 2. Sobe a cortina
    .to('#page-loader', {
        yPercent: -100,
        duration: 1.2,
        ease: 'power4.inOut',
        delay: 0.2,
        onComplete: () => {
            if (document.querySelector('#page-loader')) {
                document.querySelector('#page-loader').style.display = 'none'; 
            }
        }
    });
}

// Evento 1: Carregamento normal da página
window.addEventListener('load', hideLoader);

setTimeout(() => {
    const loader = document.querySelector('#page-loader');
    if (loader && loader.style.display !== 'none') {
        hideLoader();
    }
}, 3000);

// Evento 2: Correção do botão "Voltar" (BFCache)
// Impede que o loader fique travado na tela ao voltar no celular
window.addEventListener('pageshow', (event) => {
    if (event.persisted) { 
        const loader = document.querySelector('#page-loader');
        if (loader) {
            // Mata animações pendentes
            gsap.killTweensOf(loader);
            gsap.killTweensOf('.loader-content');
            
            // Força o desaparecimento
            loader.style.display = 'none';
            loader.style.transform = 'translateY(-100%)'; 
        }
    }
});

// Evento 3: Animação de Saída (Clique nos links)
document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('a');

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');

            // Filtros de segurança
            if (
                !href || 
                href.startsWith('#') || 
                link.target === '_blank' || 
                href.includes('mailto:') ||
                href.includes('tel:') || // Adicionado tel:
                window.location.href === href ||
                e.ctrlKey || e.metaKey // Permite abrir em nova aba
            ) {
                return;
            }

            e.preventDefault();

            const loader = document.querySelector('#page-loader');
            
            if (loader) {
                loader.style.display = 'flex';

                // Anima a cortina descendo
                gsap.fromTo('#page-loader', 
                    { yPercent: 100 }, 
                    { 
                        yPercent: 0, 
                        duration: 0.8, 
                        ease: 'power4.inOut',
                        onComplete: () => {
                            window.location.href = href;
                        }
                    }
                );
            } else {
                // Fallback caso não tenha loader na página
                window.location.href = href;
            }
        });
    });
});