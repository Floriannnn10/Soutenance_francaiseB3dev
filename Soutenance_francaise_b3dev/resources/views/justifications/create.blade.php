@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Justifier une Absence</h1>
            <a href="{{ route('justifications.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Retour à la liste
            </a>
        </div>

        <!-- Informations sur l'absence -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h2 class="text-lg font-semibold mb-3">Détails de l'absence</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Étudiant</label>
                    <p class="text-sm text-gray-900">{{ $presence->etudiant->nom }} {{ $presence->etudiant->prenom }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Classe</label>
                    <p class="text-sm text-gray-900">{{ $presence->etudiant->classe->nom ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Matière</label>
                    <p class="text-sm text-gray-900">{{ $presence->sessionDeCours->matiere->nom }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Enseignant</label>
                    <p class="text-sm text-gray-900">{{ $presence->sessionDeCours->enseignant->nom }} {{ $presence->sessionDeCours->enseignant->prenom }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date de l'absence</label>
                    <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($presence->enregistre_le)->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Délai limite</label>
                    <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($presence->enregistre_le)->addDays(14)->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Formulaire de justification -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('justifications.store', $presence->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="motif" class="block text-sm font-medium text-gray-700 mb-2">
                        Motif de l'absence *
                    </label>
                    <textarea name="motif" id="motif" rows="4"
                              class="form-textarea w-full @error('motif') border-red-500 @enderror"
                              placeholder="Décrivez le motif de l'absence..."
                              required>{{ old('motif') }}</textarea>
                    @error('motif')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="date_justification" class="block text-sm font-medium text-gray-700 mb-2">
                        Date de justification *
                    </label>
                    <input type="date" name="date_justification" id="date_justification"
                           value="{{ old('date_justification', date('Y-m-d')) }}"
                           class="form-input w-full @error('date_justification') border-red-500 @enderror"
                           required>
                    @error('date_justification')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">
                        La justification doit être faite dans un délai de 2 semaines maximum après l'absence.
                    </p>
                </div>

                <div class="mb-6">
                    <label for="piece_jointe" class="block text-sm font-medium text-gray-700 mb-2">
                        Pièce jointe (optionnel)
                    </label>
                    <input type="file" name="piece_jointe" id="piece_jointe"
                           class="form-input w-full @error('piece_jointe') border-red-500 @enderror"
                           accept=".pdf,.jpg,.jpeg,.png">
                    @error('piece_jointe')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">
                        Formats acceptés : PDF, JPG, JPEG, PNG (max 2MB)
                    </p>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('justifications.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Annuler
                    </a>
                    <button type="submit"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Justifier l'absence
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
