<x-app-layout>    <x-slot name="header">        <h2 class="font-semibold text-xl text-gray-800 leading-tight">            {{ __('Détail de l’étudiant') }}        </h2>    </x-slot>    <div class="py-6">        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">                <div class="p-6 text-gray-900">                    <div class="mb-4">                        <a href="{{ route('etudiants.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">                            <i class="fas fa-arrow-left mr-2"></i>Retour à la liste                        </a>
                        <a href="{{ route('etudiants.attribuer-parents', $etudiant) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">
                            <i class="fas fa-users mr-2"></i>Attribuer des parents
                        </a>
                    </div>                    <h3 class="text-lg font-semibold mb-4">Informations générales</h3>                    <ul class="mb-6">                        <li>                            @if($etudiant->photo)                                <img src="{{ asset('storage/' . $etudiant->photo) }}" alt="Photo" class="h-24 w-24 rounded-full object-cover mb-2">                            @endif                        </li>                        <li><strong>Nom :</strong> {{ $etudiant->nom }}</li>                        <li><strong>Prénom :</strong> {{ $etudiant->prenom }}</li>                        <li><strong>Email :</strong> {{ $etudiant->email ?? '-' }}</li>                        <li><strong>Classe :</strong> {{ $etudiant->classe->nom ?? '-' }}</li>                        <li><strong>Date de naissance :</strong> {{ $etudiant->date_naissance ? \Carbon\Carbon::parse($etudiant->date_naissance)->format('d/m/Y') : '-' }}</li>                    </ul>                    <h3 class="text-lg font-semibold mb-4">Présences</h3>                    <div class="overflow-x-auto">                        <table class="min-w-full divide-y divide-gray-200">                            <thead class="bg-gray-50">                                <tr>                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>                                </tr>                            </thead>                            <tbody class="bg-white divide-y divide-gray-200">                                @forelse($etudiant->presences as $presence)                                    <tr>                                        <td class="px-6 py-4 whitespace-nowrap">{{ $presence->sessionDeCours->start_time ? \Carbon\Carbon::parse($presence->sessionDeCours->start_time)->format('d/m/Y H:i') : '-' }}</td>                                        <td class="px-6 py-4 whitespace-nowrap">{{ $presence->sessionDeCours->matiere->nom ?? '-' }}</td>                                        <td class="px-6 py-4 whitespace-nowrap">{{ $presence->statutPresence->nom ?? '-' }}</td>                                    </tr>                                @empty                                    <tr>                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Aucune présence enregistrée.</td>                                    </tr>                                @endforelse                            </tbody>                        </table>                    </div>

                    <h3 class="text-lg font-semibold mb-4 mt-8">Parents</h3>
                    @if($etudiant->parents->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($etudiant->parents as $parent)
                                <div class="border rounded-lg p-4 bg-gray-50">
                                    <div class="font-medium text-gray-900">
                                        {{ $parent->nom }} {{ $parent->prenom }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if($parent->user)
                                            <p><strong>Email :</strong> {{ $parent->user->email }}</p>
                                        @endif
                                        @if($parent->telephone)
                                            <p><strong>Téléphone :</strong> {{ $parent->telephone }}</p>
                                        @endif
                                        @if($parent->profession)
                                            <p><strong>Profession :</strong> {{ $parent->profession }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Aucun parent attribué à cet étudiant.</p>
                    @endif
                </div>            </div>        </div>    </div></x-app-layout>
