<x-app-layout>
<div class="min-h-screen bg-gray-100">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- En-tête -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Détails de la classe</h2>
                            <p class="text-gray-600 mt-1">{{ $class->nom }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('classes.edit', $class) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Modifier
                            </a>
                            <a href="{{ route('classes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Retour
                            </a>
                        </div>
                    </div>

                    <!-- Informations de la classe -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nom de la classe</dt>
                                    <dd class="text-sm text-gray-900">{{ $class->nom }}</dd>
                                </div>
                                @if($class->specialite)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Spécialité</dt>
                                    <dd class="text-sm text-gray-900">{{ $class->specialite }}</dd>
                                </div>
                                @endif
                                @if($class->description)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="text-sm text-gray-900">{{ $class->description }}</dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                                    <dd class="text-sm text-gray-900">{{ $class->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nombre d'étudiants</dt>
                                    <dd class="text-2xl font-bold text-indigo-600">{{ $class->etudiants->count() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nombre de sessions de cours</dt>
                                    <dd class="text-2xl font-bold text-green-600">{{ $class->sessionsDeCours->count() }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Liste des étudiants -->
                    @if($class->etudiants->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Étudiants de cette classe</h3>
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prénom</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($class->etudiants as $etudiant)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($etudiant->photo)
                                                    <img src="{{ asset('storage/' . $etudiant->photo) }}" alt="Photo" class="h-10 w-10 rounded-full object-cover">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-gray-600 text-sm font-medium">
                                                            {{ substr($etudiant->nom ?? 'ET', 0, 2) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $etudiant->nom ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $etudiant->prenom ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $etudiant->email ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="#" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Liste des sessions de cours -->
                    @if($class->sessionsDeCours->count() > 0)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sessions de cours</h3>
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignant</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($class->sessionsDeCours as $session)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->matiere->nom ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->enseignant->user->nom }} {{ $session->enseignant->user->prenom ?? 'N/A' }}</td>
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
