<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-gray-100">
                    📅 Gestion des Emplois du Temps
                    @if($classeFiltree)
                        <span class="text-blue-600">- {{ $classeFiltree->nom }}</span>
                    @endif
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Gérez les horaires et les sessions de cours
                </p>
            </div>
            @if($classeFiltree)
                <a href="{{ route('emplois-du-temps.index') }}?annee_id={{ $anneeActive?->id }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voir tous les emplois du temps
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Sélecteur d'année académique avec design moderne -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-6 shadow-sm border border-blue-100 dark:border-gray-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Année académique
                                @if($classeFiltree)
                                    <span class="text-blue-600 dark:text-blue-400">- Emploi du temps de {{ $classeFiltree->nom }}</span>
                                @endif
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Sélectionnez l'année à consulter</p>
                        </div>
            </div>
            <div class="flex items-center space-x-4">
                        <label for="annee_select" class="text-sm font-medium text-gray-700 dark:text-gray-300">Année :</label>
                        <select id="annee_select" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                            @foreach($anneesAcademiques as $annee)
                                <option value="{{ $annee->id }}"
                                        {{ $anneeActive && $anneeActive->id == $annee->id ? 'selected' : '' }}
                                        data-url="{{ route('emplois-du-temps.index') }}?annee_id={{ $annee->id }}{{ $classeFiltree ? '&classe_id=' . $classeFiltree->id : '' }}">
                                    {{ $annee->nom }}
                                    @if($annee->actif)
                                        (Active)
                                    @endif
                                    - {{ $annee->getStatutAttribute() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Actions rapides avec design moderne -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Actions rapides</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button onclick="openCreateModal()"
                            class="group relative bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-4 rounded-xl transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-lg">Nouvelle Session</h4>
                                    <p class="text-blue-100 text-sm">Créer un nouveau cours</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </button>

                    <button onclick="openPresenceModal()"
                            class="group relative bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-4 rounded-xl transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-lg">Prise de Présence</h4>
                                    <p class="text-green-100 text-sm">Workshop & E-learning</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                        </div>
                </button>
            </div>
        </div>

            <!-- Filtres avec design moderne -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Filtres</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-{{ $classeFiltree ? '3' : '4' }} gap-6">
                    @if(!$classeFiltree)
                    <div class="space-y-2">
                        <label for="filter_classe" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Classe</label>
                        <select id="filter_classe" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 transition-colors">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>
                    @endif

                    <div class="space-y-2">
                        <label for="filter_enseignant" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Enseignant</label>
                        <select id="filter_enseignant" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 transition-colors">
                        <option value="">Tous les enseignants</option>
                        @foreach($enseignants as $enseignant)
                                <option value="{{ $enseignant->id }}">{{ $enseignant->prenom }} {{ $enseignant->nom }}</option>
                        @endforeach
                    </select>
                </div>

                    <div class="space-y-2">
                        <label for="filter_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type de cours</label>
                        <select id="filter_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 transition-colors">
                        <option value="">Tous les types</option>
                        @foreach($typesCours as $type)
                            <option value="{{ $type->id }}">{{ $type->nom }}</option>
                        @endforeach
                    </select>
                </div>

                    <div class="space-y-2">
                        <label for="filter_statut" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                        <select id="filter_statut" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 transition-colors">
                            <option value="">Tous les statuts</option>
                            @foreach($statutsSession as $statut)
                                <option value="{{ $statut->id }}">{{ $statut->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Liste des sessions avec design moderne -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
        </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Sessions de cours</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $sessions->count() }} session(s) trouvée(s)</p>
                </div>
            </div>
                    </div>
                    </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Date/Heure</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Classe</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Matière</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Enseignant</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="sessions-table">
                            @forelse($sessions as $session)
                                <tr class="session-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                    data-classe="{{ $session->classe_id }}"
                                    data-enseignant="{{ $session->enseignant_id }}"
                                    data-type="{{ $session->type_cours_id }}"
                                    data-statut="{{ $session->status_id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $session->start_time->format('d/m/Y') }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $session->classe->nom }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $session->matiere->nom }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                                    {{ substr($session->enseignant->prenom, 0, 1) }}{{ substr($session->enseignant->nom, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $session->enseignant->prenom }} {{ $session->enseignant->nom }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ $session->typeCours->nom }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'Planifié' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                'En cours' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                'Terminé' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                'Annulé' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                            ];
                                            $color = $statusColors[$session->statutSession->nom] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $color }}">
                                            {{ $session->statutSession->nom }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            @if(in_array($session->typeCours->nom, ['Workshop', 'E-learning']))
                                                <button onclick="openPresenceModal({{ $session->id }})"
                                                        class="p-2 text-green-600 hover:text-green-900 hover:bg-green-100 dark:hover:bg-green-900 rounded-lg transition-colors duration-200"
                                                        title="Faire l'appel">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                            <button onclick="editSession({{ $session->id }})"
                                                    class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-100 dark:hover:bg-blue-900 rounded-lg transition-colors duration-200"
                                                    title="Modifier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                            <button onclick="deleteSession({{ $session->id }})"
                                                    class="p-2 text-red-600 hover:text-red-900 hover:bg-red-100 dark:hover:bg-red-900 rounded-lg transition-colors duration-200"
                                                    title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center space-y-4">
                                            <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-full">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aucune session trouvée</h3>
                                                <p class="text-gray-500 dark:text-gray-400">Commencez par créer une nouvelle session de cours.</p>
                            </div>
                                            <button onclick="openCreateModal()" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Créer une session
                                            </button>
                    </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
</div>

    <!-- Modal de création/édition de session -->
    <div id="sessionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-0 border w-full max-w-2xl shadow-2xl rounded-xl bg-white dark:bg-gray-800">
            <!-- Header du modal -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100" id="modalTitle">Nouvelle Session</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Créer une nouvelle session de cours</p>
                    </div>
                </div>
                <button onclick="closeSessionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Contenu du formulaire -->
            <div class="p-6">
                <form id="sessionForm" class="space-y-6">
                    <input type="hidden" id="session_id" name="session_id">
                    <input type="hidden" name="annee_id" value="{{ $anneeActive?->id }}">

                    <!-- Section 1: Informations de base -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Informations de base
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="classe_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Classe <span class="text-red-500">*</span>
                                </label>
                                <select id="classe_id" name="classe_id" required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 transition-colors">
                                <option value="">Sélectionner une classe</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                            <div class="space-y-2">
                                <label for="matiere_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Matière <span class="text-red-500">*</span>
                                </label>
                                <select id="matiere_id" name="matiere_id" required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 transition-colors">
                                <option value="">Sélectionner une matière</option>
                                    @foreach($matieres as $matiere)
                                    <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                            @endforeach
                        </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Personnel et type -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Personnel et type de cours
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="enseignant_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Enseignant <span class="text-red-500">*</span>
                                </label>
                                <select id="enseignant_id" name="enseignant_id" required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 transition-colors">
                                    <option value="">Sélectionner un enseignant</option>
                                    @foreach($enseignants as $enseignant)
                                        <option value="{{ $enseignant->id }}">{{ $enseignant->prenom }} {{ $enseignant->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label for="type_cours_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Type de cours <span class="text-red-500">*</span>
                                </label>
                                <select id="type_cours_id" name="type_cours_id" required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 transition-colors">
                                <option value="">Sélectionner un type</option>
                            @foreach($typesCours as $type)
                                <option value="{{ $type->id }}">{{ $type->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                        </div>
                    </div>

                    <!-- Section 3: Horaires -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Horaires de la session
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Début <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="datetime-local" id="start_time" name="start_time" required
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 transition-colors">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                        </div>

                            <div class="space-y-2">
                                <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fin <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="datetime-local" id="end_time" name="end_time" required
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 transition-colors">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Détails supplémentaires -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Détails supplémentaires
                        </h4>

                    <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label for="status_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Statut <span class="text-red-500">*</span>
                                    </label>
                                    <select id="status_id" name="status_id" required
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 transition-colors">
                                <option value="">Sélectionner un statut</option>
                            @foreach($statutsSession as $statut)
                                <option value="{{ $statut->id }}">{{ $statut->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                                <div class="space-y-2">
                                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Lieu
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="location" name="location" placeholder="Salle, bâtiment..."
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 transition-colors">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                    </div>
                    </div>
                </div>

                            <div class="space-y-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Notes
                                </label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Informations supplémentaires, consignes particulières..."
                                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 transition-colors resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="text-red-500">*</span> Champs obligatoires
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" onclick="closeSessionModal()"
                                    class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500 transition-colors duration-200">
                        Annuler
                    </button>
                            <button type="submit"
                                    class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Enregistrer
                    </button>
                        </div>
                </div>
            </form>
        </div>
    </div>
        </div>

    <!-- Modal de prise de présence -->
    <div id="presenceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Prise de Présence - Workshop & E-learning uniquement</h3>
                    <button onclick="closePresenceModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div id="presenceContent">
                    <!-- Le contenu sera chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>

<script>
        // Gestion du changement d'année académique
        document.getElementById('annee_select')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const url = selectedOption.dataset.url;
            if (url) {
                window.location.href = url;
            }
        });

        // Filtres
        document.querySelectorAll('#filter_classe, #filter_enseignant, #filter_type, #filter_statut').forEach(filter => {
            filter.addEventListener('change', function() {
                filterSessions();
            });
        });

        function filterSessions() {
            const classeFilter = document.getElementById('filter_classe')?.value || '';
            const enseignantFilter = document.getElementById('filter_enseignant').value;
            const typeFilter = document.getElementById('filter_type').value;
            const statutFilter = document.getElementById('filter_statut').value;

            document.querySelectorAll('.session-row').forEach(row => {
                let show = true;

                if (classeFilter && row.dataset.classe !== classeFilter) show = false;
                if (enseignantFilter && row.dataset.enseignant !== enseignantFilter) show = false;
                if (typeFilter && row.dataset.type !== typeFilter) show = false;
                if (statutFilter && row.dataset.statut !== statutFilter) show = false;

                row.style.display = show ? '' : 'none';
            });
        }

        // Modal de session
function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Nouvelle Session';
            document.getElementById('sessionForm').reset();
            document.getElementById('session_id').value = '';

            // Pré-remplir la classe si on est sur une classe filtrée
            @if($classeFiltree)
            document.getElementById('classe_id').value = '{{ $classeFiltree->id }}';
            @endif

            document.getElementById('sessionModal').classList.remove('hidden');
        }

                function closeSessionModal() {
            document.getElementById('sessionModal').classList.add('hidden');

            // Réactiver l'événement change sur le type de cours avec l'événement original
            const typeCoursSelect = document.getElementById('type_cours_id');
            if (typeCoursSelect && originalChangeHandler) {
                typeCoursSelect.addEventListener('change', originalChangeHandler);
            }
        }

        // Soumission du formulaire de session
        document.getElementById('sessionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const sessionId = document.getElementById('session_id').value;

                        // Debug: Afficher les données envoyées
            console.log('Session ID:', sessionId);
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

                        // Si c'est une modification, s'assurer que les bonnes valeurs sont envoyées
            if (sessionId) {
                // Récupérer les valeurs originales de la session
                const typeCoursSelect = document.getElementById('type_cours_id');
                const enseignantSelect = document.getElementById('enseignant_id');

                // Attendre un court délai pour s'assurer que tous les événements sont terminés
                setTimeout(() => {
                    // Forcer les bonnes valeurs dans le FormData
                    formData.set('type_cours_id', typeCoursSelect.value);
                    formData.set('enseignant_id', enseignantSelect.value);

                    console.log('Valeurs forcées après délai:');
                    console.log('type_cours_id:', typeCoursSelect.value);
                    console.log('enseignant_id:', enseignantSelect.value);

                    // Envoyer la requête
                    sendRequest();
                }, 100);

                return; // Sortir de la fonction pour éviter l'envoi immédiat
            }

                        // Fonction pour envoyer la requête
            function sendRequest() {
                const url = sessionId ? `/coordinateur/session/${sessionId}` : '/coordinateur/creer-session';
                const method = sessionId ? 'PUT' : 'POST';

                console.log('URL:', url);
                console.log('Method:', method);

                fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeSessionModal();
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue');
                });
            }

            // Si c'est une création, envoyer immédiatement
            if (!sessionId) {
                sendRequest();
            }
        });

        // Modal de présence
        function openPresenceModal(sessionId = null) {
            if (sessionId) {
                // Charger les données de la session spécifique
                loadSessionPresence(sessionId);
            } else {
                // Afficher la liste des sessions en présentiel
                loadPresentielSessions();
            }
            document.getElementById('presenceModal').classList.remove('hidden');
        }

        function closePresenceModal() {
            document.getElementById('presenceModal').classList.add('hidden');
        }

        function loadPresentielSessions() {
            fetch('/coordinateur/sessions-presentiel')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = '<div class="mb-6">';
                        html += '<h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Sélectionner une session Workshop ou E-learning :</h4>';

                        if (data.sessions.length === 0) {
                            html += '<div class="text-center py-8">';
                            html += '<div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">';
                            html += '<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                            html += '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>';
                            html += '</svg></div>';
                            html += '<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aucune session disponible</h3>';
                            html += '<p class="text-gray-500 dark:text-gray-400">Aucune session Workshop ou E-learning trouvée pour cette période.</p>';
                            html += '</div>';
                        } else {
                            html += '<select id="session_select" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">';
                            html += '<option value="">Choisir une session</option>';

                            data.sessions.forEach(session => {
                                const date = new Date(session.start_time).toLocaleDateString('fr-FR');
                                const time = new Date(session.start_time).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                                const typeColor = session.type_cours.nom === 'Workshop' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';

                                html += `<option value="${session.id}">
                                    ${session.classe.nom} - ${session.matiere.nom} (${session.type_cours.nom}) - ${date} à ${time}
                                </option>`;
                            });

                            html += '</select>';
                            html += '<div class="mt-4 text-sm text-gray-600 dark:text-gray-400">';
                            html += `<span class="font-medium">${data.sessions.length}</span> session(s) Workshop/E-learning disponible(s)`;
                            html += '</div>';
                        }

                        html += '</div>';
                        html += '<div id="presence_form" class="hidden"></div>';

                        document.getElementById('presenceContent').innerHTML = html;

                        if (data.sessions.length > 0) {
                            document.getElementById('session_select').addEventListener('change', function() {
                                if (this.value) {
                                    loadSessionPresence(this.value);
                                } else {
                                    document.getElementById('presence_form').classList.add('hidden');
                                }
                            });
                        }
                    }
                });
        }

        function loadSessionPresence(sessionId) {
            fetch(`/coordinateur/session/${sessionId}/etudiants`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = '<form id="presenceForm">';

                        // Informations de la session
                        if (data.session) {
                            html += '<div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">';
                            html += '<h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">Informations de la session</h4>';
                            html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">';
                            html += `<div><strong>Matière:</strong> ${data.session.matiere.nom}</div>`;
                            html += `<div><strong>Enseignant:</strong> ${data.session.enseignant.prenom} ${data.session.enseignant.nom}</div>`;
                            html += `<div><strong>Type:</strong> ${data.session.type_cours.nom}</div>`;
                            html += `<div><strong>Date:</strong> ${data.session.start_time}</div>`;
                            html += '</div></div>';
                        }

                        html += '<div class="mb-4"><h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Liste des étudiants (toutes les classes de la promotion) :</h4></div>';
                        html += '<div class="overflow-y-auto max-h-96">';
                        html += '<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">';
                        html += '<thead class="bg-gray-50 dark:bg-gray-700"><tr>';
                        html += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Étudiant</th>';
                        html += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Classe</th>';
                        html += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>';
                        html += '</tr></thead><tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">';

                        data.etudiants.forEach(etudiant => {
                            const presence = data.presences.find(p => p.etudiant_id === etudiant.id);
                            const statutId = presence ? presence.statut_presence_id : '';

                            html += '<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">';
                            html += `<td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                                    ${etudiant.prenom.charAt(0)}${etudiant.nom.charAt(0)}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${etudiant.prenom} ${etudiant.nom}</div>
                                            </div>
                                        </div>
                                    </td>`;
                            html += `<td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            ${etudiant.classe ? etudiant.classe.nom : 'N/A'}
                                        </span>
                                    </td>`;
                            html += '<td class="px-4 py-3 whitespace-nowrap">';
                            html += '<select name="presences[' + etudiant.id + '][statut_presence_id]" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">';

                            data.statuts_presence.forEach(statut => {
                                const selected = statutId == statut.id ? 'selected' : '';
                                html += `<option value="${statut.id}" ${selected}>${statut.nom}</option>`;
                            });

                            html += '</select>';
                            html += `<input type="hidden" name="presences[${etudiant.id}][etudiant_id]" value="${etudiant.id}">`;
                            html += '</td>';
                            html += '</tr>';
                        });

                        html += '</tbody></table></div>';
                        html += '<div class="mt-6 flex justify-between items-center">';
                        html += '<div class="text-sm text-gray-600 dark:text-gray-400">';
                        html += `<span class="font-medium">${data.etudiants.length}</span> étudiant(s) à marquer`;
                        html += '</div>';
                        html += '<div class="flex space-x-3">';
                        html += '<button type="button" onclick="closePresenceModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500 transition-colors">Annuler</button>';
                        html += '<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors">Enregistrer les présences</button>';
                        html += '</div></div>';
                        html += '</form>';

                        document.getElementById('presenceContent').innerHTML = html;

                        // Gérer la soumission du formulaire de présence
                        document.getElementById('presenceForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

                            fetch(`/coordinateur/session/${sessionId}/presence`, {
        method: 'POST',
        body: formData,
        headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
                                    closePresenceModal();
                                    alert('Présences enregistrées avec succès');
        } else {
                                    alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
                                console.error('Error:', error);
                                alert('Une erreur est survenue');
    });
});
                    }
                });
        }

