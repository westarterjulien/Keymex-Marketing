<div class="space-y-8">
    {{-- Header avec gradient --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-keymex-red via-red-600 to-red-700 p-6 shadow-xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\" fill=\"rgba(255,255,255,0.07)\"%3E%3C/path%3E%3C/svg%3E')] opacity-60"></div>
        <div class="absolute top-0 right-0 -mt-16 -mr-16 h-64 w-64 rounded-full bg-white/5"></div>
        <div class="absolute bottom-0 left-0 -mb-16 -ml-16 h-48 w-48 rounded-full bg-white/5"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            {{-- Titre et description --}}
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">Hebdo Biz</h1>
                    <p class="mt-0.5 text-sm text-white/80">
                        Performance hebdomadaire &bull; Semaine {{ $selectedWeek['start']->weekOfYear }}
                    </p>
                </div>
            </div>

            {{-- Switch Navigation --}}
            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl p-1.5">
                <span class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-keymex-red shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Hebdo
                </span>
                <a href="{{ route('kpi.monthly') }}"
                   class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-white/90 hover:bg-white/10 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Mensuel
                </a>
                <a href="{{ route('kpi.yearly') }}"
                   class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-white/90 hover:bg-white/10 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Annuel
                </a>
                <a href="{{ route('kpi.custom') }}"
                   class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-white/90 hover:bg-white/10 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Personnalise
                </a>
            </div>
        </div>
    </div>

    {{-- Navigation semaines --}}
    <div class="flex items-center justify-center">
        <div class="inline-flex items-center gap-2 rounded-xl bg-white p-1.5 shadow-sm ring-1 ring-gray-200">
            <button wire:click="previousWeek"
                    class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="hidden sm:inline">Précédente</span>
            </button>

            <div class="flex flex-col items-center px-4 py-1 min-w-[180px]">
                <span class="text-sm font-semibold text-gray-900">
                    {{ $selectedWeek['start']->translatedFormat('d M') }} - {{ $selectedWeek['end']->translatedFormat('d M Y') }}
                </span>
                @if($isCurrentWeek)
                    <span class="mt-0.5 inline-flex items-center gap-1 text-xs font-medium text-green-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        Semaine en cours
                    </span>
                @else
                    <button wire:click="currentWeek" class="mt-0.5 text-xs font-medium text-keymex-red hover:text-keymex-red-hover transition-colors">
                        Revenir à aujourd'hui
                    </button>
                @endif
            </div>

            <button wire:click="nextWeek"
                    @if($isCurrentWeek) disabled @endif
                    class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-transparent">
                <span class="hidden sm:inline">Suivante</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Loading overlay centré --}}
    <div wire:loading.flex
         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; display: none; align-items: center; justify-content: center; background-color: rgba(0, 0, 0, 0.3); backdrop-filter: blur(4px);">
        <div style="background: white; border-radius: 16px; padding: 32px 48px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); display: flex; flex-direction: column; align-items: center; gap: 16px;">
            <div style="position: relative; width: 56px; height: 56px;">
                <div style="width: 56px; height: 56px; border: 4px solid rgba(200, 30, 30, 0.2); border-top-color: #c81e1e; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            </div>
            <span style="font-size: 14px; font-weight: 500; color: #374151;">Chargement des données...</span>
        </div>
    </div>
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    {{-- Erreur MongoDB --}}
    @if($mongoDbError)
        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-amber-800">Connexion MongoDB indisponible</h3>
                    <p class="mt-1 text-sm text-amber-700">Les données ne sont pas accessibles pour le moment.</p>
                </div>
            </div>
        </div>
    @endif

    <div wire:loading.remove>
        {{-- C.A Compromis --}}
        <section>
            <div class="flex items-center gap-3 mb-5">
                <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-red-50">
                    <svg class="h-5 w-5 text-keymex-red" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">C.A Compromis</h2>
            </div>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                {{-- Semaine précédente --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Semaine précédente</p>
                            <p class="text-xs text-gray-400">{{ $previousWeek['start']->format('d/m') }} - {{ $previousWeek['end']->format('d/m') }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">S-1</span>
                    </div>
                    <div class="mt-4 space-y-1">
                        <div class="flex items-baseline gap-2">
                            <span class="text-sm text-gray-500">HT :</span>
                            <span class="text-2xl font-bold text-gray-900">{{ number_format($compromisData['previous']['total_commission_ht'] / 1000, 0, ',', ' ') }}</span>
                            <span class="text-lg font-medium text-gray-500">k&euro;</span>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-sm text-gray-400">TTC :</span>
                            <span class="text-xl font-semibold text-gray-600">{{ number_format($compromisData['previous']['total_commission'] / 1000, 0, ',', ' ') }}</span>
                            <span class="text-base font-medium text-gray-400">k&euro;</span>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">{{ $compromisData['previous']['count'] }} compromis</p>
                </div>

                {{-- Semaine en cours --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-keymex-red to-keymex-red-hover p-6 text-white shadow-lg">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white/10"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-32 w-32 rounded-full bg-white/5"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-white/80">Semaine en cours</p>
                                <p class="text-xs text-white/60">{{ $selectedWeek['start']->format('d/m') }} - {{ $selectedWeek['end']->format('d/m') }}</p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 space-y-1">
                            <div class="flex items-baseline gap-2">
                                <span class="text-sm text-white/70">HT :</span>
                                <span class="text-3xl font-bold tracking-tight">{{ number_format($compromisData['current']['total_commission_ht'] / 1000, 0, ',', ' ') }}</span>
                                <span class="text-xl font-medium">k&euro;</span>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-sm text-white/70">TTC :</span>
                                <span class="text-2xl font-semibold tracking-tight text-white/90">{{ number_format($compromisData['current']['total_commission'] / 1000, 0, ',', ' ') }}</span>
                                <span class="text-lg font-medium text-white/80">k&euro;</span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-white/70">
                            {{ $compromisData['current']['count'] }} compromis signés
                        </p>
                        @if($variations['compromis_ca_vs_previous'] !== null)
                            <div class="mt-3 flex items-center gap-1.5">
                                @if($variations['compromis_ca_vs_previous'] >= 0)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-2.5 py-1 text-xs font-semibold text-white">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                                        +{{ $variations['compromis_ca_vs_previous'] }}%
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-2.5 py-1 text-xs font-semibold text-white">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                        {{ $variations['compromis_ca_vs_previous'] }}%
                                    </span>
                                @endif
                                <span class="text-xs text-white/70">vs S-1</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Même semaine N-1 --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Même semaine N-1</p>
                            <p class="text-xs text-gray-400">{{ $lastYearWeek['start']->format('d/m/Y') }} - {{ $lastYearWeek['end']->format('d/m/Y') }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">N-1</span>
                    </div>
                    <div class="mt-4 space-y-1">
                        <div class="flex items-baseline gap-2">
                            <span class="text-sm text-gray-500">HT :</span>
                            <span class="text-2xl font-bold text-gray-900">{{ number_format($compromisData['lastYear']['total_commission_ht'] / 1000, 0, ',', ' ') }}</span>
                            <span class="text-lg font-medium text-gray-500">k&euro;</span>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-sm text-gray-400">TTC :</span>
                            <span class="text-xl font-semibold text-gray-600">{{ number_format($compromisData['lastYear']['total_commission'] / 1000, 0, ',', ' ') }}</span>
                            <span class="text-base font-medium text-gray-400">k&euro;</span>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">{{ $compromisData['lastYear']['count'] }} compromis</p>
                    @if($variations['compromis_ca_vs_lastYear'] !== null)
                        <div class="mt-3 flex items-center gap-1.5">
                            @if($variations['compromis_ca_vs_lastYear'] >= 0)
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                                    +{{ $variations['compromis_ca_vs_lastYear'] }}%
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                    {{ $variations['compromis_ca_vs_lastYear'] }}%
                                </span>
                            @endif
                            <span class="text-xs text-gray-400">vs N-1</span>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Mandats Exclusifs --}}
        <section class="mt-10">
            <div class="flex items-center gap-3 mb-5">
                <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-blue-50">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Mandats Exclusifs</h2>
            </div>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                {{-- Semaine précédente --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Semaine précédente</p>
                            <p class="text-xs text-gray-400">{{ $previousWeek['start']->format('d/m') }} - {{ $previousWeek['end']->format('d/m') }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">S-1</span>
                    </div>
                    <p class="mt-4 text-3xl font-bold text-gray-900">{{ $mandatesData['previous']['count'] }}</p>
                    <p class="mt-1 text-sm text-gray-500">mandats exclusifs</p>
                </div>

                {{-- Semaine en cours --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-white shadow-lg">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white/10"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-32 w-32 rounded-full bg-white/5"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-white/80">Semaine en cours</p>
                                <p class="text-xs text-white/60">{{ $selectedWeek['start']->format('d/m') }} - {{ $selectedWeek['end']->format('d/m') }}</p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-4 text-4xl font-bold tracking-tight">{{ $mandatesData['current']['count'] }}</p>
                        <p class="mt-1 text-sm text-white/70">mandats exclusifs signés</p>
                        @if($variations['mandates_vs_previous'] !== null)
                            <div class="mt-3 flex items-center gap-1.5">
                                @if($variations['mandates_vs_previous'] >= 0)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-2.5 py-1 text-xs font-semibold text-white">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                                        +{{ $variations['mandates_vs_previous'] }}%
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-2.5 py-1 text-xs font-semibold text-white">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                        {{ $variations['mandates_vs_previous'] }}%
                                    </span>
                                @endif
                                <span class="text-xs text-white/70">vs S-1</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Même semaine N-1 --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Même semaine N-1</p>
                            <p class="text-xs text-gray-400">{{ $lastYearWeek['start']->format('d/m/Y') }} - {{ $lastYearWeek['end']->format('d/m/Y') }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">N-1</span>
                    </div>
                    <p class="mt-4 text-3xl font-bold text-gray-900">{{ $mandatesData['lastYear']['count'] }}</p>
                    <p class="mt-1 text-sm text-gray-500">mandats exclusifs</p>
                    @if($variations['mandates_vs_lastYear'] !== null)
                        <div class="mt-3 flex items-center gap-1.5">
                            @if($variations['mandates_vs_lastYear'] >= 0)
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                                    +{{ $variations['mandates_vs_lastYear'] }}%
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                    {{ $variations['mandates_vs_lastYear'] }}%
                                </span>
                            @endif
                            <span class="text-xs text-gray-400">vs N-1</span>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- TOP 10 Conseillers --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-10">
            {{-- TOP 10 CA Compromis --}}
            <section>
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-red-50">
                        <svg class="h-5 w-5 text-keymex-red" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Top 10 CA Compromis</h2>
                </div>

                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
                    @if(count($topCompromis) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Conseiller</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">CA HT</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Compromis</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($topCompromis as $kpi)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($kpi['classement'] === 1)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-yellow-100 text-yellow-700 font-bold text-sm">1</span>
                                            @elseif($kpi['classement'] === 2)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-200 text-gray-700 font-bold text-sm">2</span>
                                            @elseif($kpi['classement'] === 3)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 text-orange-700 font-bold text-sm">3</span>
                                            @else
                                                <span class="text-gray-400 text-sm pl-2">{{ $kpi['classement'] }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="font-medium text-gray-900">{{ $kpi['name'] }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right">
                                            <span class="font-semibold text-keymex-red">{{ number_format($kpi['ca_compromis'] / 1.20 / 1000, 0, ',', ' ') }} k&euro;</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                {{ $kpi['nb_compromis'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Aucun compromis cette semaine</p>
                    </div>
                    @endif
                </div>
            </section>

            {{-- TOP 10 Mandats Exclusifs --}}
            <section>
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-blue-50">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Top 10 Mandats Exclusifs</h2>
                </div>

                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
                    @if(count($topMandats) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Conseiller</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Nb Mandats</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($topMandats as $kpi)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($kpi['classement'] === 1)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-yellow-100 text-yellow-700 font-bold text-sm">1</span>
                                            @elseif($kpi['classement'] === 2)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-200 text-gray-700 font-bold text-sm">2</span>
                                            @elseif($kpi['classement'] === 3)
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 text-orange-700 font-bold text-sm">3</span>
                                            @else
                                                <span class="text-gray-400 text-sm pl-2">{{ $kpi['classement'] }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="font-medium text-gray-900">{{ $kpi['name'] }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                {{ $kpi['nb_mandats'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Aucun mandat exclusif cette semaine</p>
                    </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
