<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord - Étudiant') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informations de l'étudiant -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-16 w-16">
                            @if($etudiant->photo)
                                <img class="h-16 w-16 rounded-full" src="{{ asset('storage/' . $etudiant->photo) }}" alt="">
                                        @else
                                <div class="h-16 w-16 rounded-full bg-blue-200 flex items-center justify-center">
                                    <span class="text-xl font-semibold text-blue-800">
                                        {{ strtoupper(substr($etudiant->prenom, 0, 1) . substr($etudiant->nom, 0, 1)) }}
                                    </span>
                                    </div>
                                @endif
        </div>
                        <div class="ml-6">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $etudiant->prenom }} {{ $etudiant->nom }}</h3>
                            <p class="text-gray-600">{{ $etudiant->classe->nom ?? 'Classe non définie' }}</p>
                            <p class="text-sm text-gray-500">{{ $etudiant->email ?? 'Email non renseigné' }}</p>
    </div>
                    </div>
                </div>
            </div>

            <!-- Emploi du temps de la semaine identique à /etudiant/emploi-semaine -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">📅 Emploi du temps de la semaine</h3>
                        <span class="text-sm text-gray-600">
                            Semaine du {{ $debutSemaine->format('d/m/Y') }} au {{ $finSemaine->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th>Jour</th>
                                    <th>Horaire</th>
                                    <th>Matière</th>
                                    <th>Enseignant</th>
                                    <th>Type</th>
                                    <th>Lieu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['lundi','mardi','mercredi','jeudi','vendredi','samedi'] as $jour)
                                    @php
                                        $jourSessions = collect($emploiDuTemps)->map(function($creneau) use($jour) {
                                            return $creneau[$jour] ? $creneau[$jour] : null;
                                        })->filter();
                                    @endphp
                                    @if($jourSessions->isEmpty())
                                        <tr>
                                            <td>{{ ucfirst($jour) }}</td>
                                            <td colspan="5"><span class="italic text-gray-400">Libre</span></td>
                                        </tr>
                                    @else
                                        @foreach($jourSessions as $session)
                                            <tr>
                                                <td>
                                                    {{ ucfirst($jour) }}<br>
                                                    <span class="text-xs text-gray-500">{{ $session['date'] ?? '' }}</span>
                                                </td>
                                                <td>{{ $session['heure_debut'] ?? '' }} - {{ $session['heure_fin'] ?? '' }}</td>
                                                <td class="font-semibold">{{ $session['matiere'] ?? '' }}</td>
                                                <td>{{ $session['enseignant'] ?? '' }}</td>
                                                <td>
                                                    <span class="inline-block px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $session['type'] ?? '' }}</span>
                                                </td>
                                                <td>{{ $session['lieu'] ?? 'Non spécifié' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Résumé de la semaine -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-900">Total cours</p>
                            <p class="text-lg font-semibold text-blue-900">{{ $statistiques['total_cours'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-900">Matières</p>
                            <p class="text-lg font-semibold text-green-900">{{ $statistiques['matieres_uniques'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-purple-900">Heures</p>
                            <p class="text-lg font-semibold text-purple-900">{{ $statistiques['heures_total'] }}h</p>
                        </div>
                    </div>
                </div>

                <div class="bg-orange-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-orange-900">Jours avec cours</p>
                            <p class="text-lg font-semibold text-orange-900">{{ $statistiques['jours_avec_cours'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prochain cours -->
            <!-- Cours aujourd'hui -->
        </div>
    </div>

    <script>
        // Fonction pour rafraîchir l'emploi du temps
        function refreshEmploiTemps() {
            console.log('Rafraîchissement de l\'emploi du temps...');
            location.reload();
        }

        // Auto-refresh toutes les 5 minutes
        setInterval(function() {
            console.log('Auto-refresh de l\'emploi du temps...');
            // On peut ajouter ici une requête AJAX pour rafraîchir seulement l'emploi du temps
            // Pour l'instant, on fait un reload complet
            location.reload();
        }, 5 * 60 * 1000); // 5 minutes
    </script>
</x-app-layout>
