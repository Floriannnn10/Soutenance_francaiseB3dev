@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Emploi du temps de l'enfant</h1>
        <div class="text-sm text-gray-600">
            Année : {{ $anneeActive?->nom ?? 'Aucune année active' }} |
            Semestre : {{ $semestreActif?->nom ?? 'Aucun semestre actif' }}
        </div>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Enfant :</span>
        {{ $etudiant->nom }} {{ $etudiant->prenom }}<br>
        <span class="font-semibold">Classe :</span> {{ $etudiant->classe->nom ?? '-' }}
    </div>

    @if(Auth::user()->role && Auth::user()->role->nom === 'coordinateur')
        <div class="mb-6">
            <a href="{{ route('emplois-du-temps.create') }}" class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded">
                Modifier l'emploi du temps
            </a>
        </div>
    @endif

    @if($sessions->count() > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sessions as $session)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($session->start_time)->locale('fr')->isoFormat('dddd') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $session->matiere->nom ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $session->enseignant->nom ?? '-' }} {{ $session->enseignant->prenom ?? '' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $session->typeCours->nom ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mt-4">
            Aucun cours planifié pour cette période.
        </div>
    @endif
</div>
@endsection 