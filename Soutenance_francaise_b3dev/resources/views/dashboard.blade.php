@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-8">Tableau de bord</h1>



        <!-- Contenu existant -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Statistiques -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                <div class="flex items-center">
                    <div class="p-3 bg-white bg-opacity-20 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm opacity-75">Total Utilisateurs</p>
                        <p class="text-2xl font-bold">{{ $totalUsers ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                <div class="flex items-center">
                    <div class="p-3 bg-white bg-opacity-20 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm opacity-75">Sessions de Cours</p>
                        <p class="text-2xl font-bold">{{ $totalSessions ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                <div class="flex items-center">
                    <div class="p-3 bg-white bg-opacity-20 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm opacity-75">Présences</p>
                        <p class="text-2xl font-bold">{{ $totalPresences ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-6 text-white">
                <div class="flex items-center">
                    <div class="p-3 bg-white bg-opacity-20 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm opacity-75">Taux de Présence</p>
                        <p class="text-2xl font-bold">{{ number_format($tauxPresence ?? 0, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="mt-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions rapides</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('sessions-de-cours.create') }}" class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">Nouvelle Session</h3>
                        <p class="text-sm text-gray-600">Créer une session de cours</p>
                    </div>
                </a>

                <a href="{{ route('presences.index') }}" class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">Présences</h3>
                        <p class="text-sm text-gray-600">Gérer les présences</p>
                    </div>
                </a>

                <a href="{{ route('statistiques.index') }}" class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">Statistiques</h3>
                        <p class="text-sm text-gray-600">Voir les statistiques</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>

</script>
@endsection

