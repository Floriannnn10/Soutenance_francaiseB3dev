@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Emploi du Temps</h1>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-600">
                Année: {{ $anneeActive?->nom ?? 'Aucune année active' }} |
                Semestre: {{ $semestreActif?->nom ?? 'Aucun semestre actif' }}
            </div>
            @if(auth()->user()->hasRole(['admin', 'coordinateur']))
            <a href="{{ route('emplois-du-temps.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Créer un emploi du temps
            </a>
            @endif
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form id="filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="classe" class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                <select id="classe" name="classe" class="form-select w-full rounded-md border-gray-300">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="enseignant" class="block text-sm font-medium text-gray-700 mb-1">Enseignant</label>
                <select id="enseignant" name="enseignant" class="form-select w-full rounded-md border-gray-300">
                    <option value="">Tous les enseignants</option>
                    @foreach($enseignants as $enseignant)
                        <option value="{{ $enseignant->id }}" {{ request('enseignant') == $enseignant->id ? 'selected' : '' }}>
                            {{ $enseignant->nom }} {{ $enseignant->prenom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="semaine" class="block text-sm font-medium text-gray-700 mb-1">Semaine</label>
                <input type="week" id="semaine" name="semaine" class="form-input w-full rounded-md border-gray-300"
                       value="{{ request('semaine', date('Y-W')) }}">
            </div>

            <div class="flex items-end">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg w-full">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Calendrier hebdomadaire -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- En-têtes des jours -->
        <div class="grid grid-cols-7 border-b">
            <div class="p-4 border-r bg-gray-50"></div>
            @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] as $jour)
            <div class="p-4 border-r bg-gray-50">
                <h3 class="text-center font-semibold text-gray-700">{{ $jour }}</h3>
            </div>
            @endforeach
        </div>

        <!-- Grille horaire -->
        <div class="relative">
            <!-- Lignes des heures -->
            @for($heure = 8; $heure <= 18; $heure++)
            <div class="grid grid-cols-7 border-b" style="height: 60px;">
                <!-- Colonne des heures -->
                <div class="border-r p-2 text-sm text-gray-600 bg-gray-50">
                    {{ sprintf('%02d:00', $heure) }}
                </div>

                <!-- Colonnes des jours -->
                @foreach(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'] as $jour)
                <div class="border-r relative" id="slot-{{ $jour }}-{{ $heure }}">
                    @foreach($sessions->where('jour', $jour) as $session)
                        @php
                            $startHour = (int) date('G', strtotime($session->start_time));
                            $startMinute = (int) date('i', strtotime($session->start_time));
                            $endHour = (int) date('G', strtotime($session->end_time));
                            $endMinute = (int) date('i', strtotime($session->end_time));

                            if ($startHour == $heure || ($startHour < $heure && $endHour > $heure) || ($startHour == $heure && $endHour > $heure))
                        @endphp
                        <div class="absolute w-full p-2 {{ $session->statut === 'annulé' ? 'bg-red-100' : 'bg-blue-100' }} rounded shadow-sm overflow-hidden"
                             style="top: {{ ($startHour == $heure ? $startMinute : 0) }}px;
                                    height: {{ min(60, ($endHour - $startHour) * 60 + $endMinute - ($startHour == $heure ? $startMinute : 0)) }}px;"
                             data-session-id="{{ $session->id }}">
                            <div class="text-xs font-semibold">
                                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                            </div>
                            <div class="font-medium text-sm truncate">{{ $session->matiere->nom }}</div>
                            <div class="text-xs truncate">{{ $session->classe->nom }}</div>
                            <div class="text-xs truncate">{{ $session->enseignant->nom }} {{ $session->enseignant->prenom }}</div>

                            @if(auth()->user()->hasRole(['admin', 'coordinateur']))
                            <div class="absolute top-1 right-1 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="editSession({{ $session->id }})"
                                        class="text-blue-600 hover:text-blue-800 bg-white rounded-full p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick="deleteSession({{ $session->id }})"
                                        class="text-red-600 hover:text-red-800 bg-white rounded-full p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                            @endif
                        </div>
                        @endforeach
                </div>
                @endforeach
            </div>
            @endfor
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('filter-form').addEventListener('change', function() {
    this.submit();
});

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
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const sessionElement = document.querySelector(`[data-session-id="${sessionId}"]`);
                if (sessionElement) {
                    sessionElement.remove();
                }
                showNotification('Session supprimée avec succès', 'success');
            } else {
                throw new Error(data.message || 'Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors de la suppression de la session', 'error');
        });
    }
}

function showNotification(message, type = 'success') {
    const notificationDiv = document.createElement('div');
    notificationDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white z-50`;
    notificationDiv.textContent = message;

    document.body.appendChild(notificationDiv);

    setTimeout(() => {
        notificationDiv.remove();
    }, 3000);
}

// Ajouter des tooltips pour les sessions tronquées
document.querySelectorAll('[data-session-id]').forEach(session => {
    session.addEventListener('mouseenter', function() {
        const details = this.cloneNode(true);
        details.style.position = 'fixed';
        details.style.zIndex = '100';
        details.style.width = '200px';
        details.style.height = 'auto';
        details.style.backgroundColor = 'white';
        details.style.border = '1px solid #e2e8f0';
        details.style.borderRadius = '0.5rem';
        details.style.padding = '0.75rem';
        details.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';

        const rect = this.getBoundingClientRect();
        details.style.top = `${rect.top}px`;
        details.style.left = `${rect.right + 10}px`;

        document.body.appendChild(details);

        this.addEventListener('mouseleave', function() {
            details.remove();
        });
    });
});
</script>
@endpush
