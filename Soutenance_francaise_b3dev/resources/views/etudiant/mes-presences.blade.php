<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mes Présences') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header avec informations -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Mes Présences</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $etudiant->prenom }} {{ $etudiant->nom }} - {{ $etudiant->classe->nom ?? 'Classe non assignée' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <label for="annee_select" class="text-sm font-medium text-gray-700 dark:text-gray-300">Année :</label>
                            <select id="annee_select" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                @foreach($anneesAcademiques as $annee)
                                    <option value="{{ $annee->id }}" {{ $anneeActive && $anneeActive->id == $annee->id ? 'selected' : '' }} data-url="{{ route('etudiant.mes-presences') }}?annee_id={{ $annee->id }}">
                                        {{ $annee->nom }} @if($annee->actif) (Active) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques des présences -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Présences</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $statistiques['present'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Absences</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $statistiques['absent'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Justifiées</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $statistiques['justifie'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Retards</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $statistiques['retard'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Taux de présence -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Taux de Présence</h3>
                    <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                        @php
                            $tauxPresence = $statistiques['total'] > 0 ? round(($statistiques['present'] / $statistiques['total']) * 100, 1) : 0;
                            $couleur = $tauxPresence >= 80 ? 'bg-green-600' : ($tauxPresence >= 60 ? 'bg-yellow-600' : 'bg-red-600');
                        @endphp
                        <div class="h-4 rounded-full {{ $couleur }}" style="width: {{ $tauxPresence }}%"></div>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">0%</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $tauxPresence }}%</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">100%</span>
                    </div>
                </div>
            </div>

            <!-- Détails des présences -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Historique des Présences</h3>
                        <div class="flex space-x-2">
                            <button onclick="filterPresences('all')" class="px-3 py-1 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                                Toutes
                            </button>
                            <button onclick="filterPresences('present')" class="px-3 py-1 text-sm font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800">
                                Présences
                            </button>
                            <button onclick="filterPresences('absent')" class="px-3 py-1 text-sm font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800">
                                Absences
                            </button>
                            <button onclick="filterPresences('justifie')" class="px-3 py-1 text-sm font-medium text-yellow-700 bg-yellow-100 rounded-md hover:bg-yellow-200 dark:bg-yellow-900 dark:text-yellow-300 dark:hover:bg-yellow-800">
                                Justifiées
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Matière</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Enseignant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Heure</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($presences as $presence)
                                    <tr class="presence-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                        data-status="{{ $presence->statutPresence->code ?? '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $presence->sessionDeCours->start_time->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $presence->sessionDeCours->matiere->nom ?? 'Non défini' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ ($presence->sessionDeCours->enseignant->prenom ?? '') }} {{ ($presence->sessionDeCours->enseignant->nom ?? '') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $presence->sessionDeCours->typeCours->nom ?? 'Non défini' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'present' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                    'absent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                    'justifie' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                    'retard' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'
                                                ];
                                                $color = $statusColors[$presence->statutPresence->code ?? ''] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $color }}">
                                                {{ $presence->statutPresence->nom ?? 'Non défini' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $presence->sessionDeCours->start_time->format('H:i') }} - {{ $presence->sessionDeCours->end_time->format('H:i') }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aucune présence enregistrée</h3>
                                                <p class="text-gray-500 dark:text-gray-400">Vous n'avez pas encore de présences enregistrées pour cette année académique.</p>
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

        // Fonction pour filtrer les présences
        function filterPresences(status) {
            const rows = document.querySelectorAll('.presence-row');

            rows.forEach(row => {
                const rowStatus = row.dataset.status;

                if (status === 'all' || rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</x-app-layout>
