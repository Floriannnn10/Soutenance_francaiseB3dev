@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-6">Tableau de bord Enseignant</h2>

    <!-- Emploi du temps -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4">Mon emploi du temps (Cours en présentiel uniquement)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 border">Horaire</th>
                        <th class="py-2 px-4 border">Lundi</th>
                        <th class="py-2 px-4 border">Mardi</th>
                        <th class="py-2 px-4 border">Mercredi</th>
                        <th class="py-2 px-4 border">Jeudi</th>
                        <th class="py-2 px-4 border">Vendredi</th>
                        <th class="py-2 px-4 border">Samedi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($emploiDuTemps ?? [] as $horaire => $creneau)
                    <tr>
                        <td class="py-2 px-4 border">{{ $horaire }}</td>
                        @foreach(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'] as $jour)
                            <td class="py-2 px-4 border">
                                @if(isset($creneau[$jour]) && $creneau[$jour]['type'] === 'presentiel')
                                    <div class="p-2 rounded bg-blue-100">
                                        <p class="font-semibold">{{ $creneau[$jour]['matiere'] }}</p>
                                        <p class="text-sm">{{ $creneau[$jour]['classe'] }}</p>
                                        @if(isset($creneau[$jour]['date']))
                                            <p class="text-xs text-gray-600">{{ $creneau[$jour]['date'] }}</p>
                                        @endif
                                        <p class="text-xs text-gray-600">{{ $creneau[$jour]['type'] }}</p>
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

    <!-- Liste des sessions -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Mes sessions de cours</h3>
        @if(isset($sessions) && $sessions->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($sessions as $session)
                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold text-lg">{{ $session->matiere->nom }}</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($session->statutSession->nom === 'Planifiée') bg-blue-100 text-blue-800
                            @elseif($session->statutSession->nom === 'En cours') bg-yellow-100 text-yellow-800
                            @elseif($session->statutSession->nom === 'Terminée') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $session->statutSession->nom }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $session->classe->nom }}</p>
                    <p class="text-sm text-blue-600 mb-2">Type: {{ $session->typeCours->nom }}</p>
                    <p class="text-sm text-gray-500 mb-3">
                        {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i') }} -
                        {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                    </p>
                    <div class="flex space-x-2">
                        @if($session->typeCours->nom === 'Présentiel')
                            <a href="{{ route('enseignant.sessions-de-cours.show', $session->id) }}"
                               class="text-blue-600 hover:text-blue-800 text-sm">
                                Voir détails
                            </a>
                            <a href="{{ route('enseignant.sessions-de-cours.appel', $session->id) }}"
                               class="text-green-600 hover:text-green-800 text-sm">
                                Faire l'appel
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Aucune session trouvée</h3>
                <p class="text-gray-500">Vous n'avez pas encore de sessions de cours assignées.</p>
            </div>
        @endif
    </div>
</div>
@endsection

