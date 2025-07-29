<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Graphiques de Présence</h1>
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Analyses détaillées pour l'équipe pédagogique</p>
</div>

<!-- Filtres -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Filtres</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Classe</label>
                <select id="classeFilter" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Toutes les classes</option>
                    @foreach(\App\Models\Classe::all() as $classe)
                        <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Période</label>
                <select id="periodeFilter" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="all">Toute la période</option>
                    <option value="current">Année en cours</option>
                    <option value="last">Année précédente</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type de cours</label>
                <select id="typeCoursFilter" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Tous les types</option>
                    @foreach(\App\Models\TypeCours::all() as $type)
                        <option value="{{ $type->id }}">{{ $type->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Graphique 1: Taux de présence par étudiant -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Taux de présence par étudiant</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            <span class="inline-block w-3 h-3 bg-green-800 rounded mr-2"></span> ≥ 70% (Vert foncé) |
            <span class="inline-block w-3 h-3 bg-green-500 rounded mr-2"></span> 50.1% - 69.9% (Vert clair) |
            <span class="inline-block w-3 h-3 bg-orange-500 rounded mr-2"></span> 30.1% - 50% (Orange) |
            <span class="inline-block w-3 h-3 bg-red-600 rounded mr-2"></span> ≤ 30% (Rouge)
        </p>
        <div class="h-96">
            <canvas id="presenceParEtudiant"></canvas>
        </div>
    </div>
</div>

<!-- Graphiques 2 et 3: Taux par classe et Volume de cours -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Taux de présence par classe -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Taux de présence par classe</h3>
            <div class="h-64">
                <canvas id="presenceParClasse"></canvas>
            </div>
        </div>
    </div>

    <!-- Volume de cours par type -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Volume de cours par type</h3>
            <div class="h-64">
                <canvas id="volumeCoursParType"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Graphique 4: Volume de cours cumulé -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Volume de cours cumulé</h3>
        <div class="h-64">
            <canvas id="volumeCoursCumule"></canvas>
        </div>
    </div>
</div>

<!-- Statistiques détaillées -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-800 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Excellents (≥70%)</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100" id="excellents">0</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Bons (50-70%)</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100" id="bons">0</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Moyens (30-50%)</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100" id="moyens">0</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-600 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Faibles (≤30%)</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100" id="faibles">0</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Données pour les graphiques
const presenceData = @json(\App\Models\Classe::withCount('etudiants')->get()->map(function($classe) {
    $totalPresences = \App\Models\Presence::whereHas('sessionDeCours', function($q) use ($classe) {
        $q->where('classe_id', $classe->id);
    })->count();
    $presencesPresent = \App\Models\Presence::whereHas('sessionDeCours', function($q) use ($classe) {
        $q->where('classe_id', $classe->id);
    })->whereHas('statutPresence', function($q) {
        $q->where('nom', 'Présent');
    })->count();
    return [
        'nom' => $classe->nom,
        'taux' => $totalPresences > 0 ? round(($presencesPresent / $totalPresences) * 100) : 0,
        'etudiants' => $classe->etudiants_count
    ];
}));

const etudiantsData = @json(\App\Models\Etudiant::with(['classe', 'presences.statutPresence'])->get()->map(function($etudiant) {
    $totalPresences = $etudiant->presences()->count();
    $presencesPresent = $etudiant->presences()->whereHas('statutPresence', function($q) {
        $q->where('nom', 'Présent');
    })->count();
    $taux = $totalPresences > 0 ? round(($presencesPresent / $totalPresences) * 100) : 0;

    // Déterminer la couleur selon le taux
    if ($taux >= 70) {
        $couleur = '#166534'; // Vert foncé
    } elseif ($taux >= 50.1) {
        $couleur = '#22c55e'; // Vert clair
    } elseif ($taux >= 30.1) {
        $couleur = '#f97316'; // Orange
    } else {
        $couleur = '#dc2626'; // Rouge
    }

    return [
        'nom' => $etudiant->prenom . ' ' . $etudiant->nom,
        'taux' => $taux,
        'classe' => $etudiant->classe->nom,
        'classe_id' => $etudiant->classe_id,
        'couleur' => $couleur
    ];
})->sortByDesc('taux'));

const coursData = @json(\App\Models\SessionDeCours::with('typeCours')->get()->groupBy('type_cours_id')->map(function($sessions, $typeId) {
    $type = \App\Models\TypeCours::find($typeId);
    return [
        'type' => $type ? $type->nom : 'Non défini',
        'count' => $sessions->count()
    ];
}));

// Fonction pour mettre à jour les statistiques
function updateStats(data) {
    const excellents = data.filter(d => d.taux >= 70).length;
    const bons = data.filter(d => d.taux >= 50.1 && d.taux < 70).length;
    const moyens = data.filter(d => d.taux >= 30.1 && d.taux < 50.1).length;
    const faibles = data.filter(d => d.taux <= 30).length;

    document.getElementById('excellents').textContent = excellents;
    document.getElementById('bons').textContent = bons;
    document.getElementById('moyens').textContent = moyens;
    document.getElementById('faibles').textContent = faibles;
}

// Graphique 1: Taux de présence par étudiant
const ctx1 = document.getElementById('presenceParEtudiant').getContext('2d');
const presenceParEtudiantChart = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: etudiantsData.map(d => d.nom),
        datasets: [{
            label: 'Taux de présence (%)',
            data: etudiantsData.map(d => d.taux),
            backgroundColor: etudiantsData.map(d => d.couleur),
            borderColor: etudiantsData.map(d => d.couleur),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Graphique 2: Taux de présence par classe
const ctx2 = document.getElementById('presenceParClasse').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: presenceData.map(d => d.nom),
        datasets: [{
            label: 'Taux de présence (%)',
            data: presenceData.map(d => d.taux),
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Graphique 3: Volume de cours par type
const ctx3 = document.getElementById('volumeCoursParType').getContext('2d');
new Chart(ctx3, {
    type: 'doughnut',
    data: {
        labels: coursData.map(d => d.type),
        datasets: [{
            data: coursData.map(d => d.count),
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Graphique 4: Volume de cours cumulé
const ctx4 = document.getElementById('volumeCoursCumule').getContext('2d');
new Chart(ctx4, {
    type: 'line',
    data: {
        labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'],
        datasets: [{
            label: 'Cours en présentiel',
            data: [12, 19, 15, 25, 22, 30],
            borderColor: 'rgba(59, 130, 246, 1)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true
        }, {
            label: 'Cours en e-learning',
            data: [8, 12, 10, 18, 15, 22],
            borderColor: 'rgba(16, 185, 129, 1)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true
        }, {
            label: 'Workshops',
            data: [5, 8, 6, 12, 10, 15],
            borderColor: 'rgba(245, 158, 11, 1)',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});

// Filtres
document.getElementById('classeFilter').addEventListener('change', function() {
    const selectedClasse = this.value;
    let filteredData = etudiantsData;

    if (selectedClasse) {
        filteredData = etudiantsData.filter(d => d.classe_id == selectedClasse);
    }

    presenceParEtudiantChart.data.labels = filteredData.map(d => d.nom);
    presenceParEtudiantChart.data.datasets[0].data = filteredData.map(d => d.taux);
    presenceParEtudiantChart.data.datasets[0].backgroundColor = filteredData.map(d => d.couleur);
    presenceParEtudiantChart.data.datasets[0].borderColor = filteredData.map(d => d.couleur);
    presenceParEtudiantChart.update();

    updateStats(filteredData);
});

// Initialiser les statistiques
updateStats(etudiantsData);
</script>
