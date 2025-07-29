@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Exemples d'utilisation de Sonner</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Exemples de notifications dans les formulaires -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Notifications dans les formulaires</h2>

            <!-- Formulaire avec notification de succès -->
            <form method="POST" action="#" class="mb-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                    <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Créer avec notification
                </button>
            </form>

            <!-- Formulaire avec notification d'erreur -->
            <form method="POST" action="#" class="mb-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                    Créer avec erreur
                </button>
            </form>
        </div>

        <!-- Exemples de notifications AJAX -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Notifications AJAX</h2>

            <div class="space-y-3">
                <button onclick="testAjaxSuccess()" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    Test AJAX Succès
                </button>
                <button onclick="testAjaxError()" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Test AJAX Erreur
                </button>
                <button onclick="testAjaxPromise()" class="w-full bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">
                    Test AJAX Promesse
                </button>
            </div>
        </div>
    </div>

    <!-- Exemples de notifications contextuelles -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Notifications contextuelles</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="showStudentDropped()" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded">
                Étudiant abandonné
            </button>
            <button onclick="showSessionCreated()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Session créée
            </button>
            <button onclick="showPresenceMarked()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                Présence marquée
            </button>
        </div>
    </div>
</div>

<script>
// Exemples de notifications AJAX
function testAjaxSuccess() {
    // Simuler une requête AJAX réussie
    showNotification('success', 'Données sauvegardées avec succès !');
}

function testAjaxError() {
    // Simuler une erreur AJAX
    showNotification('error', 'Erreur lors de la sauvegarde des données.');
}

function testAjaxPromise() {
    // Simuler une promesse AJAX
    const promise = new Promise((resolve, reject) => {
        setTimeout(() => {
            Math.random() > 0.5 ? resolve() : reject();
        }, 2000);
    });

    SonnerHelper.promise(promise, {
        loading: 'Sauvegarde en cours...',
        success: 'Données sauvegardées !',
        error: 'Erreur de sauvegarde'
    });
}

// Exemples de notifications contextuelles
function showStudentDropped() {
    SonnerHelper.warning('L\'étudiant Moussa Traoré a abandonné le cours de Programmation Java', {
        duration: 6000,
        icon: '👨‍🎓'
    });
}

function showSessionCreated() {
    SonnerHelper.success('Session de cours "Développement Web PHP" créée pour la classe M2 DEV A', {
        duration: 5000,
        icon: '📚'
    });
}

function showPresenceMarked() {
    SonnerHelper.info('Présence marquée pour 25 étudiants sur 30', {
        duration: 4000,
        icon: '✅'
    });
}

// Intercepter les soumissions de formulaire pour des tests
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const button = form.querySelector('button[type="submit"]');
            const buttonText = button.textContent;

            if (buttonText.includes('succès')) {
                showNotification('success', 'Élément créé avec succès !');
            } else if (buttonText.includes('erreur')) {
                showNotification('error', 'Erreur lors de la création !');
            }
        });
    });
});
</script>
@endsection
