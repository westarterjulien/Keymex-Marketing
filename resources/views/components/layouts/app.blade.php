<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Marketing' }} - KEYMEX</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-72 flex flex-col fixed h-full p-4 hidden lg:flex">
            <!-- Sidebar Content with rounded design -->
            <div class="bg-gradient-to-b from-keymex-red to-[#8B1120] text-white flex flex-col h-full rounded-3xl shadow-2xl overflow-hidden">
                <!-- Logo -->
                <div class="p-4 pt-5">
                    <a href="{{ route('orders.index') }}" class="block">
                        <div class="flex items-center justify-center mb-1">
                            <div class="w-11 h-11 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-2xl font-black text-white">K</span>
                            </div>
                        </div>
                        <h1 class="text-lg font-black text-center text-white">KEYMEX</h1>
                        <p class="text-[10px] text-white/70 text-center font-medium">Marketing</p>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto">
                    <!-- Groupe Print -->
                    <p class="px-3 py-2 text-xs font-semibold text-white/60 uppercase tracking-wider">Print</p>

                    <a href="{{ route('standalone-bats.index') }}"
                       class="{{ request()->routeIs('standalone-bats.*') ? 'bg-white/20 shadow-lg' : 'hover:bg-white/10' }} flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white font-medium transition-all duration-200">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>BAT</span>
                    </a>

                    <a href="{{ route('orders.index') }}"
                       class="{{ request()->routeIs('orders.*') ? 'bg-white/20 shadow-lg' : 'hover:bg-white/10' }} flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white font-medium transition-all duration-200">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Commandes</span>
                    </a>

                    <a href="{{ route('stats.dashboard') }}"
                       class="{{ request()->routeIs('stats.*') ? 'bg-white/20 shadow-lg' : 'hover:bg-white/10' }} flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white font-medium transition-all duration-200">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Stats</span>
                    </a>

                    <!-- Groupe KPI -->
                    <div class="pt-3 mt-2 border-t border-white/20">
                        <p class="px-3 py-2 text-xs font-semibold text-white/60 uppercase tracking-wider">KPI</p>
                        <a href="{{ route('kpi.weekly') }}"
                           class="{{ request()->routeIs('kpi.*') ? 'bg-white/20 shadow-lg' : 'hover:bg-white/10' }} flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white font-medium transition-all duration-200">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <span>Hebdo Biz</span>
                        </a>
                    </div>

                    <!-- Groupe Reseaux sociaux -->
                    <div class="pt-3 mt-2 border-t border-white/20">
                        <p class="px-3 py-2 text-xs font-semibold text-white/60 uppercase tracking-wider">Reseaux sociaux</p>
                        <a href="{{ route('properties.for-sale') }}"
                           class="{{ request()->routeIs('properties.for-sale') ? 'bg-white/20 shadow-lg' : 'hover:bg-white/10' }} flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white font-medium transition-all duration-200">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>A vendre</span>
                        </a>
                        <a href="{{ route('properties.index') }}"
                           class="{{ request()->routeIs('properties.index') ? 'bg-white/20 shadow-lg' : 'hover:bg-white/10' }} flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white font-medium transition-all duration-200">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Compromis / Vendus</span>
                        </a>
                    </div>

                    <!-- Groupe Configuration -->
                    <div class="pt-3 mt-2 border-t border-white/20">
                        <p class="px-3 py-2 text-xs font-semibold text-white/60 uppercase tracking-wider">Configuration</p>
                        <a href="{{ route('settings.orders') }}"
                           class="{{ request()->routeIs('settings.orders') ? 'bg-white/20 shadow-lg' : 'hover:bg-white/10' }} flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white font-medium transition-all duration-200">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Commandes</span>
                        </a>
                        <a href="{{ route('settings.sso') }}"
                           class="{{ request()->routeIs('settings.sso') ? 'bg-white/20 shadow-lg' : 'hover:bg-white/10' }} flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white font-medium transition-all duration-200">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Acces SSO</span>
                        </a>
                        <a href="{{ route('settings.smtp') }}"
                           class="{{ request()->routeIs('settings.smtp') ? 'bg-white/20 shadow-lg' : 'hover:bg-white/10' }} flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white font-medium transition-all duration-200">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>Email SMTP</span>
                        </a>
                    </div>
                </nav>

                <!-- User Profile -->
                @auth
                <div class="px-3 pb-4 pt-2">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/20">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center shadow">
                                <span class="text-keymex-red font-black text-sm">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold truncate text-white">{{ auth()->user()->name ?? 'Utilisateur' }}</p>
                                <p class="text-[10px] text-white/70 truncate">{{ auth()->user()->email ?? '' }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="p-1.5 bg-white/20 hover:bg-white/30 rounded-lg transition-all" title="Déconnexion">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth
            </div>
        </aside>

        <!-- Mobile Header -->
        <div class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-keymex-red to-[#8B1120] text-white shadow-lg">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <span class="text-xl font-black text-white">K</span>
                    </div>
                    <div>
                        <h1 class="text-lg font-black">KEYMEX</h1>
                        <p class="text-xs text-white/70">Marketing</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                        class="p-2 rounded-xl bg-white/20 hover:bg-white/30 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden border-t border-white/20">
                <nav class="px-4 py-4 space-y-1">
                    <!-- Groupe Print -->
                    <p class="px-4 py-2 text-xs font-semibold text-white/50 uppercase tracking-wider">Print</p>
                    <a href="{{ route('standalone-bats.index') }}"
                       class="{{ request()->routeIs('standalone-bats.*') ? 'bg-white/20' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-white font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        BAT
                    </a>
                    <a href="{{ route('orders.index') }}"
                       class="{{ request()->routeIs('orders.*') ? 'bg-white/20' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-white font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Commandes
                    </a>
                    <a href="{{ route('stats.dashboard') }}"
                       class="{{ request()->routeIs('stats.*') ? 'bg-white/20' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-white font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Stats
                    </a>

                    <!-- Groupe KPI -->
                    <p class="px-4 py-2 mt-2 text-xs font-semibold text-white/50 uppercase tracking-wider border-t border-white/20 pt-4">KPI</p>
                    <a href="{{ route('kpi.weekly') }}"
                       class="{{ request()->routeIs('kpi.*') ? 'bg-white/20' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-white font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Hebdo Biz
                    </a>

                    <!-- Groupe Reseaux sociaux -->
                    <p class="px-4 py-2 mt-2 text-xs font-semibold text-white/50 uppercase tracking-wider border-t border-white/20 pt-4">Reseaux sociaux</p>
                    <a href="{{ route('properties.for-sale') }}"
                       class="{{ request()->routeIs('properties.for-sale') ? 'bg-white/20' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-white font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        A vendre
                    </a>
                    <a href="{{ route('properties.index') }}"
                       class="{{ request()->routeIs('properties.index') ? 'bg-white/20' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-white font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Compromis / Vendus
                    </a>

                    <!-- Groupe Configuration -->
                    <p class="px-4 py-2 mt-2 text-xs font-semibold text-white/50 uppercase tracking-wider border-t border-white/20 pt-4">Configuration</p>
                    <a href="{{ route('settings.orders') }}"
                       class="{{ request()->routeIs('settings.orders') ? 'bg-white/20' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-white font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Commandes
                    </a>
                    <a href="{{ route('settings.sso') }}"
                       class="{{ request()->routeIs('settings.sso') ? 'bg-white/20' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-white font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Acces SSO
                    </a>
                    <a href="{{ route('settings.smtp') }}"
                       class="{{ request()->routeIs('settings.smtp') ? 'bg-white/20' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-white font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email SMTP
                    </a>
                </nav>
                @auth
                <div class="px-4 pb-4 border-t border-white/20 pt-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center">
                                <span class="text-keymex-red font-bold">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-2 rounded-xl bg-white/20 hover:bg-white/30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 lg:ml-72 p-4 pt-20 lg:pt-4">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden min-h-[calc(100vh-2rem)]">
                <!-- Page content -->
                <main class="p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
