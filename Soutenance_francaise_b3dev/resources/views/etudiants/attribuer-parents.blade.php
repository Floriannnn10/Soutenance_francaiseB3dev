<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Attribuer des parents à') }} {{ $etudiant->nom }} {{ $etudiant->prenom }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Informations de l'étudiant</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p><strong>Nom :</strong> {{ $etudiant->nom }} {{ $etudiant->prenom }}</p>
                            <p><strong>Email :</strong> {{ $etudiant->email }}</p>
                            <p><strong>Classe :</strong> {{ $etudiant->classe->nom ?? 'Non assigné' }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('etudiants.store-parents', $etudiant) }}" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Sélectionner les parents
                            </label>

                            @if($parents->count() > 0)
                                <div class="space-y-3">
                                    @foreach($parents as $parent)
                                        <label class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                                            <input type="checkbox"
                                                   name="parents[]"
                                                   value="{{ $parent->id }}"
                                                   {{ $etudiant->parents->contains($parent->id) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900">
                                                    {{ $parent->nom }} {{ $parent->prenom }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $parent->user->email ?? 'Pas d\'email' }}
                                                    @if($parent->telephone)
                                                        • {{ $parent->telephone }}
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-500 mb-4">Aucun parent disponible</p>
                                    <a href="{{ route('parents.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Créer un parent
                                    </a>
                                </div>
                            @endif
                        </div>

                        @error('parents')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('etudiants.show', $etudiant) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            @if($parents->count() > 0)
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-save mr-2"></i>Attribuer les parents
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
