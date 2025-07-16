<x-app-layout>
    <div class="flex h-screen bg-gray-100">
        <main class="flex-1 overflow-y-auto p-8">
            <!-- Messages de succès et d'erreur -->
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

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Liste des matières</h1> <a
                    href="{{ route('matieres.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg> Nouvelle matière </a>
            </div>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coefficient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume horaire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignants</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($matieres as $matiere)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $matiere->nom }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $matiere->code ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $matiere->coefficient ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $matiere->volume_horaire ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                    @foreach($matiere->enseignants as $enseignant)
                                        {{ $enseignant->nom }} {{ $enseignant->prenom }},
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2"> 
                                    <a href="{{ route('matieres.show', $matiere) }}"
                                        class="inline-flex items-center px-2 py-1 text-xs text-blue-600 hover:underline">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg> Voir
                                    </a>
                                    <a
                                        href="{{ route('matieres.edit', $matiere) }}"
                                        class="inline-flex items-center px-2 py-1 text-xs text-yellow-600 hover:underline">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536M9 13h3l8-8a2.828 2.828 0 10-4-4l-8 8v3z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7l-1.5-1.5" />
                                        </svg> Éditer </a>
                                    <form action="{{ route('matieres.destroy', $matiere) }}" method="POST"
                                        class="inline"> @csrf @method('DELETE') <button type="submit"
                                            class="inline-flex items-center px-2 py-1 text-xs text-red-600 hover:underline"
                                            onclick="return confirm('Supprimer cette matière ?')"> <svg
                                                class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg> Supprimer </button> </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucune matière trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4"> {{ $matieres->links() }} </div>
            </div>
        </main>
    </div>
</x-app-layout>
