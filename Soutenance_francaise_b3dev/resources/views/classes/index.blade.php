<x-app-layout>
    <div class="flex h-screen bg-gray-100">
        <main class="flex-1 overflow-y-auto p-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold mb-8">Liste des classes</h1>
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif
                <a
                    href="{{ route('classes.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg> Nouvelle classe </a>
            </div>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">Nom</th>
                            <th class="px-4 py-2 border">Promotion</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $class)
                            <tr>
                                <td class="px-4 py-2 border">{{ $class->nom }}</td>
                                <td class="px-4 py-2 border">{{ $class->promotion ? $class->promotion->nom : '-' }}</td>
                                <td class="px-4 py-2 border">
                                    <a href="{{ route('classes.edit', $class->id) }}" class="text-yellow-600 hover:underline mr-2">Éditer</a>
                                    <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cette classe ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4"> {{ $classes->links() }} </div>
            </div>
        </main>
    </div>
</x-app-layout>
