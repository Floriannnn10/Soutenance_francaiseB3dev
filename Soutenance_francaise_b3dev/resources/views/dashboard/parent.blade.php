<x-app-layout>
   <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tableau de bord Parent') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Informations du parent -->
            @php
                $parent = auth()->user()->parent;
            @endphp
            @if($parent)
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-xl font-bold">{{ substr($parent->prenom, 0, 1) }}{{ substr($parent->nom, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $parent->prenom }} {{ $parent->nom }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Parent</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides pour Parent -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Actions rapides - Parent</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Voir les emplois du temps -->
                        <a href="#emploi-temps" class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                            <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Emplois du temps</span>
                        </a>

                        <!-- Voir les absences -->
                        <a href="#absences" class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                            <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="text-sm font-medium text-red-700 dark:text-red-300">Absences des enfants</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Enfants -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($parent->etudiants as $etudiant)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ substr($etudiant->prenom, 0, 1) }}{{ substr($etudiant->nom, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $etudiant->prenom }} {{ $etudiant->nom }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $etudiant->classe->nom }}</p>
                            </div>
                        </div>

                        <!-- Statistiques de l'enfant -->
                        <div class="space-y-3">
                            <!-- Taux de présence -->
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Taux de présence</span>
                                @php
                                    $totalPresences = $etudiant->presences()->count();
                                    $presencesPresent = $etudiant->presences()->whereHas('statutPresence', function($q) {
                                        $q->where('nom', 'Présent');
                                    })->count();
                                    $taux = $totalPresences > 0 ? round(($presencesPresent / $totalPresences) * 100) : 0;
                                @endphp
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $taux }}%</span>
                            </div>

                            <!-- Absences -->
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Absences</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $etudiant->presences()->whereHas('statutPresence', function($q) {
                                        $q->where('nom', 'Absent');
                                    })->count() }}
                                </span>
                            </div>

                            <!-- Cours suivis -->
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Cours suivis</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $etudiant->presences()->distinct('course_session_id')->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Absences détaillées des enfants -->
            <div id="absences" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Absences des enfants</h3>

                    <!-- Filtres -->
                    <div class="mb-4 flex flex-wrap gap-4">
                        <select id="filter-enfant" class="rounded-md border-gray-300 shadow-sm">
                            <option value="">Tous les enfants</option>
                            @foreach($parent->etudiants as $etudiant)
                                <option value="{{ $etudiant->id }}">{{ $etudiant->prenom }} {{ $etudiant->nom }}</option>
                            @endforeach
                        </select>
                        <select id="filter-justification" class="rounded-md border-gray-300 shadow-sm">
                            <option value="">Toutes les absences</option>
                            <option value="justifiee">Absences justifiées</option>
                            <option value="non-justifiee">Absences non justifiées</option>
                        </select>
                        <select id="filter-type" class="rounded-md border-gray-300 shadow-sm">
                            <option value="">Tous les types</option>
                            <option value="presentiel">Présentiel</option>
                            <option value="e-learning">E-learning</option>
                            <option value="workshop">Workshop</option>
                        </select>
                    </div>

                    <div class="space-y-3">
                        @php
                            $absences = collect();
                            foreach($parent->etudiants as $etudiant) {
                                $etudiantAbsences = $etudiant->presences()
                                    ->whereHas('statutPresence', function($q) {
                                        $q->where('nom', 'Absent');
                                    })
                                    ->with(['sessionDeCours.matiere', 'sessionDeCours.typeCours', 'justification'])
                                    ->orderBy('enregistre_le', 'desc')
                                    ->get();
                                $absences = $absences->merge($etudiantAbsences);
                            }
                            $absences = $absences->sortByDesc('enregistre_le');
                        @endphp

                        @if($absences->count() > 0)
                            @foreach($absences as $presence)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg absence-item"
                                 data-enfant="{{ $presence->etudiant->id }}"
                                 data-justification="{{ $presence->justification ? 'justifiee' : 'non-justifiee' }}"
                                 data-type="{{ strtolower($presence->sessionDeCours->typeCours->nom) }}">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 {{ $presence->justification ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 {{ $presence->justification ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($presence->justification)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                @endif
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $presence->etudiant->prenom }} {{ $presence->etudiant->nom }} - {{ $presence->sessionDeCours->matiere->nom }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $presence->sessionDeCours->typeCours->nom }} - {{ \Carbon\Carbon::parse($presence->enregistre_le)->format('d/m/Y') }}
                                        </p>
                                        @if($presence->justification)
                                            <p class="text-xs text-green-600 dark:text-green-400">Justifiée: {{ $presence->justification->motif }}</p>
                                        @else
                                            <p class="text-xs text-red-600 dark:text-red-400">Non justifiée</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs px-2 py-1 rounded-full {{ $presence->justification ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $presence->justification ? 'Justifiée' : 'Non justifiée' }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">Aucune absence enregistrée</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Emplois du temps des enfants -->
            <div id="emploi-temps" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Emplois du temps des enfants</h3>

                    @foreach($parent->etudiants as $etudiant)
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">{{ $etudiant->prenom }} {{ $etudiant->nom }} ({{ $etudiant->classe->nom }})</h4>
                        <div class="space-y-3">
                            @php
                                $emploiTemps = \App\Models\SessionDeCours::where('classe_id', $etudiant->classe_id)
                                    ->where('start_time', '>=', now())
                                    ->with(['matiere', 'enseignant', 'typeCours'])
                                    ->orderBy('start_time')
                                    ->take(5)
                                    ->get();
                            @endphp

                            @if($emploiTemps->count() > 0)
                                @foreach($emploiTemps as $session)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $session->matiere->nom }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $session->enseignant->prenom }} {{ $session->enseignant->nom }} - {{ $session->typeCours->nom }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</p>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <p class="text-gray-500 dark:text-gray-400">Aucun cours programmé</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <script>
                // Filtrage des absences
                document.getElementById('filter-enfant').addEventListener('change', filterAbsences);
                document.getElementById('filter-justification').addEventListener('change', filterAbsences);
                document.getElementById('filter-type').addEventListener('change', filterAbsences);

                function filterAbsences() {
                    const enfantFilter = document.getElementById('filter-enfant').value;
                    const justificationFilter = document.getElementById('filter-justification').value;
                    const typeFilter = document.getElementById('filter-type').value;
                    const items = document.querySelectorAll('.absence-item');

                    items.forEach(item => {
                        const enfant = item.dataset.enfant;
                        const justification = item.dataset.justification;
                        const type = item.dataset.type;

                        let show = true;

                        if (enfantFilter && enfant !== enfantFilter) {
                            show = false;
                        }

                        if (justificationFilter && justification !== justificationFilter) {
                            show = false;
                        }

                        if (typeFilter && type !== typeFilter) {
                            show = false;
                        }

                        item.style.display = show ? 'flex' : 'none';
                    });
                }
            </script>
            @else
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p>Profil parent non trouvé. Veuillez contacter l'administrateur.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
