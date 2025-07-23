<x-app-layout>
    <div class="py-8 px-8 max-w-7xl mx-auto">
        <!-- Header et statistiques -->
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Tableau de bord</h1>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow flex items-center p-6">
                <div class="bg-indigo-100 p-3 rounded-full mr-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Années académiques</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $nbAnnees }}</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow flex items-center p-6">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Semestre</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $nbSemestres }}</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow flex items-center p-6">
                <div class="bg-purple-100 p-3 rounded-full mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4V6a4 4 0 00-8 0v4m8 0a4 4 0 01-8 0"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Coordinateurs</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $nbCoordinateurs }}</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow flex items-center p-6">
                <div class="bg-teal-100 p-3 rounded-full mr-4">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4V6a4 4 0 00-8 0v4m8 0a4 4 0 01-8 0"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Utilisateurs</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $nbUtilisateurs }}</div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="bg-white rounded-lg shadow p-6 mb-10">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides - Administrateur</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('annees-academiques.index') }}" class="flex items-center p-3 rounded-lg bg-indigo-50 hover:bg-indigo-100 transition"><svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Années académiques</a>
                <a href="{{ route('semestres.index') }}" class="flex items-center p-3 rounded-lg bg-blue-50 hover:bg-blue-100 transition"><svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6"/></svg> Semestre</a>
                <a href="{{ route('coordinateurs.index') }}" class="flex items-center p-3 rounded-lg bg-purple-50 hover:bg-purple-100 transition"><svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4V6a4 4 0 00-8 0v4m8 0a4 4 0 01-8 0"/></svg> Coordinateur</a>
                <a href="{{ route('users.index') }}" class="flex items-center p-3 rounded-lg bg-teal-50 hover:bg-teal-100 transition"><svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4V6a4 4 0 00-8 0v4m8 0a4 4 0 01-8 0"/></svg> Utilisateurs</a>
                <a href="{{ route('promotions.index') }}" class="flex items-center p-3 rounded-lg bg-pink-50 hover:bg-pink-100 transition"><svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Promotions</a>
                <a href="{{ route('classes.index') }}" class="flex items-center p-3 rounded-lg bg-yellow-50 hover:bg-yellow-100 transition"><svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg> classes</a>
                <a href="{{ route('matieres.index') }}" class="flex items-center p-3 rounded-lg bg-red-50 hover:bg-red-100 transition"><svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Matières</a>
                <a href="{{ route('enseignants.index') }}" class="flex items-center p-3 rounded-lg bg-green-50 hover:bg-green-100 transition"><svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Enseignant</a>
                <a href="{{ route('etudiants.index') }}" class="flex items-center p-3 rounded-lg bg-orange-50 hover:bg-orange-100 transition"><svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg> Étudiant</a>
            </div>
        </div>

        <!-- Liste des utilisateurs récents -->
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Liste des utilisateurs récents</h2>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière activité</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentUsers as $recent)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $recent->nom }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-indigo-600 hover:underline">
                            {{ ucfirst($recent->role->nom ?? '-') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ $recent->updated_at ? $recent->updated_at->diffForHumans() : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Aucun utilisateur récent.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
