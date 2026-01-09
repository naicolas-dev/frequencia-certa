import './bootstrap';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

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
    position: 'top-end',
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true,
    customClass: {
        popup: `
            bg-white dark:bg-gray-900
            text-gray-800 dark:text-gray-100
            rounded-2xl
            shadow-xl
            border border-gray-100 dark:border-gray-800
            px-4 py-3
        `,
        title: 'text-sm font-semibold',
        icon: 'scale-90'
    }
});

window.toastSuccess = (msg) =>
    toast.fire({ icon: 'success', title: msg, iconColor: '#10b981' });

window.toastError = (msg) =>
    toast.fire({ icon: 'error', title: msg, iconColor: '#ef4444' });

window.toastWarning = (msg) =>
    toast.fire({ icon: 'warning', title: msg, iconColor: '#f59e0b' });

window.toastInfo = (msg) =>
    toast.fire({ icon: 'info', title: msg, iconColor: '#3b82f6' });

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

// Função auxiliar para converter a chave VAPID
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

window.pedirPermissaoNotificacao = async () => {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        return;
    }

    const permission = await Notification.requestPermission();
    
    if (permission === 'granted') {
        // 1. Obter o registo do Service Worker
        const registration = await navigator.serviceWorker.ready;

        // 2. Subscrever no PushManager (Browser)
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(window.VAPID_PUBLIC_KEY) 
            // Dica: Pode imprimir {{ config('webpush.vapid.public_key') }} num <script> no blade para não deixar hardcoded
        });

        // 3. Enviar para o Laravel
        await fetch('/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(subscription)
        });

        window.toastSuccess('Notificações ativadas com sucesso!');
    }
};