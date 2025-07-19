<x-app-layout>
   <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Créer un Nouveau Semestre') }}
            </h2>
            <a href="{{ route('semestres.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('semestres.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Nom du semestre -->
                        <div>
                            <x-input-label for="nom" :value="__('Nom du semestre')" />
                            <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" 
                                          :value="old('nom')" required autofocus 
                                          placeholder="Ex: Semestre 1" />
                            <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                        </div>

                        <!-- Année académique -->
                        <div>
                            <x-input-label for="annee_academique_id" :value="__('Année académique')" />
                            <select id="annee_academique_id" name="annee_academique_id" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Sélectionner une année académique</option>
                                @foreach($anneesAcademiques as $annee)
                                    <option value="{{ $annee->id }}" {{ old('annee_academique_id') == $annee->id ? 'selected' : '' }}>
                                        {{ $annee->nom }} ({{ $annee->date_debut->format('Y') }} - {{ $annee->date_fin->format('Y') }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('annee_academique_id')" />
                        </div>

                        <!-- Date de début -->
                        <div>
                            <x-input-label for="date_debut" :value="__('Date de début')" />
                            <x-text-input id="date_debut" name="date_debut" type="date" class="mt-1 block w-full" 
                                          :value="old('date_debut')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('date_debut')" />
                        </div>

                        <!-- Date de fin -->
                        <div>
                            <x-input-label for="date_fin" :value="__('Date de fin')" />
                            <x-text-input id="date_fin" name="date_fin" type="date" class="mt-1 block w-full" 
                                          :value="old('date_fin')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('date_fin')" />
                        </div>

                        <!-- Actif -->
                        <div class="flex items-center">
                            <input id="actif" name="actif" type="checkbox" value="1" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old('actif') ? 'checked' : '' }}>
                            <label for="actif" class="ml-2 block text-sm text-gray-900">
                                Activer ce semestre
                            </label>
                        </div>

                        <!-- Boutons -->
                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <a href="{{ route('semestres.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <x-primary-button>
                                {{ __('Créer') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 