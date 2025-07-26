@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-lg mx-auto bg-white rounded shadow p-8">
        <h1 class="text-2xl font-bold mb-6 text-center">Modifier le coordinateur</h1>
        <form action="{{ route('coordinateurs.update', $coordinateur->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="flex flex-col items-center mb-6">
                <img src="{{ $coordinateur->photo ? asset('storage/'.$coordinateur->photo) : asset('images/default-avatar.png') }}" alt="Photo" class="w-24 h-24 rounded-full mb-2 border-4 border-blue-200">
                <label for="photo" class="block text-sm font-medium text-gray-700">Changer la photo</label>
                <input type="file" name="photo" id="photo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*">
                @error('photo')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $coordinateur->prenom) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                @error('prenom')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom', $coordinateur->nom) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                @error('nom')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $coordinateur->user->email ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                @error('email')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe (optionnel)</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" minlength="8">
                <p class="text-xs text-gray-500 mt-1">Laissez vide pour ne pas changer le mot de passe. Minimum 8 caractères.</p>
                @error('password')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" minlength="8">
                @error('password_confirmation')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="promotion_id" class="block text-sm font-medium text-gray-700">Promotion</label>
                <select name="promotion_id" id="promotion_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Sélectionner une promotion</option>
                    @foreach($promotions as $promotion)
                        <option value="{{ $promotion->id }}" {{ (old('promotion_id', $coordinateur->promotion_id) == $promotion->id) ? 'selected' : '' }}>{{ $promotion->nom }}</option>
                    @endforeach
                </select>
                @error('promotion_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex justify-end gap-4 mt-8">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold shadow">Enregistrer</button>
                <a href="{{ route('coordinateurs.show', $coordinateur->id) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded font-semibold shadow">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
