<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <div class="flex h-screen bg-gray-100">
            <!-- Sidebar -->
            <aside class="w-64 bg-gray-900 flex flex-col justify-between">
                <div>
                    <!-- Logo -->
                    <div class="flex items-center justify-center h-20 border-b border-gray-800">
                        <svg class="w-10 h-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="ml-3 text-2xl font-bold text-white">Admin</span>
                    </div>
                    <!-- Menu -->
                    <nav class="mt-8">
                        <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : '' }}">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/>
                            </svg>
                            Tableau de bord
                        </a>
                        <a href="{{ route('users.create') }}" class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('users.create') ? 'bg-gray-800 text-white' : '' }}">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Créer un utilisateur
                        </a>
                        <a href="{{ route('users.index') }}" class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('users.index') ? 'bg-gray-800 text-white' : '' }}">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"/>
                            </svg>
                            Liste des utilisateurs
                        </a>
                        <a href="{{ route('matieres.index') }}" class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('matieres.*') ? 'bg-gray-800 text-white' : '' }}">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                            </svg>
                            Liste des matières
                        </a>
                        <a href="{{ route('classes.index') }}" class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('classes.*') ? 'bg-gray-800 text-white' : '' }}">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                            </svg>
                            Liste des classes
                        </a>
                        <a href="{{ route('enseignants.index') }}" class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('enseignants.*') ? 'bg-gray-800 text-white' : '' }}">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            Liste des enseignants
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('profile.edit') ? 'bg-gray-800 text-white' : '' }}">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.797.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Profil
                        </a>
                    </nav>
                </div>
                <!-- Utilisateur connecté + Déconnexion -->
                <div class="flex flex-col items-center p-6 border-t border-gray-800">
                    <img class="w-12 h-12 rounded-full object-cover" src="{{ Auth::user()->photo ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" alt="Photo">
                    <div class="mt-3 text-center">
                        <div class="text-white font-semibold">{{ Auth::user()->nom }}</div>
                        <div class="text-gray-400 text-xs">{{ Auth::user()->email }}</div>
                        <div class="text-indigo-400 text-xs capitalize">{{ Auth::user()->role->nom ?? 'Admin' }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="w-full mt-4">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </aside>
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-8">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </body>
</html>
