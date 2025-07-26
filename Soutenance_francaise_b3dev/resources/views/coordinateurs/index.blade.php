@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container mx-auto py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Liste des coordinateurs</h1>
        <a href="{{ route('coordinateurs.create') }}" class="inline-flex items-center px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-base font-semibold rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouveau coordinateur
        </a>
    </div>
    @if(session('success'))
        <div class="mb-6 p-4 rounded bg-green-100 text-green-800 border border-green-200 flex items-center animate-fade-in">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    <div class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full bg-white border rounded-lg">
            <thead class="bg-gray-50 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 border text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Photo</th>
                    <th class="px-4 py-3 border text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom</th>
                    <th class="px-4 py-3 border text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 border text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Promotion</th>
                    <th class="px-4 py-3 border text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($coordinateurs as $coordinateur)
                    <tr class="hover:bg-indigo-50 transition">
                        <td class="px-4 py-3 border text-center">
                            @if($coordinateur->photo && Storage::disk('public')->exists($coordinateur->photo))
                                <img src="{{ asset('storage/'.$coordinateur->photo) }}" alt="Photo" class="w-12 h-12 rounded-full mx-auto border-2 border-indigo-200 shadow-sm object-cover">
                            @else
                                <div class="w-12 h-12 rounded-full mx-auto border-2 border-indigo-200 shadow-sm bg-indigo-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 border font-semibold text-gray-900">{{ $coordinateur->prenom }} {{ $coordinateur->nom }}</td>
                        <td class="px-4 py-3 border text-gray-600">{{ $coordinateur->user->email ?? '-' }}</td>
                        <td class="px-4 py-3 border">
                            @if($coordinateur->promotion)
                                <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">{{ $coordinateur->promotion->nom }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 border text-center space-x-1">
                            <a href="{{ route('coordinateurs.show', $coordinateur->id) }}" class="inline-flex items-center justify-center bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-full p-2 transition" title="Voir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>
                            <a href="{{ route('coordinateurs.edit', $coordinateur->id) }}" class="inline-flex items-center justify-center bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-full p-2 transition" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2l-6 6m2 2l-6 6m2 2l6-6" /></svg>
                            </a>
                            <form action="{{ route('coordinateurs.destroy', $coordinateur->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer ce coordinateur ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center bg-red-100 hover:bg-red-200 text-red-700 rounded-full p-2 transition" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-400 py-8">Aucun coordinateur trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-8 flex justify-center">
        {{ $coordinateurs->links() }}
    </div>
</div>
@endsection
