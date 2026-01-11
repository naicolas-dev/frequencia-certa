import './bootstrap';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

import { socialLogin, consumeRedirectResult } from './firebase-auth';

window.Alpine = Alpine;
window.Swal = Swal;

window.firebaseAuth = window.firebaseAuth || {};
window.firebaseAuth.socialLogin = socialLogin;

// compatibilidade com seus Blades atuais (@click="handleSocial('google')" -> window.socialLogin)
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
    timer: 2500,
    timerProgressBar: true,
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
            .then((registration) => {
                console.log('SW registrado com sucesso:', registration.scope);
            })
            .catch((err) => {
                console.log('Falha ao registrar SW:', err);
            });
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
            // 2. Obtém o SW e a chave pública (definida no blade ou .env)
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