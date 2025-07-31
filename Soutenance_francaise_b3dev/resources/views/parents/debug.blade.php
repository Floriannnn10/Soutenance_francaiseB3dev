<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Debug - Mes enfants') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Informations de debug</h3>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-yellow-800 mb-2">Problème détecté</h4>
                        <p class="text-yellow-700">L'utilisateur connecté n'a pas de profil parent associé dans la base de données.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold mb-2">Informations utilisateur</h4>
                            <ul class="space-y-1 text-sm">
                                <li><strong>ID:</strong> {{ $debugInfo['user_id'] }}</li>
                                <li><strong>Email:</strong> {{ $debugInfo['email'] }}</li>
                                <li><strong>Nom:</strong> {{ $debugInfo['nom'] }}</li>
                                <li><strong>Prénom:</strong> {{ $debugInfo['prenom'] }}</li>
                                <li><strong>Rôles:</strong> {{ implode(', ', $debugInfo['roles']) }}</li>
                            </ul>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-semibold mb-2">Solutions possibles</h4>
                            <ul class="space-y-2 text-sm">
                                <li>1. Vérifier que l'utilisateur a le rôle 'parent'</li>
                                <li>2. Créer un profil parent dans la table 'parents'</li>
                                <li>3. Associer des étudiants au parent</li>
                                <li>4. Vérifier la relation user_id dans la table parents</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-semibold text-green-800 mb-2">Actions recommandées</h4>
                        <div class="space-y-2">
                            <p class="text-green-700">Pour résoudre ce problème :</p>
                            <ol class="list-decimal list-inside space-y-1 text-sm text-green-700">
                                <li>Connectez-vous en tant qu'administrateur</li>
                                <li>Allez dans la section "Parents"</li>
                                <li>Créez ou modifiez le profil parent pour cet utilisateur</li>
                                <li>Associez les étudiants à ce parent</li>
                            </ol>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Retour au dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
