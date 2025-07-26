<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des Semestres') }}
            </h2>
            {{-- <a href="{{ route('semestres.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>Nouveau Semestre
            </a> --}}
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
                <!-- En-tête avec bouton d'ajout et sélecteur per_page -->
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Liste des Semestres</h3>
                        <p class="mt-1 text-sm text-gray-600">Gérez les semestres de votre établissement</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Sélecteur du nombre d'éléments par page -->
                        <div class="flex items-center">
                            <label for="per_page" class="text-sm text-gray-700 mr-2">Afficher :</label>
                            <select id="per_page" name="per_page"
                                    onchange="window.location.href = updateUrlParameter(window.location.href, 'per_page', this.value)"
                                    class="border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                        <a href="{{ route('semestres.create') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Nouveau Semestre
                        </a>
                    </div>
                </div>
                <div class="p-6 text-gray-900">
                    <!-- Tableau des semestres -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nom
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Année Académique
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date de Début
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date de Fin
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        État
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($semestres as $semestre)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $semestre->nom }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $semestre->anneeAcademique->nom ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $semestre->date_debut->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $semestre->date_fin->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($semestre->statut === 'En cours') bg-green-100 text-green-800
                                                @elseif($semestre->statut === 'À venir') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $semestre->statut }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($semestre->actif) bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $semestre->actif ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-col space-y-1">
                                                <a href="{{ route('semestres.show', $semestre) }}"
                                                   class="text-blue-600 hover:text-blue-900 flex items-center" title="Voir">
                                                    <i class="fas fa-eye mr-2"></i>Voir
                                                </a>
                                                <a href="{{ route('semestres.edit', $semestre) }}"
                                                   class="text-orange-600 hover:text-orange-900 flex items-center" title="Éditer">
                                                    <i class="fas fa-edit mr-2"></i>Éditer
                                                </a>
                                                @if($semestre->actif)
                                                    <form action="{{ route('semestres.deactivate', $semestre) }}"
                                                          method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="text-orange-600 hover:text-orange-900 flex items-center" title="Désactiver"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir désactiver ce semestre ?')">
                                                            <i class="fas fa-pause mr-2"></i>Désactiver
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('semestres.activate', $semestre) }}"
                                                          method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="text-green-600 hover:text-green-900 flex items-center" title="Activer"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir activer ce semestre ?')">
                                                            <i class="fas fa-play mr-2"></i>Activer
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('semestres.destroy', $semestre) }}"
                                                      method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900 flex items-center" title="Supprimer"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce semestre ?')">
                                                        <i class="fas fa-times mr-2"></i>Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Aucun semestre trouvé.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Informations de pagination et liens -->
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Affichage de <span class="font-medium">{{ $semestres->firstItem() ?? 0 }}</span>
                            à <span class="font-medium">{{ $semestres->lastItem() ?? 0 }}</span>
                            sur <span class="font-medium">{{ $semestres->total() }}</span> résultats
                        </div>
                        <div>
                            {{ $semestres->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
function updateUrlParameter(url, param, paramVal) {
    let newAdditionalURL = "";
    let tempArray = url.split("?");
    let baseURL = tempArray[0];
    let additionalURL = tempArray[1];
    let temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (let i = 0; i < tempArray.length; i++) {
            if (tempArray[i].split('=')[0] != param) {
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }
    let rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
}
</script>
