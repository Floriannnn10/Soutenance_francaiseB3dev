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
                            <h2 class="text-2xl font-bold text-gray-900">Détails de la matière</h2>
                            <p class="text-gray-600 mt-1">{{ $matiere->nom }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('matieres.edit', $matiere) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Modifier
                            </a>
                            <a href="{{ route('matieres.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Retour
                            </a>
                        </div>
                    </div>

                    <!-- Informations de la matière -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nom de la matière</dt>
                                    <dd class="text-sm text-gray-900">{{ $matiere->nom }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Code</dt>
                                    <dd class="text-sm text-gray-900">{{ $matiere->code }}</dd>
                                </div>
                                @if($matiere->description)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="text-sm text-gray-900">{{ $matiere->description }}</dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Coefficient</dt>
                                    <dd class="text-sm text-gray-900">{{ $matiere->coefficient }}</dd>
                                </div>
                                @if($matiere->volume_horaire)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Volume horaire</dt>
                                    <dd class="text-sm text-gray-900">{{ $matiere->volume_horaire }} heures</dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                                    <dd class="text-sm text-gray-900">{{ $matiere->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nombre de sessions de cours</dt>
                                    <dd class="text-2xl font-bold text-indigo-600">{{ $matiere->sessionsDeCours->count() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Classes concernées</dt>
                                    <dd class="text-2xl font-bold text-green-600">{{ $matiere->sessionsDeCours->unique('classe_id')->count() }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Liste des sessions de cours -->
                    @if($matiere->sessionsDeCours->count() > 0)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sessions de cours</h3>
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignant</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($matiere->sessionsDeCours as $session)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->classe->nom ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->enseignant->user->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->date ? $session->date->format('d/m/Y') : 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->heure_debut ?? 'N/A' }} - {{ $session->heure_fin ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($session->statut)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ $session->statut->nom }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        Non défini
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
