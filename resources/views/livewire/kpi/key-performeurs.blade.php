<div class="space-y-6">
    {{-- Header avec gradient KEYMEX --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-keymex-red via-red-600 to-red-700 p-6 shadow-xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\" fill=\"rgba(255,255,255,0.07)\"%3E%3C/path%3E%3C/svg%3E')] opacity-60"></div>
        <div class="absolute top-0 right-0 -mt-16 -mr-16 h-64 w-64 rounded-full bg-white/5"></div>
        <div class="absolute bottom-0 left-0 -mb-16 -ml-16 h-48 w-48 rounded-full bg-white/5"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">KeyPerformeurs</h1>
                    <p class="mt-0.5 text-sm text-white/80">
                        Classement CA HT &bull; {{ $periodInfo['label'] }}
                    </p>
                </div>
            </div>

            {{-- Lien retour Hebdo Biz --}}
            <a href="{{ route('kpi.weekly') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-white/10 backdrop-blur-sm px-4 py-2.5 text-sm font-medium text-white hover:bg-white/20 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Hebdo Biz
            </a>
        </div>
    </div>

    {{-- Filtres de période --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            {{-- Type de filtre --}}
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Période :</span>
                <div class="inline-flex rounded-lg bg-gray-100 p-1">
                    <button wire:click="$set('filterType', 'year')"
                            class="{{ $filterType === 'year' ? 'bg-white shadow-sm text-keymex-red' : 'text-gray-600 hover:text-gray-900' }} px-3 py-1.5 text-sm font-medium rounded-md transition-all">
                        Année
                    </button>
                    <button wire:click="$set('filterType', 'semester')"
                            class="{{ $filterType === 'semester' ? 'bg-white shadow-sm text-keymex-red' : 'text-gray-600 hover:text-gray-900' }} px-3 py-1.5 text-sm font-medium rounded-md transition-all">
                        Semestre
                    </button>
                    <button wire:click="$set('filterType', 'quarter')"
                            class="{{ $filterType === 'quarter' ? 'bg-white shadow-sm text-keymex-red' : 'text-gray-600 hover:text-gray-900' }} px-3 py-1.5 text-sm font-medium rounded-md transition-all">
                        Trimestre
                    </button>
                    <button wire:click="$set('filterType', 'custom')"
                            class="{{ $filterType === 'custom' ? 'bg-white shadow-sm text-keymex-red' : 'text-gray-600 hover:text-gray-900' }} px-3 py-1.5 text-sm font-medium rounded-md transition-all">
                        Personnalisé
                    </button>
                </div>
            </div>

            {{-- Sélecteurs selon le type --}}
            <div class="flex items-center gap-3 flex-1">
                @if($filterType !== 'custom')
                    {{-- Navigation période --}}
                    <div class="inline-flex items-center gap-2 rounded-lg bg-gray-50 p-1 ring-1 ring-gray-200">
                        <button wire:click="previousPeriod"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <div class="flex items-center gap-2 px-2">
                            {{-- Année --}}
                            <select wire:model.live="selectedYear"
                                    class="bg-transparent border-0 text-sm font-semibold text-gray-900 focus:ring-0 cursor-pointer pr-6">
                                @for($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>

                            @if($filterType === 'semester')
                                <select wire:model.live="selectedSemester"
                                        class="bg-transparent border-0 text-sm font-semibold text-gray-900 focus:ring-0 cursor-pointer pr-6">
                                    <option value="1">S1 (Jan-Juin)</option>
                                    <option value="2">S2 (Juil-Déc)</option>
                                </select>
                            @elseif($filterType === 'quarter')
                                <select wire:model.live="selectedQuarter"
                                        class="bg-transparent border-0 text-sm font-semibold text-gray-900 focus:ring-0 cursor-pointer pr-6">
                                    <option value="1">T1 (Jan-Mars)</option>
                                    <option value="2">T2 (Avr-Juin)</option>
                                    <option value="3">T3 (Juil-Sept)</option>
                                    <option value="4">T4 (Oct-Déc)</option>
                                </select>
                            @endif
                        </div>

                        <button wire:click="nextPeriod"
                                @if(!$this->canGoNext()) disabled @endif
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                @else
                    {{-- Dates personnalisées --}}
                    <div class="flex items-center gap-2">
                        <input type="date" wire:model="customStartDate"
                               class="rounded-lg border-gray-300 text-sm focus:border-keymex-red focus:ring-keymex-red">
                        <span class="text-gray-400">→</span>
                        <input type="date" wire:model="customEndDate"
                               class="rounded-lg border-gray-300 text-sm focus:border-keymex-red focus:ring-keymex-red">
                        <button wire:click="applyCustomDates"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-keymex-red px-3 py-2 text-sm font-medium text-white hover:bg-keymex-red-hover transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Appliquer
                        </button>
                    </div>
                @endif
            </div>

            {{-- Info période --}}
            <div class="text-sm text-gray-500">
                {{ $periodInfo['start']->format('d/m/Y') }} - {{ $periodInfo['end']->format('d/m/Y') }}
            </div>
        </div>
    </div>

    {{-- Loading overlay --}}
    <div wire:loading.flex class="fixed inset-0 z-50 items-center justify-center bg-black/30 backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-8 shadow-2xl flex flex-col items-center gap-4">
            <div class="w-12 h-12 border-4 border-keymex-red/20 border-t-keymex-red rounded-full animate-spin"></div>
            <span class="text-sm font-medium text-gray-600">Chargement...</span>
        </div>
    </div>

    {{-- Cartes des catégories --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
            $categories = [
                'elite' => ['icon' => '👑', 'label' => 'Elite', 'min' => '300K€+ HT', 'bg' => 'bg-purple-600', 'text' => 'text-white'],
                'platine' => ['icon' => '💎', 'label' => 'Platine', 'min' => '200-300K€ HT', 'bg' => 'bg-slate-500', 'text' => 'text-white'],
                'or' => ['icon' => '🥇', 'label' => 'Or', 'min' => '150-200K€ HT', 'bg' => 'bg-yellow-500', 'text' => 'text-yellow-900'],
                'argent' => ['icon' => '🥈', 'label' => 'Argent', 'min' => '100-150K€ HT', 'bg' => 'bg-gray-400', 'text' => 'text-gray-800'],
                'bronze' => ['icon' => '🥉', 'label' => 'Bronze', 'min' => '50-100K€ HT', 'bg' => 'bg-orange-600', 'text' => 'text-white'],
                'non_classe' => ['icon' => '🚀', 'label' => 'En progression', 'min' => '< 50K€ HT', 'bg' => 'bg-gray-200', 'text' => 'text-gray-700'],
            ];
        @endphp

        @foreach($categories as $key => $cat)
            <div class="rounded-xl {{ $cat['bg'] }} p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xl">{{ $cat['icon'] }}</span>
                    <span class="text-xs font-semibold {{ $cat['text'] }} uppercase tracking-wide">{{ $cat['label'] }}</span>
                </div>
                <div class="text-3xl font-bold {{ $cat['text'] }}">{{ $stats[$key]['count'] ?? 0 }}</div>
                <div class="text-xs {{ $cat['text'] }} opacity-75 mt-1">{{ $cat['min'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Tableau des conseillers --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Classement</h2>
                <p class="text-sm text-gray-500">{{ count($conseillers) }} conseillers</p>
            </div>
            <div class="text-sm text-gray-500">
                CA total HT : <span class="font-semibold text-gray-900">{{ number_format(collect($conseillers)->sum('ca'), 0, ',', ' ') }} €</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Conseiller</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Dossiers</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">CA HT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($conseillers as $conseiller)
                        @php
                            $catInfo = \App\Livewire\Kpi\KeyPerformeurs::getCategoryInfo($conseiller['category']);
                            $isTop3 = $conseiller['rank'] <= 3;
                            $badgeClasses = match($conseiller['category']) {
                                'elite' => 'bg-purple-100 text-purple-800',
                                'platine' => 'bg-slate-100 text-slate-800',
                                'or' => 'bg-yellow-100 text-yellow-800',
                                'argent' => 'bg-gray-100 text-gray-800',
                                'bronze' => 'bg-orange-100 text-orange-800',
                                default => 'bg-gray-50 text-gray-600',
                            };
                        @endphp
                        <tr class="{{ $isTop3 ? 'bg-amber-50/50' : 'hover:bg-gray-50' }} transition-colors">
                            <td class="px-6 py-4">
                                @if($conseiller['rank'] === 1)
                                    <span class="text-2xl">🥇</span>
                                @elseif($conseiller['rank'] === 2)
                                    <span class="text-2xl">🥈</span>
                                @elseif($conseiller['rank'] === 3)
                                    <span class="text-2xl">🥉</span>
                                @else
                                    <span class="text-sm font-semibold text-gray-400">{{ $conseiller['rank'] }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900 {{ $isTop3 ? 'text-base' : 'text-sm' }}">
                                    {{ $conseiller['name'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                    <span>{{ $catInfo['icon'] }}</span>
                                    {{ $catInfo['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-600">{{ $conseiller['nb_dossiers'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-semibold {{ $isTop3 ? 'text-keymex-red text-base' : 'text-gray-900 text-sm' }}">
                                    {{ number_format($conseiller['ca'], 0, ',', ' ') }} € HT
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Aucun compromis sur cette période
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
