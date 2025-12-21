<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Marketing KEYMEX</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Left Panel - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-keymex-red to-[#8B1120] relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-20 left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

            <div class="relative z-10 flex flex-col justify-center items-center w-full p-12">
                <!-- Logo -->
                <div class="w-32 h-32 bg-white/20 backdrop-blur-sm rounded-3xl flex items-center justify-center mb-8 shadow-2xl">
                    <span class="text-6xl font-black text-white">K</span>
                </div>

                <h1 class="text-5xl font-black text-white mb-4">KEYMEX</h1>
                <p class="text-white/80 text-xl font-medium mb-2">Marketing</p>
                <p class="text-white/60 text-center max-w-md">
                    Plateforme de gestion des commandes marketing et supports visuels pour les biens immobiliers KEYMEX
                </p>

                <!-- Features list -->
                <div class="mt-12 space-y-4">
                    <div class="flex items-center gap-4 text-white/90">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="font-medium">Gestion des commandes marketing</span>
                    </div>
                    <div class="flex items-center gap-4 text-white/90">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <span class="font-medium">Catalogue des biens immobiliers</span>
                    </div>
                    <div class="flex items-center gap-4 text-white/90">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <span class="font-medium">Statistiques et reporting</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="flex-1 flex flex-col justify-center items-center p-8 lg:p-12">
            <!-- Mobile Logo -->
            <div class="lg:hidden mb-8 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-keymex-red to-[#8B1120] rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                    <span class="text-3xl font-black text-white">K</span>
                </div>
                <h1 class="text-2xl font-black text-gray-900">KEYMEX</h1>
                <p class="text-gray-500 font-medium">Marketing</p>
            </div>

            <div class="w-full max-w-md">
                <div class="bg-white rounded-3xl shadow-xl p-8 lg:p-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Bienvenue</h2>
                    <p class="text-gray-500 mb-8">Connectez-vous avec votre compte KEYMEX</p>

                    @if (session('error'))
                        <div class="mb-6 rounded-2xl bg-red-50 border border-red-200 p-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('auth.keymex') }}"
                       class="flex w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-keymex-red to-[#8B1120] px-6 py-4 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Se connecter avec Keymex</span>
                    </a>

                    @if(config('app.dev_login_enabled'))
                        <div class="relative my-8">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="bg-white px-4 text-gray-400">ou</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('dev.login') }}">
                            @csrf
                            <button type="submit"
                                    class="flex w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-keymex-red to-[#8B1120] px-6 py-4 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z" clip-rule="evenodd" />
                                </svg>
                                <span>Connexion Dev (Admin Test)</span>
                            </button>
                        </form>

                        <div class="mt-6 flex items-center justify-center gap-2 text-sm">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Mode developpement
                            </span>
                        </div>
                    @endif
                </div>

                <p class="mt-8 text-center text-sm text-gray-500">
                    Reserve aux collaborateurs KEYMEX
                </p>
            </div>
        </div>
    </div>
</body>
</html>
