<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier l\'abandon de matière') }}
            </h2>
            <a href="{{ route('etudiant-matiere-dropped.show', $drop->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Retour aux détails
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Informations actuelles -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Informations actuelles</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <strong>Étudiant:</strong> {{ $drop->etudiant->prenom }} {{ $drop->etudiant->nom }}
                            </div>
                            <div>
                                <strong>Matière:</strong> {{ $drop->matiere->nom }}
                            </div>
                            <div>
                                <strong>Année/Semestre:</strong> {{ $drop->anneeAcademique->nom }} - {{ $drop->semestre->nom }}
                            </div>
                        </div>
                    </div>

                    <form id="editForm" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Date d'abandon -->
                            <div>
                                <label for="date_drop" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date d'abandon <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="date_drop" name="date_drop" required
                                       value="{{ $drop->date_drop->format('Y-m-d') }}"
                                       max="{{ date('Y-m-d') }}"
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
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ $drop->raison_drop }}</textarea>
                            <div id="raison_error" class="text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('etudiant-matiere-dropped.show', $drop->id) }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Réinitialiser les erreurs
            document.querySelectorAll('[id$="_error"]').forEach(el => el.classList.add('hidden'));

            const formData = new FormData(this);

            fetch('{{ route("etudiant-matiere-dropped.update", $drop->id) }}', {
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

                    // Rediriger vers les détails
                    setTimeout(() => {
                        window.location.href = '{{ route("etudiant-matiere-dropped.show", $drop->id) }}';
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
                alert('Une erreur est survenue lors de la mise à jour');
            });
        });
    </script>
</x-app-layout>
