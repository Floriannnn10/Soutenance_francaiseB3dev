@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sonner Toaster: Initialisation...');

    // Attendre que Sonner soit disponible
    setTimeout(() => {
        // Initialisation de Sonner
        const { Toaster, toast } = window.sonner || {};

        console.log('Sonner disponible:', !!window.sonner);
        console.log('Toaster disponible:', !!Toaster);
        console.log('Toast disponible:', !!toast);

        if (Toaster && toast) {
            console.log('Sonner Toaster: Configuration...');

            // Rendre le Toaster
            const toasterContainer = document.getElementById('sonner-toaster');
            if (toasterContainer) {
                console.log('Sonner Toaster: Container trouvé');

                const toaster = new Toaster({
                    container: toasterContainer,
                    position: 'top-right',
                    richColors: true,
                    closeButton: true,
                    duration: 4000,
                    expand: true,
                    maxToasts: 5
                });

                // Exposer toast globalement
                window.Toaster = toaster;
                window.toast = toast;

                console.log('Sonner Toaster: Initialisé avec succès');

                // Gestion des messages Flash Laravel avec Sonner
                @if(session('success'))
                    console.log('Session success détectée:', "{{ session('success') }}");
                    if (window.SonnerHelper) {
                        window.SonnerHelper.success("{{ session('success') }}");
                        console.log('Notification success envoyée via SonnerHelper');
                    } else if (window.toast) {
                        window.toast.success("{{ session('success') }}");
                        console.log('Notification success envoyée via toast');
                    } else {
                        console.error('Aucune méthode de notification disponible');
                    }
                @endif

                @if(session('error'))
                    console.log('Session error détectée:', "{{ session('error') }}");
                    if (window.SonnerHelper) {
                        window.SonnerHelper.error("{{ session('error') }}");
                        console.log('Notification error envoyée via SonnerHelper');
                    } else if (window.toast) {
                        window.toast.error("{{ session('error') }}");
                        console.log('Notification error envoyée via toast');
                    } else {
                        console.error('Aucune méthode de notification disponible');
                    }
                @endif

                @if(session('warning'))
                    console.log('Session warning détectée:', "{{ session('warning') }}");
                    if (window.SonnerHelper) {
                        window.SonnerHelper.warning("{{ session('warning') }}");
                        console.log('Notification warning envoyée via SonnerHelper');
                    } else if (window.toast) {
                        window.toast.warning("{{ session('warning') }}");
                        console.log('Notification warning envoyée via toast');
                    } else {
                        console.error('Aucune méthode de notification disponible');
                    }
                @endif

                @if(session('info'))
                    console.log('Session info détectée:', "{{ session('info') }}");
                    if (window.SonnerHelper) {
                        window.SonnerHelper.info("{{ session('info') }}");
                        console.log('Notification info envoyée via SonnerHelper');
                    } else if (window.toast) {
                        window.toast.info("{{ session('info') }}");
                        console.log('Notification info envoyée via toast');
                    } else {
                        console.error('Aucune méthode de notification disponible');
                    }
                @endif
            } else {
                console.error('Sonner Toaster: Container non trouvé');
            }
        } else {
            console.error('Sonner Toaster: Toaster ou toast non disponible');
        }
    }, 100); // Attendre 100ms pour que Sonner soit chargé

    // Gestion des erreurs de validation
    @if($errors->any())
        console.log('Erreurs de validation détectées');
        @foreach($errors->all() as $error)
            setTimeout(() => {
                if (window.SonnerHelper) {
                    window.SonnerHelper.error("{{ $error }}");
                } else if (window.toast) {
                    window.toast.error("{{ $error }}");
                }
            }, 200);
        @endforeach
    @endif

    // Fonction globale pour les notifications AJAX
    window.showNotification = function(type, message, options = {}) {
        console.log('showNotification appelée:', type, message);

        setTimeout(() => {
            if (window.SonnerHelper) {
                switch(type) {
                    case 'success':
                        window.SonnerHelper.success(message, options);
                        break;
                    case 'error':
                        window.SonnerHelper.error(message, options);
                        break;
                    case 'warning':
                        window.SonnerHelper.warning(message, options);
                        break;
                    case 'info':
                        window.SonnerHelper.info(message, options);
                        break;
                    case 'loading':
                        return window.SonnerHelper.loading(message, options);
                    default:
                        window.SonnerHelper.custom(message, options);
                }
            } else if (window.toast) {
                switch(type) {
                    case 'success':
                        window.toast.success(message, options);
                        break;
                    case 'error':
                        window.toast.error(message, options);
                        break;
                    case 'warning':
                        window.toast.warning(message, options);
                        break;
                    case 'info':
                        window.toast.info(message, options);
                        break;
                    default:
                        window.toast(message, options);
                }
            } else {
                console.error('Aucune méthode de notification disponible pour showNotification');
            }
        }, 100);
    };

    // Intercepter les soumissions de formulaire pour afficher des notifications
    document.addEventListener('submit', function(e) {
        const form = e.target;
        const submitButton = form.querySelector('button[type="submit"]');

        if (submitButton && !form.hasAttribute('data-no-notification')) {
            const originalText = submitButton.innerHTML;
            const loadingToast = window.SonnerHelper ?
                window.SonnerHelper.loading('Traitement en cours...') :
                null;

            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Traitement...';
            submitButton.disabled = true;

            // Réactiver le bouton après un délai (fallback)
            setTimeout(() => {
                if (submitButton.disabled) {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                    if (loadingToast) loadingToast.dismiss();
                }
            }, 10000);
        }
    });

    console.log('Sonner Toaster: Initialisation terminée');
});
</script>
@endpush
