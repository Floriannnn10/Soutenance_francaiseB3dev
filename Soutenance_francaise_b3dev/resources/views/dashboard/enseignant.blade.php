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
                    @foreach($emploiDuTemps ?? [] as $creneau)
                    <tr>
                        <td class="py-2 px-4 border">{{ $creneau['horaire'] }}</td>
                        @foreach(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'] as $jour)
                            <td class="py-2 px-4 border">
                                @if(isset($creneau[$jour]) && $creneau[$jour]['type'] === 'presentiel')
                                    <div class="p-2 rounded bg-blue-100">
                                        <p class="font-semibold">{{ $creneau[$jour]['matiere'] }}</p>
                                        <p class="text-sm">{{ $creneau[$jour]['classe'] }}</p>
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

    <!-- Liste des cours en présentiel -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Mes cours en présentiel</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($cours ?? [] as $cours)
                @if($cours->typeCours->code === 'presentiel')
                <div class="border rounded-lg p-4">
                    <h4 class="font-semibold">{{ $cours->matiere->nom }}</h4>
                    <p class="text-sm text-gray-600">{{ $cours->classe->nom }}</p>
                    <p class="text-sm text-blue-600">Type: {{ $cours->typeCours->nom }}</p>
                    <div class="mt-2">
                        <a href="{{ route('presences.index', ['cours_id' => $cours->id]) }}"
                           class="text-blue-600 hover:text-blue-800">
                            Voir les présences
                        </a>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection

