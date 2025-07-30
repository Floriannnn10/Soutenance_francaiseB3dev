@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">


    <h2 class="text-2xl font-bold mb-6">Tableau de bord Enseignant</h2>

    <!-- Emploi du temps pour la semaine actuelle -->
    @if(isset($emploiDuTemps) && !empty($emploiDuTemps))
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold mb-4 text-blue-600">Mon emploi du temps (Cours en présentiel uniquement)</h3>
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
                        @foreach($emploiDuTemps as $horaire => $creneau)
                        <tr>
                            <td class="py-2 px-4 border font-medium">{{ $horaire }}</td>
                            @foreach(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'] as $jour)
                                <td class="py-2 px-4 border">
                                    @if(isset($creneau[$jour]) && $creneau[$jour])
                                        <div class="p-3 rounded bg-blue-50 border border-blue-200">
                                            <div class="mb-2">
                                                <p class="font-semibold text-blue-800 text-sm">{{ $creneau[$jour]['matiere'] }}</p>
                                                <p class="text-xs text-gray-600">{{ $creneau[$jour]['classe'] }}</p>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <p>{{ $creneau[$jour]['heure_debut'] }} - {{ $creneau[$jour]['heure_fin'] }}</p>
                                                <p>Salle: {{ $creneau[$jour]['salle'] }}</p>
                                                <p>{{ $creneau[$jour]['date'] }}</p>
                                            </div>
                                            <div class="mt-2 space-y-1">
                                                <a href="{{ route('enseignant.sessions-de-cours.show', $creneau[$jour]['session_id']) }}"
                                                   class="text-xs text-blue-600 hover:text-blue-800 block">
                                                    Voir détails
                                                </a>
                                                <a href="{{ route('enseignant.sessions-de-cours.appel', $creneau[$jour]['session_id']) }}"
                                                   class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 block text-center">
                                                    Faire l'appel
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-3 rounded bg-gray-50 border border-gray-200">
                                            <p class="text-xs text-gray-400 text-center">Libre</p>
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
    @else
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold mb-4">Mon emploi du temps (Cours en présentiel uniquement)</h3>
            <div class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Aucun cours programmé</h3>
                <p class="text-gray-500">Vous n'avez pas de cours programmés pour cette semaine.</p>
            </div>
        </div>
    @endif

    <!-- Liste des sessions récentes -->
    @if(isset($sessions) && $sessions->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Sessions récentes</h3>
            <div class="space-y-4">
                @foreach($sessions as $session)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold">{{ $session->matiere->nom }}</h4>
                                <p class="text-sm text-gray-600">{{ $session->classe->nom }} - {{ $session->typeCours->nom }}</p>
                                <p class="text-sm text-gray-500">{{ $session->start_time->format('d/m/Y H:i') }} - {{ $session->end_time->format('H:i') }}</p>
                            </div>
                            <div class="flex flex-col space-y-2">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($session->statutSession->nom === 'Programmée') bg-green-100 text-green-800
                                    @elseif($session->statutSession->nom === 'Planifiée') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $session->statutSession->nom }}
                                </span>
                                @if($session->typeCours->nom === 'Présentiel')
                                    <a href="{{ route('enseignant.sessions-de-cours.appel', $session->id) }}"
                                       class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 text-center">
                                        Faire l'appel
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

