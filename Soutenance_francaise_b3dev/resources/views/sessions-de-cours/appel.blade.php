@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-4">Faire l'appel</h2>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Informations de la session</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Date :</p>
                        <p class="font-medium">{{ Carbon\Carbon::parse($session->start_time)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Horaire :</p>
                        <p class="font-medium">
                            {{ Carbon\Carbon::parse($session->start_time)->format('H:i') }} -
                            {{ Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Classe :</p>
                        <p class="font-medium">{{ $session->classe->nom }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Type de cours :</p>
                        <p class="font-medium">{{ $session->typeCours->nom }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('presences.store') }}" method="POST">
                @csrf
                <input type="hidden" name="session_id" value="{{ $session->id }}">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($session->classe->etudiants as $etudiant)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $etudiant->nom }} {{ $etudiant->prenom }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select name="presences[{{ $etudiant->id }}][statut_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            @foreach($statutsPresence as $statut)
                                                <option value="{{ $statut->id }}" {{ $etudiant->presences->where('course_session_id', $session->id)->first()?->statut_presence_id === $statut->id ? 'selected' : '' }}>
                                                    {{ $statut->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="presences[{{ $etudiant->id }}][etudiant_id]" value="{{ $etudiant->id }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Enregistrer les présences
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
