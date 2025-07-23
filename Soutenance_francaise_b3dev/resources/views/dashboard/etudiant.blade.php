<x-app-layout>
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Mon Tableau de bord</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Suivi de mes cours et présences</p>
    </div>

    <!-- Informations de l'étudiant -->
    @php
        $etudiant = auth()->user()->etudiant;
    @endphp
    @if($etudiant)
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-6">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-xl font-bold">{{ substr($etudiant->prenom, 0, 1) }}{{ substr($etudiant->nom, 0, 1) }}</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $etudiant->prenom }} {{ $etudiant->nom }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $etudiant->classe->nom }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides pour Étudiant -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Actions rapides - Étudiant</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Voir mon emploi du temps -->
                <a href="{{ route('sessions-de-cours.index') }}" class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                    <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Mon emploi du temps</span>
                </a>

                <!-- Voir mes absences -->
                <a href="#absences" class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                    <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="text-sm font-medium text-red-700 dark:text-red-300">Mes absences</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Statistiques personnelles -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Taux de présence -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Taux de présence</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            @php
                                $totalPresences = $etudiant->presences()->count();
                                $presencesPresent = $etudiant->presences()->whereHas('statutPresence', function($q) {
                                    $q->where('name', 'present');
                                })->count();
                                $taux = $totalPresences > 0 ? round(($presencesPresent / $totalPresences) * 100) : 0;
                            @endphp
                            {{ $taux }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cours suivis -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cours suivis</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $etudiant->presences()->distinct('course_session_id')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absences -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Absences</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $etudiant->presences()->whereHas('statutPresence', function($q) {
                                $q->where('name', 'absent');
                            })->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mes absences détaillées -->
    <div id="absences" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Mes absences</h3>

            <!-- Filtres -->
            <div class="mb-4 flex flex-wrap gap-4">
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
                    $absences = $etudiant->presences()
                        ->whereHas('statutPresence', function($q) {
                            $q->where('name', 'absent');
                        })
                        ->with(['sessionDeCours.matiere', 'sessionDeCours.typeCours', 'justificationAbsence'])
                        ->orderBy('enregistre_le', 'desc')
                        ->get();
                @endphp

                @if($absences->count() > 0)
                    @foreach($absences as $presence)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg absence-item"
                         data-justification="{{ $presence->justificationAbsence ? 'justifiee' : 'non-justifiee' }}"
                         data-type="{{ strtolower($presence->sessionDeCours->typeCours->nom) }}">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 {{ $presence->justificationAbsence ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 {{ $presence->justificationAbsence ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($presence->justificationAbsence)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        @endif
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $presence->sessionDeCours->matiere->nom }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $presence->sessionDeCours->typeCours->nom }} - {{ \Carbon\Carbon::parse($presence->enregistre_le)->format('d/m/Y') }}
                                </p>
                                @if($presence->justificationAbsence)
                                    <p class="text-xs text-green-600 dark:text-green-400">Justifiée: {{ $presence->justificationAbsence->motif }}</p>
                                @else
                                    <p class="text-xs text-red-600 dark:text-red-400">Non justifiée</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2 py-1 rounded-full {{ $presence->justificationAbsence ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $presence->justificationAbsence ? 'Justifiée' : 'Non justifiée' }}
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

    <!-- Mon emploi du temps -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Mon emploi du temps</h3>
            <div class="space-y-3">
                @php
                    $emploiTemps = \App\Models\SessionDeCours::where('classe_id', $etudiant->classe_id)
                        ->where('start_time', '>=', now())
                        ->with(['matiere', 'enseignant', 'typeCours'])
                        ->orderBy('start_time')
                        ->take(10)
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
                    <div class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">Aucun cours programmé</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Filtrage des absences
        document.getElementById('filter-justification').addEventListener('change', filterAbsences);
        document.getElementById('filter-type').addEventListener('change', filterAbsences);

        function filterAbsences() {
            const justificationFilter = document.getElementById('filter-justification').value;
            const typeFilter = document.getElementById('filter-type').value;
            const items = document.querySelectorAll('.absence-item');

            items.forEach(item => {
                const justification = item.dataset.justification;
                const type = item.dataset.type;

                let show = true;

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
        <p>Profil étudiant non trouvé. Veuillez contacter l'administrateur.</p>
    </div>
    @endif
</x-app-layout>
