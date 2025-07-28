<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier la Session de Cours') }}
            </h2>
            <a href="{{ route('sessions-de-cours.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages de succès/erreur -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($sessionDeCour)
                    <form action="{{ route('sessions-de-cours.update', $sessionDeCour->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Semestre -->
                            <div>
                                <label for="semestre_id" class="block text-sm font-medium text-gray-700">
                                    Semestre <span class="text-red-500">*</span>
                                </label>
                                <select name="semestre_id" id="semestre_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sélectionner un semestre</option>
                                    @foreach($semestres as $semestre)
                                        <option value="{{ $semestre->id }}"
                                                {{ $sessionDeCour->semester_id == $semestre->id ? 'selected' : '' }}>
                                            {{ $semestre->nom }} ({{ $semestre->anneeAcademique->nom ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('semestre_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Classe -->
                            <div>
                                <label for="classe_id" class="block text-sm font-medium text-gray-700">
                                    Classe <span class="text-red-500">*</span>
                                </label>
                                <select name="classe_id" id="classe_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sélectionner une classe</option>
                                    @foreach($classes as $classe)
                                        <option value="{{ $classe->id }}"
                                                {{ $sessionDeCour->classe_id == $classe->id ? 'selected' : '' }}>
                                            {{ $classe->nom }} {{ $classe->niveau ? '(' . $classe->niveau . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('classe_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Matière -->
                            <div>
                                <label for="matiere_id" class="block text-sm font-medium text-gray-700">
                                    Matière <span class="text-red-500">*</span>
                                </label>
                                <select name="matiere_id" id="matiere_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sélectionner une matière</option>
                                    @foreach($matieres as $matiere)
                                        <option value="{{ $matiere->id }}"
                                                {{ $sessionDeCour->matiere_id == $matiere->id ? 'selected' : '' }}>
                                            {{ $matiere->nom }}
                                            @if($matiere->coefficient)
                                                (Coef. {{ $matiere->coefficient }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('matiere_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Enseignant -->
                            <div>
                                <label for="enseignant_id" class="block text-sm font-medium text-gray-700">
                                    Enseignant <span class="text-red-500">*</span>
                                </label>
                                <select name="enseignant_id" id="enseignant_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sélectionner un enseignant</option>
                                    @foreach($enseignants as $enseignant)
                                        <option value="{{ $enseignant->id }}"
                                                {{ $sessionDeCour->enseignant_id == $enseignant->id ? 'selected' : '' }}
                                                data-role="{{ $enseignant->user?->roles->first()?->code ?? '' }}">
                                            {{ $enseignant->prenom }} {{ $enseignant->nom }}
                                            @if($enseignant->user?->roles->first()?->code === 'coordinateur')
                                                (Coordinateur)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('enseignant_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Type de Cours -->
                            <div>
                                <label for="type_cours_id" class="block text-sm font-medium text-gray-700">
                                    Type de Cours <span class="text-red-500">*</span>
                                </label>
                                <select name="type_cours_id" id="type_cours_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sélectionner un type</option>
                                    @foreach($typesCours as $type)
                                        <option value="{{ $type->id }}"
                                                {{ $sessionDeCour->type_cours_id == $type->id ? 'selected' : '' }}>
                                            {{ $type->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type_cours_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Statut -->
                            <div>
                                <label for="status_id" class="block text-sm font-medium text-gray-700">
                                    Statut <span class="text-red-500">*</span>
                                </label>
                                <select name="status_id" id="status_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sélectionner un statut</option>
                                    @foreach($statutsSession as $statut)
                                        <option value="{{ $statut->id }}"
                                                {{ $sessionDeCour->status_id == $statut->id ? 'selected' : '' }}>
                                            {{ $statut->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Heure de début -->
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700">
                                    Heure de Début <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="start_time" id="start_time" required
                                       value="{{ \Carbon\Carbon::parse($sessionDeCour->start_time)->format('Y-m-d\TH:i') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Heure de fin -->
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700">
                                    Heure de Fin <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="end_time" id="end_time" required
                                       value="{{ \Carbon\Carbon::parse($sessionDeCour->end_time)->format('Y-m-d\TH:i') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('end_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Lieu -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">
                                Lieu
                            </label>
                            <input type="text" name="location" id="location"
                                                                       value="{{ old('location', $sessionDeCour->location) }}"
                                   placeholder="Salle de classe, amphithéâtre, laboratoire..."
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                Notes/Commentaires
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                                                             placeholder="Informations supplémentaires sur la session..."
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $sessionDeCour->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="flex items-center justify-end space-x-4">
                                                         <a href="{{ route('sessions-de-cours.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                 Annuler
                             </a>
                             <button type="submit"
                                     class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                 <i class="fas fa-save mr-2"></i>Mettre à jour
                             </button>
                        </div>
                    </form>
                    @else
                        <div class="text-center py-8">
                            <p class="text-red-600 text-lg">Session de cours introuvable.</p>
                            <a href="{{ route('sessions-de-cours.index') }}" class="text-blue-600 hover:text-blue-800 mt-4 inline-block">
                                Retour à la liste des sessions
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeCoursSelect = document.getElementById('type_cours_id');
            const enseignantSelect = document.getElementById('enseignant_id');

            // Stocker toutes les options d'enseignants
            const allEnseignantOptions = Array.from(enseignantSelect.options);
            const currentEnseignantId = '{{ $sessionDeCour->enseignant_id }}';

            // Créer un élément pour afficher le message d'aide
            const helpMessage = document.createElement('div');
            helpMessage.className = 'mt-2 text-sm text-blue-600 bg-blue-50 p-2 rounded';
            helpMessage.style.display = 'none';
            enseignantSelect.parentNode.appendChild(helpMessage);

            function filterEnseignants() {
                const selectedType = typeCoursSelect.options[typeCoursSelect.selectedIndex]?.text?.toLowerCase();
                const selectedTypeId = typeCoursSelect.value;

                // Réinitialiser les options
                enseignantSelect.innerHTML = '<option value="">Sélectionner un enseignant</option>';

                if (selectedType && (selectedType.includes('workshop') || selectedType.includes('e-learning'))) {
                    // Pour Workshop et E-learning, afficher seulement les coordinateurs
                    let coordinateurCount = 0;
                    allEnseignantOptions.forEach(option => {
                        if (option.value && option.getAttribute('data-role') === 'coordinateur') {
                            const newOption = option.cloneNode(true);
                            enseignantSelect.appendChild(newOption);
                            coordinateurCount++;
                        }
                    });

                    // Afficher le message d'aide
                    helpMessage.textContent = `Pour les cours de type "${typeCoursSelect.options[typeCoursSelect.selectedIndex].text}", seuls les coordinateurs pédagogiques peuvent être sélectionnés comme enseignants.`;
                    helpMessage.style.display = 'block';

                    // Si aucun coordinateur n'est disponible
                    if (coordinateurCount === 0) {
                        helpMessage.textContent = 'Aucun coordinateur pédagogique disponible. Veuillez d\'abord créer un coordinateur.';
                        helpMessage.className = 'mt-2 text-sm text-red-600 bg-red-50 p-2 rounded';
                    }
                } else {
                    // Pour les autres types, afficher tous les enseignants
                    allEnseignantOptions.forEach(option => {
                        if (option.value) {
                            const newOption = option.cloneNode(true);
                            enseignantSelect.appendChild(newOption);
                        }
                    });

                    // Masquer le message d'aide
                    helpMessage.style.display = 'none';
                }

                // Restaurer la sélection actuelle si elle est valide
                if (currentEnseignantId && enseignantSelect.querySelector(`option[value="${currentEnseignantId}"]`)) {
                    enseignantSelect.value = currentEnseignantId;
                }
            }

            // Appliquer le filtre au chargement et lors du changement
            filterEnseignants();
            typeCoursSelect.addEventListener('change', filterEnseignants);
        });
    </script>
</x-app-layout>