function editSession(sessionId) {
            fetch(`/api/sessions-de-cours/${sessionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalTitle').textContent = 'Modifier Session';

                                                // Debug: Afficher les données reçues
                        console.log('Données de la session:', data.session);

                        // Temporairement supprimer l'événement change AVANT de remplir le formulaire
                        const typeCoursSelect = document.getElementById('type_cours_id');
                        // Sauvegarder l'événement original et le supprimer complètement
                        originalChangeHandler = updateEnseignantField;
                        typeCoursSelect.removeEventListener('change', updateEnseignantField);

                        // Ne pas reset le formulaire, remplir directement les champs
                        document.getElementById('session_id').value = data.session.id;
                        document.getElementById('classe_id').value = data.session.classe_id;
                        document.getElementById('matiere_id').value = data.session.matiere_id;
                        document.getElementById('enseignant_id').value = data.session.enseignant_id;
                        document.getElementById('type_cours_id').value = data.session.type_cours_id;
                        document.getElementById('status_id').value = data.session.status_id;
                        document.getElementById('start_time').value = data.session.start_time.replace(' ', 'T');
                        document.getElementById('end_time').value = data.session.end_time.replace(' ', 'T');
                        document.getElementById('location').value = data.session.location || '';
                        document.getElementById('notes').value = data.session.notes || '';

                        // Debug: Vérifier les valeurs après remplissage
                        console.log('Valeurs après remplissage:');
                        console.log('type_cours_id:', document.getElementById('type_cours_id').value);
                        console.log('enseignant_id:', document.getElementById('enseignant_id').value);

                        // Appliquer la logique de sélection automatique pour Workshop/E-learning
                        updateEnseignantField(true);

                        // Ne PAS réactiver l'événement pendant l'édition
                        // Il sera réactivé quand on ferme le modal

                        // Debug: Vérifier les valeurs après updateEnseignantField
                        console.log('Valeurs après updateEnseignantField:');
                        console.log('type_cours_id:', document.getElementById('type_cours_id').value);
                        console.log('enseignant_id:', document.getElementById('enseignant_id').value);

                        document.getElementById('sessionModal').classList.remove('hidden');
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue');
                });
}

function deleteSession(sessionId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette session ?')) {
                fetch(`/sessions-de-cours/${sessionId}`, {
            method: 'DELETE',
            headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                        location.reload();
            } else {
                        alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue');
                });
            }
        }

                                                                // Fonction globale pour gérer la logique d'information pour Workshop et E-learning
        function updateEnseignantField(isEditing = false) {
            const typeCoursSelect = document.getElementById('type_cours_id');
            const enseignantSelect = document.getElementById('enseignant_id');

            if (!typeCoursSelect || !enseignantSelect) return;

            // Si c'est un événement de changement, ignorer complètement
            if (isEditing instanceof Event) {
                console.log('Ignoring change event completely');
                return;
            }

            const selectedType = typeCoursSelect.value;
            const selectedOption = typeCoursSelect.options[typeCoursSelect.selectedIndex];
            const typeName = selectedOption.text;

            console.log('updateEnseignantField - type_cours_id:', selectedType);
            console.log('updateEnseignantField - typeName:', typeName);
            console.log('updateEnseignantField - isEditing:', isEditing);

            if (typeName === 'Workshop' || typeName === 'E-learning') {
                // Ajouter une note explicative
                if (!document.getElementById('enseignant-note')) {
                    const note = document.createElement('div');
                    note.id = 'enseignant-note';
                    note.className = 'text-sm text-blue-600 dark:text-blue-400 mt-1 flex items-center';
                    note.innerHTML = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Vous devez être l\'enseignant pour ce type de cours';
                    enseignantSelect.parentNode.appendChild(note);
                }

                // Ajouter une bordure bleue pour indiquer le changement automatique
                enseignantSelect.classList.add('border-blue-300', 'bg-blue-50');
                enseignantSelect.classList.remove('border-gray-300');

                // Sélectionner automatiquement le coordinateur
                // On va chercher l'enseignant qui correspond au coordinateur connecté
                console.log('Recherche du coordinateur: {{ Auth::user()->coordinateur->prenom }} {{ Auth::user()->coordinateur->nom }}');
                console.log('Options disponibles:', Array.from(enseignantSelect.options).map(opt => opt.text));

                const coordinateurOption = Array.from(enseignantSelect.options).find(option =>
                    option.text.includes('{{ Auth::user()->coordinateur->prenom }}') &&
                    option.text.includes('{{ Auth::user()->coordinateur->nom }}')
                );

                console.log('Option trouvée:', coordinateurOption);

                if (coordinateurOption) {
                    enseignantSelect.value = coordinateurOption.value;
                    console.log('Coordinateur sélectionné automatiquement:', coordinateurOption.value);
                } else if (enseignantSelect.options.length > 0) {
                    // Fallback: sélectionner le premier enseignant
                    enseignantSelect.value = enseignantSelect.options[0].value;
                    console.log('Aucun coordinateur trouvé, sélection du premier enseignant:', enseignantSelect.options[0].value);
                }
            } else {
                // Supprimer la note explicative
                const note = document.getElementById('enseignant-note');
                if (note) {
                    note.remove();
                }

                // Retirer la bordure bleue
                enseignantSelect.classList.remove('border-blue-300', 'bg-blue-50');
                enseignantSelect.classList.add('border-gray-300');
            }
        }

        // Gérer la logique d'information pour Workshop et E-learning
                        document.addEventListener('DOMContentLoaded', function() {
            const typeCoursSelect = document.getElementById('type_cours_id');
            const enseignantSelect = document.getElementById('enseignant_id');

            // Écouter les changements sur le type de cours
            if (typeCoursSelect) {
                typeCoursSelect.addEventListener('change', updateEnseignantField);
            }

            // Appliquer la logique au chargement de la page
            updateEnseignantField();
        });

        // Variable globale pour stocker l'événement original
        let originalChangeHandler = null;

        // Variable globale pour suivre l'état d'édition
        let isCurrentlyEditing = false;
</script>
</x-app-layout>
