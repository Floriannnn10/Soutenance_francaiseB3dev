<x-app-layout>
<div class="min-h-screen bg-gray-100">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- En-tête -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Modifier la classe</h2>
                            <p class="text-gray-600 mt-1">{{ $class->nom }}</p>
                        </div>
                        <a href="{{ route('classes.show', $class) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Retour aux détails
                        </a>
                    </div>

                    <!-- Formulaire -->
                    <form method="POST" action="{{ route('classes.update', $class) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Nom de la classe -->
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom de la classe <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="nom"
                                   id="nom"
                                   value="{{ old('nom', $class->nom) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('nom') border-red-500 @enderror"
                                   placeholder="Ex: 6ème A, Terminale S, BTS Informatique..."
                                   required>
                            @error('nom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Niveau -->
                        <div>
                            <label for="niveau" class="block text-sm font-medium text-gray-700 mb-2">
                                Niveau
                            </label>
                            <select name="niveau"
                                    id="niveau"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('niveau') border-red-500 @enderror">
                                <option value="">Sélectionnez un niveau</option>
                                <option value="Prépa 1" {{ old('niveau', $class->niveau) == 'Prépa 1' ? 'selected' : '' }}>Prépa 1</option>
                                <option value="Prépa 2" {{ old('niveau', $class->niveau) == 'Prépa 2' ? 'selected' : '' }}>Prépa 2</option>
                                <option value="Bachelor 2 création digitale" {{ old('niveau', $class->niveau) == 'Bachelor 2 création digitale' ? 'selected' : '' }}>Bachelor 2 création digitale</option>
                                <option value="Bachelor 2 communication digitale" {{ old('niveau', $class->niveau) == 'Bachelor 2 communication digitale' ? 'selected' : '' }}>Bachelor 2 communication digitale</option>
                                <option value="Bachelor 2 Développement" {{ old('niveau', $class->niveau) == 'Bachelor 2 Développement' ? 'selected' : '' }}>Bachelor 2 Développement</option>
                                <option value="Bachelor 3 création digitale" {{ old('niveau', $class->niveau) == 'Bachelor 3 création digitale' ? 'selected' : '' }}>Bachelor 3 création digitale</option>
                                <option value="Bachelor 3 communication digitale" {{ old('niveau', $class->niveau) == 'Bachelor 3 communication digitale' ? 'selected' : '' }}>Bachelor 3 communication digitale</option>
                                <option value="Bachelor 3 Développement" {{ old('niveau', $class->niveau) == 'Bachelor 3 Développement' ? 'selected' : '' }}>Bachelor 3 Développement</option>
                                <option value="Master 1 Intélligence artificielle" {{ old('niveau', $class->niveau) == 'Master 1 Intélligence artificielle' ? 'selected' : '' }}>Master 1 Intélligence artificielle</option>
                                <option value="Master 2 Data Science" {{ old('niveau', $class->niveau) == 'Master 2 Data Science' ? 'selected' : '' }}>Master 2 Data Science</option>
                            </select>
                            @error('niveau')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Informations sur les données liées -->
                        @if($class->etudiants->count() > 0 || $class->sessionsDeCours->count() > 0)
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
                                        <p>Cette classe contient des données liées :</p>
                                        <ul class="list-disc list-inside mt-1">
                                            @if($class->etudiants->count() > 0)
                                                <li>{{ $class->etudiants->count() }} étudiant(s)</li>
                                            @endif
                                            @if($class->sessionsDeCours->count() > 0)
                                                <li>{{ $class->sessionsDeCours->count() }} session(s) de cours</li>
                                            @endif
                                        </ul>
                                        <p class="mt-2">La modification de cette classe peut affecter ces données.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Boutons d'action -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('classes.show', $class) }}"
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
