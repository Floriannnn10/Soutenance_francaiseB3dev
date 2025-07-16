<x-app-layout>
    <div class="max-w-2xl mx-auto py-10">
        <div class="bg-white rounded-lg shadow p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Créer une matière</h1>
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
            <form method="POST" action="{{ route('matieres.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Code de la matière</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex : SVT, MATH, PHY...">
                </div>
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700">Nom de la matière</label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required autofocus class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="coefficient" class="block text-sm font-medium text-gray-700">Coefficient</label>
                    <input type="number" name="coefficient" id="coefficient" value="{{ old('coefficient') }}" required min="1" step="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex : 1, 2, 3...">
                </div>
                <div>
                    <label for="volume_horaire" class="block text-sm font-medium text-gray-700">Volume horaire</label>
                    <input type="number" name="volume_horaire" id="volume_horaire" value="{{ old('volume_horaire') }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex : 4, 6, 8...">
                    <p class="text-xs text-gray-500 mt-1">Nombre d'heures par semaine</p>
                </div>
                <div>
                    <label for="enseignants" class="block text-sm font-medium text-gray-700">Enseignants</label>
                    <select name="enseignants[]" id="enseignants" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($enseignants as $enseignant)
                            <option value="{{ $enseignant->id }}" {{ in_array($enseignant->id, old('enseignants', [])) ? 'selected' : '' }}>{{ $enseignant->prenom }} {{ $enseignant->nom }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélectionner plusieurs enseignants.</p>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('matieres.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-md mr-2">Annuler</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow transition">Créer</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
