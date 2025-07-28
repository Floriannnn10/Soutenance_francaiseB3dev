<x-app-layout>
<div class="min-h-screen bg-gray-100">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- En-tête -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Détails de l'utilisateur</h2>
                            <p class="text-gray-600 mt-1">Informations détaillées de l'utilisateur</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Modifier
                            </a>
                            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Retour
                            </a>
                        </div>
                    </div>

                    <!-- Informations utilisateur -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Photo de profil -->
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200 mb-4">
                                @if($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo de profil" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8c0 2.208-1.79 4-3.998 4s-3.998-1.792-3.998-4 1.79-4 3.998-4 3.998 1.792 3.998 4z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $user->prenom }} {{ $user->nom }}</h3>
                            <p class="text-sm text-gray-600">{{ $user->roles->first()->nom ?? 'Rôle non défini' }}</p>
                        </div>

                        <!-- Détails -->
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Informations personnelles</h4>
                                <div class="mt-2 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">Nom complet :</span>
                                        <span class="text-sm text-gray-900">{{ $user->prenom }} {{ $user->nom }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">Email :</span>
                                        <span class="text-sm text-gray-900">{{ $user->email }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">Rôle :</span>
                                        <span class="text-sm text-gray-900">{{ $user->roles->first()->nom ?? 'Non défini' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">Créé le :</span>
                                        <span class="text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Informations spécifiques selon le rôle -->
                            @if($user->etudiant)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Informations étudiant</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-700">Classe :</span>
                                            <span class="text-sm text-gray-900">{{ $user->etudiant->classe->nom ?? 'Non assigné' }}</span>
                                        </div>
                                        @if($user->etudiant->date_naissance)
                                            <div class="flex justify-between">
                                                <span class="text-sm font-medium text-gray-700">Date de naissance :</span>
                                                <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($user->etudiant->date_naissance)->format('d/m/Y') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($user->enseignant)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Informations enseignant</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-700">Statut :</span>
                                            <span class="text-sm text-gray-900">Enseignant actif</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($user->parent)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Informations parent</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-700">Téléphone :</span>
                                            <span class="text-sm text-gray-900">{{ $user->parent->telephone ?? 'Non renseigné' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($user->coordinateur)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Informations coordinateur</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-700">Statut :</span>
                                            <span class="text-sm text-gray-900">Coordinateur actif</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-3">
                                <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Modifier l'utilisateur
                                </a>
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
