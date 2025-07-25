<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sessions de Cours') }}
            </h2>
            <a href="{{ route('sessions-de-cours.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle Session
            </a>
        </div>
    </x-slot>

    <div class="py-6">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filtres -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="filter_semestre" class="block text-sm font-medium text-gray-700">Semestre</label>
                            <select id="filter_semestre" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Tous les semestres</option>
                                <!-- Options dynamiques via JavaScript -->
                            </select>
                        </div>
                        <div>
                            <label for="filter_classe" class="block text-sm font-medium text-gray-700">Classe</label>
                            <select id="filter_classe" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Toutes les classes</option>
                                <!-- Options dynamiques via JavaScript -->
                            </select>
                        </div>
                        <div>
                            <label for="filter_matiere" class="block text-sm font-medium text-gray-700">Matière</label>
                            <select id="filter_matiere" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Toutes les matières</option>
                                <!-- Options dynamiques via JavaScript -->
                            </select>
                        </div>
                        <div>
                            <label for="filter_statut" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select id="filter_statut" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Tous les statuts</option>
                                <!-- Options dynamiques via JavaScript -->
                            </select>
                        </div>
                    </div>

                    <!-- Tableau des sessions -->
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
                                                <a href="{{ route('sessions-de-cours.show', $session->id) }}"
                                                   class="text-blue-600 hover:text-blue-900 flex items-center" title="Voir">
                                                    <i class="fas fa-eye mr-2"></i>Voir
                                                </a>
                                                @php
                                                    $type = strtolower(str_replace(['é', 'è', 'ê', 'ë'], 'e', $session->type_cours_nom ?? ''));
                                                @endphp
                                                @if($type === 'workshop' || $type === 'e-learning' || $type === 'elearning')
                                                    <a href="{{ route('sessions-de-cours.appel', $session->id) }}"
                                                       class="text-green-600 hover:text-green-900 flex items-center" title="Faire l'Appel">
                                                        <i class="fas fa-clipboard-check mr-2"></i>Faire l'Appel
                                                    </a>
                                                @endif
                                                <a href="{{ route('sessions-de-cours.edit', $session->id) }}"
                                                   class="text-orange-600 hover:text-orange-900 flex items-center" title="Éditer">
                                                    <i class="fas fa-edit mr-2"></i>Éditer
                                                </a>
                                                <form action="{{ route('sessions-de-cours.destroy', $session->id) }}"
                                                      method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900 flex items-center" title="Supprimer"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette session ?')">
                                                        <i class="fas fa-times mr-2"></i>Supprimer
                                                    </button>
                                                </form>
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
            </div>
        </div>
    </div>
</x-app-layout>
