<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une notification') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('notifications.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea name="message" id="message" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="type" id="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Sélectionner un type</option>
                                <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>Information</option>
                                <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>Succès</option>
                                <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>Avertissement</option>
                                <option value="error" {{ old('type') == 'error' ? 'selected' : '' }}>Erreur</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="utilisateurs" class="block text-sm font-medium text-gray-700">Utilisateurs</label>
                            <select name="utilisateurs[]" id="utilisateurs" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                @foreach($utilisateurs as $utilisateur)
                                    <option value="{{ $utilisateur->id }}" {{ in_array($utilisateur->id, old('utilisateurs', [])) ? 'selected' : '' }}>
                                        {{ $utilisateur->prenom }} {{ $utilisateur->nom }} ({{ $utilisateur->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('utilisateurs')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('notifications.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Créer la notification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
