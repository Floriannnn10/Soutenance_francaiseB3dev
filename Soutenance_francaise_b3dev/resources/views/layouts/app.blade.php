<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Sonner pour les toasts -->
    <script src="https://cdn.jsdelivr.net/npm/sonner@1.4.0/dist/index.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sonner@1.4.0/dist/index.css">
    <script>
        // Initialiser Sonner correctement
        document.addEventListener('DOMContentLoaded', function() {
            // Vérifier si Sonner est chargé
            if (typeof window.toast === 'undefined') {
                console.warn('Sonner non chargé correctement');
            } else {
                console.log('Sonner initialisé avec succès');
            }
        });
    </script>



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

<body class="min-h-screen">
    <!-- Sonner Toaster -->
    <div id="sonner"></div>
    <script>
        // Initialiser Sonner - La bibliothèque expose directement window.toast
        document.addEventListener('DOMContentLoaded', function() {
            // Vérifier si Sonner est chargé
            if (typeof window.toast === 'undefined') {
                console.warn('Sonner toast non disponible, utilisation du fallback alert');
            }
        });
    </script>
    @php
        $user = Auth::user();
        $roleCode = $user->roles->first()->code;
    @endphp

    <!-- Header Section -->
    <header class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-start">
                <!-- Left Side - Title and Subtitle -->
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-black rounded mr-4"></div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tableau de bord</h1>
                        <p class="text-sm text-gray-600 mt-1">Gestion complète du système académique</p>
                    </div>
                </div>

                <!-- Right Side - User Info -->
                <div class="flex items-center">
                    <div class="text-right mr-4">
                        <p class="text-sm text-gray-600">Bienvenue,</p>
                        <p class="font-bold text-gray-900">{{ $user->roles->first()->nom ?? 'Utilisateur' }}</p>
                    </div>
                    @php
                        $photo = null;
                        if ($user->photo) {
                            $photo = asset('storage/' . $user->photo);
                        } elseif ($user->coordinateur && $user->coordinateur->photo) {
                            $photo = asset('storage/' . $user->coordinateur->photo);
                        } else {
                            $photo = 'https://ui-avatars.com/api/?name=' . urlencode($user->nom . ' ' . $user->prenom) . '&background=000000&color=ffffff';
                        }
                    @endphp
                    <img class="w-10 h-10 rounded-full object-cover" src="{{ $photo }}" alt="Avatar">
                </div>
            </div>
        </div>
    </header>

    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Logo Section -->
            <div class="flex justify-center items-center py-4 md:py-6">
                <div class="flex items-center">
                    <img src="{{ asset('Images/logo_ifran-removebg-preview.png') }}" alt="Logo" class="h-8 w-auto md:h-12">
                </div>
            </div>

            <!-- Divider Line -->
            <div class="border-t border-gray-300 mb-4"></div>

            <!-- Navigation Section -->
            <div class="flex flex-col md:flex-row md:justify-center md:items-center py-2 md:py-4">
                <!-- Desktop Navigation -->
                <div class="hidden md:flex flex-wrap justify-center items-center gap-2 lg:gap-4 xl:gap-6">
                    @if($roleCode === 'admin')
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : '' }}">
                            [ TABLEAU DE BORD ]
                        </a>
                        <a href="{{ route('users.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('users.*') ? 'text-blue-600 font-semibold' : '' }}">
                            UTILISATEURS
                        </a>
                        <a href="{{ route('classes.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('classes.*') ? 'text-blue-600 font-semibold' : '' }}">
                            CLASSES
                        </a>
                        <a href="{{ route('matieres.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('matieres.*') ? 'text-blue-600 font-semibold' : '' }}">
                            MATIÈRES
                        </a>
                        <a href="{{ route('enseignants.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('enseignants.*') ? 'text-blue-600 font-semibold' : '' }}">
                            ENSEIGNANTS
                        </a>
                        <a href="{{ route('etudiants.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('etudiants.*') ? 'text-blue-600 font-semibold' : '' }}">
                            ÉTUDIANTS
                        </a>
                        <a href="{{ route('promotions.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('promotions.*') ? 'text-blue-600 font-semibold' : '' }}">
                            PROMOTIONS
                        </a>
                        <a href="{{ route('annees-academiques.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('annees-academiques.*') ? 'text-blue-600 font-semibold' : '' }}">
                            ANNÉES
                        </a>
                        <a href="{{ route('semestres.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('semestres.*') ? 'text-blue-600 font-semibold' : '' }}">
                            SEMESTRES
                        </a>
                        <a href="{{ route('coordinateurs.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('coordinateurs.*') ? 'text-blue-600 font-semibold' : '' }}">
                            COORDINATEURS
                        </a>
                        <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('profile.edit') ? 'text-blue-600 font-semibold' : '' }}">
                            PROFIL
                        </a>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-700 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium transition whitespace-nowrap">
                                DÉCONNEXION
                            </button>
                        </form>
                    @elseif($roleCode === 'coordinateur')
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : '' }}">
                            [ TABLEAU DE BORD ]
                        </a>
                        <a href="{{ route('emplois-du-temps.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('emplois-du-temps.*') ? 'text-blue-600 font-semibold' : '' }}">
                            EMPLOIS DU TEMPS
                        </a>
                        <a href="{{ route('sessions-de-cours.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('sessions-de-cours.*') ? 'text-blue-600 font-semibold' : '' }}">
                            SESSIONS
                        </a>
                        <a href="{{ route('presences.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('presences.*') ? 'text-blue-600 font-semibold' : '' }}">
                            PRÉSENCES
                        </a>
                        <a href="{{ route('etudiant-matiere-dropped.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('etudiant-matiere-dropped.*') ? 'text-blue-600 font-semibold' : '' }}">
                            ÉTUDIANTS DROPPÉS
                        </a>
                        <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('profile.edit') ? 'text-blue-600 font-semibold' : '' }}">
                            PROFIL
                        </a>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-700 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium transition whitespace-nowrap">
                                DÉCONNEXION
                            </button>
                        </form>
                    @elseif($roleCode === 'enseignant')
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : '' }}">
                            [ TABLEAU DE BORD ]
                        </a>
                        <a href="{{ route('enseignant.presences.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('enseignant.presences.*') ? 'text-blue-600 font-semibold' : '' }}">
                            PRÉSENCES
                        </a>
                        <a href="{{ route('enseignant.sessions-de-cours.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('enseignant.sessions-de-cours.*') ? 'text-blue-600 font-semibold' : '' }}">
                            EMPLOI DU TEMPS
                        </a>
                        <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('profile.edit') ? 'text-blue-600 font-semibold' : '' }}">
                            PROFIL
                        </a>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-700 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium transition whitespace-nowrap">
                                DÉCONNEXION
                            </button>
                        </form>
                    @elseif($roleCode === 'etudiant')
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : '' }}">
                            [ TABLEAU DE BORD ]
                        </a>
                        <a href="{{ route('cours.etudiant') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('cours.etudiant') ? 'text-blue-600 font-semibold' : '' }}">
                            MES COURS
                        </a>
                        <a href="{{ route('presences.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('presences.*') ? 'text-blue-600 font-semibold' : '' }}">
                            MES PRÉSENCES
                        </a>
                        <a href="{{ route('emplois-du-temps.index') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('emplois-du-temps.*') ? 'text-blue-600 font-semibold' : '' }}">
                            MON EMPLOI DU TEMPS
                        </a>
                        <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('profile.edit') ? 'text-blue-600 font-semibold' : '' }}">
                            PROFIL
                        </a>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-700 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium transition whitespace-nowrap">
                                DÉCONNEXION
                            </button>
                        </form>
                    @elseif($roleCode === 'parent')
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : '' }}">
                            [ TABLEAU DE BORD ]
                        </a>
                        <a href="{{ route('parents.mes-enfants') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('parents.*') ? 'text-blue-600 font-semibold' : '' }}">
                            MES ENFANTS
                        </a>
                        <a href="{{ route('presences.enfants') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('presences.*') ? 'text-blue-600 font-semibold' : '' }}">
                            PRÉSENCES DE MES ENFANTS
                        </a>
                        <a href="{{ route('emplois-du-temps.enfants') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('emplois-du-temps.*') ? 'text-blue-600 font-semibold' : '' }}">
                            EMPLOI DU TEMPS
                        </a>
                        <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-blue-600 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium whitespace-nowrap {{ request()->routeIs('profile.edit') ? 'text-blue-600 font-semibold' : '' }}">
                            PROFIL
                        </a>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-700 px-2 md:px-3 lg:px-4 py-2 text-xs md:text-sm font-medium transition whitespace-nowrap">
                                DÉCONNEXION
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-300">
            <div class="px-4 py-4 space-y-2">
                <!-- User Profile (Mobile) -->
                <div class="flex items-center px-3 py-3 border-b border-gray-200">
                    @php
                        $photo = null;
                        if ($user->photo) {
                            $photo = asset('storage/' . $user->photo);
                        } elseif ($user->coordinateur && $user->coordinateur->photo) {
                            $photo = asset('storage/' . $user->coordinateur->photo);
                        } else {
                            $photo = 'https://ui-avatars.com/api/?name=' . urlencode($user->nom . ' ' . $user->prenom);
                        }
                    @endphp
                    <img class="w-12 h-12 rounded-full object-cover mr-4" src="{{ $photo }}" alt="Photo">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">{{ $user->nom }}</div>
                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                    </div>
                </div>

                @if($roleCode === 'admin')
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : '' }}">
                        [ TABLEAU DE BORD ]
                    </a>
                    <a href="{{ route('users.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('users.*') ? 'text-blue-600 font-semibold' : '' }}">
                        UTILISATEURS
                    </a>
                    <a href="{{ route('classes.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('classes.*') ? 'text-blue-600 font-semibold' : '' }}">
                        CLASSES
                    </a>
                    <a href="{{ route('matieres.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('matieres.*') ? 'text-blue-600 font-semibold' : '' }}">
                        MATIÈRES
                    </a>
                    <a href="{{ route('enseignants.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('enseignants.*') ? 'text-blue-600 font-semibold' : '' }}">
                        ENSEIGNANTS
                    </a>
                    <a href="{{ route('etudiants.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('etudiants.*') ? 'text-blue-600 font-semibold' : '' }}">
                        ÉTUDIANTS
                    </a>
                    <a href="{{ route('promotions.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('promotions.*') ? 'text-blue-600 font-semibold' : '' }}">
                        PROMOTIONS
                    </a>
                    <a href="{{ route('annees-academiques.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('annees-academiques.*') ? 'text-blue-600 font-semibold' : '' }}">
                        ANNÉES
                    </a>
                    <a href="{{ route('semestres.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('semestres.*') ? 'text-blue-600 font-semibold' : '' }}">
                        SEMESTRES
                    </a>
                    <a href="{{ route('coordinateurs.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('coordinateurs.*') ? 'text-blue-600 font-semibold' : '' }}">
                        COORDINATEURS
                    </a>
                @elseif($roleCode === 'coordinateur')
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : '' }}">
                        [ TABLEAU DE BORD ]
                    </a>
                    <a href="{{ route('emplois-du-temps.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('emplois-du-temps.*') ? 'text-blue-600 font-semibold' : '' }}">
                        EMPLOIS DU TEMPS
                    </a>
                    <a href="{{ route('sessions-de-cours.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('sessions-de-cours.*') ? 'text-blue-600 font-semibold' : '' }}">
                        SESSIONS
                    </a>
                    <a href="{{ route('presences.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('presences.*') ? 'text-blue-600 font-semibold' : '' }}">
                        PRÉSENCES
                    </a>
                @elseif($roleCode === 'enseignant')
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : '' }}">
                        [ TABLEAU DE BORD ]
                    </a>
                    <a href="{{ route('enseignant.presences.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('enseignant.presences.*') ? 'text-blue-600 font-semibold' : '' }}">
                        PRÉSENCES
                    </a>
                    <a href="{{ route('enseignant.sessions-de-cours.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('enseignant.sessions-de-cours.*') ? 'text-blue-600 font-semibold' : '' }}">
                        EMPLOI DU TEMPS
                    </a>
                @elseif($roleCode === 'etudiant')
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : '' }}">
                        [ TABLEAU DE BORD ]
                    </a>
                    <a href="{{ route('cours.etudiant') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('cours.etudiant') ? 'text-blue-600 font-semibold' : '' }}">
                        MES COURS
                    </a>
                    <a href="{{ route('presences.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('presences.*') ? 'text-blue-600 font-semibold' : '' }}">
                        MES PRÉSENCES
                    </a>
                    <a href="{{ route('emplois-du-temps.index') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('emplois-du-temps.*') ? 'text-blue-600 font-semibold' : '' }}">
                        MON EMPLOI DU TEMPS
                    </a>
                @elseif($roleCode === 'parent')
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : '' }}">
                        [ TABLEAU DE BORD ]
                    </a>
                    <a href="{{ route('parents.mes-enfants') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('parents.*') ? 'text-blue-600 font-semibold' : '' }}">
                        MES ENFANTS
                    </a>
                    <a href="{{ route('presences.enfants') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('presences.*') ? 'text-blue-600 font-semibold' : '' }}">
                        PRÉSENCES DE MES ENFANTS
                    </a>
                    <a href="{{ route('emplois-du-temps.enfants') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('emplois-du-temps.*') ? 'text-blue-600 font-semibold' : '' }}">
                        EMPLOI DU TEMPS
                    </a>
                @endif

                <!-- Profil pour tous les rôles -->
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition {{ request()->routeIs('profile.edit') ? 'text-blue-600 font-semibold' : '' }}">
                    PROFIL
                </a>

                <!-- Logout -->
                <div class="border-t border-gray-200 pt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left block px-4 py-2 text-base font-medium text-red-600 hover:text-red-700 hover:bg-red-50 transition">
                            DÉCONNEXION
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
        <!-- Page Content -->
        <div class="p-4 sm:p-6 lg:p-8">
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </main>

    <!-- JavaScript for mobile menu toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        });
    </script>

    @stack('scripts')

    <!-- Notifications DaisyUI pour les messages flash -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                console.log('Flash success:', '{{ session('success') }}');
                if (typeof window.showNotification === 'function') {
                    window.showNotification('success', '{{ session('success') }}');
                } else {
                    console.error('showNotification function not found');
                }
            @endif

            @if(session('error'))
                console.log('Flash error:', '{{ session('error') }}');
                if (typeof window.showNotification === 'function') {
                    window.showNotification('error', '{{ session('error') }}');
                } else {
                    console.error('showNotification function not found');
                }
            @endif

            @if(session('warning'))
                console.log('Flash warning:', '{{ session('warning') }}');
                if (typeof window.showNotification === 'function') {
                    window.showNotification('warning', '{{ session('warning') }}');
                } else {
                    console.error('showNotification function not found');
                }
            @endif

            @if(session('info'))
                console.log('Flash info:', '{{ session('info') }}');
                if (typeof window.showNotification === 'function') {
                    window.showNotification('info', '{{ session('info') }}');
                } else {
                    console.error('showNotification function not found');
                }
            @endif

            <!-- Gestion des erreurs de validation -->
            @if($errors->any())
                @foreach($errors->all() as $error)
                    console.log('Validation error:', '{{ $error }}');
                    if (typeof window.showNotification === 'function') {
                        window.showNotification('error', '{{ $error }}');
                    } else {
                        console.error('showNotification function not found');
                    }
                @endforeach
            @endif
        });
    </script>

</body>

</html>
