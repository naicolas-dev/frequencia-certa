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
