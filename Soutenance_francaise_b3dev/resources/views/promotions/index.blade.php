@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-8">Gestion des promotions</h1>
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif
    <a href="{{ route('promotions.create') }}" class="mb-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Ajouter une promotion</a>
    <table class="min-w-full bg-white border rounded-lg overflow-hidden shadow">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">Nom</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($promotions as $promotion)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border font-semibold">{{ $promotion->nom }}</td>
                    <td class="px-4 py-2 border text-center">
                        <a href="{{ route('promotions.edit', $promotion->id) }}" class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-xs">Modifier</a>
                        <form action="{{ route('promotions.destroy', $promotion->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cette promotion ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center text-gray-500 py-8">Aucune promotion trouvée.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
