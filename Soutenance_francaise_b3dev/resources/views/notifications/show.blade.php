<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails de la notification') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">📢 Notification</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-700 mb-2">Message</h4>
                                <p class="text-gray-900 bg-gray-50 p-3 rounded">{{ $notification->message }}</p>
                            </div>

                            <div>
                                <h4 class="font-medium text-gray-700 mb-2">Type</h4>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($notification->type === 'warning') bg-yellow-100 text-yellow-800
                                    @elseif($notification->type === 'success') bg-green-100 text-green-800
                                    @elseif($notification->type === 'error') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $notification->type }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="font-medium text-gray-700 mb-2">Utilisateurs concernés</h4>
                            <div class="bg-gray-50 p-3 rounded">
                                @if($notification->utilisateurs->count() > 0)
                                    <ul class="space-y-1">
                                        @foreach($notification->utilisateurs as $utilisateur)
                                            <li class="text-sm text-gray-900">
                                                {{ $utilisateur->prenom }} {{ $utilisateur->nom }} ({{ $utilisateur->email }})
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500">Aucun utilisateur assigné</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="font-medium text-gray-700 mb-2">Informations</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Créée le:</span>
                                    <span class="ml-2 text-gray-900">{{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Modifiée le:</span>
                                    <span class="ml-2 text-gray-900">{{ \Carbon\Carbon::parse($notification->updated_at)->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('notifications.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Retour
                        </a>
                        <a href="{{ route('notifications.edit', $notification) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Modifier
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
