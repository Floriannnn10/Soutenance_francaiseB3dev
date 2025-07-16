<x-app-layout>
<div class="min-h-screen bg-gray-100">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Messages de succès et d'erreur -->
                    @if(session('success'))
                        <div class="mb-6 p-4 rounded bg-green-100 text-green-800 border border-green-200 flex items-center animate-fade-in">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 rounded bg-red-100 text-red-800 border border-red-200 flex items-center animate-fade-in">
                            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- En-tête -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Modifier la matière</h2>
                            <p class="text-gray-600 mt-1">{{ $matiere->nom }}</p>
                        </div>
                        <a href="{{ route('matieres.show', $matiere) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Détails matière
                        </a>
                    </div>

                    <!-- Formulaire -->
                    <form method="POST" action="{{ route('matieres.update', $matiere) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Nom de la matière -->
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom de la matière <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="nom"
                                   id="nom"
                                   value="{{ old('nom', $matiere->nom) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('nom') border-red-500 @enderror"
                                   placeholder="Ex: Mathématiques, Français, Histoire..."
                                   required>
                            @error('nom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Code -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="code"
                                   id="code"
                                   value="{{ old('code', $matiere->code) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('code') border-red-500 @enderror"
                                   placeholder="Ex: MAT, FRA, HIS..."
                                   required>
                            <p class="mt-1 text-sm text-gray-500">Code court unique pour identifier la matière</p>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Coefficient -->
                        <div>
                            <label for="coefficient" class="block text-sm font-medium text-gray-700 mb-2">
                                Coefficient <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="coefficient"
                                   id="coefficient"
                                   value="{{ old('coefficient', $matiere->coefficient) }}"
                                   step="0.1"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('coefficient') border-red-500 @enderror"
                                   placeholder="Ex: 2.0, 1.5..."
                                   required>
                            <p class="mt-1 text-sm text-gray-500">Pondération pour le calcul des moyennes</p>
                            @error('coefficient')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Volume horaire -->
                        <div>
                            <label for="volume_horaire" class="block text-sm font-medium text-gray-700 mb-2">
                                Volume horaire <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="volume_horaire"
                                   id="volume_horaire"
                                   value="{{ old('volume_horaire', $matiere->volume_horaire) }}"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('volume_horaire') border-red-500 @enderror"
                                   placeholder="Ex: 4, 6, 8..."
                                   required>
                            <p class="mt-1 text-sm text-gray-500">Nombre d'heures par semaine</p>
                            @error('volume_horaire')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Enseignants -->
                        <div>
                            <label for="enseignants" class="block text-sm font-medium text-gray-700 mb-2">Enseignants</label>
                            <select name="enseignants[]" id="enseignants" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($enseignants as $enseignant)
                                    <option value="{{ $enseignant->id }}" {{ in_array($enseignant->id, old('enseignants', $matiere->enseignants->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $enseignant->prenom }} {{ $enseignant->nom }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélectionner plusieurs enseignants.</p>
                        </div>

                        <!-- Informations sur les données liées -->
                        @if($matiere->sessionsDeCours->count() > 0)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Attention</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Cette matière contient {{ $matiere->sessionsDeCours->count() }} session(s) de cours.</p>
                                        <p class="mt-2">La modification de cette matière peut affecter ces sessions.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Boutons d'action -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('matieres.show', $matiere) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Annuler
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
