@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Tableau de Bord - Statistiques</h1>
            <p class="text-gray-600">Analysez les performances académiques et les tendances</p>
        </div>

        <!-- Sélecteur d'année académique -->
        <div class="mb-8 bg-white rounded-xl shadow-sm p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Année Académique</label>
            <select id="annee_academique" class="w-full max-w-xs rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                @foreach($anneeAcademiques as $annee)
                    <option value="{{ $annee->id }}" {{ $annee->id === $anneeSelectionnee->id ? 'selected' : '' }}>
                        {{ $annee->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Graphiques -->
        <div class="space-y-8">
            <!-- Section 1: Présence -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Taux de présence par étudiant -->
                <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Taux de Présence par Étudiant</h3>
                        <div class="flex space-x-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-600 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">≥ 70%</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-300 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">50-70%</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-orange-400 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">30-50%</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">< 30%</span>
                            </div>
                        </div>
                    </div>
                    <div id="taux-presence-etudiants" class="h-96"></div>
                </div>

                <!-- Taux de présence par classe -->
                <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Taux de Présence par Classe</h3>
                    <div id="taux-presence-classes" class="h-96"></div>
                </div>
            </div>

            <!-- Section 2: Volume de cours -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Volume de cours par type -->
                <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Répartition des Cours par Type</h3>
                    <div id="volume-cours-type" class="h-96"></div>
                </div>

                <!-- Volume de cours cumulé -->
                <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Évolution du Volume Horaire</h3>
                    <div id="volume-cours-cumule" class="h-96"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration commune
    const chartOptions = {
        chart: {
            fontFamily: 'Inter, system-ui, sans-serif',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true
                }
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            }
        },
        grid: {
            borderColor: '#e2e8f0',
            strokeDashArray: 5,
            xaxis: {
                lines: {
                    show: true
                }
            },
            yaxis: {
                lines: {
                    show: true
                }
            }
        },
        tooltip: {
            theme: 'light',
            style: {
                fontSize: '12px'
            }
        }
    };

    // Taux de présence par étudiant
    const tauxPresenceEtudiants = @json($tauxPresenceEtudiants);
    new ApexCharts(document.querySelector("#taux-presence-etudiants"), {
        ...chartOptions,
        chart: {
            ...chartOptions.chart,
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: true,
                distributed: true,
                barHeight: '70%',
                borderRadius: 4
            }
        },
        colors: tauxPresenceEtudiants.map(item => item.couleur),
        series: [{
            name: 'Taux de présence',
            data: tauxPresenceEtudiants.map(item => item.taux)
        }],
        xaxis: {
            categories: tauxPresenceEtudiants.map(item => item.nom),
            labels: {
                formatter: function(val) {
                    return val + '%';
                },
                style: {
                    fontSize: '12px'
                }
            },
            max: 100
        },
        yaxis: {
            labels: {
                style: {
                    fontSize: '11px'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val + '%';
            },
            style: {
                fontSize: '11px',
                fontWeight: 'bold'
            }
        }
    }).render();

    // Taux de présence par classe
    const tauxPresenceClasses = @json($tauxPresenceClasses);
    new ApexCharts(document.querySelector("#taux-presence-classes"), {
        ...chartOptions,
        chart: {
            ...chartOptions.chart,
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '60%',
                distributed: false
            }
        },
        colors: ['#3b82f6', '#1d4ed8', '#1e40af', '#1e3a8a'],
        series: [{
            name: 'Taux de présence',
            data: tauxPresenceClasses.map(item => item.taux)
        }],
        xaxis: {
            categories: tauxPresenceClasses.map(item => item.classe),
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            max: 100,
            labels: {
                formatter: function(val) {
                    return val + '%';
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val + '%';
            },
            style: {
                fontSize: '11px',
                fontWeight: 'bold'
            }
        }
    }).render();

    // Volume de cours par type
    const volumeCoursParType = @json($volumeCoursParType);
    new ApexCharts(document.querySelector("#volume-cours-type"), {
        ...chartOptions,
        chart: {
            ...chartOptions.chart,
            type: 'donut',
            height: 350
        },
        series: volumeCoursParType.map(item => item.volume),
        labels: volumeCoursParType.map(item => item.type),
        colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
        legend: {
            position: 'bottom',
            fontSize: '12px',
            markers: {
                width: 12,
                height: 12
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '60%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '14px',
                            fontWeight: 600
                        },
                        value: {
                            show: true,
                            fontSize: '16px',
                            fontWeight: 600,
                            formatter: function(val) {
                                return val + ' min';
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '14px',
                            fontWeight: 600,
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' min';
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                return opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0) > 0
                    ? Math.round((val / opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0)) * 100) + '%'
                    : '0%';
            },
            style: {
                fontSize: '11px',
                fontWeight: 'bold'
            }
        }
    }).render();

    // Volume de cours cumulé
    const volumeCoursCumule = @json($volumeCoursCumule);
    new ApexCharts(document.querySelector("#volume-cours-cumule"), {
        ...chartOptions,
        chart: {
            ...chartOptions.chart,
            type: 'line',
            height: 350,
            stacked: false
        },
        colors: ['#3b82f6', '#10b981'],
        series: [
            {
                name: 'Volume par année',
                type: 'column',
                data: volumeCoursCumule.map(item => item.volume)
            },
            {
                name: 'Volume cumulé',
                type: 'line',
                data: volumeCoursCumule.map(item => item.cumule)
            }
        ],
        stroke: {
            width: [0, 4],
            curve: 'smooth'
        },
        fill: {
            opacity: [0.85, 1],
            gradient: {
                inverseColors: false,
                shade: 'light',
                type: "vertical",
                opacityFrom: 0.85,
                opacityTo: 0.55,
                stops: [0, 100, 100, 100]
            }
        },
        xaxis: {
            categories: volumeCoursCumule.map(item => item.annee),
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: [
            {
                title: {
                    text: 'Volume horaire (minutes)',
                    style: {
                        fontSize: '12px'
                    }
                },
                labels: {
                    formatter: function(val) {
                        return val + ' min';
                    }
                }
            }
        ],
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val + ' min';
            },
            style: {
                fontSize: '10px',
                fontWeight: 'bold'
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left'
        }
    }).render();

    // Gestion du changement d'année académique
    document.getElementById('annee_academique').addEventListener('change', function() {
        window.location.href = '{{ route("statistiques.index") }}?annee_id=' + this.value;
    });
});
</script>
@endpush
@endsection
