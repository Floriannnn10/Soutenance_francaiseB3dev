<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Cours') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">📚 Mes Cours</h3>
                        <div class="text-sm text-gray-500">
                            {{ $sessions->total() }} cours trouvés
                        </div>
                    </div>

                    @if($sessions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($sessions as $session)
                                <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                    <div class="p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="font-semibold text-gray-900">{{ $session->matiere->nom }}</h4>
                                            <span class="inline-block px-2 py-1 text-xs rounded-full
                                                @if($session->typeCours->code === 'presentiel') bg-blue-100 text-blue-800
                                                @elseif($session->typeCours->code === 'workshop') bg-green-100 text-green-800
                                                @else bg-purple-100 text-purple-800 @endif">
                                                {{ $session->typeCours->nom }}
                                            </span>
                                        </div>

                                        <div class="space-y-2 text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <span class="font-medium">📅 Date:</span>
                                                <span class="ml-2">{{ $session->start_time->format('d/m/Y') }}</span>
                                            </div>

                                            <div class="flex items-center">
                                                <span class="font-medium">🕐 Heure:</span>
                                                <span class="ml-2">{{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</span>
                                            </div>

                                            <div class="flex items-center">
                                                <span class="font-medium">👨‍🏫 Enseignant:</span>
                                                <span class="ml-2">{{ $session->enseignant->prenom }} {{ $session->enseignant->nom }}</span>
                                            </div>

                                            <div class="flex items-center">
                                                <span class="font-medium">🏢 Salle:</span>
                                                <span class="ml-2">{{ $session->salle ?? 'Non définie' }}</span>
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-gray-200">
                                            <div class="flex justify-between items-center">
                                                <span class="text-xs text-gray-500">
                                                    Statut:
                                                    <span class="font-medium
                                                        @if($session->statutSession->code === 'terminee') text-green-600
                                                        @elseif($session->statutSession->code === 'annulee') text-red-600
                                                        @else text-blue-600 @endif">
                                                        {{ $session->statutSession->nom }}
                                                    </span>
                                                </span>

                                                @if($session->typeCours->code === 'presentiel')
                                                    <span class="text-xs text-blue-600 font-medium">
                                                        📍 Présentiel
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $sessions->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-6xl mb-4">📚</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun cours trouvé</h3>
                            <p class="text-gray-500">Vous n'avez pas encore de cours programmés.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
