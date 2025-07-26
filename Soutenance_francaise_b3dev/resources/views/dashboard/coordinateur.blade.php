<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Coordinateur Pédagogique') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informations du coordinateur -->
            <div class="mb-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <div class="flex items-center gap-6">
                        @php
                            $photo = $coordinateur && $coordinateur->photo
                                ? asset('storage/'.$coordinateur->photo)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($coordinateur?->prenom . ' ' . $coordinateur?->nom);
            @endphp
                        <img src="{{ $photo }}" alt="Photo" class="w-16 h-16 rounded-full object-cover">
                    <div>
                            <div class="text-xl font-bold text-gray-900">{{ $coordinateur?->prenom }} {{ $coordinateur?->nom }}</div>
                            <div class="text-gray-600">Coordinateur Pédagogique</div>
                            @if($promotion)
                                <div class="text-blue-600 font-semibold mt-1">Promotion : {{ $promotion->nom }}</div>
                            @else
                                <div class="text-red-600 font-semibold mt-1">Aucune promotion assignée</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($promotion)
                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Étudiants</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_etudiants'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Classes</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_classes'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Sessions</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_sessions'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Taux Présence</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['taux_presence'] }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <a href="{{ route('emplois-du-temps.index') }}" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Emplois du temps</h3>
                                <p class="text-gray-600">Gérer les emplois du temps</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('justifications.index') }}" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Justifications</h3>
                                <p class="text-gray-600">{{ $stats['justifications_en_attente'] }} en attente</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('sessions-de-cours.index') }}" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Sessions de cours</h3>
                                <p class="text-gray-600">Gérer les sessions</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Classes de la promotion -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Classes de la promotion {{ $promotion->nom }}</h3>
                @if($classes->count())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($classes as $classe)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-semibold text-blue-700">{{ $classe->nom }}</h4>
                                    <p class="text-sm text-gray-600">{{ $classe->etudiants->count() }} étudiants</p>
                                </div>
                        @endforeach
                        </div>
                @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <p class="mt-2 text-gray-500">Aucune classe pour cette promotion</p>
                        </div>
                @endif
            </div>
            @else
                <!-- Message si aucune promotion assignée -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-yellow-800">Aucune promotion assignée</h3>
                    <p class="mt-1 text-yellow-700">Contactez l'administrateur pour vous assigner une promotion.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>


