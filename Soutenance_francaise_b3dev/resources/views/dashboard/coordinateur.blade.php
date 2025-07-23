<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Coordinateur Pédagogique') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $coordinateur = auth()->user()->coordinateur;
                $promotion = $coordinateur?->promotion;
                $classes = $promotion ? $promotion->classes : collect();
            @endphp
            <div class="mb-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 flex items-center gap-6">
                    <img src="{{ $coordinateur && $coordinateur->photo ? asset('storage/'.$coordinateur->photo) : asset('images/default-avatar.png') }}" alt="Photo" class="w-16 h-16 rounded-full">
                    <div>
                        <div class="text-lg font-bold">{{ $coordinateur?->prenom }} {{ $coordinateur?->nom }}</div>
                        <div class="text-gray-600 dark:text-gray-300">Promotion : <span class="font-semibold">{{ $promotion?->nom ?? '-' }}</span></div>
                    </div>
                </div>
            </div>
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-2">Classes de la promotion</h3>
                @if($classes->count())
                    <ul class="list-disc list-inside">
                        @foreach($classes as $classe)
                            <li class="mb-1 font-medium text-blue-700">{{ $classe->nom }}</li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-gray-400">Aucune classe pour cette promotion</span>
                @endif
            </div>
            <!-- Le reste du dashboard (actions rapides, graphiques, etc.) peut rester inchangé ou être adapté pour n'utiliser que $classes -->
            @yield('dashboard_content')
        </div>
    </div>
</x-app-layout>


