<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Erreur - Accès Parent') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="text-6xl mb-4">⚠️</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">
                        {{ $message }}
                    </h3>

                    @if(isset($user_info))
                        <div class="bg-gray-50 rounded-lg p-4 mt-6 text-left">
                            <h4 class="font-semibold text-gray-900 mb-2">Informations utilisateur :</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <div><strong>ID :</strong> {{ $user_info['id'] }}</div>
                                <div><strong>Email :</strong> {{ $user_info['email'] ?? 'Non défini' }}</div>
                                <div><strong>Nom :</strong> {{ $user_info['nom'] ?? 'Non défini' }}</div>
                                <div><strong>Prénom :</strong> {{ $user_info['prenom'] ?? 'Non défini' }}</div>
                                @if(isset($user_info['roles']))
                                    <div><strong>Rôles :</strong> {{ implode(', ', $user_info['roles']) }}</div>
                                @endif
                                @if(isset($user_info['error']))
                                    <div><strong>Erreur :</strong> {{ $user_info['error'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 space-y-4">
                        <p class="text-gray-600">
                            Pour accéder aux fonctionnalités parent, vous devez :
                        </p>
                        <ul class="text-left text-gray-600 space-y-2 max-w-md mx-auto">
                            <li>• Avoir un compte utilisateur avec le rôle "Parent"</li>
                            <li>• Avoir un profil parent créé dans la base de données</li>
                            <li>• Être associé à au moins un étudiant</li>
                        </ul>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('dashboard') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                            Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
