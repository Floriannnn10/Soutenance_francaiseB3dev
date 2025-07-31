<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Emploi du Temps de la Semaine') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header avec informations -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Emploi du Temps de la Semaine</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $etudiant->prenom }} {{ $etudiant->nom }} - {{ $etudiant->classe->nom ?? 'Classe non assignée' }}
                                </p>
                                <p class="text-sm text-purple-600 dark:text-purple-400">
                                    Semaine du {{ $debutSemaine->format('d/m/Y') }} au {{ $finSemaine->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <label for="annee_select" class="text-sm font-medium text-gray-700 dark:text-gray-300">Année :</label>
                            <select id="annee_select" class="rounded-lg border-gray-300 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                @foreach($anneesAcademiques as $annee)
                                    <option value="{{ $annee->id }}" {{ $anneeActive && $anneeActive->id == $annee->id ? 'selected' : '' }} data-url="{{ route('etudiant.emploi-semaine') }}?annee_id={{ $annee->id }}">
                                        {{ $annee->nom }} @if($annee->actif) (Active) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation des semaines -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button onclick="previousWeek()" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <h3 id="currentWeek" class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                Semaine du {{ $debutSemaine->format('d/m/Y') }} au {{ $finSemaine->format('d/m/Y') }}
                            </h3>
                            <button onclick="nextWeek()" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="currentWeek()" class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                Cette semaine
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
                </div>
            </div>

            <!-- Emploi du temps -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Horaire</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Lundi<br><span class="text-xs font-normal text-gray-400 dark:text-gray-500" id="date-lundi">{{ $debutSemaine->format('d/m') }}</span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Mardi<br><span class="text-xs font-normal text-gray-400 dark:text-gray-500" id="date-mardi">{{ $debutSemaine->addDay()->format('d/m') }}</span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Mercredi<br><span class="text-xs font-normal text-gray-400 dark:text-gray-500" id="date-mercredi">{{ $debutSemaine->addDays(2)->format('d/m') }}</span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Jeudi<br><span class="text-xs font-normal text-gray-400 dark:text-gray-500" id="date-jeudi">{{ $debutSemaine->addDays(3)->format('d/m') }}</span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Vendredi<br><span class="text-xs font-normal text-gray-400 dark:text-gray-500" id="date-vendredi">{{ $debutSemaine->addDays(4)->format('d/m') }}</span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Samedi<br><span class="text-xs font-normal text-gray-400 dark:text-gray-500" id="date-samedi">{{ $debutSemaine->addDays(5)->format('d/m') }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($emploiDuTemps as $horaire => $creneaux)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $horaire }}</div>
                                        </td>
                                        @foreach(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'] as $jour)
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($creneaux[$jour])
                                                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                                                        <div class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                                            {{ $creneaux[$jour]['matiere'] }}
                                                        </div>
                                                        <div class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                                            <div>{{ $creneaux[$jour]['enseignant'] }}</div>
                                                            <div class="mt-1">
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                    {{ $creneaux[$jour]['type'] }}
                                                                </span>
                                                            </div>
                                                            <div class="mt-1 text-xs opacity-75">
                                                                {{ $creneaux[$jour]['lieu'] }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-400 dark:text-gray-500 italic">
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
            </div>

            <!-- Résumé de la semaine -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Résumé de la Semaine</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-purple-900 dark:text-purple-100">Total de cours</p>
                                    <p class="text-lg font-semibold text-purple-900 dark:text-purple-100">
                                        @php
                                            $totalCours = 0;
                                            foreach($emploiDuTemps as $creneaux) {
                                                foreach(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'] as $jour) {
                                                    if($creneaux[$jour]) $totalCours++;
                                                }
                                            }
                                            echo $totalCours;
                                        @endphp
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-900 dark:text-green-100">Matières</p>
                                    <p class="text-lg font-semibold text-green-900 dark:text-green-100">
                                        @php
                                            $matieres = [];
                                            foreach($emploiDuTemps as $creneaux) {
                                                foreach(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'] as $jour) {
                                                    if($creneaux[$jour] && !in_array($creneaux[$jour]['matiere'], $matieres)) {
                                                        $matieres[] = $creneaux[$jour]['matiere'];
                                                    }
                                                }
                                            }
                                            echo count($matieres);
                                        @endphp
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Heures de cours</p>
                                    <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">
                                        {{ $totalCours * 2 }}h
                                    </p>
                                </div>
                            </div>
                        </div>
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

        // Fonction pour naviguer vers la semaine précédente
        function previousWeek() {
            console.log('previousWeek() appelée');
            try {
                const currentUrl = new URL(window.location.href);
                let currentDate;

                if (currentUrl.searchParams.get('week')) {
                    currentDate = new Date(currentUrl.searchParams.get('week'));
                } else {
                    currentDate = new Date();
                }

                currentDate.setDate(currentDate.getDate() - 7);
                const newWeek = currentDate.toISOString().split('T')[0];
                currentUrl.searchParams.set('week', newWeek);

                console.log('Navigation vers:', currentUrl.toString());
                window.location.href = currentUrl.toString();
            } catch (error) {
                console.error('Erreur dans previousWeek:', error);
            }
        }

        // Fonction pour naviguer vers la semaine suivante
        function nextWeek() {
            console.log('nextWeek() appelée');
            try {
                const currentUrl = new URL(window.location.href);
                let currentDate;

                if (currentUrl.searchParams.get('week')) {
                    currentDate = new Date(currentUrl.searchParams.get('week'));
                } else {
                    currentDate = new Date();
                }

                currentDate.setDate(currentDate.getDate() + 7);
                const newWeek = currentDate.toISOString().split('T')[0];
                currentUrl.searchParams.set('week', newWeek);

                console.log('Navigation vers:', currentUrl.toString());
                window.location.href = currentUrl.toString();
            } catch (error) {
                console.error('Erreur dans nextWeek:', error);
            }
        }

        // Fonction pour revenir à la semaine actuelle
        function currentWeek() {
            console.log('currentWeek() appelée');
            try {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.delete('week');

                console.log('Navigation vers:', currentUrl.toString());
                window.location.href = currentUrl.toString();
            } catch (error) {
                console.error('Erreur dans currentWeek:', error);
            }
        }

        // Vérifier que les fonctions sont bien définies
        console.log('Fonctions de navigation chargées:', {
            previousWeek: typeof previousWeek,
            nextWeek: typeof nextWeek,
            currentWeek: typeof currentWeek
        });

        // Fonction pour mettre à jour les dates dans l'en-tête du tableau
        function updateTableDates() {
            try {
                const currentUrl = new URL(window.location.href);
                let weekStart;

                if (currentUrl.searchParams.get('week')) {
                    weekStart = new Date(currentUrl.searchParams.get('week'));
                } else {
                    weekStart = new Date();
                }

                // Ajuster au lundi de la semaine
                const dayOfWeek = weekStart.getDay();
                const daysToMonday = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
                weekStart.setDate(weekStart.getDate() - daysToMonday);

                // Mettre à jour les dates dans l'en-tête
                const jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
                jours.forEach((jour, index) => {
                    const dateElement = document.getElementById(`date-${jour}`);
                    if (dateElement) {
                        const dateJour = new Date(weekStart);
                        dateJour.setDate(weekStart.getDate() + index);
                        dateElement.textContent = dateJour.toLocaleDateString('fr-FR', {
                            day: '2-digit',
                            month: '2-digit'
                        });
                    }
                });

                console.log('Dates du tableau mises à jour');
            } catch (error) {
                console.error('Erreur lors de la mise à jour des dates:', error);
            }
        }

        // Mettre à jour les dates au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            updateTableDates();
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

                // Construire l'URL d'export pour la semaine
                const currentUrl = new URL(window.location.href);
                const weekParam = currentUrl.searchParams.get('week');
                let exportUrl = `/etudiant/export-emploi-du-temps-semaine?format=${format}`;

                if (weekParam) {
                    exportUrl += `&week=${weekParam}`;
                }

                // Créer un lien temporaire pour le téléchargement
                const link = document.createElement('a');
                link.href = exportUrl;
                link.download = `emploi_du_temps_semaine_${format}.${format}`;
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
