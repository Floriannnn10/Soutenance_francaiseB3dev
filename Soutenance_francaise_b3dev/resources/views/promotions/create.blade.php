@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Créer une promotion</h1>
    <form action="{{ route('promotions.store') }}" method="POST" class="max-w-md bg-white p-6 rounded shadow">
        @csrf
        <div class="mb-4">
            <label for="nom" class="block text-sm font-medium text-gray-700">Nom de la promotion</label>
            <input type="text" name="nom" id="nom" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('nom') }}" required>
            @error('nom')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Créer</button>
        <a href="{{ route('promotions.index') }}" class="ml-4 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>
@endsection
