@php
use Illuminate\Support\Facades\Storage;
@endphp
<x-app-layout>
    <div class="bg-white rounded-lg shadow p-8">
        @if(session('success'))
            <div class="mb-6 p-4 rounded bg-green-100 text-green-800 border border-green-200 flex items-center animate-fade-in">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded bg-red-100 text-red-800 border border-red-200 flex items-center animate-fade-in">
                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Liste des utilisateurs</h1>
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-base font-semibold rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvel utilisateur
            </a>
        </div>
        <!-- Filtres -->
        <form method="GET" action="{{ route('users.index') }}" class="mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Barre de recherche -->
                <div class="relative max-w-xs">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4-4m0 0A7 7 0 1010 17a7 7 0 007-7z" />
                        </svg>
                    </span>
                </div>

                <!-- Filtre par rôle -->
                <div class="max-w-xs">
                    <select name="role_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->nom) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Boutons d'action -->
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition">
                        Filtrer
                    </button>
                    <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-md transition">
                        Réinitialiser
                    </a>
                </div>
            </div>
        </form>

        <!-- Indicateur de filtre actif -->
        @if(request('search') || request('role_id'))
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                        </svg>
                        <span class="text-sm font-medium text-blue-700">Filtres actifs :</span>
                        @if(request('search'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Recherche: "{{ request('search') }}"
                            </span>
                        @endif
                        @if(request('role_id'))
                            @php
                                $selectedRole = $roles->find(request('role_id'));
                            @endphp
                            @if($selectedRole)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Rôle: {{ ucfirst($selectedRole->nom) }}
                                </span>
                            @endif
                        @endif
                    </div>
                    <a href="{{ route('users.index') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">
                        Effacer tous les filtres
                    </a>
                </div>
            </div>
        @endif

        <!-- Compteur et tableau des utilisateurs -->
        <div class="mb-4 text-sm text-gray-600">
            Affichage de {{ $users->firstItem() ?? 0 }} à {{ $users->lastItem() ?? 0 }} sur {{ $users->total() }} utilisateur(s)
            @if(request('search') || request('role_id'))
                (filtré)
            @endif
        </div>

        <div class="overflow-x-auto rounded-lg shadow">
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
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $user->nom }} {{ $user->prenom }}</td>
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

    <script>
        // Auto-submit du formulaire quand le rôle change
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.querySelector('select[name="role_id"]');
            if (roleSelect) {
                roleSelect.addEventListener('change', function() {
                    // Ne pas auto-submit si aucun rôle n'est sélectionné
                    if (this.value !== '') {
                        this.closest('form').submit();
                    }
                });
            }
        });
    </script>
</x-app-layout>
