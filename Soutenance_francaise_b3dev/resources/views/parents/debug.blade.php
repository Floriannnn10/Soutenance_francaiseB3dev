<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Debug - Informations utilisateur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">🔍 Informations de l'utilisateur connecté</h3>

                    <div class="bg-gray-100 p-4 rounded-lg">
                        <h4 class="font-medium mb-2">Données utilisateur:</h4>
                        <ul class="space-y-1 text-sm">
                            <li><strong>ID:</strong> {{ $debugInfo['user_id'] }}</li>
                            <li><strong>Email:</strong> {{ $debugInfo['email'] }}</li>
                            <li><strong>Nom:</strong> {{ $debugInfo['nom'] }}</li>
                            <li><strong>Prénom:</strong> {{ $debugInfo['prenom'] }}</li>
                            <li><strong>Rôles:</strong> {{ implode(', ', $debugInfo['roles']) }}</li>
                        </ul>
                    </div>

                    <div class="mt-6 bg-yellow-100 p-4 rounded-lg">
                        <h4 class="font-medium text-yellow-800 mb-2">⚠️ Problème détecté</h4>
                        <p class="text-yellow-700">
                            Cet utilisateur n'a pas d'enregistrement ParentEtudiant associé dans la base de données.
                        </p>
                        <p class="text-yellow-700 mt-2">
                            Cela peut arriver si l'utilisateur a été créé manuellement sans créer l'enregistrement ParentEtudiant correspondant.
                        </p>
                    </div>

                    <div class="mt-6 bg-blue-100 p-4 rounded-lg">
                        <h4 class="font-medium text-blue-800 mb-2">💡 Solutions possibles</h4>
                        <ul class="text-blue-700 space-y-1">
                            <li>• Se connecter avec un autre compte parent existant</li>
                            <li>• Créer l'enregistrement ParentEtudiant manuellement</li>
                            <li>• Utiliser le compte: <strong>babe@ifran.com</strong> / <strong>password</strong></li>
                        </ul>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
