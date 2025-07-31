<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Emplois du temps de mes enfants') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(count($emploisDuTemps) > 0)
                @foreach($emploisDuTemps as $enfantId => $emploi)
                    @php
                        $enfant = $emploi['etudiant'];
                        $sessions = $emploi['sessions'];
                    @endphp

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">
                                📚 {{ $enfant->prenom }} {{ $enfant->nom }} - {{ $enfant->classe->nom ?? 'Classe non assignée' }}
                            </h3>

                            @if($sessions->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white border border-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Heure</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enseignant</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($sessions as $session)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $session->start_time->format('d/m/Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $session->matiere->nom ?? 'Non défini' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ ($session->enseignant->prenom ?? '') }} {{ ($session->enseignant->nom ?? '') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            {{ $session->typeCours->nom ?? 'Non défini' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                            {{ $session->statutSession->nom ?? 'Non défini' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-600">
                                        Aucune session programmée pour {{ $enfant->prenom }}.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            Aucun enfant trouvé
                        </h3>
                        <p class="text-gray-600">
                            Vous n'avez pas encore d'enfants associés à votre compte parent.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
