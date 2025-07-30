<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Test des Notifications DaisyUI</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                        <button onclick="window.showSuccess('Test de succès !')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                            Test Succès (Vert)
                        </button>

                        <button onclick="window.showWarning('Test d\'avertissement !')" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded">
                            Test Avertissement (Orange)
                        </button>

                        <button onclick="window.showError('Test d\'erreur !')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                            Test Erreur (Rouge)
                        </button>

                        <button onclick="window.showInfo('Test d\'information !')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Test Info (Bleu)
                        </button>
                    </div>

                    <div class="bg-gray-100 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2">Instructions de test :</h2>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Cliquez sur les boutons ci-dessus pour tester les notifications</li>
                            <li>Ouvrez la console du navigateur (F12) pour voir les logs de debug</li>
                            <li>Les notifications doivent apparaître en haut à droite</li>
                            <li>Elles doivent disparaître automatiquement après 5 secondes</li>
                        </ol>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-2">Debug Info :</h3>
                        <div id="debug-info" class="bg-gray-200 p-3 rounded text-sm">
                            Chargement...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const debugInfo = document.getElementById('debug-info');
            debugInfo.innerHTML = `
                <strong>Fonctions disponibles :</strong><br>
                showNotification: ${typeof window.showNotification === 'function' ? '✅' : '❌'}<br>
                showSuccess: ${typeof window.showSuccess === 'function' ? '✅' : '❌'}<br>
                showError: ${typeof window.showError === 'function' ? '✅' : '❌'}<br>
                showWarning: ${typeof window.showWarning === 'function' ? '✅' : '❌'}<br>
                showInfo: ${typeof window.showInfo === 'function' ? '✅' : '❌'}<br>
                <br>
                <strong>DaisyUI disponible :</strong> ${typeof window.daisyui !== 'undefined' ? '✅' : '❌'}<br>
                <strong>Body classes :</strong> ${document.body.className}<br>
                <strong>Toast container :</strong> ${document.querySelector('.toast') ? '✅' : '❌'}
            `;
        });
    </script>
</x-app-layout>
