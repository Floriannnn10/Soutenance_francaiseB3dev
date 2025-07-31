<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Enfants') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($enfants->count() > 0)
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            👨‍👩‍👧‍👦 Mes Enfants ({{ $enfants->count() }})
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($enfants as $enfant)
                                <div class="bg-white border border-gray-200 rounded-lg p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">
                                        {{ $enfant->prenom }} {{ $enfant->nom }}
                                    </h4>
                                    <p class="text-gray-600 mb-4">{{ $enfant->classe->nom ?? 'Classe non assignée' }}</p>

                                    <div class="space-y-2 text-sm">
                                        <div>📧 {{ $enfant->email }}</div>
                                        @if($enfant->date_naissance)
                                            <div>🎂 {{ $enfant->date_naissance->format('d/m/Y') }}</div>
                                        @endif
                                        <div>📊 Présences: {{ $enfant->presences->where('statut_presence.nom', 'Présent')->count() }} / {{ $enfant->presences->count() }}</div>
                                    </div>

                                    <div class="mt-4">
                                        <a href="{{ route('etudiants.show', $enfant) }}"
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                            Voir détails
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                Aucun enfant trouvé
                            </h3>
                            <p class="text-gray-600">
                                Vous n'avez pas encore d'enfants associés à votre compte parent.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
