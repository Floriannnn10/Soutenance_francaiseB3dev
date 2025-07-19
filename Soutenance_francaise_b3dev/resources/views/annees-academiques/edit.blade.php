<x-app-layout>
   <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier l\'Année Académique') }}: {{ $anneeAcademique->nom }}
            </h2>
            <a href="{{ route('annees-academiques.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('annees-academiques.update', $anneeAcademique) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Nom de l'année académique -->
                        <div>
                            <x-input-label for="nom" :value="__('Nom de l\'année académique')" />
                            <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" 
                                          :value="old('nom', $anneeAcademique->nom)" required autofocus 
                                          placeholder="Ex: 2024-2025" />
                            <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                        </div>

                        <!-- Date de début -->
                        <div>
                            <x-input-label for="date_debut" :value="__('Date de début')" />
                            <x-text-input id="date_debut" name="date_debut" type="date" class="mt-1 block w-full" 
                                          :value="old('date_debut', $anneeAcademique->date_debut?->format('Y-m-d'))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('date_debut')" />
                        </div>

                        <!-- Date de fin -->
                        <div>
                            <x-input-label for="date_fin" :value="__('Date de fin')" />
                            <x-text-input id="date_fin" name="date_fin" type="date" class="mt-1 block w-full" 
                                          :value="old('date_fin', $anneeAcademique->date_fin?->format('Y-m-d'))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('date_fin')" />
                        </div>

                        <!-- Actif -->
                        <div class="flex items-center">
                            <input id="actif" name="actif" type="checkbox" value="1" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old('actif', $anneeAcademique->actif) ? 'checked' : '' }}>
                            <label for="actif" class="ml-2 block text-sm text-gray-900">
                                Activer cette année académique
                            </label>
                        </div>

                        <!-- Boutons -->
                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <a href="{{ route('annees-academiques.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <x-primary-button>
                                {{ __('Mettre à jour') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 