<x-app-layout>
    <div class="max-w-2xl mx-auto py-10">
        <div class="bg-white rounded-lg shadow p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Créer un coordinateur</h1>
            @if($errors->any())
                <div class="mb-4 p-4 rounded bg-red-100 text-red-800 border border-red-200 flex items-center animate-fade-in">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Veuillez corriger les erreurs ci-dessous.</span>
                </div>
            @endif
            <form action="{{ route('coordinateurs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                    <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('prenom')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('nom')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('email')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <input type="password" name="password" id="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" minlength="8">
                    <p class="text-xs text-gray-500 mt-1">Le mot de passe doit contenir au moins 8 caractères.</p>
                    @error('password')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" minlength="8">
                    @error('password_confirmation')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Photo (optionnel)</label>
                    <input type="file" name="photo" id="photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-700 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('photo')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="promotion_id" class="block text-sm font-medium text-gray-700">Promotion</label>
                    <select name="promotion_id" id="promotion_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sélectionner une promotion</option>
                        @foreach($promotions as $promotion)
                            <option value="{{ $promotion->id }}" {{ old('promotion_id') == $promotion->id ? 'selected' : '' }}>{{ $promotion->nom }}</option>
                        @endforeach
                    </select>
                    @error('promotion_id')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end gap-4 mt-8">
                    <a href="{{ route('coordinateurs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-md">Annuler</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow transition">Créer</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
