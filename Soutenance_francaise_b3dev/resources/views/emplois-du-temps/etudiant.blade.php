<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mon Emploi du Temps') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header avec informations de l'étudiant -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Mon Emploi du Temps</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $etudiant->prenom }} {{ $etudiant->nom }} - {{ $etudiant->classe->nom ?? 'Classe non assignée' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <label for="annee_select" class="text-sm font-medium text-gray-700 dark:text-gray-300">Année :</label>
                            <select id="annee_select" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                @foreach($anneesAcademiques ?? [] as $annee)
                                    <option value="{{ $annee->id }}" {{ $anneeActive && $anneeActive->id == $annee->id ? 'selected' : '' }} data-url="{{ route('emplois-du-temps.etudiant') }}?annee_id={{ $annee->id }}">
                                        {{ $annee->nom }} @if($annee->actif) (Active) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation par date -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Navigation par semaine</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400" id="current-week-display">
                                    Semaine du {{ now()->startOfWeek()->format('d/m/Y') }} au {{ now()->endOfWeek()->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="previousWeek()" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200" title="Semaine précédente">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button onclick="currentWeek()" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                                Cette semaine
                            </button>
                            <button onclick="nextWeek()" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200" title="Semaine suivante">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>

                            <!-- Boutons d'export -->
                            <div class="flex items-center space-x-2 ml-4 border-l border-gray-300 dark:border-gray-600 pl-4">
                                <button onclick="exportEmploiDuTemps('pdf')" class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-red-600 bg-red-100 rounded-lg hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800 transition-colors duration-200" title="Exporter en PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>PDF</span>
                                </button>
                                <button onclick="exportEmploiDuTemps('png')" class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 transition-colors duration-200" title="Exporter en PNG">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>PNG</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sélecteur de date -->
                    <div class="mt-4 flex items-center space-x-4">
                        <label for="date_picker" class="text-sm font-medium text-gray-700 dark:text-gray-300">Sélectionner une date :</label>
                        <input type="date" id="date_picker" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                               value="{{ now()->format('Y-m-d') }}" onchange="goToWeek(this.value)">
                        <button onclick="today()" class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500 transition-colors duration-200">
                            Aujourd'hui
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cartes d'actions rapides -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <a href="{{ route('etudiant.mes-cours') }}" class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white hover:from-green-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Mes Cours</h3>
                            <p class="text-green-100">{{ $sessions->count() }} session(s)</p>
                        </div>
                        <div class="p-3 bg-green-400 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm">
                        <p>Prochain cours : {{ $sessions->where('start_time', '>', now())->first()?->start_time?->format('d/m/Y H:i') ?? 'Aucun' }}</p>
                    </div>
                </a>

                <a href="{{ route('etudiant.mes-presences') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Mes Présences</h3>
                            <p class="text-blue-100">Taux de présence</p>
                        </div>
                        <div class="p-3 bg-blue-400 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm">
                        <p>Moyenne : {{ round($sessions->avg('taux_presence') ?? 0, 1) }}%</p>
                    </div>
                </a>

                <a href="{{ route('etudiant.emploi-semaine') }}" class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white hover:from-purple-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Emploi du temps de la semaine</h3>
                            <p class="text-purple-100">{{ $sessions->where('start_time', '>=', now()->startOfWeek())->where('start_time', '<=', now()->endOfWeek())->count() }} cours cette semaine</p>
                        </div>
                        <div class="p-3 bg-purple-400 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm">
                        <p>{{ $sessions->unique('matiere_id')->count() }} matière(s) au programme</p>
                    </div>
                </a>
            </div>

            <!-- Liste des sessions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Mes Sessions de Cours</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $sessions->count() }} session(s) trouvée(s)</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="exportEmploiDuTemps('png')" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200" title="Exporter en PNG">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                            <button onclick="exportEmploiDuTemps('pdf')" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200" title="Exporter en PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Filtres</h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Filtre par matière -->
                            <div>
                                <label for="filter_matiere" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Matière</label>
                                <select id="filter_matiere" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100">
                                    <option value="">Toutes les matières</option>
                                    @foreach($sessions->unique('matiere_id') as $session)
                                        <option value="{{ $session->matiere_id }}">{{ $session->matiere->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre par type de cours -->
                            <div>
                                <label for="filter_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type de cours</label>
                                <select id="filter_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100">
                                    <option value="">Tous les types</option>
                                    @foreach($sessions->unique('type_cours_id') as $session)
                                        <option value="{{ $session->type_cours_id }}">{{ $session->typeCours->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre par date -->
                            <div>
                                <label for="filter_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Période</label>
                                <select id="filter_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100">
                                    <option value="">Toutes les dates</option>
                                    <option value="today">Aujourd'hui</option>
                                    <option value="week">Cette semaine</option>
                                    <option value="month">Ce mois</option>
                                    <option value="future">Cours à venir</option>
                                    <option value="past">Cours passés</option>
                                </select>
                            </div>

                            <!-- Bouton de réinitialisation -->
                            <div class="flex items-end">
                                <button onclick="resetFilters()" class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500 transition-colors duration-200">
                                    Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques rapides -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-blue-900 dark:text-blue-100">Total</p>
                                    <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{ $sessions->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-green-900 dark:text-green-100">À venir</p>
                                    <p class="text-lg font-semibold text-green-900 dark:text-green-100">{{ $sessions->where('start_time', '>', now())->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-yellow-900 dark:text-yellow-100">Aujourd'hui</p>
                                    <p class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">{{ $sessions->where('start_time', '>=', now()->startOfDay())->where('start_time', '<=', now()->endOfDay())->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-purple-900 dark:text-purple-100">Matières</p>
                                    <p class="text-lg font-semibold text-purple-900 dark:text-purple-100">{{ $sessions->unique('matiere_id')->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emploi du temps hebdomadaire avec dates -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Emploi du temps de la semaine</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400" id="week-display">
                                Semaine du {{ now()->startOfWeek()->format('d/m/Y') }} au {{ now()->endOfWeek()->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Horaire</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Lundi<br><span class="text-xs font-normal" id="date-lundi">{{ now()->startOfWeek()->format('d/m') }}</span>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Mardi<br><span class="text-xs font-normal" id="date-mardi">{{ now()->startOfWeek()->addDay()->format('d/m') }}</span>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Mercredi<br><span class="text-xs font-normal" id="date-mercredi">{{ now()->startOfWeek()->addDays(2)->format('d/m') }}</span>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Jeudi<br><span class="text-xs font-normal" id="date-jeudi">{{ now()->startOfWeek()->addDays(3)->format('d/m') }}</span>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Vendredi<br><span class="text-xs font-normal" id="date-vendredi">{{ now()->startOfWeek()->addDays(4)->format('d/m') }}</span>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Samedi<br><span class="text-xs font-normal" id="date-samedi">{{ now()->startOfWeek()->addDays(5)->format('d/m') }}</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @php
                                        $creneaux = [
                                            '08:00-10:00' => '08:00',
                                            '10:00-12:00' => '10:00',
                                            '14:00-16:00' => '14:00',
                                            '16:00-18:00' => '16:00'
                                        ];
                                        $joursAnglais = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                                        $joursFrancais = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
                                    @endphp

                                    @foreach($creneaux as $horaire => $heure)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 border-r border-gray-200 dark:border-gray-700">
                                                {{ $horaire }}
                                            </td>
                                            @foreach($joursAnglais as $index => $jourAnglais)
                                                @php
                                                    $jourFrancais = $joursFrancais[$index];
                                                    $dateJour = now()->startOfWeek()->next($jourAnglais);
                                                    $heureDebut = $dateJour->copy()->setTimeFromTimeString($heure);
                                                    $heureFin = $heureDebut->copy()->addHours(2);
                                                    $session = $sessions->where('start_time', '>=', $heureDebut->copy()->subMinutes(30))
                                                        ->where('start_time', '<', $heureFin->copy()->addMinutes(30))
                                                        ->first();
                                                @endphp
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 {{ $index < 5 ? 'border-r border-gray-200 dark:border-gray-700' : '' }}">
                                                    @if($session)
                                                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-200 dark:border-blue-800">
                                                            <div class="font-medium text-blue-900 dark:text-blue-100">
                                                                {{ $session->matiere->nom }}
                                                            </div>
                                                            <div class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                                                {{ $session->enseignant->prenom }} {{ $session->enseignant->nom }}
                                                            </div>
                                                            <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                                {{ $session->typeCours->nom }}
                                                            </div>
                                                            <div class="text-xs text-blue-500 dark:text-blue-500 mt-1">
                                                                {{ $session->location ?? 'Non spécifié' }}
                                                            </div>
                                                            <div class="text-xs text-blue-400 dark:text-blue-600 mt-1">
                                                                {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-gray-400 dark:text-gray-500 text-center py-2">
                                                            Libre
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Matière</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Enseignant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Heure</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lieu</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($sessions as $session)
                                    <tr class="session-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                        data-matiere="{{ $session->matiere_id }}"
                                        data-type="{{ $session->type_cours_id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                            {{ substr($session->matiere->nom ?? 'ND', 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $session->matiere->nom ?? 'Non défini' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $session->typeCours->nom ?? 'Non défini' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ ($session->enseignant->prenom ?? '') }} {{ ($session->enseignant->nom ?? '') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $session->start_time->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $typeColors = [
                                                    'Présentiel' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                    'E-learning' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                    'Workshop' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
                                                ];
                                                $color = $typeColors[$session->typeCours->nom ?? ''] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $color }}">
                                                {{ $session->typeCours->nom ?? 'Non défini' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'Planifié' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                    'En cours' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                    'Terminé' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                    'Annulé' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                    'Programmé' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200'
                                                ];
                                                $color = $statusColors[$session->statutSession->nom ?? ''] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $color }}">
                                                {{ $session->statutSession->nom ?? 'Non défini' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $session->location ?? 'Non spécifié' }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aucune session trouvée</h3>
                                                <p class="text-gray-500 dark:text-gray-400">Vous n'avez pas encore de sessions de cours programmées.</p>
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

        // Fonction pour exporter l'emploi du temps
        function exportEmploiDuTemps(format = 'pdf') {
            console.log('Export de l\'emploi du temps en format:', format);

            // Afficher un toast de chargement
            showToast('Téléchargement en cours...', 'info');

            if (format === 'pdf') {
                // Pour PDF, téléchargement direct
                window.open(`{{ route("etudiant.export-emploi-du-temps") }}?format=pdf`, '_blank');
                showToast('PDF généré avec succès !', 'success');
            } else {
                // Pour PNG, utiliser html2canvas
                generatePNG();
            }
        }

        // Fonction pour générer PNG avec html2canvas
        function generatePNG() {
            // Créer un élément temporaire pour l'export
            const exportElement = document.createElement('div');
            exportElement.style.position = 'absolute';
            exportElement.style.left = '-9999px';
            exportElement.style.top = '0';
            exportElement.style.width = '1200px';
            exportElement.style.backgroundColor = 'white';
            exportElement.style.padding = '20px';
            exportElement.style.fontFamily = 'Arial, sans-serif';

            // Récupérer le contenu du tableau
            const table = document.querySelector('table');
            if (table) {
                exportElement.innerHTML = `
                    <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px;">
                        <h1 style="color: #333; margin: 0; font-size: 28px; font-weight: bold;">📚 Emploi du Temps</h1>
                        <p style="color: #666; margin: 8px 0; font-size: 16px;"><strong>👤 Étudiant :</strong> {{ Auth::user()->etudiant->prenom ?? '' }} {{ Auth::user()->etudiant->nom ?? '' }}</p>
                        <p style="color: #666; margin: 8px 0; font-size: 16px;"><strong>🏫 Classe :</strong> {{ Auth::user()->etudiant->classe->nom ?? 'Non assignée' }}</p>
                        <p style="color: #666; margin: 8px 0; font-size: 16px;"><strong>📋 Date d'export :</strong> ${new Date().toLocaleDateString('fr-FR')}</p>
                    </div>
                    ${table.outerHTML}
                    <div style="margin-top: 30px; text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 10px;">
                        <p>📄 Document généré automatiquement le ${new Date().toLocaleDateString('fr-FR')}</p>
                        <p>📊 Total des sessions : ${table.querySelectorAll('tbody tr').length}</p>
                    </div>
                `;

                document.body.appendChild(exportElement);

                // Utiliser html2canvas pour convertir en image
                if (typeof html2canvas !== 'undefined') {
                    html2canvas(exportElement, {
                        scale: 2,
                        backgroundColor: '#ffffff',
                        width: 1200,
                        height: exportElement.scrollHeight,
                        useCORS: true,
                        allowTaint: true,
                        logging: false
                    }).then(canvas => {
                        // Convertir en blob et télécharger
                        canvas.toBlob(function(blob) {
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = `emploi_du_temps_${new Date().toISOString().split('T')[0]}.png`;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            document.body.removeChild(a);
                            document.body.removeChild(exportElement);

                            showToast('Image PNG générée avec succès !', 'success');
                        }, 'image/png', 0.95);
                    }).catch(error => {
                        console.error('Erreur html2canvas:', error);
                        showToast('Erreur lors de la génération PNG', 'error');
                        document.body.removeChild(exportElement);
                    });
                } else {
                    // Si html2canvas n'est pas disponible, afficher un message
                    showToast('html2canvas non disponible. Veuillez utiliser l\'export PDF.', 'error');
                    document.body.removeChild(exportElement);
                }
            } else {
                showToast('Aucun tableau trouvé à exporter', 'error');
            }
        }

        // Fonction pour afficher un toast DaisyUI
        function showToast(message, type = 'info') {
            // Créer l'élément toast
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} fixed top-4 right-4 z-50 max-w-sm`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        ${type === 'success' ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>' :
                          type === 'error' ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>' :
                          '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>'}
                    </svg>
                    <span class="ml-2">${message}</span>
                </div>
            `;

            // Ajouter au DOM
            document.body.appendChild(toast);

            // Supprimer après 3 secondes
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 3000);
        }

        // Fonction pour voir les détails d'une session
        function viewSessionDetails(sessionId) {
            console.log('Voir les détails de la session:', sessionId);
            // Ici on peut ajouter la logique pour afficher les détails
            alert('Détails de la session ' + sessionId);
        }

        // Fonction pour ajouter au calendrier
        function addToCalendar(sessionId) {
            console.log('Ajouter au calendrier:', sessionId);
            // Ici on peut ajouter la logique pour ajouter au calendrier
            alert('Session ajoutée au calendrier');
        }

        // Fonctions pour les filtres
        function applyFilters() {
            const matiereFilter = document.getElementById('filter_matiere').value;
            const typeFilter = document.getElementById('filter_type').value;
            const dateFilter = document.getElementById('filter_date').value;

            const rows = document.querySelectorAll('.session-row');
            let visibleCount = 0;

            rows.forEach(row => {
                let show = true;

                // Filtre par matière
                if (matiereFilter && row.dataset.matiere !== matiereFilter) {
                    show = false;
                }

                // Filtre par type
                if (typeFilter && row.dataset.type !== typeFilter) {
                    show = false;
                }

                // Filtre par date
                if (dateFilter) {
                    const sessionDate = new Date(row.querySelector('td:nth-child(3)').textContent.split('/').reverse().join('-'));
                    const today = new Date();

                    switch(dateFilter) {
                        case 'today':
                            show = show && sessionDate.toDateString() === today.toDateString();
                            break;
                        case 'week':
                            const weekStart = new Date(today.setDate(today.getDate() - today.getDay()));
                            const weekEnd = new Date(weekStart.getTime() + 6 * 24 * 60 * 60 * 1000);
                            show = show && sessionDate >= weekStart && sessionDate <= weekEnd;
                            break;
                        case 'month':
                            show = show && sessionDate.getMonth() === today.getMonth() && sessionDate.getFullYear() === today.getFullYear();
                            break;
                        case 'future':
                            show = show && sessionDate > today;
                            break;
                        case 'past':
                            show = show && sessionDate < today;
                            break;
                    }
                }

                row.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });

            // Mettre à jour le compteur
            const counter = document.querySelector('p.text-sm.text-gray-600');
            if (counter) {
                counter.textContent = visibleCount + ' session(s) trouvée(s)';
            }
        }

        function resetFilters() {
            document.getElementById('filter_matiere').value = '';
            document.getElementById('filter_type').value = '';
            document.getElementById('filter_date').value = '';

            const rows = document.querySelectorAll('.session-row');
            rows.forEach(row => {
                row.style.display = '';
            });

            // Mettre à jour le compteur
            const counter = document.querySelector('p.text-sm.text-gray-600');
            if (counter) {
                counter.textContent = rows.length + ' session(s) trouvée(s)';
            }
        }

        // Écouter les changements des filtres
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('filter_matiere')?.addEventListener('change', applyFilters);
            document.getElementById('filter_type')?.addEventListener('change', applyFilters);
            document.getElementById('filter_date')?.addEventListener('change', applyFilters);
        });

        // Variables globales pour la navigation par date
        let currentWeekStart = new Date();
        currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay() + 1); // Lundi

        // Fonction pour mettre à jour l'affichage de la semaine
        function updateWeekDisplay() {
            const weekEnd = new Date(currentWeekStart);
            weekEnd.setDate(weekEnd.getDate() + 6); // Samedi

            const display = document.getElementById('current-week-display');
            if (display) {
                display.textContent = `Semaine du ${formatDate(currentWeekStart)} au ${formatDate(weekEnd)}`;
            }

            // Mettre à jour le sélecteur de date
            const datePicker = document.getElementById('date_picker');
            if (datePicker) {
                datePicker.value = currentWeekStart.toISOString().split('T')[0];
            }

            // Mettre à jour les dates dans l'emploi du temps hebdomadaire
            updateWeeklyTimetableDates();
        }

        // Fonction pour mettre à jour les dates dans l'emploi du temps hebdomadaire
        function updateWeeklyTimetableDates() {
            try {
                const jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
                const dateLundi = new Date(currentWeekStart);

                jours.forEach((jour, index) => {
                    const dateElement = document.getElementById(`date-${jour}`);
                    if (dateElement) {
                        const dateJour = new Date(dateLundi);
                        dateJour.setDate(dateLundi.getDate() + index);
                        dateElement.textContent = formatDateShort(dateJour);
                    }
                });

                // Mettre à jour l'affichage de la semaine
                const weekDisplay = document.getElementById('week-display');
                if (weekDisplay) {
                    const weekEnd = new Date(currentWeekStart);
                    weekEnd.setDate(weekEnd.getDate() + 6);
                    weekDisplay.textContent = `Semaine du ${formatDate(currentWeekStart)} au ${formatDate(weekEnd)}`;
                }

                console.log('Dates de l\'emploi du temps hebdomadaire mises à jour');
            } catch (error) {
                console.error('Erreur lors de la mise à jour des dates:', error);
            }
        }

        // Fonction pour formater une date courte (dd/mm)
        function formatDateShort(date) {
            return date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit'
            });
        }

        // Fonction pour formater une date complète (dd/mm/yyyy)
        function formatDate(date) {
            return date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        // Fonction pour aller à la semaine précédente
        function previousWeek() {
            try {
                currentWeekStart.setDate(currentWeekStart.getDate() - 7);
                updateWeekDisplay();
                filterSessionsByWeek();
                console.log('Navigation vers la semaine précédente');
            } catch (error) {
                console.error('Erreur lors de la navigation vers la semaine précédente:', error);
            }
        }

        // Fonction pour aller à la semaine suivante
        function nextWeek() {
            try {
                currentWeekStart.setDate(currentWeekStart.getDate() + 7);
                updateWeekDisplay();
                filterSessionsByWeek();
                console.log('Navigation vers la semaine suivante');
            } catch (error) {
                console.error('Erreur lors de la navigation vers la semaine suivante:', error);
            }
        }

        // Fonction pour aller à la semaine actuelle
        function currentWeek() {
            try {
                currentWeekStart = new Date();
                currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay() + 1);
                updateWeekDisplay();
                filterSessionsByWeek();
                console.log('Navigation vers la semaine actuelle');
            } catch (error) {
                console.error('Erreur lors de la navigation vers la semaine actuelle:', error);
            }
        }

        // Fonction pour aller à aujourd'hui
        function today() {
            try {
                const today = new Date();
                currentWeekStart = new Date(today);
                currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay() + 1);
                updateWeekDisplay();
                filterSessionsByWeek();
                console.log('Navigation vers aujourd\'hui');
            } catch (error) {
                console.error('Erreur lors de la navigation vers aujourd\'hui:', error);
            }
        }

        // Fonction pour aller à une semaine spécifique
        function goToWeek(dateString) {
            try {
                const selectedDate = new Date(dateString);
                currentWeekStart = new Date(selectedDate);
                currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay() + 1);
                updateWeekDisplay();
                filterSessionsByWeek();
                console.log('Navigation vers la semaine du', dateString);
            } catch (error) {
                console.error('Erreur lors de la navigation vers la semaine:', error);
            }
        }

        // Fonction pour filtrer les sessions par semaine
        function filterSessionsByWeek() {
            try {
                const weekEnd = new Date(currentWeekStart);
                weekEnd.setDate(weekEnd.getDate() + 6);

                const rows = document.querySelectorAll('.session-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const dateCell = row.querySelector('td:nth-child(3)');
                    if (dateCell) {
                        const sessionDate = new Date(dateCell.textContent.split('/').reverse().join('-'));

                        // Vérifier si la session est dans la semaine sélectionnée
                        const isInWeek = sessionDate >= currentWeekStart && sessionDate <= weekEnd;

                        row.style.display = isInWeek ? '' : 'none';
                        if (isInWeek) visibleCount++;
                    }
                });

                // Mettre à jour le compteur
                const counter = document.querySelector('p.text-sm.text-gray-600');
                if (counter) {
                    counter.textContent = visibleCount + ' session(s) trouvée(s)';
                }

                console.log(`Sessions filtrées pour la semaine du ${formatDate(currentWeekStart)} au ${formatDate(weekEnd)}`);
            } catch (error) {
                console.error('Erreur lors du filtrage des sessions:', error);
            }
        }

        // Initialiser l'affichage au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            updateWeekDisplay();
            filterSessionsByWeek();
        });

        // Fonction pour exporter l'emploi du temps
        function exportEmploiDuTemps(format) {
            try {
                console.log(`Export de l'emploi du temps en ${format}...`);

                // Afficher un message de chargement
                const loadingMessage = document.createElement('div');
                loadingMessage.id = 'loading-export';
                loadingMessage.className = 'fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                loadingMessage.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Téléchargement en cours...</span>
                    </div>
                `;
                document.body.appendChild(loadingMessage);

                // Construire l'URL d'export
                const exportUrl = `/etudiant/export-emploi-du-temps-v2?format=${format}`;

                // Créer un lien temporaire pour le téléchargement
                const link = document.createElement('a');
                link.href = exportUrl;
                link.download = `emploi_du_temps_${format}.${format}`;
                link.style.display = 'none';
                document.body.appendChild(link);

                // Déclencher le téléchargement
                link.click();

                // Nettoyer après un délai
                setTimeout(() => {
                    document.body.removeChild(link);
                    if (document.getElementById('loading-export')) {
                        document.body.removeChild(document.getElementById('loading-export'));
                    }
                }, 3000);

                console.log(`Export ${format} déclenché avec succès`);

            } catch (error) {
                console.error('Erreur lors de l\'export:', error);

                // Afficher un message d'erreur
                if (document.getElementById('loading-export')) {
                    document.body.removeChild(document.getElementById('loading-export'));
                }

                const errorMessage = document.createElement('div');
                errorMessage.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                errorMessage.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Erreur lors de l'export</span>
                    </div>
                `;
                document.body.appendChild(errorMessage);

                setTimeout(() => {
                    if (document.body.contains(errorMessage)) {
                        document.body.removeChild(errorMessage);
                    }
                }, 5000);
            }
        }
    </script>
</x-app-layout>
