<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Emplois du temps de mes enfants') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($enfants->count() > 0)
                        @foreach($enfants as $enfant)
                            <div class="mb-8 border rounded-lg p-6 bg-gray-50">
                                <h3 class="text-xl font-semibold mb-4">{{ $enfant->prenom }} {{ $enfant->nom }}</h3>
                                <p class="text-gray-600 mb-4">{{ $enfant->classe->nom ?? 'Classe non assignée' }}</p>

                                @if($enfant->classe)
                                    <div class="bg-white rounded-lg p-4">
                                        <p class="text-gray-600">L'emploi du temps pour la classe {{ $enfant->classe->nom }} sera bientôt disponible.</p>
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-4">Aucun emploi du temps disponible pour cet enfant.</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500">Aucun enfant trouvé dans votre compte.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
