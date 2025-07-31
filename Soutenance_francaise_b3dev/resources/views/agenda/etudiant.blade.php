<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">📅 Mon Agenda</h1>
                <p class="text-gray-600">Bienvenue {{ $etudiant->prenom }} {{ $etudiant->nom }} - {{ $etudiant->classe->nom ?? 'Classe non définie' }}</p>
            </div>

            <!-- Statistiques rapides -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-400 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm opacity-90">Cours à venir</p>
                            <p class="text-2xl font-bold">{{ $sessionsFutures->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-400 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm opacity-90">Cours récents</p>
                            <p class="text-2xl font-bold">{{ $sessionsRecentes->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-400 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm opacity-90">Prochain cours</p>
                            <p class="text-lg font-bold">
                                @if($sessionsFutures->count() > 0)
                                    {{ $sessionsFutures->first()->start_time->format('d/m H:i') }}
                                @else
                                    Aucun
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglets -->
            <div class="mb-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="showTab('calendrier')" id="tab-calendrier" class="tab-button active py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                            📅 Calendrier
                        </button>
                        <button onclick="showTab('liste')" id="tab-liste" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            📋 Liste des cours
                        </button>
                        <button onclick="showTab('recent')" id="tab-recent" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            ⏰ Cours récents
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Contenu des onglets -->
            <div id="content-calendrier" class="tab-content">
                <!-- Calendrier -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Calendrier des cours</h2>
                        <div class="flex space-x-2">
                            <button onclick="previousMonth()" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <span id="current-month" class="text-lg font-medium text-gray-900">{{ now()->format('F Y') }}</span>
                            <button onclick="nextMonth()" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Grille du calendrier -->
                    <div class="grid grid-cols-7 gap-1 mb-4">
                        <div class="text-center text-sm font-medium text-gray-500 py-2">Dim</div>
                        <div class="text-center text-sm font-medium text-gray-500 py-2">Lun</div>
                        <div class="text-center text-sm font-medium text-gray-500 py-2">Mar</div>
                        <div class="text-center text-sm font-medium text-gray-500 py-2">Mer</div>
                        <div class="text-center text-sm font-medium text-gray-500 py-2">Jeu</div>
                        <div class="text-center text-sm font-medium text-gray-500 py-2">Ven</div>
                        <div class="text-center text-sm font-medium text-gray-500 py-2">Sam</div>
                    </div>

                    <div id="calendar-grid" class="grid grid-cols-7 gap-1">
                        <!-- Le calendrier sera généré par JavaScript -->
                    </div>
                </div>
            </div>

            <div id="content-liste" class="tab-content hidden">
                <!-- Liste des cours futurs -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">📚 Mes cours à venir</h2>

                    @if($sessionsFutures->count() > 0)
                        <div class="space-y-4">
                            @foreach($sessionsFutures as $session)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $session->matiere->nom }}</h3>
                                            <p class="text-gray-600">{{ $session->classe->nom }} - {{ $session->type_cours->nom ?? 'Présentiel' }}</p>
                                            <p class="text-sm text-gray-500">
                                                📅 {{ $session->start_time->format('d/m/Y') }}
                                                ⏰ {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                                            </p>
                                            @if($session->enseignant)
                                                <p class="text-sm text-gray-500">👨‍🏫 {{ $session->enseignant->prenom }} {{ $session->enseignant->nom }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                À venir
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun cours à venir</h3>
                            <p class="mt-1 text-sm text-gray-500">Vous n'avez pas de cours programmés pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div id="content-recent" class="tab-content hidden">
                <!-- Cours récents -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">⏰ Cours récents</h2>

                    @if($sessionsRecentes->count() > 0)
                        <div class="space-y-4">
                            @foreach($sessionsRecentes as $session)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $session->matiere->nom }}</h3>
                                            <p class="text-gray-600">{{ $session->classe->nom }} - {{ $session->type_cours->nom ?? 'Présentiel' }}</p>
                                            <p class="text-sm text-gray-500">
                                                📅 {{ $session->start_time->format('d/m/Y') }}
                                                ⏰ {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                                            </p>
                                            @if($session->enseignant)
                                                <p class="text-sm text-gray-500">👨‍🏫 {{ $session->enseignant->prenom }} {{ $session->enseignant->nom }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                                Terminé
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun cours récent</h3>
                            <p class="mt-1 text-sm text-gray-500">Aucun cours n'a été suivi récemment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentDate = new Date();
        let sessionsData = @json($sessionsParMois);

        function showTab(tabName) {
            // Masquer tous les contenus
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Désactiver tous les onglets
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Afficher le contenu sélectionné
            document.getElementById(`content-${tabName}`).classList.remove('hidden');

            // Activer l'onglet sélectionné
            document.getElementById(`tab-${tabName}`).classList.add('border-blue-500', 'text-blue-600');
            document.getElementById(`tab-${tabName}`).classList.remove('border-transparent', 'text-gray-500');

            // Si c'est le calendrier, le générer
            if (tabName === 'calendrier') {
                generateCalendar();
            }
        }

        function generateCalendar() {
            const grid = document.getElementById('calendar-grid');
            const monthYear = document.getElementById('current-month');

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            monthYear.textContent = new Date(year, month).toLocaleDateString('fr-FR', {
                month: 'long',
                year: 'numeric'
            });

            // Premier jour du mois
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            grid.innerHTML = '';

            // Générer les jours
            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);

                const dayElement = document.createElement('div');
                dayElement.className = 'min-h-[80px] p-2 border border-gray-200 text-sm';

                // Vérifier si c'est le jour actuel
                const isToday = date.toDateString() === new Date().toDateString();
                const isCurrentMonth = date.getMonth() === month;

                if (isToday) {
                    dayElement.classList.add('bg-blue-50', 'border-blue-300');
                } else if (!isCurrentMonth) {
                    dayElement.classList.add('bg-gray-50', 'text-gray-400');
                }

                // Numéro du jour
                const dayNumber = document.createElement('div');
                dayNumber.className = 'font-medium mb-1';
                dayNumber.textContent = date.getDate();
                dayElement.appendChild(dayNumber);

                // Sessions pour ce jour
                const sessionsForDay = getSessionsForDay(date);
                sessionsForDay.forEach(session => {
                    const sessionElement = document.createElement('div');
                    sessionElement.className = 'text-xs p-1 mb-1 rounded bg-blue-100 text-blue-800 truncate';
                    sessionElement.title = `${session.matiere.nom} - ${session.start_time}`;
                    sessionElement.textContent = session.matiere.nom;
                    dayElement.appendChild(sessionElement);
                });

                grid.appendChild(dayElement);
            }
        }

        function getSessionsForDay(date) {
            const dateString = date.toISOString().split('T')[0];
            const sessions = [];

            Object.values(sessionsData).forEach(monthData => {
                Object.entries(monthData.sessions).forEach(([day, daySessions]) => {
                    daySessions.forEach(session => {
                        const sessionDate = new Date(session.start_time);
                        if (sessionDate.toDateString() === date.toDateString()) {
                            sessions.push(session);
                        }
                    });
                });
            });

            return sessions;
        }

        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            generateCalendar();
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            generateCalendar();
        }

        // Initialiser le calendrier au chargement
        document.addEventListener('DOMContentLoaded', function() {
            generateCalendar();
        });
    </script>
</x-app-layout>
