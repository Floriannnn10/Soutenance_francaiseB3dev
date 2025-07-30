<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Étudiants ayant abandonné des matières') }}
            </h2>
            <a href="{{ route('etudiant-matiere-dropped.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Marquer un abandon
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">🔍 Filtres</h3>
                    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Étudiant</label>
                            <select name="etudiant_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Tous les étudiants</option>
                                @foreach($etudiants ?? [] as $etudiant)
                                    <option value="{{ $etudiant->id }}">{{ $etudiant->prenom }} {{ $etudiant->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Matière</label>
                            <select name="matiere_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Toutes les matières</option>
                                @foreach($matieres ?? [] as $matiere)
                                    <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                            <input type="date" name="date_from" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                            <input type="date" name="date_to" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="lg:col-span-4 flex justify-between items-center">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                🔍 Filtrer
                            </button>
                            <button type="button" onclick="resetFilters()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                🔄 Réinitialiser
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">📊 Statistiques</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $drops->count() }}</div>
                            <div class="text-sm text-gray-600">Total des abandons</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $drops->where('date_drop', '>=', now()->subDays(30))->count() }}</div>
                            <div class="text-sm text-gray-600">Abandons ce mois</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ $drops->unique('etudiant_id')->count() }}</div>
                            <div class="text-sm text-gray-600">Étudiants concernés</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des abandons -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">📋 Liste des abandons</h3>

                    @if($drops->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Année/Semestre</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date d'abandon</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Raison</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($drops as $drop)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($drop->etudiant->photo)
                                                            <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $drop->etudiant->photo) }}" alt="">
                                                        @else
                                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                                <span class="text-sm font-medium text-gray-700">
                                                                    {{ strtoupper(substr($drop->etudiant->prenom, 0, 1) . substr($drop->etudiant->nom, 0, 1)) }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $drop->etudiant->prenom }} {{ $drop->etudiant->nom }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $drop->etudiant->classe->nom ?? 'N/A' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $drop->matiere->nom }}</div>
                                                <div class="text-sm text-gray-500">{{ $drop->matiere->code }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $drop->anneeAcademique->nom }}</div>
                                                <div class="text-sm text-gray-500">{{ $drop->semestre->nom }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $drop->date_drop->format('d/m/Y') }}</div>
                                                <div class="text-sm text-gray-500">{{ $drop->date_drop->diffForHumans() }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    {{ $drop->raison_drop ? Str::limit($drop->raison_drop, 50) : 'Aucune raison spécifiée' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('etudiant-matiere-dropped.show', $drop->id) }}" class="text-blue-600 hover:text-blue-900">Voir</a>
                                                    <a href="{{ route('etudiant-matiere-dropped.edit', $drop->id) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                                    <button onclick="restoreStudent({{ $drop->id }})" class="text-green-600 hover:text-green-900">Rétablir</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 text-lg">Aucun étudiant n'a abandonné de matière pour le moment.</div>
                            <a href="{{ route('etudiant-matiere-dropped.create') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Marquer le premier abandon
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function resetFilters() {
            document.getElementById('filterForm').reset();
            document.getElementById('filterForm').submit();
        }

        function restoreStudent(dropId) {
            if (confirm('Êtes-vous sûr de vouloir rétablir cet étudiant dans la matière ?')) {
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
                        location.reload();
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
