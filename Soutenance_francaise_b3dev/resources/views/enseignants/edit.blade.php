<x-app-layout>
    <div class="max-w-xl mx-auto py-10">
        <div class="bg-white rounded-lg shadow p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Modifier l'enseignant</h1>
            @if($errors->any())
                <div class="mb-6 p-4 rounded bg-red-100 text-red-800 border border-red-200 flex items-center animate-fade-in">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <div>
                        <p class="font-medium">Erreurs de validation :</p>
                        <ul class="mt-1 text-sm">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            <form method="POST" action="{{ route('enseignants.update', $enseignant) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                {{-- Champ utilisateur associé supprimé --}}
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom', $enseignant->nom) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nom de famille">
                    @error('nom')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                    <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $enseignant->prenom) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Prénom">
                    @error('prenom')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $enseignant->user->email ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="email@example.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe (optionnel)</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" minlength="8">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour ne pas changer le mot de passe. Minimum 8 caractères.</p>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" minlength="8">
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="matieres" class="block text-sm font-medium text-gray-700">Matières enseignées</label>
                    <select name="matieres[]" id="matieres" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}" {{ in_array($matiere->id, old('matieres', $enseignant->matieres->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $matiere->nom }} ({{ $matiere->code }}) - Coef: {{ $matiere->coefficient }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélectionner plusieurs matières.</p>
                </div>
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
                    <input type="file" name="photo" id="photo" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @if($enseignant->photo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $enseignant->photo) }}" alt="Photo actuelle" class="w-20 h-20 object-cover rounded">
                            <p class="text-xs text-gray-500 mt-1">Photo actuelle</p>
                        </div>
                    @endif
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés : JPG, PNG, GIF. Taille max : 2MB</p>
                    @error('photo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('enseignants.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-md mr-2">Annuler</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md shadow transition">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
