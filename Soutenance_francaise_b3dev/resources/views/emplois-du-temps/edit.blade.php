@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Modifier une session de cours</h1>
        <a href="{{ route('emplois-du-temps.index') }}" class="text-gray-600 hover:text-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('emplois-du-temps.update', $session) }}" method="POST" id="edit-session-form">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Classe *
                    </label>
                    <select name="classe_id" id="classe_id" class="form-select w-full rounded-md border-gray-300" required>
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ $session->classe_id == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('classe_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="matiere_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Matière *
                    </label>
                    <select name="matiere_id" id="matiere_id" class="form-select w-full rounded-md border-gray-300" required>
                        <option value="">Sélectionner une matière</option>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}" {{ $session->matiere_id == $matiere->id ? 'selected' : '' }}>
                                {{ $matiere->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('matiere_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="enseignant_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Enseignant *
                    </label>
                    <select name="enseignant_id" id="enseignant_id" class="form-select w-full rounded-md border-gray-300" required>
                        <option value="">Sélectionner un enseignant</option>
                        @foreach($enseignants as $enseignant)
                            <option value="{{ $enseignant->id }}" {{ $session->enseignant_id == $enseignant->id ? 'selected' : '' }}>
                                {{ $enseignant->nom }} {{ $enseignant->prenom }}
                            </option>
                        @endforeach
                    </select>
                    @error('enseignant_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type_cours_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Type de cours *
                    </label>
                    <select name="type_cours_id" id="type_cours_id" class="form-select w-full rounded-md border-gray-300" required>
                        <option value="">Sélectionner un type</option>
                        @foreach($typesCours as $type)
                            <option value="{{ $type->id }}" {{ $session->type_cours_id == $type->id ? 'selected' : '' }}>
                                {{ $type->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_cours_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jour" class="block text-sm font-medium text-gray-700 mb-1">
                        Jour *
                    </label>
                    <select name="jour" id="jour" class="form-select w-full rounded-md border-gray-300" required>
                        <option value="">Sélectionner un jour</option>
                        @foreach(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'] as $jour)
                            <option value="{{ $jour }}" {{ $session->jour == $jour ? 'selected' : '' }}>
                                {{ ucfirst($jour) }}
                            </option>
                        @endforeach
                    </select>
                    @error('jour')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="heure_debut" class="block text-sm font-medium text-gray-700 mb-1">
                            Heure début *
                        </label>
                        <input type="time" name="heure_debut" id="heure_debut"
                               class="form-input w-full rounded-md border-gray-300"
                               value="{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}"
                               required>
                        @error('heure_debut')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="heure_fin" class="block text-sm font-medium text-gray-700 mb-1">
                            Heure fin *
                        </label>
                        <input type="time" name="heure_fin" id="heure_fin"
                               class="form-input w-full rounded-md border-gray-300"
                               value="{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}"
                               required>
                        @error('heure_fin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" onclick="history.back()"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Annuler
                </button>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('edit-session-form');
    const heureDebut = document.getElementById('heure_debut');
    const heureFin = document.getElementById('heure_fin');

    function validateHours() {
        if (heureDebut.value && heureFin.value) {
            if (heureDebut.value >= heureFin.value) {
                heureFin.setCustomValidity('L\'heure de fin doit être après l\'heure de début');
            } else {
                heureFin.setCustomValidity('');
            }
        }
    }

    heureDebut.addEventListener('change', validateHours);
    heureFin.addEventListener('change', validateHours);

    form.addEventListener('submit', function(e) {
        validateHours();
        if (!form.checkValidity()) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush
