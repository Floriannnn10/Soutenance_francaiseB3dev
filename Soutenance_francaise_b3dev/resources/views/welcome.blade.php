<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Système de Gestion Académique') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-white overflow-hidden">
    <main
        class="h-screen flex items-center justify-center md:justify-start bg-gradient-to-b from-[#E8EDF5] from-100% to-white to-50% overflow-hidden">
        <div
            class="relative w-full max-w-none mx-auto flex flex-col md:flex-row items-center justify-center md:justify-start px-0 h-screen overflow-hidden">
            <!-- Left Image (Visible on desktop) -->
            <div class="hidden md:block md:w-1/2 flex items-center justify-start pl-0">
                <img src="{{ asset('Images\welwoce_page-removebg-preview.png') }}" alt="Illustration de bienvenue"
                    class="object-contain w-full h-full -mt-12">
            </div>

            <!-- Right Content -->
            <div class="w-full md:w-1/2 flex flex-col items-center justify-center text-center relative">
                <!-- Circle Background (absolute) -->
                <div class="hidden md:block absolute w-80 h-80 bg-[#DBE8F2] rounded-full top-10 right-10 -z-10"></div>

                <!-- Logo with background circle -->
                <div class="relative mb-8 hidden md:block">
                    <div class="w-80 h-80 bg-[#DBE8F2] rounded-full absolute -top-12 -left-4"></div>
                    <img src="{{ asset('Images/logo_app_white.png') }}" alt="Logo IFRAN PRÉSENCE"
                        class="w-64 h-auto relative z-10 mr-12 mb-12 -mt-4">
                </div>

                <!-- Title -->
                <h1 class="text-3xl md:text-4xl font-bold text-black mb-6">
                    <span>IFRAN </span><span class="text-red-600">PRÉSENCE</span>
                </h1>

                <!-- Description -->
                <p class="text-base md:text-lg text-black opacity-80 mb-6 max-w-md">
                    Connectez-vous pour accéder à votre espace de travail sécurisé
                </p>

                <!-- Connexion Button -->
                <a href="{{ route('login') }}"
                    class="inline-block bg-[#DBE8F2] text-black font-bold py-3 px-8 rounded-full shadow-lg hover:bg-[#c0d4e6] transition transform hover:scale-105">
                    Se connecter
                </a>
            </div>
        </div>
    </main>
</body>

</html>
