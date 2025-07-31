<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-6">🧪 Test des Notifications de Drop</h2>

                    <!-- Informations sur les notifications -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">📊 Statistiques des Notifications</h3>
                        <p><strong>Notifications créées :</strong> {{ \App\Models\CustomNotification::count() }}</p>
                        <p><strong>Drops créés :</strong> {{ \App\Models\EtudiantMatiereDropped::count() }}</p>
                        <p><strong>Étudiants en situation de dropping :</strong> {{ \App\Models\Etudiant::where('email', 'like', '%dropped%')->count() }}</p>
                    </div>

                    <!-- Test manuel des notifications -->
                    <div class="mb-6 p-4 bg-green-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">🔧 Test Manuel</h3>
                        <p class="mb-4">Cliquez sur les boutons ci-dessous pour tester les notifications :</p>

                        <div class="space-x-4">
                            <button onclick="testWarningNotification()" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                ⚠️ Test Warning (Drop)
                            </button>

                            <button onclick="testSuccessNotification()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                ✅ Test Success
                            </button>

                            <button onclick="testErrorNotification()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                ❌ Test Error
                            </button>
                        </div>
                    </div>

                    <!-- Liste des notifications existantes -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">📋 Notifications Existantes</h3>
                        @php
                            $notifications = \App\Models\CustomNotification::with('utilisateurs')->get();
                        @endphp

                        @if($notifications->count() > 0)
                            <div class="space-y-2">
                                @foreach($notifications as $notification)
                                    <div class="p-3 bg-white rounded border">
                                        <p><strong>Message :</strong> {{ $notification->message }}</p>
                                        <p><strong>Type :</strong> {{ $notification->type }}</p>
                                        <p><strong>Date :</strong> {{ $notification->created_at->format('d/m/Y H:i') }}</p>
                                        <p><strong>Destinataires :</strong> {{ $notification->utilisateurs->count() }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">Aucune notification trouvée.</p>
                        @endif
                    </div>

                    <!-- Instructions -->
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">📝 Instructions</h3>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Connectez-vous avec un compte étudiant en situation de dropping</li>
                            <li>Les notifications devraient s'afficher automatiquement au chargement de la page</li>
                            <li>Si aucune notification n'apparaît, vérifiez la console du navigateur</li>
                            <li>Utilisez les boutons de test pour vérifier que le système fonctionne</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testWarningNotification() {
            if (typeof window.showNotification === 'function') {
                window.showNotification('warning', 'Test de notification de drop - Vous avez été droppé de la matière "Mathématiques" le 30/07/2025 à 13:30. Vous devez reprendre ce cours l\'année prochaine.');
            } else {
                alert('Fonction showNotification non trouvée');
            }
        }

        function testSuccessNotification() {
            if (typeof window.showNotification === 'function') {
                window.showNotification('success', 'Test de notification de succès');
            } else {
                alert('Fonction showNotification non trouvée');
            }
        }

        function testErrorNotification() {
            if (typeof window.showNotification === 'function') {
                window.showNotification('error', 'Test de notification d\'erreur');
            } else {
                alert('Fonction showNotification non trouvée');
            }
        }

        // Afficher les notifications de drop au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page de test chargée');

            // Vérifier si window.showNotification existe
            if (typeof window.showNotification === 'function') {
                console.log('✅ Fonction showNotification disponible');
            } else {
                console.error('❌ Fonction showNotification non disponible');
            }
        });
    </script>
</x-app-layout>
