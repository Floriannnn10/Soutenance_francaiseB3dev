<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tableau de bord Enseignant') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Informations de l'enseignant -->
            @php
                // On tente de retrouver l'enseignant par l'email de l'utilisateur connecté
                $enseignant = \App\Models\Enseignant::where('email', auth()->user()->email ?? null)->first();
            @endphp
            @if($enseignant)
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-xl font-bold">{{ substr($enseignant->prenom, 0, 1) }}{{ substr($enseignant->nom, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $enseignant->prenom }} {{ $enseignant->nom }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Enseignant</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides pour Enseignant -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Actions rapides - Enseignant</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Voir mon emploi du temps -->
                        <a href="{{ route('sessions-de-cours.index') }}" class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                            <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Mon emploi du temps</span>
                        </a>

                        <!-- Faire l'appel -->
                        <a href="{{ route('presences.index') }}" class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-700 dark:text-green-300">Faire l'appel (Présentiel)</span>
                        </a>

                        <!-- Notifications -->
                        <a href="{{ route('notifications.index') }}" class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                            <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19A2 2 0 004 6v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-1.81-1.81z"></path>
                            </svg>
                            <span class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Notifications</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistiques de l'enseignant -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Sessions de cours -->
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
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sessions de cours</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $enseignant->sessionsDeCours()->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Classes enseignées -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Classes enseignées</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $enseignant->sessionsDeCours()->distinct('classe_id')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Matières enseignées -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Matières enseignées</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $enseignant->sessionsDeCours()->distinct('matiere_id')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mes cours de la semaine -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Mes cours de la semaine</h3>
                    <div class="space-y-3">
                        @php
                            $debutSemaine = now()->startOfWeek();
                            $finSemaine = now()->endOfWeek();
                            $coursSemaine = $enseignant->sessionsDeCours()
                                ->where('start_time', '>=', $debutSemaine)
                                ->where('start_time', '<=', $finSemaine)
                                ->with(['classe', 'matiere'])
                                ->orderBy('start_time')
                                ->get();
                        @endphp

                        @if($coursSemaine->count() > 0)
                            @foreach($coursSemaine as $session)
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
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $session->classe->nom }} - {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($session->typeCours->nom === 'Présentiel')
                                        <a href="{{ route('sessions-de-cours.appel', $session->id) }}"
                                           class="text-green-600 hover:text-green-900 text-xs">
                                            <i class="fas fa-clipboard-check mr-1"></i>Faire l'appel
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $session->typeCours->nom }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">Aucun cours programmé cette semaine</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Prochains cours -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Prochains cours</h3>
                    <div class="space-y-3">
                        @php
                            $prochainsCours = $enseignant->sessionsDeCours()
                                ->where('start_time', '>', now())
                                ->with(['classe', 'matiere'])
                                ->orderBy('start_time')
                                ->take(5)
                                ->get();
                        @endphp

                        @if($prochainsCours->count() > 0)
                            @foreach($prochainsCours as $session)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $session->matiere->nom }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $session->classe->nom }} - {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $session->typeCours->nom }}</span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">Aucun cours à venir</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @else
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p>Profil enseignant non trouvé. Veuillez contacter l'administrateur.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

