<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tableau de bord Coordinateur') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Informations du coordinateur -->
            @php
                $coordinateur = auth()->user()->coordinateur;
            @endphp
            @if($coordinateur)
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-indigo-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-xl font-bold">{{ substr($coordinateur->prenom, 0, 1) }}{{ substr($coordinateur->nom, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $coordinateur->prenom }} {{ $coordinateur->nom }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Coordinateur</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques générales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Classes -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Classes</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Models\Classe::count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Étudiants -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Étudiants</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Models\Etudiant::count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Enseignants -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Enseignants</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Models\Enseignant::count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sessions de cours -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sessions de cours</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Models\SessionDeCours::count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vue d'ensemble par classe -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Effectifs par classe -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Effectifs par classe</h3>
                        <div class="space-y-4">
                            @foreach(\App\Models\Classe::withCount('etudiants')->get() as $classe)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $classe->nom }}</span>
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mr-2">{{ $classe->etudiants_count }} étudiants</span>
                                    <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        @php
                                            $pourcentage = $classe->etudiants_count > 0 ? min(100, ($classe->etudiants_count / 30) * 100) : 0;
                                        @endphp
                                        <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $pourcentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Présence par classe -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Taux de présence par classe</h3>
                        <div class="space-y-4">
                            @foreach(\App\Models\Classe::withCount('etudiants')->get() as $classe)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $classe->nom }}</span>
                                @php
                                    $totalPresences = \App\Models\Presence::whereHas('sessionDeCours', function($q) use ($classe) {
                                        $q->where('classe_id', $classe->id);
                                    })->count();
                                    $presencesPresent = \App\Models\Presence::whereHas('sessionDeCours', function($q) use ($classe) {
                                        $q->where('classe_id', $classe->id);
                                    })->whereHas('statutPresence', function($q) {
                                        $q->where('name', 'present');
                                    })->count();
                                    $taux = $totalPresences > 0 ? round(($presencesPresent / $totalPresences) * 100) : 0;
                                @endphp
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $taux }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Actions rapides</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('classes.index') }}" class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors">
                            <svg class="w-6 h-6 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Gérer les classes</span>
                        </a>

                        <a href="{{ route('etudiants.index') }}" class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                            <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Gérer les étudiants</span>
                        </a>

                        <a href="{{ route('enseignants.index') }}" class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-700 dark:text-green-300">Gérer les enseignants</span>
                        </a>

                        <a href="{{ route('sessions-de-cours.index') }}" class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                            <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Voir les sessions</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sessions récentes -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Sessions récentes</h3>
                    <div class="space-y-3">
                        @foreach(\App\Models\SessionDeCours::with(['classe', 'matiere', 'enseignant'])->latest()->take(5)->get() as $session)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $session->matiere->nom }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $session->classe->nom }} - {{ $session->enseignant->prenom }} {{ $session->enseignant->nom }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $session->start_time->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                <div class="p-6 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Aucune information de coordinateur trouvée</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
