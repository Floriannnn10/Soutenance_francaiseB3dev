<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Créer une Session de Cours') }}
            </h2>
            <a href="{{ route('semestres.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages de succès/erreur -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('sessions-de-cours.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Semestre -->
                            <div>
                                <label for="semestre_id" class="block text-sm font-medium text-gray-700">
                                    Semestre <span class="text-red-500">*</span>
                                </label>
                                <select name="semestre_id" id="semestre_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sélectionner un semestre</option>
                                    @foreach($semestres as $semestre)
                                        <option value="{{ $semestre->id }}"
                                                {{ request('semestre_id') == $semestre->id ? 'selected' : '' }}>
                                            {{ $semestre->nom }} ({{ $semestre->anneeAcademique->nom ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('semestre_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Classe -->
                            <div>
                                <label for="classe_id" class="block text-sm font-medium text-gray-700">
                                    Classe <span class="text-red-500">*</span>
                                </label>
                                <select name="classe_id" id="classe_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sélectionner une classe</option>
                                    @foreach($classes as $classe)
                                        <option value="{{ $classe->id }}">
                                            {{ $classe->nom }} {{ $classe->niveau ? '(' . $classe->niveau . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('classe_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Matière -->
                            <div>
                                <label for="matiere_id" class="block text-sm font-medium text-gray-700">
                                    Matière <span class="text-red-500">*</span>
                                </label>
                                <select name="matiere_id" id="matiere_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sélectionner une matière</option>
                                    @foreach($matieres as $matiere)
                                        <option value="{{ $matiere->id }}">
                                            {{ $matiere->nom }}
                                            @if($matiere->coefficient)
                                                (Coef. {{ $matiere->coefficient }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('matiere_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Enseignant -->
                            <div>
                                <label for="enseignant_id" class="block text-sm font-medium text-gray-700">
                                    Enseignant <span class="text-red-500">*</span>
                                </label>
                                <select name="enseignant_id" id="enseignant_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sélectionner un enseignant</option>
                                    @foreach($enseignants as $enseignant)
                                        <option value="{{ $enseignant->id }}">
                                            {{ $enseignant->prenom }} {{ $enseignant->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('enseignant_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Type de Cours -->
                            <div>
                                <label for="type_cours_id" class="block text-sm font-medium text-gray-700">
                                    Type de Cours <span class="text-red-500">*</span>
                                </label>
                                <select name="type_cours_id" id="type_cours_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sélectionner un type</option>
                                    @foreach($typesCours as $type)
                                        <option value="{{ $type->id }}">
                                            {{ $type->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type_cours_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Statut -->
                            <div>
                                <label for="status_id" class="block text-sm font-medium text-gray-700">
                                    Statut <span class="text-red-500">*</span>
                                </label>
                                <select name="status_id" id="status_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sélectionner un statut</option>
                                    @foreach($statutsSession as $statut)
                                        <option value="{{ $statut->id }}"
                                                {{ $statut->nom === 'Programmée' ? 'selected' : '' }}>
                                            {{ $statut->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Heure de début -->
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700">
                                    Heure de Début <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="start_time" id="start_time" required
                                       value="{{ old('start_time') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Heure de fin -->
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700">
                                    Heure de Fin <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="end_time" id="end_time" required
                                       value="{{ old('end_time') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('end_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Lieu -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">
                                Lieu
                            </label>
                            <input type="text" name="location" id="location"
                                   value="{{ old('location') }}"
                                   placeholder="Salle de classe, amphithéâtre, laboratoire..."
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                Notes/Commentaires
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                      placeholder="Informations supplémentaires sur la session..."
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('semestres.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-save mr-2"></i>Créer la Session
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
