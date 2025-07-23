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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom styles for icons fallback -->
    <style>
        .action-icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            text-align: center;
            font-size: 14px;
            line-height: 20px;
            border-radius: 3px;
            transition: all 0.2s;
        }

        /* Fallback si Font Awesome ne charge pas */
        .fas::before {
            font-family: "Font Awesome 6 Free", "Arial", sans-serif !important;
        }

        /* Styles pour les boutons d'actions */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 4px;
            transition: all 0.2s;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-action:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Si Font Awesome ne charge pas, afficher les émojis */
        @supports not (font-family: "Font Awesome 6 Free") {
            .fa-eye::before {
                content: "👁";
                font-family: emoji;
            }

            .fa-edit::before {
                content: "✏️";
                font-family: emoji;
            }

            .fa-play::before {
                content: "▶️";
                font-family: emoji;
            }

            .fa-trash::before {
                content: "🗑️";
                font-family: emoji;
            }

            .fa-plus::before {
                content: "➕";
                font-family: emoji;
            }
        }
    </style>
</head>

<body class="min-h-screen" style="background: linear-gradient(to bottom, #E8EDF5 100%, #FFFFFF 50%);">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 flex flex-col justify-between"
            style="background: linear-gradient(to bottom, #E8EDF5 100%, #FFFFFF 50%);">
            <div>
                <!-- Logo -->
                <div class="flex items-center justify-center h-20 border-b border-gray-800">
                    <svg class="w-10 h-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="ml-3 text-2xl font-bold text-white">Admin</span>
                </div>
                <!-- Menu -->
                <nav class="mt-8">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : '' }}">
                        <span class="mr-3">🏠</span> Tableau de bord
                    </a>
                    <a href="{{ route('annees-academiques.index') }}"
                        class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('annees-academiques.*') ? 'bg-gray-800 text-white' : '' }}">
                        <span class="mr-3">📅</span> Années académiques
                    </a>
                    <a href="{{ route('semestres.index') }}"
                        class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('semestres.*') ? 'bg-gray-800 text-white' : '' }}">
                        <span class="mr-3">🔒</span> Semestre
                    </a>
                    <a href="{{ route('coordinateurs.index') }}"
                        class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('coordinateurs.*') ? 'bg-gray-800 text-white' : '' }}">
                        <span class="mr-3">👥</span> Coordinateur
                    </a>
                    <a href="{{ route('users.index') }}"
                        class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('users.index') ? 'bg-gray-800 text-white' : '' }}">
                        <span class="mr-3">👤</span> Utilisateurs
                    </a>
                    <a href="{{ route('classes.index') }}"
                        class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('classes.*') ? 'bg-gray-800 text-white' : '' }}">
                        <span class="mr-3">📚</span> classes
                    </a>
                    <a href="{{ route('matieres.index') }}"
                        class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('matieres.*') ? 'bg-gray-800 text-white' : '' }}">
                        <span class="mr-3">📄</span> Matières
                    </a>
                    <a href="{{ route('enseignants.index') }}"
                        class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('enseignants.*') ? 'bg-gray-800 text-white' : '' }}">
                        <span class="mr-3">🧑‍🏫</span> Enseignant
                    </a>
                    <a href="{{ route('etudiants.index') }}"
                        class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('etudiants.*') ? 'bg-gray-800 text-white' : '' }}">
                        <span class="mr-3">🎓</span> Étudiant
                    </a>
                    <a href="{{ route('users.create') }}"
                        class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('users.create') ? 'bg-gray-800 text-white' : '' }}">
                        <span class="mr-3">➕</span> Créer un utilisateur
                    </a>
                    @if (auth()->user() && auth()->user()->role && auth()->user()->role->nom === 'admin')
                    <a href="{{ route('promotions.index') }}"
                        class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('promotions.*') ? 'bg-gray-800 text-white' : '' }}">
                        <span class="mr-3">🏷️</span> Promotions
                    </a>
                @endif
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('profile.edit') ? 'bg-gray-800 text-white' : '' }}">
                        <span class="mr-3">👤</span> Profil
                    </a>
                </nav>
            </div>
            <!-- Utilisateur connecté + Déconnexion -->
            <div class="flex flex-col items-center p-6 border-t border-gray-800">
                @php
                    $user = Auth::user();
                    $photo = null;
                    if ($user->photo) {
                        $photo = asset('storage/' . $user->photo);
                    } elseif ($user->enseignant && $user->enseignant->photo) {
                        $photo = asset('storage/' . $user->enseignant->photo);
                    } elseif ($user->parent && $user->parent->photo) {
                        $photo = asset('storage/' . $user->parent->photo);
                    } elseif ($user->coordinateur && $user->coordinateur->photo) {
                        $photo = asset('storage/' . $user->coordinateur->photo);
                    } else {
                        $photo =
                            'https://ui-avatars.com/api/?name=' .
                            urlencode($user->prenom ?? ($user->name ?? $user->nom));
                    }
                @endphp
                <img class="w-12 h-12 rounded-full object-cover" src="{{ $photo }}" alt="Photo">
                <div class="mt-3 text-center">
                    <div class="text-white font-semibold">{{ Auth::user()->nom }}</div>
                    <div class="text-gray-400 text-xs">{{ Auth::user()->email }}</div>
                    <div class="text-indigo-400 text-xs capitalize">{{ Auth::user()->role->nom ?? 'Admin' }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="w-full mt-4">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <!-- Header Section -->
            @isset($header)
                <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-6">
                    {{ $header }}
                </header>
            @endisset

            <!-- Page Content -->
            <div class="p-8">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>
    {{-- Actions rapides (si présentes dans le layout) --}}
    {{-- @if (auth()->user() && auth()->user()->role && auth()->user()->role->nom === 'admin')
        <a href="{{ route('promotions.index') }}"
            class="flex items-center p-3 rounded-lg bg-pink-50 hover:bg-pink-100 transition">
            <span class="mr-2">🏷️</span> Promotions
        </a>
    @endif --}}
</body>

</html>
