<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Présences de mes enfants') }}
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

                                @if($enfant->presences->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($enfant->presences->sortByDesc('enregistre_le') as $presence)
                                                    <tr>
                                                        <td class="px-6 py-4 text-sm text-gray-900">
                                                            {{ \Carbon\Carbon::parse($presence->enregistre_le)->format('d/m/Y') }}
                                                        </td>
                                                        <td class="px-6 py-4 text-sm text-gray-900">
                                                            {{ $presence->sessionDeCours->matiere->nom ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            @if($presence->statutPresence)
                                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                                    @if($presence->statutPresence->code === 'present') bg-green-100 text-green-800
                                                                    @elseif($presence->statutPresence->code === 'absent') bg-red-100 text-red-800
                                                                    @else bg-gray-100 text-gray-800 @endif">
                                                                    {{ $presence->statutPresence->nom }}
                                                                </span>
                                                            @else
                                                                <span class="text-gray-500">Non défini</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-4">Aucune présence enregistrée pour cet enfant.</p>
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
