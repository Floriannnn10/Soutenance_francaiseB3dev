@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Création d'Emploi du Temps</h1>
        <div class="text-sm text-gray-600">
            Année: {{ $anneeActive?->nom ?? 'Aucune année active' }} |
            Semestre: {{ $semestreActif?->nom ?? 'Aucun semestre actif' }}
        </div>
    </div>

    @if(!$anneeActive || !$semestreActif)
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            <strong>Attention :</strong> Aucune année académique ou semestre n'est actuellement actif.
            Veuillez activer une année académique et un semestre avant de créer un emploi du temps.
        </div>
    @else
        @php
            $periodeActive = isset($anneeActive) && isset($semestreActif) && $anneeActive->actif && $semestreActif->actif;
        @endphp
        <form action="{{ route('emplois-du-temps.generer') }}" method="POST" id="emploi-form">
            @csrf
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Configuration de l'emploi du temps</h2>

                <div class="mb-4">
                    <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Classe *
                    </label>
                    <select name="classe_id" id="classe_id" class="form-select w-full" required>
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="sessions-container">
                    <h3 class="text-md font-medium mb-3">Sessions de cours</h3>
                    <div id="sessions-list">
                        <!-- Les sessions seront ajoutées ici dynamiquement -->
                    </div>

                    <button type="button" id="add-session" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded {{ !$periodeActive ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}" {{ !$periodeActive ? 'disabled' : '' }}>
                        + Ajouter une session
                    </button>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" onclick="history.back()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Annuler
                </button>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded {{ !$periodeActive ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}" {{ !$periodeActive ? 'disabled' : '' }}>
                    Générer l'emploi du temps
                </button>
            </div>
        </form>

        @if(!$periodeActive)
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4 mt-4">
                <strong>Attention :</strong> Les actions de modification sont désactivées car la période sélectionnée n'est pas active.
            </div>
        @endif

        <!-- Affichage des emplois du temps existants -->
        @if($sessions->count() > 0)
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-4">Emplois du temps existants</h2>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horaire</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sessions->groupBy('classe_id') as $classeId => $sessionsClasse)
                            @foreach($sessionsClasse as $session)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $session->classe->nom }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $session->matiere->nom }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $session->enseignant->nom }} {{ $session->enseignant->prenom }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $session->typeCours->nom }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i') }} -
                                    {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
let sessionCount = 0;
let existingSessions = @json($sessions);

function addSession() {
    sessionCount++;
    const sessionHtml = `
        <div class="session-item border border-gray-300 rounded p-4 mb-4" data-session="${sessionCount}">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-medium">Session ${sessionCount}</h4>
                <button type="button" onclick="removeSession(${sessionCount})" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Matière *</label>
                    <select name="sessions[${sessionCount}][matiere_id]" class="form-select w-full" required>
                        <option value="">Sélectionner une matière</option>
                        @foreach(\App\Models\Matiere::all() as $matiere)
                            <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Enseignant *</label>
                    <select name="sessions[${sessionCount}][enseignant_id]" class="form-select w-full enseignant-select" required>
                        <option value="">Sélectionner un enseignant</option>
                        @foreach($enseignants as $enseignant)
                            <option value="{{ $enseignant->id }}">{{ $enseignant->nom }} {{ $enseignant->prenom }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de cours *</label>
                    <select name="sessions[${sessionCount}][type_cours_id]" class="form-select w-full" required>
                        <option value="">Sélectionner un type</option>
                        @foreach(\App\Models\TypeCours::all() as $type)
                            <option value="{{ $type->id }}">{{ $type->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jour *</label>
                    <select name="sessions[${sessionCount}][jour]" class="form-select w-full jour-select" required>
                        <option value="">Sélectionner un jour</option>
                        <option value="lundi">Lundi</option>
                        <option value="mardi">Mardi</option>
                        <option value="mercredi">Mercredi</option>
                        <option value="jeudi">Jeudi</option>
                        <option value="vendredi">Vendredi</option>
                        <option value="samedi">Samedi</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Heure début *</label>
                    <input type="time" name="sessions[${sessionCount}][heure_debut]"
                           class="form-input w-full heure-debut" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Heure fin *</label>
                    <input type="time" name="sessions[${sessionCount}][heure_fin]"
                           class="form-input w-full heure-fin" required>
                </div>
            </div>
            <div class="text-red-500 text-sm mt-2 conflit-message" style="display: none;"></div>
        </div>
    `;

    document.getElementById('sessions-list').insertAdjacentHTML('beforeend', sessionHtml);
    initializeSessionValidation(sessionCount);
}

function removeSession(sessionId) {
    const sessionElement = document.querySelector(`[data-session="${sessionId}"]`);
    if (sessionElement) {
        sessionElement.remove();
    }
}

function initializeSessionValidation(sessionId) {
    const sessionElement = document.querySelector(`[data-session="${sessionId}"]`);
    const enseignantSelect = sessionElement.querySelector('.enseignant-select');
    const jourSelect = sessionElement.querySelector('.jour-select');
    const heureDebut = sessionElement.querySelector('.heure-debut');
    const heureFin = sessionElement.querySelector('.heure-fin');
    const conflitMessage = sessionElement.querySelector('.conflit-message');

    function validateSession() {
        const enseignantId = enseignantSelect.value;
        const jour = jourSelect.value;
        const debut = heureDebut.value;
        const fin = heureFin.value;

        if (!enseignantId || !jour || !debut || !fin) return;

        // Vérifier les conflits avec les sessions existantes
        const conflits = existingSessions.filter(session => {
            if (session.enseignant_id == enseignantId && session.jour === jour) {
                const sessionDebut = session.start_time.split(' ')[1];
                const sessionFin = session.end_time.split(' ')[1];

                return (debut >= sessionDebut && debut < sessionFin) ||
                       (fin > sessionDebut && fin <= sessionFin) ||
                       (debut <= sessionDebut && fin >= sessionFin);
            }
            return false;
        });

        if (conflits.length > 0) {
            const message = `Conflit : L'enseignant a déjà un cours prévu à cette période`;
            conflitMessage.textContent = message;
            conflitMessage.style.display = 'block';
            heureDebut.setCustomValidity(message);
            heureFin.setCustomValidity(message);
        } else {
            conflitMessage.style.display = 'none';
            heureDebut.setCustomValidity('');
            heureFin.setCustomValidity('');
        }

        // Vérifier que l'heure de fin est après l'heure de début
        if (debut >= fin) {
            const message = 'L\'heure de fin doit être après l\'heure de début';
            conflitMessage.textContent = message;
            conflitMessage.style.display = 'block';
            heureFin.setCustomValidity(message);
        }
    }

    enseignantSelect.addEventListener('change', validateSession);
    jourSelect.addEventListener('change', validateSession);
    heureDebut.addEventListener('change', validateSession);
    heureFin.addEventListener('change', validateSession);
}

document.getElementById('add-session').addEventListener('click', addSession);

// Ajouter une première session au chargement
document.addEventListener('DOMContentLoaded', function() {
    addSession();
});

// Validation du formulaire
document.getElementById('emploi-form').addEventListener('submit', function(e) {
    const sessions = document.querySelectorAll('.session-item');
    let hasErrors = false;

    sessions.forEach(session => {
        const heureDebut = session.querySelector('.heure-debut');
        const heureFin = session.querySelector('.heure-fin');

        if (heureDebut.validity.customError || heureFin.validity.customError) {
            hasErrors = true;
        }
    });

    if (hasErrors) {
        e.preventDefault();
        alert('Veuillez corriger les conflits d\'horaires avant de soumettre le formulaire.');
    }
});
</script>
@endpush
