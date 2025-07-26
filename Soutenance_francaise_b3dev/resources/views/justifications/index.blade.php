@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestion des Justifications d'Absence</h1>
        <div class="text-sm text-gray-600">
            Année: {{ $anneeActive?->nom ?? 'Aucune année active' }} | 
            Semestre: {{ $semestreActif?->nom ?? 'Aucun semestre actif' }}
        </div>
    </div>

    @if(!$anneeActive || !$semestreActif)
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            <strong>Attention :</strong> Aucune année académique ou semestre n'est actuellement actif.
        </div>
    @else
        @if($absences->count() > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($absences as $absence)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $absence->etudiant->nom }} {{ $absence->etudiant->prenom }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $absence->etudiant->classe->nom ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $absence->sessionDeCours->matiere->nom }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $absence->sessionDeCours->enseignant->nom }} {{ $absence->sessionDeCours->enseignant->prenom }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($absence->enregistre_le)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($absence->justification)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Justifiée
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Non justifiée
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($absence->justification)
                                    <a href="{{ route('justifications.show', $absence->justification->id) }}" 
                                       class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                                    <a href="{{ route('justifications.edit', $absence->justification->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>
                                    <form action="{{ route('justifications.destroy', $absence->justification->id) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette justification ?')">
                                            Supprimer
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('justifications.create', $absence->id) }}" 
                                       class="text-green-600 hover:text-green-900">Justifier</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
                <strong>Information :</strong> Aucune absence enregistrée pour cette période.
            </div>
        @endif
    @endif
</div>
@endsection 