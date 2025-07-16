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

    <!-- Prochains cours -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Prochains cours</h3>
            <div class="space-y-3">
                @php
                    $prochainsCours = \App\Models\SessionDeCours::where('classe_id', $etudiant->classe_id)
                        ->where('start_time', '>', now())
                        ->with(['matiere', 'enseignant'])
                        ->orderBy('start_time')
                        ->take(5)
                        ->get();
                @endphp
                @forelse($prochainsCours as $cours)
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cours->matiere->nom }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $cours->enseignant->prenom }} {{ $cours->enseignant->nom }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cours->start_time->format('d/m/Y') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $cours->start_time->format('H:i') }} - {{ $cours->end_time->format('H:i') }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">Aucun cours prévu</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Dernières présences -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Dernières présences</h3>
            <div class="space-y-3">
                @php
                    $dernieresPresences = $etudiant->presences()
                        ->with(['sessionDeCours.matiere', 'statutPresence'])
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp
                @forelse($dernieresPresences as $presence)
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($presence->statutPresence->name === 'present')
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            @elseif($presence->statutPresence->name === 'absent')
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $presence->sessionDeCours->matiere->nom }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $presence->sessionDeCours->start_time->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $presence->statutPresence->display_name }}</span>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">Aucune présence enregistrée</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="p-6 text-center">
            <p class="text-gray-500 dark:text-gray-400">Aucune information d'étudiant trouvée</p>
        </div>
    </div>
    @endif
</x-app-layout>
