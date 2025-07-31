<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-6">🧪 Test Simple des Notifications</h2>

                    <!-- Informations de debug -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">🔍 Debug Info</h3>
                        <div id="debug-info">
                            <p><strong>Fonction showNotification :</strong> <span id="showNotification-status">Vérification...</span></p>
                            <p><strong>Notifications en base :</strong> {{ \App\Models\CustomNotification::count() }}</p>
                            <p><strong>Utilisateur connecté :</strong> {{ auth()->user() ? auth()->user()->email : 'Non connecté' }}</p>
                        </div>
                    </div>

                    <!-- Test manuel -->
                    <div class="mb-6 p-4 bg-green-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">🔧 Test Manuel</h3>
                        <button onclick="testNotification()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            🧪 Tester une Notification
                        </button>
                    </div>

                    <!-- Instructions -->
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">📝 Instructions</h3>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Cliquez sur le bouton "Tester une Notification"</li>
                            <li>Une notification devrait apparaître en haut à droite</li>
                            <li>Vérifiez la console du navigateur (F12) pour les logs</li>
                            <li>Si ça ne fonctionne pas, vérifiez que DaisyUI est bien chargé</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testNotification() {
            console.log('Test de notification...');

            if (typeof window.showNotification === 'function') {
                window.showNotification('warning', 'Test de notification - Vous avez été droppé de la matière "Mathématiques" le 15/01/2025 à 14:30. Vous devez reprendre ce cours l\'année prochaine.');
                console.log('✅ Notification envoyée');
            } else {
                console.error('❌ Fonction showNotification non disponible');
                alert('Fonction showNotification non disponible');
            }
        }

        // Vérifier au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page de test simple chargée');

            const statusElement = document.getElementById('showNotification-status');

            if (typeof window.showNotification === 'function') {
                statusElement.textContent = '✅ Disponible';
                statusElement.className = 'text-green-600 font-bold';
                console.log('✅ Fonction showNotification disponible');
            } else {
                statusElement.textContent = '❌ Non disponible';
                statusElement.className = 'text-red-600 font-bold';
                console.error('❌ Fonction showNotification non disponible');
            }
        });
    </script>
</x-app-layout>
