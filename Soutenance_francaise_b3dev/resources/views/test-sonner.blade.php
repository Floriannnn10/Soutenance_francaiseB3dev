@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-8">Test des Notifications Sonner</h1>

        <!-- Notifications Flash Laravel -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Notifications Flash Laravel</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('test-sonner.post') }}?type=success&message=Test de succès"
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Succès
                </a>

                <a href="{{ route('test-sonner.post') }}?type=error&message=Test d'erreur"
                   class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Erreur
                </a>

                <a href="{{ route('test-sonner.post') }}?type=warning&message=Test d'avertissement"
                   class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Avertissement
                </a>

                <a href="{{ route('test-sonner.post') }}?type=info&message=Test d'information"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Information
                </a>
            </div>
        </div>

        <!-- Notifications AJAX -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Notifications AJAX</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button onclick="testAjaxNotification('success', 'Test AJAX succès')"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    AJAX Succès
                </button>

                <button onclick="testAjaxNotification('error', 'Test AJAX erreur')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    AJAX Erreur
                </button>

                <button onclick="testAjaxNotification('warning', 'Test AJAX avertissement')"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    AJAX Avertissement
                </button>

                <button onclick="testAjaxNotification('info', 'Test AJAX information')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    AJAX Information
                </button>
            </div>
        </div>

        <!-- Notifications Directes -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Notifications Directes</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button onclick="testDirectNotification('success', 'Test direct succès')"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Direct Succès
                </button>

                <button onclick="testDirectNotification('error', 'Test direct erreur')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Direct Erreur
                </button>

                <button onclick="testDirectNotification('warning', 'Test direct avertissement')"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Direct Avertissement
                </button>

                <button onclick="testDirectNotification('info', 'Test direct information')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Direct Information
                </button>
            </div>
        </div>

        <!-- Test de chargement -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Test de Chargement</h2>
            <button onclick="testLoadingNotification()"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Test Chargement
            </button>
        </div>

        <!-- Statut Sonner -->
        <div class="mt-8 p-4 bg-gray-100 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Statut Sonner</h3>
            <div id="sonner-status" class="text-sm text-gray-600">
                Vérification en cours...
            </div>
        </div>
    </div>
</div>

<script>
function testAjaxNotification(type, message) {
    const formData = new FormData();
    formData.append('type', type);
    formData.append('message', message);

    fetch('{{ route("test-sonner.post") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.notification && window.showNotification) {
            window.showNotification(data.notification.type, data.notification.message);
        } else if (window.SonnerHelper) {
            switch(data.notification.type) {
                case 'success':
                    window.SonnerHelper.success(data.notification.message);
                    break;
                case 'error':
                    window.SonnerHelper.error(data.notification.message);
                    break;
                case 'warning':
                    window.SonnerHelper.warning(data.notification.message);
                    break;
                case 'info':
                    window.SonnerHelper.info(data.notification.message);
                    break;
            }
        } else if (window.toast) {
            switch(data.notification.type) {
                case 'success':
                    window.toast.success(data.notification.message);
                    break;
                case 'error':
                    window.toast.error(data.notification.message);
                    break;
                case 'warning':
                    window.toast.warning(data.notification.message);
                    break;
                case 'info':
                    window.toast.info(data.notification.message);
                    break;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (window.SonnerHelper) {
            window.SonnerHelper.error('Erreur lors du test AJAX');
        } else if (window.toast) {
            window.toast.error('Erreur lors du test AJAX');
        }
    });
}

function testDirectNotification(type, message) {
    if (window.SonnerHelper) {
        switch(type) {
            case 'success':
                window.SonnerHelper.success(message);
                break;
            case 'error':
                window.SonnerHelper.error(message);
                break;
            case 'warning':
                window.SonnerHelper.warning(message);
                break;
            case 'info':
                window.SonnerHelper.info(message);
                break;
        }
    } else if (window.toast) {
        switch(type) {
            case 'success':
                window.toast.success(message);
                break;
            case 'error':
                window.toast.error(message);
                break;
            case 'warning':
                window.toast.warning(message);
                break;
            case 'info':
                window.toast.info(message);
                break;
        }
    } else {
        alert('Sonner non disponible');
    }
}

function testLoadingNotification() {
    if (window.SonnerHelper) {
        const loadingToast = window.SonnerHelper.loading('Chargement en cours...');
        setTimeout(() => {
            loadingToast.dismiss();
            window.SonnerHelper.success('Chargement terminé !');
        }, 3000);
    } else if (window.toast) {
        const loadingToast = window.toast.loading('Chargement en cours...');
        setTimeout(() => {
            loadingToast.dismiss();
            window.toast.success('Chargement terminé !');
        }, 3000);
    } else {
        alert('Sonner non disponible');
    }
}

// Vérifier le statut de Sonner
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const statusDiv = document.getElementById('sonner-status');
        let status = '';

        if (window.SonnerHelper) {
            status += '✅ SonnerHelper disponible<br>';
        } else {
            status += '❌ SonnerHelper non disponible<br>';
        }

        if (window.toast) {
            status += '✅ Toast disponible<br>';
        } else {
            status += '❌ Toast non disponible<br>';
        }

        if (window.Toaster) {
            status += '✅ Toaster disponible<br>';
        } else {
            status += '❌ Toaster non disponible<br>';
        }

        if (window.showNotification) {
            status += '✅ showNotification disponible<br>';
        } else {
            status += '❌ showNotification non disponible<br>';
        }

        statusDiv.innerHTML = status;
    }, 1000);
});
</script>
@endsection
