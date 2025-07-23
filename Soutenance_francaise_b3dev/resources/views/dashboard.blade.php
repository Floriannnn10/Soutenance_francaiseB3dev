<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Administrateur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-active { background-color: #e5e7eb; color: #111827; }
    </style>
</head>
<body class="min-h-screen" style="background: linear-gradient(to bottom, #E8EDF5 100%, #FFFFFF 50%);">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div x-data="{ open: false }" class="relative">
            <!-- Desktop Sidebar -->
            <aside class="hidden md:flex flex-col bg-white shadow-lg w-80 min-h-screen sticky top-0">
                <!-- Logo et profil utilisateur -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mr-3">
                            <img src="{{ asset('Images/logo_app_white.png') }}" alt="Logo" class="w-12 h-20 object-contain">
                        </div>
                        <div>
                            <h2 class="font-semibold text-lg text-gray-900">{{ Auth::user()->nom ?? Auth::user()->name }}</h2>
                            <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-6">
                    <ul class="space-y-2">
                        <li><a href="#" class="flex items-center px-4 py-3 rounded-lg sidebar-active font-medium"><i class="fas fa-tachometer-alt mr-3 text-gray-600"></i>Tableau de bord</a></li>
                        <li><a href="{{ route('users.create') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-user-plus mr-3"></i>Créer un utilisateur</a></li>
                        <li><a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-users mr-3"></i>Liste des utilisateurs</a></li>
                        <li><a href="{{ route('matieres.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-book mr-3"></i>Liste des matières</a></li>
                        <li><a href="{{ route('classes.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-chalkboard mr-3"></i>Liste des classes</a></li>
                        <li><a href="{{ route('enseignants.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-user-tie mr-3"></i>Liste des enseignants</a></li>
                        <li><a href="{{ route('etudiants.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-user-graduate mr-3"></i>Liste des étudiants</a></li>
                        <li><a href="{{ route('annees-academiques.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-calendar-alt mr-3"></i>Années académiques</a></li>
                        <li><a href="{{ route('semestres.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-calendar mr-3"></i>Semestres</a></li>
                        <li><a href="{{ route('coordinateurs.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-user-cog mr-3"></i>Coordinateurs</a></li>
                    </ul>
                </nav>

                <!-- Footer sidebar -->
                <div class="p-6 border-t border-gray-200">
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 mb-2"><i class="fas fa-user-circle mr-3"></i>Profil</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 w-full text-left"><i class="fas fa-sign-out-alt mr-3"></i>Déconnexion</button></form>
                </div>
            </aside>

            <!-- Mobile Hamburger -->
            <div class="md:hidden flex items-center p-4 bg-white shadow w-full">
                <button @click="open = !open" class="text-gray-600 focus:outline-none mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-lg">dP</div>
            </div>

            <!-- Mobile Sidebar -->
            <div x-show="open" @click.away="open = false" class="fixed inset-0 z-50 bg-black bg-opacity-40 flex">
                <aside class="bg-white w-64 h-full flex flex-col">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mr-3">
                                <img src="{{ asset('Images/logo_app_white.png') }}" alt="Logo" class="w-12 h-12 object-contain">
                            </div>
                            <div>
                                <h2 class="font-semibold text-lg text-gray-900">{{ Auth::user()->nom ?? Auth::user()->name }}</h2>
                                <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>
                    <nav class="flex-1 p-6">
                        <ul class="space-y-2">
                            <li><a href="#" class="flex items-center px-4 py-3 rounded-lg sidebar-active font-medium"><i class="fas fa-tachometer-alt mr-3 text-gray-600"></i>Tableau de bord</a></li>
                            <li><a href="{{ route('users.create') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-user-plus mr-3"></i>Créer un utilisateur</a></li>
                            <li><a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-users mr-3"></i>Liste des utilisateurs</a></li>
                            <li><a href="{{ route('matieres.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-book mr-3"></i>Liste des matières</a></li>
                            <li><a href="{{ route('classes.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-chalkboard mr-3"></i>Liste des classes</a></li>
                            <li><a href="{{ route('enseignants.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-user-tie mr-3"></i>Liste des enseignants</a></li>
                            <li><a href="{{ route('etudiants.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-user-graduate mr-3"></i>Liste des étudiants</a></li>
                            <li><a href="{{ route('annees-academiques.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-calendar-alt mr-3"></i>Années académiques</a></li>
                            <li><a href="{{ route('semestres.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-calendar mr-3"></i>Semestres</a></li>
                            <li><a href="{{ route('coordinateurs.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50"><i class="fas fa-user-cog mr-3"></i>Coordinateurs</a></li>
                        </ul>
                    </nav>
                    <div class="p-6 border-t border-gray-200">
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 mb-2"><i class="fas fa-user-circle mr-3"></i>Profil</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 w-full text-left"><i class="fas fa-sign-out-alt mr-3"></i>Déconnexion</button></form>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Tableau de bord</h1>

            <!-- Cartes de statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-2xl text-indigo-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Années académiques</p>
                            <p class="text-2xl font-semibold text-gray-900">5</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-2xl text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Semestre</p>
                            <p class="text-2xl font-semibold text-gray-900">8</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-2xl text-purple-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Coordinateurs</p>
                            <p class="text-2xl font-semibold text-gray-900">6</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-friends text-2xl text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Utilisateurs</p>
                            <p class="text-2xl font-semibold text-gray-900">56</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <section class="bg-white rounded-lg shadow p-8 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Actions rapides - Administrateur</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('annees-academiques.index') }}" class="h-16 bg-blue-100 rounded-lg flex items-center justify-center hover:bg-blue-200 transition">
                        <span class="text-blue-700 font-medium">Années académiques</span>
                    </a>
                    <a href="{{ route('semestres.index') }}" class="h-16 bg-green-100 rounded-lg flex items-center justify-center hover:bg-green-200 transition">
                        <span class="text-green-700 font-medium">Semestre</span>
                    </a>
                    <a href="{{ route('coordinateurs.index') }}" class="h-16 bg-purple-100 rounded-lg flex items-center justify-center hover:bg-purple-200 transition">
                        <span class="text-purple-700 font-medium">Coordinateur</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="h-16 bg-indigo-100 rounded-lg flex items-center justify-center hover:bg-indigo-200 transition">
                        <span class="text-indigo-700 font-medium">Utilisateurs</span>
                    </a>
                    <a href="{{ route('classes.index') }}" class="h-16 bg-yellow-100 rounded-lg flex items-center justify-center hover:bg-yellow-200 transition">
                        <span class="text-yellow-700 font-medium">Classes</span>
                    </a>
                    <a href="{{ route('matieres.index') }}" class="h-16 bg-pink-100 rounded-lg flex items-center justify-center hover:bg-pink-200 transition">
                        <span class="text-pink-700 font-medium">Matieres</span>
                    </a>
                    <a href="{{ route('enseignants.index') }}" class="h-16 bg-teal-100 rounded-lg flex items-center justify-center hover:bg-teal-200 transition">
                        <span class="text-teal-700 font-medium">Enseignant</span>
                    </a>
                    <a href="{{ route('etudiants.index') }}" class="h-16 bg-orange-100 rounded-lg flex items-center justify-center hover:bg-orange-200 transition">
                        <span class="text-orange-700 font-medium">Étudiant</span>
                    </a>
                </div>
            </section>

            <!-- Liste des utilisateurs récents -->
            <section class="bg-white rounded-lg shadow p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Liste des utilisateurs récents</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-medium text-gray-900">Nom</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-900">Rôle</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-900">Dernière activité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100">
                                <td class="py-4 px-4 text-gray-900">Léa Dubois</td>
                                <td class="py-4 px-4 text-gray-600">Étudiant</td>
                                <td class="py-4 px-4 text-gray-600">Il y a 2 jours</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="py-4 px-4 text-gray-900">Thomas Leclerc</td>
                                <td class="py-4 px-4 text-gray-600">Enseignant</td>
                                <td class="py-4 px-4 text-gray-600">Il y a 1 jour</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="py-4 px-4 text-gray-900">Chloé Martin</td>
                                <td class="py-4 px-4 text-gray-600">Étudiant</td>
                                <td class="py-4 px-4 text-gray-600">Il y a 3 jours</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="py-4 px-4 text-gray-900">Lucas Moreau</td>
                                <td class="py-4 px-4 text-gray-600">Enseignant</td>
                                <td class="py-4 px-4 text-gray-600">Il y a 4 jours</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="py-4 px-4 text-gray-900">Emma Bernard</td>
                                <td class="py-4 px-4 text-gray-600">Étudiant</td>
                                <td class="py-4 px-4 text-gray-600">Il y a 1 jour</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

