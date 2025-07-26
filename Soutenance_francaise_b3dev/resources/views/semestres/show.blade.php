<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du Semestre') }}: {{ $semestre->nom }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('semestres.edit', $semestre) }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </a>
                <a href="{{ route('semestres.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Informations générales -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Informations Générales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $semestre->nom }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Année Académique</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="{{ route('annees-academiques.show', $semestre->anneeAcademique) }}"
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $semestre->anneeAcademique->nom }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de Début</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $semestre->date_debut->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de Fin</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $semestre->date_fin->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Statut</label>
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($semestre->statut === 'En cours') bg-green-100 text-green-800
                                @elseif($semestre->statut === 'À venir') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $semestre->statut }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">État</label>
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($semestre->actif) bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $semestre->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Durée</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $semestre->date_debut->diffInDays($semestre->date_fin) }} jours
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sessions de cours -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Sessions de Cours ({{ $dependancesCount['sessions_cours'] }})</h3>
                        <a href="{{ route('sessions-de-cours.create', ['semestre_id' => $semestre->id]) }}"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            <i class="fas fa-plus mr-2"></i>Nouvelle Session
                        </a>
                    </div>

                    @if($dependancesCount['sessions_cours'] > 0)
                        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                <p class="text-sm text-yellow-800">
                                    <strong>Attention :</strong> Ce semestre contient {{ $dependancesCount['sessions_cours'] }} session(s) de cours
                                    @if($dependancesCount['presences'] > 0)
                                        avec {{ $dependancesCount['presences'] }} présence(s) enregistrée(s).
                                    @endif
                                    Il ne peut pas être supprimé tant que ces éléments existent.
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($dependancesCount['sessions_cours'] > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Matière
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Classe
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Enseignant
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Horaires
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lieu
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($sessionsDeCours as $session)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $session->matiere_nom ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $session->classe_nom ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $session->enseignant_prenom ?? '' }} {{ $session->enseignant_nom ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($session->start_time && $session->end_time)
                                                    {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i') }} -
                                                    {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                                @else
                                                    Non programmé
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $session->location ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                                                <a href="#" class="text-orange-600 hover:text-orange-900 mr-3">Éditer</a>
                                                <a href="#" class="text-red-600 hover:text-red-900">Supprimer</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">Aucune session de cours trouvée pour ce semestre.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Statistiques</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <i class="fas fa-chalkboard-teacher text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Sessions de Cours</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $semestre->sessionsDeCours->count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <i class="fas fa-check-circle text-green-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Présences</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $presencesCount ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-100 rounded-lg">
                                    <i class="fas fa-calendar-day text-yellow-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Jours Restants</p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        {{ max(0, now()->diffInDays($semestre->date_fin, false)) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
