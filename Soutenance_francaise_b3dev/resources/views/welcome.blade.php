<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IFRAN PRÉSENCE') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <!-- Logo en haut à gauche -->
    <div class="absolute top-8 left-8 z-10">
        <img src="{{ asset('Images/logo_ifran-removebg-preview.png') }}" alt="Logo IFRAN" class="w-52 h-auto">
    </div>

    <main class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md mx-auto text-center">
            <!-- Titre principal -->
            <h2 class="text-3xl font-bold mb-4">
                <span class="text-black">IFRAN </span>
                <span class="text-red-600">PRÉSENCE</span>
            </h2>

            <!-- Description -->
            <p class="text-black mb-8">
                Connectez-vous pour accéder à votre espace de travail sécurisé
            </p>

            <!-- Bouton de connexion -->
            <a href="{{ route('login') }}"
                class="inline-block bg-[#FD0800] text-white font-bold py-3 px-8 rounded-lg shadow-md hover:bg-black transition-colors">
                Se connecter
            </a>
        </div>
    </main>
</body>

</html>
