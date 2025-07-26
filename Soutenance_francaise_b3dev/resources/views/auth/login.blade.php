<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex min-h-screen w-full">
        <!-- Sidebar Logo (Hidden on mobile) -->
        <div
            class="hidden md:flex md:w-1/2 bg-black items-center justify-center relative overflow-hidden min-h-screen">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-20 h-20 bg-white rounded-full animate-pulse"></div>
                <div class="absolute top-32 right-16 w-16 h-16 bg-white rounded-full animate-pulse delay-1000"></div>
                <div class="absolute bottom-20 left-20 w-12 h-12 bg-white rounded-full animate-pulse delay-2000"></div>
                <div class="absolute bottom-32 right-10 w-24 h-24 bg-white rounded-full animate-pulse delay-3000"></div>
            </div>

            <!-- Logo Container -->
            <div class="relative z-10 text-center px-8">
                <div class="mb-8 transform hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('Images/logo_ifran-removebg-preview.png') }}" alt="Logo IFRAN"
                        class="w-80 h-auto object-contain mx-auto drop-shadow-2xl">
                </div>
                <h2 class="text-black text-4xl font-bold mb-4 leading-tight">SYSTÈME DE <br> GESTION ACADEMIQUE</h2>
                <p class="text-black text-xl leading-relaxed max-w-md mx-auto">Connectez-vous pour accéder à votre
                    espace de travail sécurisé</p>

                <!-- Features List -->
                <div class="mt-12 space-y-3 text-left max-w-sm mx-auto">
                    <div class="flex items-center text-black">
                        <svg class="w-5 h-5 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>Gestion des présences</span>
                    </div>
                    <div class="flex items-center text-black">
                        <svg class="w-5 h-5 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>Suivi académique</span>
                    </div>
                    <div class="flex items-center text-black">
                        <svg class="w-5 h-5 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>Communication en temps réel</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="flex-1 flex flex-col justify-center bg-white relative">
            <!-- Form Container -->
            <div class="w-full max-w-xl mx-auto px-12 py-16 space-y-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="mb-4">
                        <svg class="w-12 h-12 mx-auto text-indigo-600 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Connexion</h2>
                    <p class="text-gray-600">Accédez à votre compte sécurisé</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Adresse email')" class="text-gray-700 font-medium" />
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                    </path>
                                </svg>
                            </div>
                            <x-text-input id="email"
                                class="block w-full pl-10 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                type="email" name="email" :value="old('email')" required autofocus
                                autocomplete="username" placeholder="votre@email.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Mot de passe')" class="text-gray-700 font-medium" />
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                            <x-text-input id="password"
                                class="block w-full pl-10 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                type="password" name="password" required autocomplete="current-password"
                                placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-black text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-2">
                            <label for="remember_me"
                                class="ml-2 text-sm text-gray-700">{{ __('Se souvenir de moi') }}</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-indigo-600 hover:text-indigo-500 hover:underline transition-colors"
                                href="{{ route('password.request') }}">
                                {{ __('Mot de passe oublié ?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="w-full py-4 px-4 bg-[#FD0800] text-white font-semibold rounded-lg hover:bg-black focus:outline-none focus:ring-2 focus:ring-[#FD0800] focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02] shadow-lg active:scale-[0.98]">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                {{ __('Se connecter') }}
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Footer -->
                {{-- <div class="text-center mt-8 pt-6 border-t border-gray-200">
                    <p class="text-gray-600 text-sm">
                        Pas encore de compte ?
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="text-indigo-600 hover:text-indigo-500 font-medium hover:underline transition-colors">
                                Créer un compte
                            </a>
                        @endif
                    </p>
                </div> --}}
            </div>
        </div>
    </div>
</x-guest-layout>
