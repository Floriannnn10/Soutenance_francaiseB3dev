@extends('layouts.app')

@section('content')
<div class="p-8">
    <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 mb-4 bg-gray-200 hover:bg-[#FD0800] hover:text-white text-gray-800 text-sm font-medium rounded transition">
        ← Retour
    </a>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Liste des présences</h1>
        <div class="text-sm text-gray-600">
            {{ $presences->total() }} présences trouvées
        </div>
    </div>

    <!-- Filtre par classe -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Filtrer par classe</h2>
        <form method="GET" action="{{ route('presences.index') }}" class="flex items-end space-x-4">
            <div class="flex-1 max-w-md">
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

            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-[#FD0800] text-white font-medium py-2 px-4 rounded-md transition">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
                <a href="{{ route('presences.index') }}" class="bg-gray-500 hover:bg-[#FD0800] text-white font-medium py-2 px-4 rounded-md transition">
                    <i class="fas fa-times mr-2"></i>Réinitialiser
                </a>
            </div>
        </form>
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
