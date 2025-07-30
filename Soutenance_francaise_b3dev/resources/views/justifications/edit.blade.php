<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Modifier la justification</h1>

                    <form method="POST" action="{{ route('justifications.update', $justification->id) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="motif" class="block text-sm font-medium text-gray-700">Motif</label>
                            <textarea name="motif" id="motif" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('motif', $justification->motif) }}</textarea>
                            @error('motif')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_justification" class="block text-sm font-medium text-gray-700">Date de justification</label>
                            <input type="date" name="date_justification" id="date_justification" value="{{ old('date_justification', \Carbon\Carbon::parse($justification->date_justification)->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('date_justification')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="piece_jointe" class="block text-sm font-medium text-gray-700">Nouvelle pièce jointe (optionnel)</label>
                            <input type="file" name="piece_jointe" id="piece_jointe" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('piece_jointe')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($justification->piece_jointe)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Pièce jointe actuelle</h3>
                            <a href="{{ asset('storage/' . $justification->piece_jointe) }}" target="_blank" class="text-blue-600 hover:underline">
                                Voir le document actuel
                            </a>
                        </div>
                        @endif

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('justifications.show', $justification->id) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Annuler</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
