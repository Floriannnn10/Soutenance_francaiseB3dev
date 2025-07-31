<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Emploi du temps - Parent') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        👨‍👩‍👧‍👦 Mes Enfants
                    </h3>

                    @if($enfants->count() > 0)
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
                                    </div>

                                    <div class="mt-4 space-y-2">
                                        <a href="{{ route('emplois-du-temps.enfants') }}"
                                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded text-sm">
                                            Voir emploi du temps
                                        </a>
                                        <a href="{{ route('presences.enfants') }}"
                                           class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded text-sm">
                                            Voir présences
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
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
