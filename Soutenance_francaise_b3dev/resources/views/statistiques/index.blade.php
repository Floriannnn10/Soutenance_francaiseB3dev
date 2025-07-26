@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-4">Statistiques</h2>

        <!-- Sélecteur d'année académique -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700">Année Académique</label>
            <select id="annee_academique" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @foreach($anneeAcademiques as $annee)
                    <option value="{{ $annee->id }}" {{ $annee->id === $anneeSelectionnee->id ? 'selected' : '' }}>
                        {{ $annee->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Graphiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Taux de présence par étudiant -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Taux de présence par étudiant</h3>
                <div id="taux-presence-etudiants"></div>
        </div>

            <!-- Taux de présence par classe -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Taux de présence par classe</h3>
                <div id="taux-presence-classes"></div>
            </div>

            <!-- Volume de cours par type -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Volume de cours par type</h3>
                <div id="volume-cours-type"></div>
        </div>

            <!-- Volume de cours cumulé -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Volume de cours cumulé</h3>
                <div id="volume-cours-cumule"></div>
        </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Taux de présence par étudiant
    const tauxPresenceEtudiants = @json($tauxPresenceEtudiants);
    new ApexCharts(document.querySelector("#taux-presence-etudiants"), {
        chart: {
            type: 'bar',
            height: 400
        },
        plotOptions: {
            bar: {
                horizontal: true,
                distributed: true
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
                }
            }
        },
        yaxis: {
            max: 100
        }
    }).render();

    // Taux de présence par classe
    const tauxPresenceClasses = @json($tauxPresenceClasses);
    new ApexCharts(document.querySelector("#taux-presence-classes"), {
        chart: {
            type: 'bar',
            height: 400
        },
        series: [{
            name: 'Taux de présence',
            data: tauxPresenceClasses.map(item => item.taux)
        }],
        xaxis: {
            categories: tauxPresenceClasses.map(item => item.classe),
            labels: {
                formatter: function(val) {
                    return val + '%';
                }
            }
        },
        yaxis: {
            max: 100
        }
    }).render();

    // Volume de cours par type
    const volumeCoursParType = @json($volumeCoursParType);
    new ApexCharts(document.querySelector("#volume-cours-type"), {
        chart: {
            type: 'pie',
            height: 400
        },
        series: volumeCoursParType.map(item => item.volume),
        labels: volumeCoursParType.map(item => item.type),
        legend: {
            position: 'bottom'
        }
    }).render();

    // Volume de cours cumulé
    const volumeCoursCumule = @json($volumeCoursCumule);
    new ApexCharts(document.querySelector("#volume-cours-cumule"), {
        chart: {
            type: 'line',
            height: 400
        },
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
        xaxis: {
            categories: volumeCoursCumule.map(item => item.annee)
        },
        yaxis: [
            {
                title: {
                    text: 'Volume horaire'
                }
            }
        ]
    }).render();

    // Gestion du changement d'année académique
    document.getElementById('annee_academique').addEventListener('change', function() {
        window.location.href = '{{ route("statistiques.index") }}?annee_id=' + this.value;
    });
});
</script>
@endpush
@endsection
