<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mes Cours - Agenda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header avec informations -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Agenda de Mes Cours</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $etudiant->prenom }} {{ $etudiant->nom }} - {{ $etudiant->classe->nom ?? 'Classe non assignée' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <label for="annee_select" class="text-sm font-medium text-gray-700 dark:text-gray-300">Année :</label>
                            <select id="annee_select" class="rounded-lg border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                @foreach($anneesAcademiques as $annee)
                                    <option value="{{ $annee->id }}" {{ $anneeActive && $anneeActive->id == $annee->id ? 'selected' : '' }} data-url="{{ route('etudiant.mes-cours') }}?annee_id={{ $annee->id }}">
                                        {{ $annee->nom }} @if($annee->actif) (Active) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation du calendrier -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button onclick="previousMonth()" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <h3 id="currentMonth" class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ now()->format('F Y') }}
                            </h3>
                            <button onclick="nextMonth()" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="today()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors duration-200">
                                Aujourd'hui
                            </button>
                            <button onclick="currentWeek()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500 transition-colors duration-200">
                                Cette semaine
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendrier -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- En-têtes des jours -->
                    <div class="grid grid-cols-7 gap-1 mb-4">
                        <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Dim</div>
                        <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Lun</div>
                        <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Mar</div>
                        <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Mer</div>
                        <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Jeu</div>
                        <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Ven</div>
                        <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">Sam</div>
                    </div>

                    <!-- Grille du calendrier -->
                    <div id="calendarGrid" class="grid grid-cols-7 gap-1">
                        <!-- Le contenu sera généré par JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Modal pour les détails d'un cours -->
            <div id="courseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                <div class="relative top-10 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Détails du Cours</h3>
                            <button onclick="closeCourseModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div id="courseModalContent">
                            <!-- Le contenu sera chargé dynamiquement -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentDate = new Date();
        let sessions = @json($sessions);

        // Gestion du changement d'année académique
        document.getElementById('annee_select')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const url = selectedOption.dataset.url;
            if (url) {
                window.location.href = url;
            }
        });

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            document.getElementById('currentMonth').textContent = new Date(year, month).toLocaleDateString('fr-FR', {
                month: 'long',
                year: 'numeric'
            });

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            const calendarGrid = document.getElementById('calendarGrid');
            calendarGrid.innerHTML = '';

            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);

                const dayElement = document.createElement('div');
                dayElement.className = 'min-h-24 border border-gray-200 dark:border-gray-700 p-2 relative';

                // Si c'est un jour du mois précédent ou suivant
                if (date.getMonth() !== month) {
                    dayElement.className += ' bg-gray-50 dark:bg-gray-700 text-gray-400';
                } else {
                    dayElement.className += ' bg-white dark:bg-gray-800';
                }

                // Si c'est aujourd'hui
                const today = new Date();
                if (date.toDateString() === today.toDateString()) {
                    dayElement.className += ' bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-600';
                }

                dayElement.innerHTML = `
                    <div class="text-sm font-medium mb-1">${date.getDate()}</div>
                    <div class="space-y-1" id="events-${date.toISOString().split('T')[0]}">
                        <!-- Les événements seront ajoutés ici -->
                    </div>
                `;

                calendarGrid.appendChild(dayElement);
            }

            // Ajouter les sessions au calendrier
            addSessionsToCalendar();
        }

        function addSessionsToCalendar() {
            sessions.forEach(session => {
                const sessionDate = new Date(session.start_time);
                const dateString = sessionDate.toISOString().split('T')[0];
                const eventContainer = document.getElementById(`events-${dateString}`);

                if (eventContainer) {
                    const eventElement = document.createElement('div');
                    eventElement.className = 'text-xs p-1 rounded cursor-pointer transition-colors duration-200';

                    // Couleur selon le type de cours
                    const typeColors = {
                        'Présentiel': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'E-learning': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'Workshop': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
                    };

                    const color = typeColors[session.type_cours?.nom] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                    eventElement.className += ` ${color}`;

                    eventElement.innerHTML = `
                        <div class="font-medium">${session.matiere?.nom || 'Cours'}</div>
                        <div class="text-xs opacity-75">${sessionDate.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}</div>
                    `;

                    eventElement.onclick = () => showCourseDetails(session);
                    eventContainer.appendChild(eventElement);
                }
            });
        }

        function showCourseDetails(session) {
            const modal = document.getElementById('courseModal');
            const content = document.getElementById('courseModalContent');

            const sessionDate = new Date(session.start_time);
            const endDate = new Date(session.end_time);

            content.innerHTML = `
                <div class="space-y-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">${session.matiere?.nom || 'Cours'}</h4>
                        <div class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                            <p><strong>Date :</strong> ${sessionDate.toLocaleDateString('fr-FR')}</p>
                            <p><strong>Heure :</strong> ${sessionDate.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})} - ${endDate.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}</p>
                            <p><strong>Enseignant :</strong> ${session.enseignant?.prenom || ''} ${session.enseignant?.nom || ''}</p>
                            <p><strong>Type :</strong> ${session.type_cours?.nom || 'Non défini'}</p>
                            <p><strong>Lieu :</strong> ${session.location || 'Non spécifié'}</p>
                            <p><strong>Statut :</strong> ${session.statut_session?.nom || 'Non défini'}</p>
                        </div>
                    </div>
                </div>
            `;

            modal.classList.remove('hidden');
        }

        function closeCourseModal() {
            document.getElementById('courseModal').classList.add('hidden');
        }

        function previousMonth() {
            console.log('previousMonth() appelée');
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        }

        function nextMonth() {
            console.log('nextMonth() appelée');
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        }

        function today() {
            console.log('today() appelée');
            currentDate = new Date();
            renderCalendar();
        }

        function currentWeek() {
            console.log('currentWeek() appelée');
            currentDate = new Date();
            renderCalendar();
        }

        // Vérifier que les fonctions sont bien définies
        console.log('Fonctions de navigation du calendrier chargées:', {
            previousMonth: typeof previousMonth,
            nextMonth: typeof nextMonth,
            today: typeof today,
            currentWeek: typeof currentWeek
        });

        // Initialiser le calendrier
        renderCalendar();
    </script>
</x-app-layout>
