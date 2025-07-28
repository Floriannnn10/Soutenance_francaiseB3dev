<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $session->matiere_nom ?? 'Session de Cours' }}
                </h2>
                <p class="text-sm text-gray-600">
                    {{ $session->classe_nom }} • {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="flex space-x-2">
                @php
                    $type = strtolower(str_replace(['é', 'è', 'ê', 'ë'], 'e', $session->type_cours_nom ?? ''));
                    $typeCode = strtolower($session->type_cours_code ?? '');
                    $user = auth()->user();
                    $isCoordinateur = $user && $user->roles->first()->code === 'coordinateur';
                    $isEnseignant = $user && $user->roles->first()->code === 'enseignant';
                @endphp
                @if(($isCoordinateur && ($type === 'workshop' || $typeCode === 'workshop' || $type === 'e-learning' || $typeCode === 'e_learning' || $type === 'elearning')) || ($isEnseignant && ($type === 'presentiel' || $typeCode === 'presentiel')))
                    <a href="{{ route('sessions-de-cours.appel', $session->id) }}"
                       class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md flex items-center">
                        <i class="fas fa-clipboard-check mr-2"></i>Faire l'Appel
                    </a>
                @endif
                <a href="{{ route('sessions-de-cours.edit', $session->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md flex items-center">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </a>
                <a href="{{ route('sessions-de-cours.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages de succès/erreur -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Détails de la session -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Détails de la Session</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Matière</dt>
                                    <dd class="text-lg text-gray-900">{{ $session->matiere_nom }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Classe</dt>
                                    <dd class="text-lg text-gray-900">{{ $session->classe_nom }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Enseignant</dt>
                                    <dd class="text-lg text-gray-900">{{ $session->enseignant_prenom }} {{ $session->enseignant_nom }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Type de cours</dt>
                                    <dd class="text-lg text-gray-900">{{ $session->type_cours_nom }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Semestre</dt>
                                    <dd class="text-lg text-gray-900">{{ $session->semestre_nom }} ({{ $session->annee_nom }})</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date et Horaires</dt>
                                    <dd class="text-lg text-gray-900">
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y') }}<br>
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Lieu</dt>
                                    <dd class="text-lg text-gray-900">{{ $session->location ?: 'Non défini' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                    <dd>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($session->statut_nom === 'Terminée') bg-green-100 text-green-800
                                            @elseif($session->statut_nom === 'En cours') bg-blue-100 text-blue-800
                                            @elseif($session->statut_nom === 'Programmée') bg-yellow-100 text-yellow-800
                                            @elseif($session->statut_nom === 'Annulée') bg-red-100 text-red-800
                                            @elseif($session->statut_nom === 'Reportée') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $session->statut_nom }}
                                        </span>
                                    </dd>
                                </div>
                                @if($session->notes)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                        <dd class="text-sm text-gray-900">{{ $session->notes }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résumé des présences -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Présences</h3>

                    @if($presencesStats['total'] > 0)
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Présents</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $presencesStats['present'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-red-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-2 bg-red-100 rounded-lg">
                                        <i class="fas fa-times text-red-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Absents</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $presencesStats['absent'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-2 bg-yellow-100 rounded-lg">
                                        <i class="fas fa-exclamation text-yellow-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Justifiés</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $presencesStats['justified'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-2 bg-purple-100 rounded-lg">
                                        <i class="fas fa-clock text-purple-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Retards</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $presencesStats['late'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-2 bg-gray-100 rounded-lg">
                                        <i class="fas fa-users text-gray-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Total</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $presencesStats['total'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Taux de présence -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm font-medium text-gray-700 mb-1">
                                <span>Taux de présence</span>
                                <span>{{ number_format(($presencesStats['present'] / $presencesStats['total']) * 100, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full"
                                     style="width: {{ ($presencesStats['present'] / $presencesStats['total']) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="text-center">
                            @if(($isCoordinateur && ($type === 'workshop' || $typeCode === 'workshop' || $type === 'e-learning' || $typeCode === 'e_learning' || $type === 'elearning' || $type === 'presentiel' || $typeCode === 'presentiel')) || ($isEnseignant && ($type === 'presentiel' || $typeCode === 'presentiel')))
                                <a href="{{ route('sessions-de-cours.appel', $session->id) }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                                    <i class="fas fa-edit mr-2"></i>Modifier les Présences
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-clipboard-check text-6xl"></i>
                            </div>
                            <p class="text-gray-500 mb-4">Aucun appel n'a encore été fait pour cette session.</p>
                            @if(($isCoordinateur && ($type === 'workshop' || $typeCode === 'workshop' || $type === 'e-learning' || $typeCode === 'e_learning' || $type === 'elearning' || $type === 'presentiel' || $typeCode === 'presentiel')) || ($isEnseignant && ($type === 'presentiel' || $typeCode === 'presentiel')))
                                <a href="{{ route('sessions-de-cours.appel', $session->id) }}"
                                   class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-md">
                                    <i class="fas fa-clipboard-check mr-2"></i>Faire l'Appel Maintenant
                                </a>
                            @else
                                <p class="text-gray-400 text-sm">Vous n'êtes pas autorisé à faire l'appel pour ce type de cours.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
