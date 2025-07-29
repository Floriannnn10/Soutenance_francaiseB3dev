@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-6">Tableau de bord Étudiant</h2>

    <!-- Emploi du temps -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4">Mon emploi du temps</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 border">Horaire</th>
                        <th class="py-2 px-4 border">Lundi</th>
                        <th class="py-2 px-4 border">Mardi</th>
                        <th class="py-2 px-4 border">Mercredi</th>
                        <th class="py-2 px-4 border">Jeudi</th>
                        <th class="py-2 px-4 border">Vendredi</th>
                        <th class="py-2 px-4 border">Samedi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($emploiDuTemps ?? [] as $creneau)
                    <tr>
                        <td class="py-2 px-4 border">{{ $creneau['horaire'] }}</td>
                        @foreach(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'] as $jour)
                            <td class="py-2 px-4 border">
                                @if(isset($creneau[$jour]))
                                    <div class="p-2 rounded
                                        @if($creneau[$jour]['type'] === 'presentiel')
                                            bg-blue-100
                                        @elseif($creneau[$jour]['type'] === 'e-learning')
                                            bg-green-100
                                        @else
                                            bg-yellow-100
                                        @endif
                                    ">
                                        <p class="font-semibold">{{ $creneau[$jour]['matiere'] }}</p>
                                        <p class="text-sm">{{ $creneau[$jour]['enseignant'] }}</p>
                                        <p class="text-xs text-gray-600">{{ $creneau[$jour]['type'] }}</p>
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Statistiques de présence -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Taux de présence global -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Mon taux de présence</h3>
            <div class="flex items-center justify-center">
                <div class="relative w-32 h-32">
                    <svg class="w-full h-full" viewBox="0 0 36 36">
                        <path d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none"
                            stroke="#eee"
                            stroke-width="3"
                        />
                        <path d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none"
                            stroke="{{ ($tauxPresence ?? 0) >= 70 ? '#15803d' : (($tauxPresence ?? 0) >= 50 ? '#22c55e' : (($tauxPresence ?? 0) >= 30 ? '#f97316' : '#ef4444')) }}"
                            stroke-width="3"
                            stroke-dasharray="{{ $tauxPresence ?? 0 }}, 100"
                        />
                    </svg>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center">
                        <span class="text-2xl font-bold">{{ number_format($tauxPresence ?? 0, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des absences -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Historique des absences</h3>
            <div class="space-y-4">
                @forelse($absences ?? [] as $absence)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div>
                            <p class="font-medium">{{ $absence->sessionDeCours->matiere->nom }}</p>
                            <p class="text-sm text-gray-600">{{ $absence->sessionDeCours->start_time ? $absence->sessionDeCours->start_time->format('d/m/Y H:i') : 'Date non disponible' }}</p>
                        </div>
                        <div>
                            @if($absence->justification)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Justifiée</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Non justifiée</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">Aucune absence enregistrée</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Comparaison avec les années précédentes -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Évolution du taux de présence</h3>
        <div id="chartEvolution"></div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const evolutionData = @json($evolutionPresence ?? []);

    if (evolutionData.length > 0) {
        new ApexCharts(document.querySelector("#chartEvolution"), {
            series: [{
                name: 'Taux de présence',
                data: evolutionData.map(item => item.taux)
            }],
            chart: {
                type: 'line',
                height: 350
            },
            xaxis: {
                categories: evolutionData.map(item => item.annee)
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return val + "%"
                    }
                }
            },
            markers: {
                size: 6
            },
            stroke: {
                curve: 'smooth'
            }
        }).render();
    }
});
</script>
@endpush
@endsection
