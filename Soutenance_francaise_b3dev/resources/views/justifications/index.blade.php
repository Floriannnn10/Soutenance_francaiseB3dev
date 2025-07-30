<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Gestion des Justifications d'Absence</h1>
                        <div class="text-sm text-gray-600">
                            Année: {{ $anneeActive?->nom ?? 'Aucune année active' }} |
                            Semestre: {{ $semestreActif?->nom ?? 'Aucun semestre actif' }}
                        </div>
                    </div>

                    @if(!$anneeActive || !$semestreActif)
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                            <strong>Attention :</strong> Aucune année académique ou semestre n'est actuellement actif.
                        </div>
                    @else
                        <!-- Filtres -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h2 class="text-lg font-semibold mb-4">Filtres</h2>
                            <form method="GET" action="{{ route('justifications.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Recherche -->
                                <div>
                                    <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                                           placeholder="Nom étudiant..."
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <!-- Étudiant -->
                                <div>
                                    <label for="etudiant_id" class="block text-sm font-medium text-gray-700">Étudiant</label>
                                    <select name="etudiant_id" id="etudiant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Tous les étudiants</option>
                                        @foreach($etudiants as $etudiant)
                                            <option value="{{ $etudiant->id }}" {{ request('etudiant_id') == $etudiant->id ? 'selected' : '' }}>
                                                {{ $etudiant->nom }} {{ $etudiant->prenom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Matière -->
                                <div>
                                    <label for="matiere_id" class="block text-sm font-medium text-gray-700">Matière</label>
                                    <select name="matiere_id" id="matiere_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Toutes les matières</option>
                                        @foreach($matieres as $matiere)
                                            <option value="{{ $matiere->id }}" {{ request('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                                {{ $matiere->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Enseignant -->
                                <div>
                                    <label for="enseignant_id" class="block text-sm font-medium text-gray-700">Enseignant</label>
                                    <select name="enseignant_id" id="enseignant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Tous les enseignants</option>
                                        @foreach($enseignants as $enseignant)
                                            <option value="{{ $enseignant->id }}" {{ request('enseignant_id') == $enseignant->id ? 'selected' : '' }}>
                                                {{ $enseignant->nom }} {{ $enseignant->prenom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Statut justification -->
                                <div>
                                    <label for="statut_justification" class="block text-sm font-medium text-gray-700">Statut</label>
                                    <select name="statut_justification" id="statut_justification" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Tous les statuts</option>
                                        <option value="justifiee" {{ request('statut_justification') === 'justifiee' ? 'selected' : '' }}>Justifiées</option>
                                        <option value="non_justifiee" {{ request('statut_justifiation') === 'non_justifiee' ? 'selected' : '' }}>Non justifiées</option>
                                    </select>
                                </div>

                                <!-- Date début -->
                                <div>
                                    <label for="date_debut" class="block text-sm font-medium text-gray-700">Date début</label>
                                    <input type="date" name="date_debut" id="date_debut" value="{{ request('date_debut') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <!-- Date fin -->
                                <div>
                                    <label for="date_fin" class="block text-sm font-medium text-gray-700">Date fin</label>
                                    <input type="date" name="date_fin" id="date_fin" value="{{ request('date_fin') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">






























                    </div>

                                <!-- Boutons -->
                                <div class="flex items-end space-x-2">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                        Filtrer
                                    </button>
                                    <a href="{{ route('justifications.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                        Réinitialiser
                                    </a>
                                </div>
                            </form>
                        </div>



                        <!-- Statistiques -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $totalAbsences }}</div>
                                <div class="text-sm text-blue-600">Total absences</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $justifiees }}</div>
                                <div class="text-sm text-green-600">Justifiées</div>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">{{ $nonJustifiees }}</div>
                                <div class="text-sm text-red-600">Non justifiées</div>
                            </div>
                        </div>

                        @if(($totalAbsences ?? 0) > 0)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignant</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($absences as $absence)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $absence->etudiant->nom }} {{ $absence->etudiant->prenom }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $absence->etudiant->classe->nom ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $absence->sessionDeCours->matiere->nom }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $absence->sessionDeCours->enseignant->nom }} {{ $absence->sessionDeCours->enseignant->prenom }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($absence->enregistre_le)->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($absence->justification)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Justifiée
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Non justifiée
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if($absence->justification)
                                                    <a href="{{ route('justifications.show', $absence->justification->id) }}"
                                                       class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                                                    <a href="{{ route('justifications.edit', $absence->justification->id) }}"
                                                       class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>
                                                    <form action="{{ route('justifications.destroy', $absence->justification->id) }}"
                                                          method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette justification ?')">
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('justifications.create', $absence->id) }}"
                                                       class="text-green-600 hover:text-green-900">Justifier</a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6">
                                {{ $absences->links() }}
                            </div>

                        @else
                            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
                                <strong>Information :</strong> Aucune absence enregistrée pour cette période.
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
