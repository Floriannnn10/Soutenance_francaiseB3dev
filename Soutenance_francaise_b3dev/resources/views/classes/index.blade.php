<x-app-layout>
    <div class="flex h-screen bg-gray-100">
        <main class="flex-1 overflow-y-auto p-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Liste des classes</h1> <a
                    href="{{ route('classes.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg> Nouvelle classe </a>
            </div>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Niveau</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($classes as $classe)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $classe->nom }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $classe->niveau ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2"> <a
                                        href="{{ route('classes.edit', $classe) }}"
                                        class="inline-flex items-center px-2 py-1 text-xs text-yellow-600 hover:underline">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536M9 13h3l8-8a2.828 2.828 0 10-4-4l-8 8v3z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7l-1.5-1.5" />
                                        </svg> Éditer </a>
                                    <form action="{{ route('classes.destroy', $classe) }}" method="POST"
                                        class="inline"> @csrf @method('DELETE') <button type="submit"
                                            class="inline-flex items-center px-2 py-1 text-xs text-red-600 hover:underline"
                                            onclick="return confirm('Supprimer cette classe ?')"> <svg
                                                class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg> Supprimer </button> </form>
                                </td>
                        </tr> @empty <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">Aucune classe trouvée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4"> {{ $classes->links() }} </div>
            </div>
        </main>
    </div>
</x-app-layout>
