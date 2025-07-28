<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Emploi du Temps - Enseignant') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header avec sélecteur d'année -->
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
                                <p class="text-sm text-gray-600 dark:text-gray-400">Gérez vos sessions de cours en présentiel</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <label for="annee_select" class="text-sm font-medium text-gray-700 dark:text-gray-300">Année :</label>
                            <select id="annee_select" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                @foreach($anneesAcademiques as $annee)
                                    <option value="{{ $annee->id }}" {{ $anneeActive && $anneeActive->id == $annee->id ? 'selected' : '' }} data-url="{{ route('emplois-du-temps.enseignant') }}?annee_id={{ $annee->id }}">
                                        {{ $annee->nom }} @if($annee->actif) (Active) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Prise de Présence</h3>
                            <p class="text-blue-100">Cours en présentiel</p>
                        </div>
                        <div class="p-3 bg-blue-400 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <button onclick="openPresenceModal()" class="mt-4 w-full bg-blue-400 hover:bg-blue-300 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Faire l'appel
                    </button>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Mes Sessions</h3>
                            <p class="text-green-100">{{ $sessions->count() }} session(s)</p>
                        </div>
                        <div class="p-3 bg-green-400 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm">
                        <p>Prochain cours : {{ $sessions->where('start_time', '>', now())->first()?->start_time?->format('d/m/Y H:i') ?? 'Aucun' }}</p>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Statistiques</h3>
                            <p class="text-purple-100">Taux de présence</p>
                        </div>
                        <div class="p-3 bg-purple-400 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm">
                        <p>Moyenne : {{ round($sessions->avg('taux_presence') ?? 0, 1) }}%</p>
                    </div>
                </div>
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
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Matière</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Classe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Heure</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($sessions as $session)
                                    <tr class="session-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                                        data-classe="{{ $session->classe_id }}"
                                        data-matiere="{{ $session->matiere_id }}"
                                        data-type="{{ $session->type_cours_id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                            {{ substr($session->matiere->nom, 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $session->matiere->nom }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $session->typeCours->nom }}
                                                    </div>
                                                </div>
                                            </div>
                            </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $session->classe->nom }}</div>
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
                                                $statusColors = [
                                                    'Planifié' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                    'En cours' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                    'Terminé' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                    'Annulé' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                                ];
                                                $color = $statusColors[$session->statutSession->nom] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $color }}">
                                                {{ $session->statutSession->nom }}
                                            </span>
                            </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                @if($session->typeCours->nom === 'Présentiel')
                                                    <button onclick="openPresenceModal({{ $session->id }})"
                                                            class="p-2 text-green-600 hover:text-green-900 hover:bg-green-100 dark:hover:bg-green-900 rounded-lg transition-colors duration-200"
                                                            title="Faire l'appel">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                                <button onclick="viewSession({{ $session->id }})"
                                                        class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-100 dark:hover:bg-blue-900 rounded-lg transition-colors duration-200"
                                                        title="Voir les détails">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
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
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aucune session trouvée</h3>
                                                <p class="text-gray-500 dark:text-gray-400">Vous n'avez pas encore de sessions de cours en présentiel.</p>
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

    <!-- Modal de prise de présence -->
    <div id="presenceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Prise de Présence - Cours en Présentiel</h3>
                    <button onclick="closePresenceModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div id="presenceContent">
                    <!-- Le contenu sera chargé dynamiquement -->
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

        // Modal de prise de présence
        function openPresenceModal(sessionId = null) {
            if (sessionId) {
                // Charger directement les étudiants pour une session spécifique
                loadSessionPresence(sessionId);
            } else {
                // Charger la liste des sessions en présentiel
                loadPresentielSessions();
            }
            document.getElementById('presenceModal').classList.remove('hidden');
        }

        function closePresenceModal() {
            document.getElementById('presenceModal').classList.add('hidden');
        }

        function loadSessionPresence(sessionId) {
            fetch(`/enseignant/session/${sessionId}/etudiants`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const session = data.session;
                        const etudiants = data.etudiants;
                        const statuts = data.statuts_presence;
                        const presences = data.presences;

                        let html = `
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
                                <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">Informations de la session</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-blue-700 dark:text-blue-300">Matière :</span>
                                        <span class="text-blue-900 dark:text-blue-100">${session.matiere.nom}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-blue-700 dark:text-blue-300">Classe :</span>
                                        <span class="text-blue-900 dark:text-blue-100">${session.classe.nom}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-blue-700 dark:text-blue-300">Type :</span>
                                        <span class="text-blue-900 dark:text-blue-100">${session.type_cours.nom}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-blue-700 dark:text-blue-300">Date :</span>
                                        <span class="text-blue-900 dark:text-blue-100">${new Date(session.start_time).toLocaleDateString('fr-FR')}</span>
                                    </div>
                                </div>
                            </div>

                            <form id="presenceForm" class="space-y-4">
                                <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">Liste des étudiants de la classe</h4>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                            <thead class="bg-gray-50 dark:bg-gray-600">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Étudiant</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                        `;

                        etudiants.forEach(etudiant => {
                            const presence = presences.find(p => p.etudiant_id === etudiant.id);
                            const selectedStatus = presence ? presence.statut_presence_id : '';

                            html += `
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        ${etudiant.prenom.charAt(0)}${etudiant.nom.charAt(0)}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    ${etudiant.prenom} ${etudiant.nom}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <select name="presences[${etudiant.id}]" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100">
                                            <option value="">Non défini</option>
                            `;

                            statuts.forEach(statut => {
                                const selected = selectedStatus == statut.id ? 'selected' : '';
                                html += `<option value="${statut.id}" ${selected}>${statut.nom}</option>`;
                            });

                            html += `
                                        </select>
                                    </td>
                                </tr>
                            `;
                        });

                        html += `
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-600 border-t border-gray-200 dark:border-gray-500">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            ${etudiants.length} étudiant(s) dans cette classe
                                        </p>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="closePresenceModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                                        Annuler
                                    </button>
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                        Enregistrer les présences
                                    </button>
                                </div>
                            </form>
                        `;

                        document.getElementById('presenceContent').innerHTML = html;

                        // Gérer la soumission du formulaire de présence
                        document.getElementById('presenceForm').addEventListener('submit', function(e) {
                            e.preventDefault();

                            const formData = new FormData(this);

                            fetch(`/enseignant/session/${sessionId}/presence`, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    closePresenceModal();
                                    alert('Présences enregistrées avec succès');
                                } else {
                                    alert('Erreur: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Une erreur est survenue');
                            });
                        });
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue');
                });
        }

        function loadPresentielSessions() {
            const anneeId = document.getElementById('annee_select')?.value;
            const url = `/enseignant/sessions-presentiel${anneeId ? '?annee_id=' + anneeId : ''}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const sessions = data.sessions;

                        if (sessions.length === 0) {
                            document.getElementById('presenceContent').innerHTML = `
                                <div class="text-center py-8">
                                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full inline-block mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aucune session en présentiel</h3>
                                    <p class="text-gray-500 dark:text-gray-400">Vous n'avez pas de sessions en présentiel pour cette période.</p>
                                </div>
                            `;
                            return;
                        }

                        let html = `
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Sélectionner une session en présentiel</h4>
                                <div class="space-y-3">
                        `;

                        sessions.forEach(session => {
                            const date = new Date(session.start_time);
                            html += `
                                <button onclick="loadSessionPresence(${session.id})" class="w-full text-left p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-gray-100">${session.matiere.nom}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">${session.classe.nom} - ${date.toLocaleDateString('fr-FR')} ${date.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}</div>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </button>
                            `;
                        });

                        html += `
                                </div>
                                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    ${sessions.length} session(s) en présentiel trouvée(s)
                                </div>
                            </div>
                        `;

                        document.getElementById('presenceContent').innerHTML = html;
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue');
                });
        }

        function viewSession(sessionId) {
            // Implémenter la vue détaillée d'une session
            alert('Fonctionnalité à implémenter');
        }
    </script>
</x-app-layout>
