<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-6">🧪 Test des Drops et Notifications</h2>

                    <!-- Statistiques -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">📊 Statistiques</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white p-3 rounded border">
                                <p class="text-2xl font-bold text-blue-600">{{ \App\Models\EtudiantMatiereDropped::count() }}</p>
                                <p class="text-sm text-gray-600">Drops créés</p>
                            </div>
                            <div class="bg-white p-3 rounded border">
                                <p class="text-2xl font-bold text-green-600">{{ \App\Models\CustomNotification::count() }}</p>
                                <p class="text-sm text-gray-600">Notifications</p>
                            </div>
                            <div class="bg-white p-3 rounded border">
                                <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Etudiant::where('email', 'like', '%dropped%')->count() }}</p>
                                <p class="text-sm text-gray-600">Étudiants dropped</p>
                            </div>
                        </div>
                    </div>

                    <!-- Test des notifications -->
                    <div class="mb-6 p-4 bg-green-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">🔧 Test des Notifications</h3>
                        <p class="mb-4">Testez les notifications de drop :</p>

                        <div class="space-x-4">
                            <button onclick="testDropNotification()" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                ⚠️ Test Notification Drop
                            </button>

                            <button onclick="processAutomaticDrops()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                🔄 Traiter Drops Automatiques
                            </button>

                            <button onclick="checkMissingNotifications()" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                📧 Vérifier Notifications Manquantes
                            </button>
                        </div>
                    </div>

                    <!-- Liste des drops -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">📋 Drops Existants</h3>
                        @php
                            $drops = \App\Models\EtudiantMatiereDropped::with(['etudiant', 'matiere'])->get();
                        @endphp

                        @if($drops->count() > 0)
                            <div class="space-y-2">
                                @foreach($drops as $drop)
                                    <div class="p-3 bg-white rounded border">
                                        <p><strong>Étudiant :</strong> {{ $drop->etudiant->prenom }} {{ $drop->etudiant->nom }}</p>
                                        <p><strong>Matière :</strong> {{ $drop->matiere->nom }}</p>
                                        <p><strong>Date :</strong> {{ $drop->date_drop->format('d/m/Y H:i') }}</p>
                                        <p><strong>Raison :</strong> {{ $drop->raison_drop }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">Aucun drop trouvé.</p>
                        @endif
                    </div>

                    <!-- Instructions -->
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">📝 Instructions</h3>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Cliquez sur "Test Notification Drop" pour voir une notification de test</li>
                            <li>Utilisez "Traiter Drops Automatiques" pour relancer le système</li>
                            <li>Connectez-vous avec un compte étudiant dropped pour voir les vraies notifications</li>
                            <li>Vérifiez la console du navigateur (F12) pour les logs de debug</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testDropNotification() {
            const testMessage = "Vous avez été droppé de la matière \"Mathématiques\" le 15/01/2025 à 14:30. Vous devez reprendre ce cours l'année prochaine.";

            if (typeof window.showNotification === 'function') {
                window.showNotification('warning', testMessage);
                console.log('✅ Notification de test affichée');
            } else {
                alert('Fonction showNotification non trouvée');
                console.error('❌ Fonction showNotification non disponible');
            }
        }

        function processAutomaticDrops() {
            fetch('/api/drops/process-automatic', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof window.showNotification === 'function') {
                        window.showNotification('success', data.message);
                    } else {
                        alert(data.message);
                    }
                } else {
                    if (typeof window.showNotification === 'function') {
                        window.showNotification('error', data.message);
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof window.showNotification === 'function') {
                    window.showNotification('error', 'Erreur lors du traitement des drops');
                } else {
                    alert('Erreur lors du traitement des drops');
                }
            });
        }

        function checkMissingNotifications() {
            fetch('/api/drops/check-notifications', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof window.showNotification === 'function') {
                        window.showNotification('success', data.message);
                    } else {
                        alert(data.message);
                    }
                } else {
                    if (typeof window.showNotification === 'function') {
                        window.showNotification('error', data.message);
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof window.showNotification === 'function') {
                    window.showNotification('error', 'Erreur lors de la vérification des notifications');
                } else {
                    alert('Erreur lors de la vérification des notifications');
                }
            });
        }

        // Vérifier au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page de test des drops chargée');

            if (typeof window.showNotification === 'function') {
                console.log('✅ Fonction showNotification disponible');
            } else {
                console.error('❌ Fonction showNotification non disponible');
            }
        });
    </script>
</x-app-layout>
