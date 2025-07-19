<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Appel - {{ $session->matiere_nom }}
                </h2>
                <p class="text-sm text-gray-600">
                    {{ $session->classe_nom }} • {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i') }}
                </p>
            </div>
            <a href="{{ route('sessions-de-cours.show', $session->id) }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informations de la session -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-blue-50 border-b">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Enseignant</p>
                            <p class="text-lg text-gray-900">{{ $session->enseignant_prenom }} {{ $session->enseignant_nom }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Horaires</p>
                            <p class="text-lg text-gray-900">
                                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Lieu</p>
                            <p class="text-lg text-gray-900">{{ $session->location ?: 'Non défini' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Étudiants</p>
                            <p class="text-lg text-gray-900">{{ $etudiants->count() }} inscrits</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire d'appel -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('sessions-de-cours.enregistrer-presences', $session->id) }}" method="POST">
                        @csrf

                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Liste des Étudiants</h3>

                            <!-- Boutons de sélection rapide -->
                            <div class="mb-4 flex flex-wrap gap-2">
                                <button type="button" onclick="setAllPresences('present')"
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                    <i class="fas fa-check mr-1"></i>Tous Présents
                                </button>
                                <button type="button" onclick="setAllPresences('absent')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                    <i class="fas fa-times mr-1"></i>Tous Absents
                                </button>
                                <button type="button" onclick="clearAllPresences()"
                                        class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                                    <i class="fas fa-eraser mr-1"></i>Effacer
                                </button>
                            </div>
                        </div>

                        @if($etudiants->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Étudiant
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Email
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Statut de Présence
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($etudiants as $etudiant)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                                <span class="text-sm font-medium text-gray-700">
                                                                    {{ strtoupper(substr($etudiant->name ?? 'E', 0, 1)) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $etudiant->name ?? 'Nom non défini' }}
                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                ID: {{ $etudiant->id }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $etudiant->email ?? 'Non défini' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex space-x-2">
                                                        @foreach($statutsPresence as $statut)
                                                            <label class="inline-flex items-center">
                                                                <input type="radio"
                                                                       name="presences[{{ $etudiant->id }}]"
                                                                       value="{{ $statut->id }}"
                                                                       {{ isset($presencesExistantes[$etudiant->id]) && $presencesExistantes[$etudiant->id]->presence_status_id == $statut->id ? 'checked' : '' }}
                                                                       class="form-radio h-4 w-4 text-blue-600">
                                                                <span class="ml-2 text-sm
                                                                    @if($statut->nom === 'Présent') text-green-600
                                                                    @elseif($statut->nom === 'Absent') text-red-600
                                                                    @elseif($statut->nom === 'Absent Justifié') text-yellow-600
                                                                    @elseif($statut->nom === 'Retard') text-purple-600
                                                                    @elseif($statut->nom === 'Parti Tôt') text-pink-600
                                                                    @else text-gray-600
                                                                    @endif">
                                                                    {{ $statut->nom }}
                                                                </span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="mt-6 flex items-center justify-end space-x-4">
                                <a href="{{ route('sessions-de-cours.show', $session->id) }}"
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Annuler
                                </a>
                                <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-save mr-2"></i>Enregistrer les Présences
                                </button>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">Aucun étudiant inscrit dans cette classe.</p>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
// Fonctions JavaScript pour la sélection rapide
function setAllPresences(type) {
    const statutsMap = {
        'present': @json($statutsPresence->where('nom', 'Présent')->first()->id ?? 1),
        'absent': @json($statutsPresence->where('nom', 'Absent')->first()->id ?? 2)
    };

    const radioButtons = document.querySelectorAll(`input[type="radio"][value="${statutsMap[type]}"]`);
    radioButtons.forEach(radio => {
        radio.checked = true;
    });
}

function clearAllPresences() {
    const radioButtons = document.querySelectorAll('input[type="radio"]');
    radioButtons.forEach(radio => {
        radio.checked = false;
    });
}

// Validation côté client
document.querySelector('form').addEventListener('submit', function(e) {
    const etudiantRows = document.querySelectorAll('tbody tr');
    let hasUnmarked = false;

    etudiantRows.forEach(row => {
        const radios = row.querySelectorAll('input[type="radio"]');
        const isChecked = Array.from(radios).some(radio => radio.checked);

        if (!isChecked) {
            hasUnmarked = true;
            row.classList.add('bg-yellow-50');
        } else {
            row.classList.remove('bg-yellow-50');
        }
    });

    if (hasUnmarked) {
        e.preventDefault();
        alert('Veuillez marquer la présence pour tous les étudiants (les lignes en jaune).');
        return false;
    }
});
</script>
