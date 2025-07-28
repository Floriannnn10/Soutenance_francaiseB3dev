@extends('layouts.app')

@section('content')
<div class="p-8">
    <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 mb-4 bg-gray-200 hover:bg-[#FD0800] hover:text-white text-gray-800 text-sm font-medium rounded transition">
        ← Retour
    </a>

    <!-- Messages d'erreur -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Liste des présences</h1>
        <div class="text-sm text-gray-600">
            {{ $presences->total() }} présences trouvées
        </div>
    </div>

    <!-- Résumé des filtres appliqués -->
    @if(request('classe_id') || request('statut_id') || request('date_debut') || request('date_fin'))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="text-sm font-medium text-blue-800 mb-2">Filtres appliqués :</h3>
            <div class="flex flex-wrap gap-2">
                @if(request('classe_id'))
                    @php
                        $classeFiltree = $classes->firstWhere('id', request('classe_id'));
                    @endphp
                    @if($classeFiltree)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Classe: {{ $classeFiltree->nom }}
                        </span>
                    @endif
                @endif
                @if(request('statut_id'))
                    @php
                        $statutFiltre = $statutsPresence->firstWhere('id', request('statut_id'));
                    @endphp
                    @if($statutFiltre)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Statut: {{ $statutFiltre->nom }}
                        </span>
                    @endif
                @endif
                @if(request('date_debut'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        À partir du: {{ \Carbon\Carbon::parse(request('date_debut'))->format('d/m/Y') }}
                    </span>
                @endif
                @if(request('date_fin'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Jusqu'au: {{ \Carbon\Carbon::parse(request('date_fin'))->format('d/m/Y') }}
                    </span>
                @endif
            </div>
        </div>
    @endif

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Filtrer les présences</h2>
        <form method="GET" action="{{ route('presences.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                <label for="statut_id" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut_id" id="statut_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les statuts</option>
                    @foreach($statutsPresence as $statut)
                        <option value="{{ $statut->id }}" {{ request('statut_id') == $statut->id ? 'selected' : '' }}>
                            {{ $statut->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                <input type="date" name="date_debut" id="date_debut"
                       value="{{ request('date_debut', now()->subDays(30)->format('Y-m-d')) }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                <input type="date" name="date_fin" id="date_fin"
                       value="{{ request('date_fin', now()->format('Y-m-d')) }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex items-end">
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-[#FD0800] text-white font-medium py-2 px-4 rounded-md transition">
                        <i class="fas fa-search mr-2"></i>Filtrer
                    </button>
                    <a href="{{ route('presences.index') }}" class="bg-gray-500 hover:bg-[#FD0800] text-white font-medium py-2 px-4 rounded-md transition">
                        <i class="fas fa-times mr-2"></i>Réinitialiser
                    </a>
                </div>
            </div>
        </form>

        <!-- Périodes rapides -->
        <div class="mt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Périodes rapides :</h4>
            <div class="flex space-x-2">
                <a href="{{ route('presences.index', array_merge(request()->query(), ['date_debut' => now()->startOfWeek()->format('Y-m-d'), 'date_fin' => now()->endOfWeek()->format('Y-m-d')])) }}"
                   class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded-full transition">
                    Cette semaine
                </a>
                <a href="{{ route('presences.index', array_merge(request()->query(), ['date_debut' => now()->startOfMonth()->format('Y-m-d'), 'date_fin' => now()->endOfMonth()->format('Y-m-d')])) }}"
                   class="text-xs bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded-full transition">
                    Ce mois
                </a>
                <a href="{{ route('presences.index', array_merge(request()->query(), ['date_debut' => now()->subDays(7)->format('Y-m-d'), 'date_fin' => now()->format('Y-m-d')])) }}"
                   class="text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-3 py-1 rounded-full transition">
                    Derniers 7 jours
                </a>
                <a href="{{ route('presences.index', array_merge(request()->query(), ['date_debut' => now()->subDays(30)->format('Y-m-d'), 'date_fin' => now()->format('Y-m-d')])) }}"
                   class="text-xs bg-purple-100 hover:bg-purple-200 text-purple-800 px-3 py-1 rounded-full transition">
                    Derniers 30 jours
                </a>
            </div>
        </div>
    </div>

    <!-- Tableau des présences -->
    <div class="bg-white rounded-lg shadow p-6">
        @if($presences->count() > 0)
            <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($presences as $presence)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $presence->etudiant->prenom ?? '' }} {{ $presence->etudiant->nom ?? '' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $presence->sessionDeCours->classe->nom ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $presence->sessionDeCours->matiere->nom ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($presence->sessionDeCours->start_time)->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                        @if ($presence->statutPresence->nom === 'Présent') bg-green-100 text-green-800
                                        @elseif($presence->statutPresence->nom === 'Absent') bg-red-100 text-red-800
                                        @elseif($presence->statutPresence->nom === 'Retard') bg-orange-100 text-orange-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $presence->statutPresence->nom ?? '' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        {{ $presence->enregistre_le ? $presence->enregistre_le->format('d/m/Y H:i') : '' }}
                                    </div>
                                </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $presences->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-500 text-lg mb-2">Aucune présence trouvée</div>
                <div class="text-gray-400 text-sm">Essayez de modifier le filtre par classe</div>
            </div>
        @endif
    </div>
</div>
@endsection
