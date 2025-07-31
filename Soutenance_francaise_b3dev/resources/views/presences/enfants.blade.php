<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Présences de mes enfants') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Section des filtres -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Filtres</h3>

                    <form method="GET" action="{{ route('presences.enfants') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Filtre par enfant -->
                            <div>
                                <label for="enfant_id" class="block text-sm font-medium text-gray-700 mb-1">Enfant</label>
                                <select name="enfant_id" id="enfant_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Tous les enfants</option>
                                    @foreach($enfants as $enfant)
                                        <option value="{{ $enfant->id }}" {{ request('enfant_id') == $enfant->id ? 'selected' : '' }}>
                                            {{ $enfant->prenom }} {{ $enfant->nom }} ({{ $enfant->classe->nom ?? 'Classe non assignée' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre par statut -->
                            <div>
                                <label for="statut_id" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                                <select name="statut_id" id="statut_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Tous les statuts</option>
                                    @foreach($statutsPresence as $statut)
                                        <option value="{{ $statut->id }}" {{ request('statut_id') == $statut->id ? 'selected' : '' }}>
                                            {{ $statut->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre par date de début -->
                            <div>
                                <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                                <input type="date" name="date_debut" id="date_debut"
                                       value="{{ request('date_debut') }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Filtre par date de fin -->
                            <div>
                                <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                                <input type="date" name="date_fin" id="date_fin"
                                       value="{{ request('date_fin') }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-2">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Filtrer
                                </button>
                                <a href="{{ route('presences.enfants') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Réinitialiser
                                </a>
                            </div>

                            <!-- Sélecteur de pagination -->
                            <div class="flex items-center space-x-2">
                                <label for="per_page" class="text-sm font-medium text-gray-700">Par page:</label>
                                <select name="per_page" id="per_page" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                </select>
                            </div>
                        </div>
                    </form>

                    @if($errors->any())
                        <div class="mt-4 bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Erreurs de validation</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section des résultats -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($enfants->count() > 0)
                        @if($presences->count() > 0)
                            <!-- Statistiques -->
                            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-semibold text-blue-800 mb-2">Résumé</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-blue-600">{{ $presences->total() }}</span> présences trouvées
                                    </div>
                                    <div>
                                        <span class="font-medium text-green-600">{{ $presences->where('statutPresence.code', 'present')->count() }}</span> présences
                                    </div>
                                    <div>
                                        <span class="font-medium text-red-600">{{ $presences->where('statutPresence.code', 'absent')->count() }}</span> absences
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-600">{{ $enfants->count() }}</span> enfant(s)
                                    </div>
                                </div>
                            </div>

                            <!-- Tableau des présences -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enfant</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignant</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($presences as $presence)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ \Carbon\Carbon::parse($presence->enregistre_le)->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <div class="flex items-center">
                                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">
                                                            {{ strtoupper(substr($presence->etudiant->nom, 0, 1) . substr($presence->etudiant->prenom, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="font-medium">{{ $presence->etudiant->prenom }} {{ $presence->etudiant->nom }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $presence->etudiant->classe->nom ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $presence->sessionDeCours->matiere->nom ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $presence->sessionDeCours->enseignant->prenom ?? '' }} {{ $presence->sessionDeCours->enseignant->nom ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($presence->statutPresence)
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                            @if($presence->statutPresence->code === 'present') bg-green-100 text-green-800
                                                            @elseif($presence->statutPresence->code === 'absent') bg-red-100 text-red-800
                                                            @else bg-gray-100 text-gray-800 @endif">
                                                            {{ $presence->statutPresence->nom }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-500">Non défini</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6">
                                {{ $presences->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune présence trouvée</h3>
                                    <p class="mt-1 text-sm text-gray-500">Aucune présence ne correspond aux critères de recherche.</p>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun enfant trouvé</h3>
                                <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore d'enfants associés à votre compte.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
