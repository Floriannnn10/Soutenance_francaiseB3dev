<x-app-layout>
    <div class="max-w-3xl mx-auto py-10">
        <div class="bg-white rounded-lg shadow p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Modifier l'utilisateur</h1>
            @if(session('success'))
                <div class="mb-4 p-4 rounded bg-green-100 text-green-800 border border-green-200 flex items-center animate-fade-in">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-4 rounded bg-red-100 text-red-800 border border-red-200 flex items-center animate-fade-in">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif
            <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $user->nom) }}" required autofocus class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $user->prenom) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700">Rôle</label>
                    <select name="role_id" id="role_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sélectionner un rôle</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->roles->first()->id ?? '') == $role->id ? 'selected' : '' }}>{{ ucfirst($role->nom) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe (optionnel)</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" minlength="6">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour ne pas changer le mot de passe. Minimum 6 caractères.</p>
                    @error('password')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" minlength="6">
                    @error('password_confirmation')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Photo (optionnelle)</label>
                    <input type="file" name="photo" id="photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @if($user->photo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo actuelle" class="h-16 w-16 rounded-full object-cover">
                        </div>
                    @endif
                </div>

                <!-- Champs conditionnels selon le rôle -->
                <div id="classe-field" style="display: none;">
                    <label for="classe_id" class="block text-sm font-medium text-gray-700">Classe (pour les étudiants)</label>
                    <select name="classe_id" id="classe_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ old('classe_id', $user->etudiant?->classe_id) == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="telephone-field" style="display: none;">
                    <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone (pour les parents)</label>
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $user->parent?->telephone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div id="promotion-field" style="display: none;">
                    <label for="promotion_id" class="block text-sm font-medium text-gray-700">Promotion (pour les coordinateurs)</label>
                    <select name="promotion_id" id="promotion_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sélectionner une promotion</option>
                        @foreach($promotions as $promotion)
                            <option value="{{ $promotion->id }}" {{ old('promotion_id', $user->coordinateur?->promotion_id) == $promotion->id ? 'selected' : '' }}>{{ $promotion->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="matieres-field" style="display: none;">
                    <label for="matieres" class="block text-sm font-medium text-gray-700">Matières enseignées (pour les enseignants)</label>
                    <select name="matieres[]" id="matieres" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}" {{ (collect(old('matieres', $user->enseignant?->matieres->pluck('id')))->contains($matiere->id)) ? 'selected' : '' }}>{{ $matiere->nom }} ({{ $matiere->code }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélectionner plusieurs matières.</p>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-md mr-2">Annuler</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow transition">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role_id');
            const classeField = document.getElementById('classe-field');
            const telephoneField = document.getElementById('telephone-field');
            const promotionField = document.getElementById('promotion-field');
            const matieresField = document.getElementById('matieres-field');

            function toggleFields() {
                const selected = roleSelect.options[roleSelect.selectedIndex]?.text?.toLowerCase();

                // Masquer tous les champs
                classeField.style.display = 'none';
                telephoneField.style.display = 'none';
                promotionField.style.display = 'none';
                matieresField.style.display = 'none';

                // Afficher les champs selon le rôle
                if(selected && (selected.includes('étudiant') || selected.includes('etudiant'))) {
                    classeField.style.display = '';
                } else if(selected && selected.includes('parent')) {
                    telephoneField.style.display = '';
                } else if(selected && selected.includes('coordinateur')) {
                    promotionField.style.display = '';
                } else if(selected && selected.includes('enseignant')) {
                    matieresField.style.display = '';
                }
            }

            // Initialiser l'affichage
            toggleFields();

            // Écouter les changements
            roleSelect.addEventListener('change', toggleFields);
        });
    </script>
</x-app-layout>
