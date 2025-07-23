@extends('layouts.app')@section('content')
<div class="p-8">
    <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 mb-4 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded transition">
        ← Retour
    </a>
    <h1 class="text-2xl font-bold mb-4">Liste des présences</h1>
    <div class="bg-white rounded-lg shadow p-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Session</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($presences as $presence)
                    <tr>
                        <td class="px-4 py-2">{{ $presence->etudiant->prenom ?? '' }} {{ $presence->etudiant->nom ?? '' }}</td>
                        <td class="px-4 py-2">{{ $presence->sessionDeCours->classe->nom ?? '' }}</td>
                        <td class="px-4 py-2">{{ $presence->sessionDeCours->matiere->nom ?? '' }}</td>
                        <td class="px-4 py-2">{{ $presence->sessionDeCours->id ?? '' }}</td>
                        <td class="px-4 py-2"> <span class="px-2 py-1 rounded text-xs font-semibold @if ($presence->statutPresence->nom === 'Présent') bg-green-100 text-green-800 @elseif($presence->statutPresence->nom === 'Absent') bg-red-100 text-red-800 @elseif($presence->statutPresence->nom === 'Retard') bg-orange-100 text-orange-800 @else bg-gray-100 text-gray-800 @endif"> {{ $presence->statutPresence->nom ?? '' }} </span> </td>
                        <td class="px-4 py-2">{{ $presence->enregistre_le ? $presence->enregistre_le->format('d/m/Y H:i') : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4"> {{ $presences->links() }} </div>
    </div>
</div>
@endsection
