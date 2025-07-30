<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Marquer un abandon de matière') }}
            </h2>
            <a href="{{ route('etudiant-matiere-dropped.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form id="dropForm" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Étudiant -->
                            <div>
                                <label for="etudiant_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Étudiant <span class="text-red-500">*</span>
                                </label>
                                <select id="etudiant_id" name="etudiant_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Sélectionner un étudiant</option>
                                    @foreach($etudiants as $etudiant)
                                        <option value="{{ $etudiant->id }}">
                                            {{ $etudiant->prenom }} {{ $etudiant->nom }} - {{ $etudiant->classe->nom ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="etudiant_error" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>

                            <!-- Matière -->
                            <div>
                                <label for="matiere_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Matière <span class="text-red-500">*</span>
                                </label>
                                <select id="matiere_id" name="matiere_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Sélectionner une matière</option>
                                    @foreach($matieres as $matiere)
                                        <option value="{{ $matiere->id }}">
                                            {{ $matiere->nom }} ({{ $matiere->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <div id="matiere_error" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>

                            <!-- Année académique -->
                            <div>
                                <label for="annee_academique_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Année académique <span class="text-red-500">*</span>
                                </label>
                                <select id="annee_academique_id" name="annee_academique_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Sélectionner une année</option>
                                    @foreach($anneesAcademiques as $annee)
                                        <option value="{{ $annee->id }}" {{ $annee->active ? 'selected' : '' }}>
                                            {{ $annee->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="annee_error" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>

                            <!-- Semestre -->
                            <div>
                                <label for="semestre_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Semestre <span class="text-red-500">*</span>
                                </label>
                                <select id="semestre_id" name="semestre_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Sélectionner un semestre</option>
                                    @foreach($semestres as $semestre)
                                        <option value="{{ $semestre->id }}" {{ $semestre->active ? 'selected' : '' }}>
                                            {{ $semestre->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="semestre_error" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>

                            <!-- Date d'abandon -->
                            <div>
                                <label for="date_drop" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date d'abandon <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="date_drop" name="date_drop" required
                                       max="{{ date('Y-m-d') }}"
                                       value="{{ date('Y-m-d') }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <div id="date_error" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>
                        </div>

                        <!-- Raison de l'abandon -->
                        <div>
                            <label for="raison_drop" class="block text-sm font-medium text-gray-700 mb-2">
                                Raison de l'abandon (optionnel)
                            </label>
                            <textarea id="raison_drop" name="raison_drop" rows="4"
                                      placeholder="Expliquez la raison de l'abandon de cette matière..."
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                            <div id="raison_error" class="text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('etudiant-matiere-dropped.index') }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Marquer l'abandon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('dropForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Réinitialiser les erreurs
            document.querySelectorAll('[id$="_error"]').forEach(el => el.classList.add('hidden'));

            const formData = new FormData(this);

            fetch('{{ route("etudiant-matiere-dropped.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Afficher un toast de succès
                    if (typeof toast !== 'undefined') {
                        toast.success(data.message);
                    } else {
                        alert(data.message);
                    }

                    // Rediriger vers la liste
                    setTimeout(() => {
                        window.location.href = '{{ route("etudiant-matiere-dropped.index") }}';
                    }, 1000);
                } else {
                    // Afficher les erreurs
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorElement = document.getElementById(field + '_error');
                            if (errorElement) {
                                errorElement.textContent = data.errors[field][0];
                                errorElement.classList.remove('hidden');
                            }
                        });
                    } else {
                        alert(data.message || 'Une erreur est survenue');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de l\'enregistrement');
            });
        });
    </script>
</x-app-layout>
