import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Système de notifications DaisyUI
window.showNotification = function(type, message) {
    console.log('showNotification called:', type, message);

    // Supprimer les toasts existants
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => toast.remove());

                // Créer le toast
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 z-50 toast-notification';

                // Déterminer la classe d'alerte selon le type
            let alertClass = 'bg-blue-100 border-blue-400 text-blue-700';
            let iconClass = 'text-blue-500';
            let icon = 'ℹ️';

            switch(type) {
                case 'success':
                    alertClass = 'bg-green-100 border-green-400 text-green-700';
                    iconClass = 'text-green-500';
                    icon = '✅';
                    break;
                case 'error':
                    alertClass = 'bg-red-100 border-red-400 text-red-700';
                    iconClass = 'text-red-500';
                    icon = '❌';
                    break;
                case 'warning':
                    alertClass = 'bg-yellow-100 border-yellow-400 text-yellow-700';
                    iconClass = 'text-yellow-500';
                    icon = '⚠️';
                    break;
                case 'info':
                default:
                    alertClass = 'bg-blue-100 border-blue-400 text-blue-700';
                    iconClass = 'text-blue-500';
                    icon = 'ℹ️';
                    break;
            }

            toast.innerHTML = `
                <div class="flex items-center p-4 mb-4 border rounded-lg ${alertClass} shadow-lg">
                    <span class="mr-2 text-lg">${icon}</span>
                    <span class="font-medium">${message}</span>
                </div>
            `;

    // Ajouter au body
    document.body.appendChild(toast);
    console.log('Toast added to DOM:', toast);

    // Supprimer automatiquement après 20 secondes pour les notifications de drop
    const duration = type === 'warning' ? 20000 : 5000;
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, duration);
};

// Fonction pour gérer les réponses JSON des contrôleurs
window.handleControllerResponse = function(response) {
    if (response && response.notification) {
        const { type, message } = response.notification;
        window.showNotification(type, message);
    }
};

// Fonctions utilitaires pour différents types de notifications
window.showSuccess = function(message) {
    window.showNotification('success', message);
};

window.showError = function(message) {
    window.showNotification('error', message);
};

window.showWarning = function(message) {
    window.showNotification('warning', message);
};

window.showInfo = function(message) {
    window.showNotification('info', message);
};

// Debug: Vérifier que les fonctions sont disponibles
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking notification functions...');
    console.log('showNotification available:', typeof window.showNotification === 'function');
    console.log('showSuccess available:', typeof window.showSuccess === 'function');
    console.log('showError available:', typeof window.showError === 'function');
    console.log('showWarning available:', typeof window.showWarning === 'function');
    console.log('showInfo available:', typeof window.showInfo === 'function');
});
