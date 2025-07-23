<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Coordinateur Pédagogique') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages de succès/erreur -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Actions rapides pour Coordinateur -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Actions rapides - Coordinateur Pédagogique</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Gérer les emplois du temps -->
                        <a href="{{ route('sessions-de-cours.index') }}" class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                            <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Gérer les emplois du temps</span>
                        </a>

                        <!-- Créer une session de cours -->
                        <a href="{{ route('sessions-de-cours.create') }}" class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-700 dark:text-green-300">Créer une session</span>
                        </a>

                        <!-- Faire l'appel (e-learning et workshops) -->
                        <a href="{{ route('presences.index') }}" class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                            <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Faire l'appel (E-learning/Workshops)</span>
                        </a>

                        <!-- Justifier les absences -->
                        <a href="{{ route('presences.index') }}" class="flex items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                            <svg class="w-6 h-6 text-orange-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-orange-700 dark:text-orange-300">Justifier les absences</span>
                        </a>

                        <!-- Notifications -->
                        <a href="{{ route('notifications.index') }}" class="flex items-center p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900/30 transition-colors">
                            <svg class="w-6 h-6 text-teal-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19A2 2 0 004 6v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-1.81-1.81z"></path>
                            </svg>
                            <span class="text-sm font-medium text-teal-700 dark:text-teal-300">Notifications</span>
                        </a>

                        <!-- Gérer les classes -->
                        <a href="{{ route('classes.index') }}" class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors">
                            <svg class="w-6 h-6 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Gérer les classes</span>
                        </a>

                        <!-- Gérer les étudiants -->
                        <a href="{{ route('etudiants.index') }}" class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                            <svg class="w-6 h-6 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-purple-700 dark:text-purple-300">Gérer les étudiants</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filtres pour les graphiques -->
            <div class="mb-8">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="classe_id" class="block text-sm font-medium text-gray-700">Classe</label>
                        <select name="classe_id" id="classe_id" class="mt-1 block w-48 rounded-md border-gray-300 shadow-sm">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" @if($classeId == $classe->id) selected @endif>{{ $classe->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="debut" class="block text-sm font-medium text-gray-700">Début</label>
                        <input type="date" name="debut" id="debut" value="{{ $periodeDebut }}" class="mt-1 block w-36 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label for="fin" class="block text-sm font-medium text-gray-700">Fin</label>
                        <input type="date" name="fin" id="fin" value="{{ $periodeFin }}" class="mt-1 block w-36 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow transition">Filtrer</button>
                </form>
            </div>

            <!-- Graphiques pour l'équipe pédagogique -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Taux de présence par étudiant</h3>
                    <canvas id="presenceEtudiantChart"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Taux de présence par classe</h3>
                    <canvas id="presenceClasseChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Volume de cours par type</h3>
                    <canvas id="volumeTypeChart"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Volume cumulé de cours dispensés</h3>
                    <canvas id="volumeCumuleChart"></canvas>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                // Graphe taux de présence par étudiant
                const presenceData = @json($presenceParEtudiant);
                const ctx1 = document.getElementById('presenceEtudiantChart').getContext('2d');
                const colors1 = presenceData.map(e => {
                    if (e.taux >= 70) return '#15803d'; // vert foncé
                    if (e.taux > 50) return '#4ade80'; // vert clair
                    if (e.taux > 30) return '#f59e42'; // orange
                    return '#ef4444'; // rouge
                });
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: presenceData.map(e => e.nom),
                        datasets: [{
                            label: 'Taux de présence (%)',
                            data: presenceData.map(e => e.taux),
                            backgroundColor: colors1,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        scales: { x: { min: 0, max: 100, ticks: { stepSize: 10 } } },
                        plugins: { legend: { display: false } }
                    }
                });

                // Graphe taux de présence par classe
                const presenceClasse = @json($presenceParClasse);
                const ctx2 = document.getElementById('presenceClasseChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: presenceClasse.map(e => e.nom),
                        datasets: [{
                            label: 'Taux de présence (%)',
                            data: presenceClasse.map(e => e.taux),
                            backgroundColor: '#6366f1',
                        }]
                    },
                    options: {
                        scales: { y: { min: 0, max: 100, ticks: { stepSize: 10 } } },
                        plugins: { legend: { display: false } }
                    }
                });

                // Graphe volume de cours par type
                const volumeType = @json($volumeParType);
                const ctx3 = document.getElementById('volumeTypeChart').getContext('2d');
                new Chart(ctx3, {
                    type: 'doughnut',
                    data: {
                        labels: volumeType.map(e => e.type),
                        datasets: [{
                            label: 'Volume',
                            data: volumeType.map(e => e.nb),
                            backgroundColor: ['#6366f1', '#f59e42', '#10b981'], // 3 couleurs pour 3 types
                        }]
                    },
                    options: {
                        plugins: { legend: { position: 'bottom' } }
                    }
                });

                // Graphe volume cumulé de cours dispensés
                const volumeCumule = @json($volumeCumule);
                const ctx4 = document.getElementById('volumeCumuleChart').getContext('2d');
                new Chart(ctx4, {
                    type: 'bar',
                    data: {
                        labels: volumeCumule.map(e => e.periode),
                        datasets: [{
                            label: 'Volume de cours',
                            data: volumeCumule.map(e => e.nb),
                            backgroundColor: '#8b5cf6',
                        }]
                    },
                    options: {
                        scales: { y: { beginAtZero: true } },
                        plugins: { legend: { display: false } }
                    }
                });
            </script>

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
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i') }}
                                </span>
                                <a href="{{ route('sessions-de-cours.appel', $session->id) }}"
                                   class="text-green-600 hover:text-green-900 text-xs">
                                    <i class="fas fa-clipboard-check mr-1"></i>Appel
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


