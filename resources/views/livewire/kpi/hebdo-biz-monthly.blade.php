<div>
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hebdo Biz - Mensuel</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $selectedMonth['start']->translatedFormat('F Y') }}
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <a href="{{ route('kpi.weekly') }}"
               class="inline-flex items-center gap-2 rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Vue hebdomadaire
            </a>
        </div>
    </div>

    {{-- Navigation mois --}}
    <div class="mt-6 flex items-center justify-center gap-4">
        <button wire:click="previousMonth"
                class="inline-flex items-center gap-2 rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Mois precedent
        </button>

        <div class="text-center min-w-[200px]">
            <p class="text-lg font-semibold text-gray-900">
                {{ $selectedMonth['start']->translatedFormat('F Y') }}
            </p>
            @if(!$isCurrentMonth)
                <button wire:click="currentMonth" class="text-xs text-keymex-red hover:underline">
                    Revenir au mois en cours
                </button>
            @else
                <span class="text-xs text-green-600 font-medium">Mois en cours</span>
            @endif
        </div>

        <button wire:click="nextMonth"
                @if($isCurrentMonth) disabled @endif
                class="inline-flex items-center gap-2 rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
            Mois suivant
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    @if($mongoDbError)
        <div class="mt-6 rounded-lg bg-yellow-50 border border-yellow-200 p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Connexion MongoDB indisponible</h3>
                    <p class="mt-1 text-sm text-yellow-700">
                        Les donnees ne sont pas accessibles pour le moment. Veuillez reessayer plus tard.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- C.A Compromis --}}
    <div class="mt-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">C.A Compromis</h2>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Mois selectionne --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-keymex-red px-4 py-3">
                    <h3 class="text-sm font-medium text-white">Mois selectionne</h3>
                    <p class="text-xs text-red-100">{{ $selectedMonth['start']->translatedFormat('F Y') }}</p>
                </div>
                <div class="p-6">
                    <p class="text-3xl font-bold text-gray-900">
                        {{ number_format($compromisData['current']['total_price'] / 1000, 0, ',', ' ') }} k
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $compromisData['current']['count'] }} compromis
                    </p>
                </div>
            </div>

            {{-- Mois précédent --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-gray-100 px-4 py-3">
                    <h3 class="text-sm font-medium text-gray-700">Mois precedent</h3>
                    <p class="text-xs text-gray-500">{{ $previousMonth['start']->translatedFormat('F Y') }}</p>
                </div>
                <div class="p-6">
                    <p class="text-2xl font-bold text-gray-700">
                        {{ number_format($compromisData['previous']['total_price'] / 1000, 0, ',', ' ') }} k
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $compromisData['previous']['count'] }} compromis
                    </p>
                    @if($variations['compromis_ca_vs_previous'] !== null)
                        <p class="mt-2 text-sm font-medium {{ $variations['compromis_ca_vs_previous'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            @if($variations['compromis_ca_vs_previous'] >= 0)
                                <svg class="inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                            @else
                                <svg class="inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            @endif
                            {{ $variations['compromis_ca_vs_previous'] > 0 ? '+' : '' }}{{ $variations['compromis_ca_vs_previous'] }}% vs M-1
                        </p>
                    @endif
                </div>
            </div>

            {{-- Même mois N-1 --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-gray-100 px-4 py-3">
                    <h3 class="text-sm font-medium text-gray-700">Meme mois N-1</h3>
                    <p class="text-xs text-gray-500">{{ $lastYearMonth['start']->translatedFormat('F Y') }}</p>
                </div>
                <div class="p-6">
                    <p class="text-2xl font-bold text-gray-700">
                        {{ number_format($compromisData['lastYear']['total_price'] / 1000, 0, ',', ' ') }} k
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $compromisData['lastYear']['count'] }} compromis
                    </p>
                    @if($variations['compromis_ca_vs_lastYear'] !== null)
                        <p class="mt-2 text-sm font-medium {{ $variations['compromis_ca_vs_lastYear'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            @if($variations['compromis_ca_vs_lastYear'] >= 0)
                                <svg class="inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                            @else
                                <svg class="inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            @endif
                            {{ $variations['compromis_ca_vs_lastYear'] > 0 ? '+' : '' }}{{ $variations['compromis_ca_vs_lastYear'] }}% vs N-1
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Mandats Exclusifs --}}
    <div class="mt-10">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Mandats Exclusifs</h2>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Mois selectionne --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-keymex-red px-4 py-3">
                    <h3 class="text-sm font-medium text-white">Mois selectionne</h3>
                    <p class="text-xs text-red-100">{{ $selectedMonth['start']->translatedFormat('F Y') }}</p>
                </div>
                <div class="p-6">
                    <p class="text-3xl font-bold text-gray-900">
                        {{ $mandatesData['current']['count'] }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">mandats exclusifs</p>
                </div>
            </div>

            {{-- Mois précédent --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-gray-100 px-4 py-3">
                    <h3 class="text-sm font-medium text-gray-700">Mois precedent</h3>
                    <p class="text-xs text-gray-500">{{ $previousMonth['start']->translatedFormat('F Y') }}</p>
                </div>
                <div class="p-6">
                    <p class="text-2xl font-bold text-gray-700">
                        {{ $mandatesData['previous']['count'] }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">mandats exclusifs</p>
                    @if($variations['mandates_vs_previous'] !== null)
                        <p class="mt-2 text-sm font-medium {{ $variations['mandates_vs_previous'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            @if($variations['mandates_vs_previous'] >= 0)
                                <svg class="inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                            @else
                                <svg class="inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            @endif
                            {{ $variations['mandates_vs_previous'] > 0 ? '+' : '' }}{{ $variations['mandates_vs_previous'] }}% vs M-1
                        </p>
                    @endif
                </div>
            </div>

            {{-- Même mois N-1 --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-gray-100 px-4 py-3">
                    <h3 class="text-sm font-medium text-gray-700">Meme mois N-1</h3>
                    <p class="text-xs text-gray-500">{{ $lastYearMonth['start']->translatedFormat('F Y') }}</p>
                </div>
                <div class="p-6">
                    <p class="text-2xl font-bold text-gray-700">
                        {{ $mandatesData['lastYear']['count'] }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">mandats exclusifs</p>
                    @if($variations['mandates_vs_lastYear'] !== null)
                        <p class="mt-2 text-sm font-medium {{ $variations['mandates_vs_lastYear'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            @if($variations['mandates_vs_lastYear'] >= 0)
                                <svg class="inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                            @else
                                <svg class="inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            @endif
                            {{ $variations['mandates_vs_lastYear'] > 0 ? '+' : '' }}{{ $variations['mandates_vs_lastYear'] }}% vs N-1
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
