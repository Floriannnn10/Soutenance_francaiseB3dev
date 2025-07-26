@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- En-tête -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestion des emplois du temps</h1>
                <p class="text-gray-600 mt-1">Planifiez et gérez les sessions de cours de votre promotion</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm">
                    <span class="text-sm font-medium text-gray-700">Année: {{ $anneeActive->annee }}</span>
                </div>
                <button onclick="openCreateModal()" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transform transition-all duration-200 hover:scale-105 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Créer une session
                </button>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtres</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                    <select id="filter-classe" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Enseignant</label>
                    <select id="filter-enseignant" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Tous les enseignants</option>
                        @foreach($enseignants as $enseignant)
                            <option value="{{ $enseignant->id }}">{{ $enseignant->nom }} {{ $enseignant->prenom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de cours</label>
                    <select id="filter-type" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Tous les types</option>
                        @foreach($typesCours as $type)
                            <option value="{{ $type->id }}">{{ $type->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Semaine</label>
                    <input type="week" id="filter-semaine" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="{{ date('Y-W') }}">
                </div>
            </div>
        </div>

        <!-- Calendrier -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- En-têtes des jours -->
            <div class="grid grid-cols-7 bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
                <div class="p-4 border-r border-purple-500"></div>
                @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] as $jour)
                <div class="p-4 border-r border-purple-500">
                    <h3 class="text-center font-semibold">{{ $jour }}</h3>
                </div>
                @endforeach
            </div>

            <!-- Grille horaire -->
            <div class="relative">
                @for($heure = 8; $heure <= 18; $heure++)
                <div class="grid grid-cols-7 border-b border-gray-200" style="height: 80px;">
                    <!-- Colonne des heures -->
                    <div class="border-r border-gray-200 p-3 text-sm text-gray-600 bg-gray-50 flex items-center justify-center font-medium">
                        {{ sprintf('%02d:00', $heure) }}
                    </div>

                    <!-- Colonnes des jours -->
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $jourIndex => $jour)
                    <div class="border-r border-gray-200 relative hover:bg-gray-50 transition-colors" id="slot-{{ $jour }}-{{ $heure }}">
                        @php
                            $jourSessions = $sessions->filter(function($session) use ($jour, $heure) {
                                $sessionDate = \Carbon\Carbon::parse($session->start_time);
                                $sessionDay = strtolower($sessionDate->format('l'));
                                $sessionHour = (int) $sessionDate->format('G');

                                return $sessionDay === $jour && $sessionHour == $heure;
                            });
                        @endphp

                        @foreach($jourSessions as $session)
                            @php
                                $startHour = (int) date('G', strtotime($session->start_time));
                                $startMinute = (int) date('i', strtotime($session->start_time));
                                $endHour = (int) date('G', strtotime($session->end_time));
                                $endMinute = (int) date('i', strtotime($session->end_time));
                                $duration = ($endHour - $startHour) * 60 + ($endMinute - $startMinute);
                                $height = max(40, min(80, $duration * 80 / 60));
                            @endphp
                            <div class="absolute w-full p-2 rounded-lg shadow-md overflow-hidden cursor-pointer transform transition-all duration-200 hover:scale-105 {{ $session->statutSession->code === 'annule' ? 'bg-red-100 border-red-300' : 'bg-gradient-to-r from-blue-100 to-purple-100 border border-blue-300' }}"
                                 style="top: {{ $startMinute * 80 / 60 }}px; height: {{ $height }}px;"
                                 data-session-id="{{ $session->id }}"
                                 onclick="showSessionDetails({{ $session->id }})">
                                <div class="text-xs font-semibold text-gray-700 mb-1">
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                </div>
                                <div class="font-medium text-sm text-gray-800 truncate">{{ $session->matiere->nom }}</div>
                                <div class="text-xs text-gray-600 truncate">{{ $session->classe->nom }}</div>
                                <div class="text-xs text-gray-600 truncate">{{ $session->enseignant->nom }} {{ $session->enseignant->prenom }}</div>

                                <!-- Actions -->
                                <div class="absolute top-1 right-1 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="editSession({{ $session->id }})" class="text-blue-600 hover:text-blue-800 bg-white rounded-full p-1 shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button onclick="deleteSession({{ $session->id }})" class="text-red-600 hover:text-red-800 bg-white rounded-full p-1 shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                @endfor
            </div>
        </div>
    </div>
</div>

<!-- Modal de création de session -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900">Créer une session de cours</h2>
                    <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form id="createSessionForm" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Colonne gauche -->
                    <div class="space-y-4">
                    <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Classe *</label>
                            <select name="classe_id" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Sélectionner une classe</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Matière *</label>
                            <select name="matiere_id" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Sélectionner une matière</option>
                                @foreach(\App\Models\Matiere::all() as $matiere)
                                    <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de cours *</label>
                            <select name="type_cours_id" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Sélectionner un type</option>
                            @foreach($typesCours as $type)
                                <option value="{{ $type->id }}">{{ $type->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date et heure de début *</label>
                            <input type="datetime-local" name="start_time" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Notes optionnelles..."></textarea>
                        </div>
                    </div>

                    <!-- Colonne droite -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Enseignant *</label>
                            <select name="enseignant_id" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Sélectionner un enseignant</option>
                                @foreach($enseignants as $enseignant)
                                    <option value="{{ $enseignant->id }}">{{ $enseignant->nom }} {{ $enseignant->prenom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut *</label>
                            <select name="status_id" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Sélectionner un statut</option>
                            @foreach($statutsSession as $statut)
                                <option value="{{ $statut->id }}">{{ $statut->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date et heure de fin *</label>
                            <input type="datetime-local" name="end_time" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeCreateModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 transform hover:scale-105">
                        Créer la session
                    </button>
                </div>
            </form>
        </div>
    </div>
        </div>

<!-- Modal de détails de session -->
<div id="sessionDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">Détails de la session</h2>
                    <button onclick="closeSessionDetailsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="sessionDetailsContent" class="p-6">
                <!-- Le contenu sera rempli dynamiquement -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Fonctions pour les modals
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

function showSessionDetails(sessionId) {
    // Ici vous pouvez faire un appel AJAX pour récupérer les détails de la session
    // Pour l'instant, on affiche juste un message
    document.getElementById('sessionDetailsContent').innerHTML = `
        <div class="space-y-4">
            <div>
                <h3 class="font-semibold text-gray-900">Session #${sessionId}</h3>
                <p class="text-gray-600">Détails de la session...</p>
            </div>
        </div>
    `;
    document.getElementById('sessionDetailsModal').classList.remove('hidden');
}

function closeSessionDetailsModal() {
    document.getElementById('sessionDetailsModal').classList.add('hidden');
}

// Gestion du formulaire de création
document.getElementById('createSessionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('{{ route("emplois-du-temps.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Session créée avec succès !', 'success');
            closeCreateModal();
            // Recharger la page pour afficher la nouvelle session
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message || 'Erreur lors de la création', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la création de la session', 'error');
    });
});

// Fonctions d'édition et suppression
function editSession(sessionId) {
    window.location.href = `/emplois-du-temps/${sessionId}/edit`;
}

function deleteSession(sessionId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette session ?')) {
        const token = document.querySelector('meta[name="csrf-token"]').content;

        fetch(`/emplois-du-temps/${sessionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Session supprimée avec succès', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message || 'Erreur lors de la suppression', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors de la suppression de la session', 'error');
        });
    }
}

// Système de notifications
function showNotification(message, type = 'success') {
    const notificationDiv = document.createElement('div');
    notificationDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    notificationDiv.textContent = message;

    document.body.appendChild(notificationDiv);

    // Animation d'entrée
    setTimeout(() => {
        notificationDiv.style.transform = 'translateX(0)';
    }, 100);

    // Suppression automatique
    setTimeout(() => {
        notificationDiv.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notificationDiv.remove();
        }, 300);
    }, 3000);
}

// Filtres
document.querySelectorAll('#filter-classe, #filter-enseignant, #filter-type, #filter-semaine').forEach(filter => {
    filter.addEventListener('change', function() {
        // Ici vous pouvez implémenter la logique de filtrage
        console.log('Filtre changé:', this.value);
    });
});

// Fermer les modals en cliquant à l'extérieur
document.querySelectorAll('#createModal, #sessionDetailsModal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
</script>
@endpush
