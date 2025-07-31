<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sessions de Cours') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('sessions-de-cours.historique') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md flex items-center">
                    <i class="fas fa-history mr-2"></i>
                    Historique
                </a>
                <a href="{{ route('sessions-de-cours.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Nouvelle Session
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Messages d'erreur et de succès -->
                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Filtres -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Filtrer les sessions</h3>
                        <form method="GET" action="{{ Auth::user()->roles->first()->code === 'enseignant' ? route('enseignant.sessions-de-cours.index') : route('sessions-de-cours.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label for="annee_academique_id" class="block text-sm font-medium text-gray-700 mb-1">Année académique</label>
                                <select name="annee_academique_id" id="annee_academique_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Toutes les années</option>
                                    @foreach($anneesAcademiques as $annee)
                                        <option value="{{ $annee->id }}" {{ request('annee_academique_id') == $annee->id ? 'selected' : '' }}>
                                            {{ $annee->nom }} - {{ $annee->statut }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="semestre_id" class="block text-sm font-medium text-gray-700 mb-1">Semestre</label>
                                <select name="semestre_id" id="semestre_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Tous les semestres</option>
                                    @foreach($semestres as $semestre)
                                        <option value="{{ $semestre->id }}" {{ request('semestre_id') == $semestre->id ? 'selected' : '' }}>
                                            {{ $semestre->nom }} ({{ $semestre->anneeAcademique->nom ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                                <select name="classe_id" id="classe_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Toutes les classes</option>
                                    @foreach($classes as $classe)
                                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                            {{ $classe->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="matiere_id" class="block text-sm font-medium text-gray-700 mb-1">Matière</label>
                                <select name="matiere_id" id="matiere_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Toutes les matières</option>
                                    @foreach($matieres as $matiere)
                                        <option value="{{ $matiere->id }}" {{ request('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                            {{ $matiere->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end">
                                <div class="flex space-x-2">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition">
                                        <i class="fas fa-search mr-2"></i>Filtrer
                                    </button>
                                    <a href="{{ Auth::user()->roles->first()->code === 'enseignant' ? route('enseignant.sessions-de-cours.index') : route('sessions-de-cours.index') }}" class="bg-gray-500 hover:bg-[#FD0800] text-white font-medium py-2 px-4 rounded-md transition">
                                        <i class="fas fa-times mr-2"></i>Réinitialiser
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Onglets pour séparer sessions récentes et futures -->
                    <div class="mb-6">
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8">
                                <button onclick="showTab('recentes')" id="tab-recentes" class="tab-button active py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                                    ⏰ Sessions Récentes
                                </button>
                                <button onclick="showTab('futures')" id="tab-futures" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    📅 Sessions Futures
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- Contenu des onglets -->
                    <div id="content-recentes" class="tab-content">
                        <h3 class="text-lg font-semibold mb-4">Sessions Récentes (Passées)</h3>
                        <!-- Tableau des sessions récentes -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date/Heure
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Matière
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Classe
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Enseignant
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type de cours
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Lieu
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($sessions as $session)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $session->matiere_nom }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $session->classe_nom }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $session->enseignant_prenom }} {{ $session->enseignant_nom }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $session->type_cours_nom }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $session->location ?: 'Non défini' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($session->statut_nom === 'Terminée') bg-green-100 text-green-800
                                                @elseif($session->statut_nom === 'En cours') bg-blue-100 text-blue-800
                                                @elseif($session->statut_nom === 'Programmée') bg-yellow-100 text-yellow-800
                                                @elseif($session->statut_nom === 'Annulée') bg-red-100 text-red-800
                                                @elseif($session->statut_nom === 'Reportée') bg-purple-100 text-purple-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $session->statut_nom }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-col space-y-1">
                                                <a href="{{ Auth::user()->roles->first()->code === 'enseignant' ? route('enseignant.sessions-de-cours.show', $session->id) : route('sessions-de-cours.show', $session->id) }}"
                                                   class="text-blue-600 hover:text-blue-900 flex items-center" title="Voir">
                                                    <i class="fas fa-eye mr-2"></i>Voir
                                                </a>
                                                @php
                                                    $type = strtolower(str_replace(['é', 'è', 'ê', 'ë'], 'e', $session->type_cours_nom ?? ''));
                                                    $typeCode = strtolower($session->type_cours_code ?? '');
                                                    $user = auth()->user();
                                                    $isCoordinateur = $user && $user->roles->first()->code === 'coordinateur';
                                                    $isEnseignant = $user && $user->roles->first()->code === 'enseignant';

                                                    // Calculer le statut de l'année
                                                    $anneeStatut = 'En cours';
                                                    if ($session->annee_date_debut && $session->annee_date_fin) {
                                                        $now = now();
                                                        $dateDebut = \Carbon\Carbon::parse($session->annee_date_debut);
                                                        $dateFin = \Carbon\Carbon::parse($session->annee_date_fin);

                                                        if ($now->lt($dateDebut)) {
                                                            $anneeStatut = 'À venir';
                                                        } elseif ($now->gt($dateFin)) {
                                                            $anneeStatut = 'Terminée';
                                                        }
                                                    }

                                                    $peutModifier = true;
                                                    if ($isCoordinateur && $anneeStatut === 'Terminée') {
                                                        $peutModifier = false;
                                                    }
                                                @endphp
                                                @if(($isCoordinateur && ($type === 'workshop' || $typeCode === 'workshop' || $type === 'e-learning' || $typeCode === 'e_learning' || $type === 'elearning')) || ($isEnseignant && ($type === 'presentiel' || $typeCode === 'presentiel')))
                                                    @if($peutModifier)
                                                        <a href="{{ $isEnseignant ? route('enseignant.sessions-de-cours.appel', $session->id) : route('sessions-de-cours.appel', $session->id) }}"
                                                           class="text-green-600 hover:text-green-900 flex items-center" title="Faire l'Appel">
                                                            <i class="fas fa-clipboard-check mr-2"></i>Faire l'Appel
                                                        </a>
                                                    @endif
                                                @endif
                                                @if($peutModifier)
                                                    @if(!($isCoordinateur && ($type === 'presentiel' || $typeCode === 'presentiel')) && !($isEnseignant && ($type === 'presentiel' || $typeCode === 'presentiel')))
                                                        <a href="{{ $isEnseignant ? route('enseignant.sessions-de-cours.edit', $session->id) : route('sessions-de-cours.edit', $session->id) }}"
                                                           class="text-orange-600 hover:text-orange-900 flex items-center" title="Éditer">
                                                            <i class="fas fa-edit mr-2"></i>Éditer
                                                        </a>
                                                        <form action="{{ $isEnseignant ? route('enseignant.sessions-de-cours.destroy', $session->id) : route('sessions-de-cours.destroy', $session->id) }}"
                                                              method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="text-red-600 hover:text-red-900 flex items-center" title="Supprimer"
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette session ?')">
                                                                <i class="fas fa-times mr-2"></i>Supprimer
                                                            </button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 text-xs">Lecture seule</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Aucune session de cours trouvée.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($sessions) && method_exists($sessions, 'links'))
                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Affichage de <span class="font-medium">{{ $sessions->firstItem() ?? 0 }}</span>
                                à <span class="font-medium">{{ $sessions->lastItem() ?? 0 }}</span>
                                sur <span class="font-medium">{{ $sessions->total() }}</span> résultats
                            </div>
                            <div>
                                {{ $sessions->links() }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Contenu pour les sessions futures -->
                <div id="content-futures" class="tab-content hidden">
                    <h3 class="text-lg font-semibold mb-4">Sessions Futures (À venir)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Heure</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($sessionsFutures ?? [] as $session)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $session->matiere_nom }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $session->classe_nom }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $session->type_cours_nom }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($session->statut_nom === 'Programmée') bg-green-100 text-green-800
                                                @elseif($session->statut_nom === 'Planifiée') bg-yellow-100 text-yellow-800
                                                @elseif($session->statut_nom === 'Annulée') bg-red-100 text-red-800
                                                @elseif($session->statut_nom === 'Reportée') bg-purple-100 text-purple-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $session->statut_nom }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-col space-y-1">
                                                <a href="{{ Auth::user()->roles->first()->code === 'enseignant' ? route('enseignant.sessions-de-cours.show', $session->id) : route('sessions-de-cours.show', $session->id) }}"
                                                   class="text-blue-600 hover:text-blue-900 flex items-center" title="Voir">
                                                    <i class="fas fa-eye mr-2"></i>Voir
                                                </a>
                                                @if(\Carbon\Carbon::parse($session->start_time) > now())
                                                    <span class="text-yellow-600 text-xs flex items-center">
                                                        <i class="fas fa-clock mr-1"></i>Pas encore commencé
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Aucune session future trouvée.
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
        }
    </script>
</x-app-layout>
