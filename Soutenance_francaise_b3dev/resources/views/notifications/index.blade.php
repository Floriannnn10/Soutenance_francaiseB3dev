<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">📢 Notifications</h3>
                        <a href="{{ route('notifications.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Créer une notification
                        </a>
                    </div>

                    @if($notifications->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateurs</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($notifications as $notification)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $notification->message }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($notification->type === 'warning') bg-yellow-100 text-yellow-800
                                                    @elseif($notification->type === 'success') bg-green-100 text-green-800
                                                    @elseif($notification->type === 'error') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $notification->type }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $notification->utilisateurs->count() }} utilisateur(s)
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('notifications.show', $notification) }}" class="text-blue-600 hover:text-blue-900">Voir</a>
                                                    <a href="{{ route('notifications.edit', $notification) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                                    <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')">
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-6H4v6zM4 5h6V4H4v1zM4 11h6v-1H4v1zM4 17h6v-1H4v1zM10 5h10V4H10v1zM10 11h10v-1H10v1zM10 17h10v-1H10v1z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune notification</h3>
                                <p class="mt-1 text-sm text-gray-500">Aucune notification n'a été créée.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
