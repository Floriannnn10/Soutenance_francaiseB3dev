@php
use Illuminate\Support\Facades\Storage;
@endphp
<x-app-layout>
    <div class="py-8 px-8 max-w-7xl mx-auto">
        @if(session('success'))
            <div class="mb-4 p-4 rounded bg-green-100 text-green-800 border border-green-200 flex items-center animate-fade-in">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 rounded bg-red-100 text-red-800 border border-red-200 flex items-center animate-fade-in">
                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Liste des utilisateurs</h1>
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvel utilisateur
            </a>
        </div>
        <!-- Barre de recherche -->
        <form method="GET" action="{{ route('users.index') }}" class="mb-4">
            <div class="relative max-w-xs">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <span class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4-4m0 0A7 7 0 1010 17a7 7 0 007-7z" />
                    </svg>
                </span>
            </div>
        </form>
        <!-- Tableau des utilisateurs -->
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->photo && Storage::disk('public')->exists($user->photo))
                                <img src="{{ asset('storage/'.$user->photo) }}" alt="Photo" class="w-10 h-10 rounded-full object-cover border">
                            @else
                                <span class="inline-block w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 capitalize">{{ $user->roles->first()->nom ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                            <a href="{{ route('users.show', $user) }}" class="inline-flex items-center px-2 py-1 text-xs text-indigo-600 hover:underline">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Voir
                            </a>
                            <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-2 py-1 text-xs text-yellow-600 hover:underline">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13h3l8-8a2.828 2.828 0 10-4-4l-8 8v3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7l-1.5-1.5" />
                                </svg>
                                Éditer
                            </a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-2 py-1 text-xs text-red-600 hover:underline" onclick="return confirm('Supprimer cet utilisateur ?')">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun utilisateur trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
