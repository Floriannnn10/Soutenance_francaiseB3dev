<x-app-layout>
    <div class="max-w-4xl mx-auto py-10">
        <div class="bg-white rounded-lg shadow p-8">
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

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Détails de l'enseignant</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('enseignants.edit', $enseignant) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md shadow transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13h3l8-8a2.828 2.828 0 10-4-4l-8 8v3z" />
                        </svg>
                        Modifier
                    </a>
                    <a href="{{ route('enseignants.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-md shadow transition">
                        Retour
                    </a>
                </div>
            </div>

            <!-- Informations de base -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Nom :</span>
                            <p class="text-gray-900">{{ $enseignant->nom }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Prénom :</span>
                            <p class="text-gray-900">{{ $enseignant->prenom }}</p>
                        </div>
                        {{-- Utilisateur associé supprimé --}}
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Matières enseignées :</span>
                            <p class="text-gray-900">{{ $enseignant->matieres->count() }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Sessions de cours :</span>
                            <p class="text-gray-900">{{ $enseignant->sessionsDeCours->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Matières enseignées -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Matières enseignées</h3>
                @if($enseignant->matieres->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($enseignant->matieres as $matiere)
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h4 class="font-medium text-gray-900">{{ $matiere->nom }}</h4>
                                <p class="text-sm text-gray-500">Code: {{ $matiere->code }}</p>
                                <p class="text-sm text-gray-500">Coefficient: {{ $matiere->coefficient }}</p>
                                <p class="text-sm text-gray-500">Volume horaire: {{ $matiere->volume_horaire }}h</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucune matière assignée</p>
                @endif
            </div>

            <!-- Sessions de cours récentes -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sessions de cours récentes</h3>
                @if($enseignant->sessionsDeCours->count())
                    <div class="space-y-3">
                        @foreach($enseignant->sessionsDeCours->take(5) as $session)
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $session->matiere->nom }}</h4>
                                        <p class="text-sm text-gray-500">{{ $session->classe->nom }}</p>
                                        <p class="text-sm text-gray-500">{{ $session->start_time->format('d/m/Y H:i') }} - {{ $session->end_time->format('H:i') }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $session->status->nom ?? 'Non défini' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucune session de cours</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 