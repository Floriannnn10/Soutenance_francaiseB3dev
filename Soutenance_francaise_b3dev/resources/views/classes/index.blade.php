<x-app-layout>
    <div class="bg-white rounded-lg shadow p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Liste des Classes</h1>
            <a href="{{ route('classes.create') }}" class="inline-flex items-center px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-base font-semibold rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvelle Classe
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
                        <th class="px-4 py-3 border text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom</th>
                        <th class="px-4 py-3 border text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Promotion</th>
                        <th class="px-4 py-3 border text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($classes as $classe)
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-4 py-3 border font-semibold text-gray-900">{{ $classe->nom }}</td>
                            <td class="px-4 py-3 border text-gray-600">{{ $classe->promotion->nom ?? 'N/A' }}</td>
                            <td class="px-4 py-3 border text-center space-x-1">
                                <a href="{{ route('classes.show', $classe) }}" class="inline-flex items-center justify-center bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-full p-2 transition" title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                <a href="{{ route('classes.edit', $classe) }}" class="inline-flex items-center justify-center bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-full p-2 transition" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2l-6 6m2 2l-6 6m2 2l6-6" /></svg>
                                </a>
                                <form action="{{ route('classes.destroy', $classe) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cette classe ?');">
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
                            <td colspan="3" class="text-center text-gray-400 py-8">Aucune classe trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
