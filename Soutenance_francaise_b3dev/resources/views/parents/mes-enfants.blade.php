<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes enfants') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($enfants->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($enfants as $enfant)
                                <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                    <div class="p-6">
                                        <div class="flex items-center mb-4">
                                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                                                {{ strtoupper(substr($enfant->nom, 0, 1) . substr($enfant->prenom, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $enfant->prenom }} {{ $enfant->nom }}</h3>
                                                <p class="text-sm text-gray-600">{{ $enfant->classe->nom ?? 'Classe non assignée' }}</p>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Taux de présence:</span>
                                                <span class="font-medium">
                                                    @php
                                                        $totalPresences = $enfant->presences->count();
                                                        $presentPresences = $enfant->presences->where('statutPresence.code', 'present')->count();
                                                        $taux = $totalPresences > 0 ? round(($presentPresences / $totalPresences) * 100, 1) : 0;
                                                    @endphp
                                                    {{ $taux }}%
                                                </span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Absences:</span>
                                                <span class="font-medium text-red-600">
                                                    {{ $enfant->presences->where('statutPresence.code', 'absent')->count() }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Cours suivis:</span>
                                                <span class="font-medium">{{ $totalPresences }}</span>
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <a href="{{ route('presences.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Voir les présences →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun enfant trouvé</h3>
                                <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore d'enfants associés à votre compte.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
