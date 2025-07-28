<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détail du parent') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('parents.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
                        </a>
                        <a href="{{ route('parents.edit', $parent) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <div class="text-center">
                                @if($parent->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($parent->photo))
                                    <img src="{{ asset('storage/' . $parent->photo) }}" alt="Photo" class="h-32 w-32 rounded-full object-cover mx-auto mb-4">
                                @else
                                    <div class="h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 mx-auto mb-4">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                @endif
                                <h3 class="text-lg font-semibold text-gray-900">{{ $parent->nom }} {{ $parent->prenom }}</h3>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Informations générales</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p><strong>Nom :</strong> {{ $parent->nom }}</p>
                                    <p><strong>Prénom :</strong> {{ $parent->prenom }}</p>
                                    <p><strong>Email :</strong> {{ $parent->user->email ?? '-' }}</p>
                                </div>
                                <div>
                                    <p><strong>Téléphone :</strong> {{ $parent->telephone ?? '-' }}</p>
                                    <p><strong>Profession :</strong> {{ $parent->profession ?? '-' }}</p>
                                    <p><strong>Adresse :</strong> {{ $parent->adresse ?? '-' }}</p>
                                </div>
                            </div>

                            <h3 class="text-lg font-semibold mb-4 mt-8">Étudiants</h3>
                            @if($parent->etudiants->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($parent->etudiants as $etudiant)
                                        <div class="border rounded-lg p-4 bg-gray-50">
                                            <div class="font-medium text-gray-900">
                                                {{ $etudiant->nom }} {{ $etudiant->prenom }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <p><strong>Email :</strong> {{ $etudiant->email ?? '-' }}</p>
                                                <p><strong>Classe :</strong> {{ $etudiant->classe->nom ?? 'Non assigné' }}</p>
                                                <p><strong>Date de naissance :</strong> {{ $etudiant->date_naissance ? \Carbon\Carbon::parse($etudiant->date_naissance)->format('d/m/Y') : '-' }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">Aucun étudiant attribué à ce parent.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
