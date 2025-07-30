<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord - Étudiant') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informations de l'étudiant -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-16 w-16">
                            @if($etudiant->photo)
                                <img class="h-16 w-16 rounded-full" src="{{ asset('storage/' . $etudiant->photo) }}" alt="">
                            @else
                                <div class="h-16 w-16 rounded-full bg-blue-200 flex items-center justify-center">
                                    <span class="text-xl font-semibold text-blue-800">
                                        {{ strtoupper(substr($etudiant->prenom, 0, 1) . substr($etudiant->nom, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-6">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $etudiant->prenom }} {{ $etudiant->nom }}</h3>
                            <p class="text-gray-600">{{ $etudiant->classe->nom ?? 'Classe non définie' }}</p>
                            <p class="text-sm text-gray-500">{{ $etudiant->email ?? 'Email non renseigné' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emploi du temps de la semaine -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">📅 Emploi du temps de la semaine</h3>
                    @if($sessions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($sessions as $session)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-semibold text-gray-900">{{ $session->matiere->nom }}</h4>
                                        <span class="text-sm text-gray-500">{{ $session->start_time->format('d/m/Y') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</p>
                                    <p class="text-sm text-gray-500">{{ $session->enseignant->prenom }} {{ $session->enseignant->nom }}</p>
                                    <span class="inline-block px-2 py-1 text-xs rounded-full
                                        @if($session->typeCours->code === 'presentiel') bg-blue-100 text-blue-800
                                        @elseif($session->typeCours->code === 'workshop') bg-green-100 text-green-800
                                        @else bg-purple-100 text-purple-800 @endif">
                                        {{ $session->typeCours->nom }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Aucune session de cours cette semaine.</p>
                    @endif
                </div>
            </div>

            <!-- Liens rapides -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('emplois-du-temps.etudiant') }}" class="bg-blue-500 hover:bg-blue-700 text-white p-6 rounded-lg text-center">
                    <div class="text-2xl mb-2">📋</div>
                    <h3 class="font-semibold">Emploi du temps</h3>
                    <p class="text-sm opacity-90">Voir mon emploi du temps complet</p>
                </a>

                <a href="{{ route('presences.etudiant') }}" class="bg-green-500 hover:bg-green-700 text-white p-6 rounded-lg text-center">
                    <div class="text-2xl mb-2">✅</div>
                    <h3 class="font-semibold">Mes présences</h3>
                    <p class="text-sm opacity-90">Consulter mon historique de présence</p>
                </a>

                <a href="{{ route('profile.edit') }}" class="bg-purple-500 hover:bg-purple-700 text-white p-6 rounded-lg text-center">
                    <div class="text-2xl mb-2">👤</div>
                    <h3 class="font-semibold">Mon profil</h3>
                    <p class="text-sm opacity-90">Modifier mes informations</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Notification des matières droppées -->
    @if($matieresDropped->count() > 0)
        <script>
            // Attendre 30 secondes avant d'afficher la notification
            setTimeout(function() {
                @foreach($matieresDropped as $drop)
                    // Afficher un toast pour chaque matière droppée
                    if (typeof window.toast !== 'undefined' && typeof window.toast.error === 'function') {
                        try {
                            window.toast.error(
                                '⚠️ Vous avez abandonné la matière "{{ $drop->matiere->nom }}" le {{ $drop->date_drop->format("d/m/Y") }}. ' +
                                'Vous devrez la reprendre l\'année suivante.',
                                {
                                    duration: 10000, // 10 secondes
                                    position: 'top-right'
                                }
                            );
                        } catch (error) {
                            console.error('Erreur lors de l\'affichage du toast:', error);
                            alert('⚠️ Vous avez abandonné la matière "{{ $drop->matiere->nom }}" le {{ $drop->date_drop->format("d/m/Y") }}. Vous devrez la reprendre l\'année suivante.');
                        }
                    } else {
                        // Fallback si toast n'est pas disponible
                        console.warn('Sonner toast non disponible, utilisation du fallback alert');
                        alert('⚠️ Vous avez abandonné la matière "{{ $drop->matiere->nom }}" le {{ $drop->date_drop->format("d/m/Y") }}. Vous devrez la reprendre l\'année suivante.');
                    }
                @endforeach
            }, 30000); // 30 secondes
        </script>
    @endif
</x-app-layout>
