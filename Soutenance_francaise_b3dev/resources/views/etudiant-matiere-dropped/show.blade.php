<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de l\'abandon de matière') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('etudiant-matiere-dropped.edit', $drop->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Modifier
                </a>
                <a href="{{ route('etudiant-matiere-dropped.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Retour à la liste
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informations de l'étudiant -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-800 mb-4">👨‍🎓 Informations de l'étudiant</h3>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-blue-800 font-semibold">
                                            {{ strtoupper(substr($drop->etudiant->prenom, 0, 1) . substr($drop->etudiant->nom, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $drop->etudiant->prenom }} {{ $drop->etudiant->nom }}</p>
                                        <p class="text-sm text-gray-600">{{ $drop->etudiant->classe->nom ?? 'Classe non définie' }}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <p><strong>Email:</strong> {{ $drop->etudiant->email ?? 'Non renseigné' }}</p>
                                    <p><strong>Date de naissance:</strong> {{ $drop->etudiant->date_naissance ? $drop->etudiant->date_naissance->format('d/m/Y') : 'Non renseignée' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Informations de la matière -->
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-800 mb-4">📚 Matière abandonnée</h3>
                            <div class="space-y-2">
                                <p class="font-semibold text-gray-900">{{ $drop->matiere->nom }}</p>
                                <p class="text-sm text-gray-600"><strong>Code:</strong> {{ $drop->matiere->code }}</p>
                                <p class="text-sm text-gray-600"><strong>Coefficient:</strong> {{ $drop->matiere->coefficient }}</p>
                                <p class="text-sm text-gray-600"><strong>Volume horaire:</strong> {{ $drop->matiere->volume_horaire }}h</p>
                            </div>
                        </div>

                        <!-- Informations académiques -->
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">📅 Informations académiques</h3>
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600"><strong>Année académique:</strong> {{ $drop->anneeAcademique->nom }}</p>
                                <p class="text-sm text-gray-600"><strong>Semestre:</strong> {{ $drop->semestre->nom }}</p>
                                <p class="text-sm text-gray-600"><strong>Date d'abandon:</strong> {{ $drop->date_drop->format('d/m/Y') }}</p>
                                <p class="text-sm text-gray-600"><strong>Il y a:</strong> {{ $drop->date_drop->diffForHumans() }}</p>
                            </div>
                        </div>

                        <!-- Informations de l'abandon -->
                        <div class="bg-red-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-red-800 mb-4">❌ Détails de l'abandon</h3>
                            <div class="space-y-2">
                                @if($drop->raison_drop)
                                    <p class="text-sm text-gray-600"><strong>Raison:</strong></p>
                                    <p class="text-sm text-gray-800 bg-white p-2 rounded border">{{ $drop->raison_drop }}</p>
                                @else
                                    <p class="text-sm text-gray-600"><strong>Raison:</strong> <span class="text-gray-400">Aucune raison spécifiée</span></p>
                                @endif

                                @if($drop->droppedByUser)
                                    <p class="text-sm text-gray-600"><strong>Marqué par:</strong> {{ $drop->droppedByUser->prenom }} {{ $drop->droppedByUser->nom }}</p>
                                @else
                                    <p class="text-sm text-gray-600"><strong>Marqué par:</strong> <span class="text-gray-400">Utilisateur non spécifié</span></p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex justify-center space-x-4">
                        <button onclick="restoreStudent({{ $drop->id }})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            🔄 Rétablir l'étudiant dans cette matière
                        </button>
                        <a href="{{ route('etudiant-matiere-dropped.edit', $drop->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            ✏️ Modifier
                        </a>
                        <a href="{{ route('etudiant-matiere-dropped.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            📋 Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function restoreStudent(dropId) {
            if (confirm('Êtes-vous sûr de vouloir rétablir cet étudiant dans la matière ? Cette action ne peut pas être annulée.')) {
                fetch(`/etudiant-matiere-dropped/${dropId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Étudiant rétabli avec succès !');
                        window.location.href = '{{ route("etudiant-matiere-dropped.index") }}';
                    } else {
                        alert('Erreur lors du rétablissement de l\'étudiant');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors du rétablissement de l\'étudiant');
                });
            }
        }
    </script>
</x-app-layout>
