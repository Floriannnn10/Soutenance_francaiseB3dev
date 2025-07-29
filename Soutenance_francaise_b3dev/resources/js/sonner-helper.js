// Helper pour Sonner dans Laravel
import { Toaster, toast } from 'sonner';

console.log('Sonner Helper: Import de Toaster et toast reussi');

// Fonctions utilitaires pour les notifications
window.SonnerHelper = {
    // Notification de succès
    success: (message, options = {}) => {
        console.log('SonnerHelper.success appele:', message);
        toast.success(message, {
            duration: 4000,
            ...options
        });
    },

    // Notification d'erreur
    error: (message, options = {}) => {
        console.log('SonnerHelper.error appele:', message);
        toast.error(message, {
            duration: 5000,
            ...options
        });
    },

    // Notification d'information
    info: (message, options = {}) => {
        console.log('SonnerHelper.info appele:', message);
        toast.info(message, {
            duration: 3000,
            ...options
        });
    },

    // Notification d'avertissement
    warning: (message, options = {}) => {
        console.log('SonnerHelper.warning appele:', message);
        toast.warning(message, {
            duration: 4000,
            ...options
        });
    },

    // Notification de chargement
    loading: (message, options = {}) => {
        console.log('SonnerHelper.loading appele:', message);
        return toast.loading(message, {
            duration: Infinity,
            ...options
        });
    },

    // Notification de promesse
    promise: (promise, messages = {}) => {
        console.log('SonnerHelper.promise appele:', messages);
        return toast.promise(promise, {
            loading: messages.loading || 'Chargement...',
            success: messages.success || 'Succes !',
            error: messages.error || 'Erreur !'
        });
    },

    // Notification personnalisée
    custom: (message, options = {}) => {
        console.log('SonnerHelper.custom appele:', message);
        return toast(message, {
            duration: 4000,
            ...options
        });
    }
};

// Exposer toast et Toaster globalement
window.toast = toast;
window.Toaster = Toaster;

console.log('Sonner Helper: Initialisation terminee');
console.log('SonnerHelper disponible:', !!window.SonnerHelper);
console.log('toast disponible:', !!window.toast);
console.log('Toaster disponible:', !!window.Toaster);
