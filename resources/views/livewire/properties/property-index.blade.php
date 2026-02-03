<div>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Biens immobiliers</h1>
        <p class="mt-1 text-sm text-gray-500">Suivi des biens pour communication RS</p>
    </div>

    {{-- Onglets --}}
    <div class="mt-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="setTab('compromis')"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors {{ $activeTab === 'compromis' ? 'border-keymex-red text-keymex-red' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Sous compromis
                @if($activeTab === 'compromis' && $compromisProperties->count() > 0)
                    <span class="ml-2 rounded-full bg-keymex-red/10 px-2.5 py-0.5 text-xs font-medium text-keymex-red">
                        {{ $compromisProperties->count() }}
                    </span>
                @endif
            </button>
            <button wire:click="setTab('vendus')"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors {{ $activeTab === 'vendus' ? 'border-keymex-red text-keymex-red' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Vendus (Actes signes)
                @if($activeTab === 'vendus' && $soldProperties->count() > 0)
                    <span class="ml-2 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                        {{ $soldProperties->count() }}
                    </span>
                @endif
            </button>
        </nav>
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
                        Les donnees des biens immobiliers ne sont pas accessibles pour le moment.
                        Veuillez reessayer plus tard ou contacter l'administrateur systeme.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-6">
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1">
                <label for="search" class="sr-only">Rechercher</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search"
                           type="search"
                           id="search"
                           class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-keymex-red sm:text-sm"
                           placeholder="Rechercher par reference, ville ou conseiller...">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            {{-- Onglet Compromis --}}
            @if($activeTab === 'compromis')
            @forelse($compromisProperties as $property)
                @php
                    $deadline = isset($property['dates']['legal_deadline']) ? \Carbon\Carbon::parse($property['dates']['legal_deadline']) : null;
                    $daysLeft = $deadline ? (int) now()->diffInDays($deadline, false) : null;
                    $canCommunicate = $property['is_legal_deadline_passed'];
                    $compromisDelay = $property['compromis_delay_days'] ?? null;
                    $communication = $communications[$property['id']] ?? null;
                @endphp
                <div class="bg-white shadow rounded-lg overflow-hidden {{ $canCommunicate ? 'ring-2 ring-green-500' : 'opacity-60' }}">
                    {{-- Bandeau statut delai --}}
                    @if($property['is_legal_deadline_passed'])
                        <div class="bg-green-500 px-3 py-1.5 text-center">
                            <p class="text-xs font-medium text-white">Pret pour communication</p>
                        </div>
                    @elseif($daysLeft !== null)
                        <div class="{{ $daysLeft <= 3 ? 'bg-yellow-500' : 'bg-blue-500' }} px-3 py-1.5 text-center">
                            <p class="text-xs font-medium text-white">
                                @if($daysLeft <= 0)
                                    Delai SRU termine aujourd'hui
                                @elseif($daysLeft === 1)
                                    1 jour restant
                                @else
                                    {{ $daysLeft }} jours restants
                                @endif
                            </p>
                        </div>
                    @endif

                    {{-- Photo principale --}}
                    <div class="relative h-32 bg-gray-100">
                        @if(!empty($property['photos']))
                            <img src="{{ $property['photos'][0] }}"
                                 alt="{{ $property['reference'] }}"
                                 class="h-full w-full object-cover" />
                            @if(count($property['photos']) > 1)
                                <span class="absolute bottom-1 right-1 bg-black/70 text-white text-[10px] px-1.5 py-0.5 rounded">
                                    {{ count($property['photos']) }} photos
                                </span>
                            @endif
                        @else
                            <div class="h-full w-full flex items-center justify-center">
                                <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                        @endif

                        {{-- Badge communication RS --}}
                        @if(in_array($property['id'], $communicatedIds))
                            <span class="absolute top-1 left-1 bg-green-500 text-white text-[10px] px-1.5 py-0.5 rounded-full flex items-center gap-0.5">
                                <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                RS
                            </span>
                        @endif
                    </div>

                    <div class="p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-keymex-red">{{ $property['reference'] }}</p>
                                <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $property['type'] }}</h3>
                                <p class="text-xs text-gray-500 truncate">{{ $property['address']['city'] }}</p>
                            </div>
                            @if($property['price'])
                                <p class="text-sm font-bold text-gray-900 whitespace-nowrap">
                                    {{ number_format($property['price'] / 1000, 0, ',', ' ') }}k
                                </p>
                            @endif
                        </div>

                        <div class="mt-2 flex flex-wrap items-center gap-1.5 text-xs text-gray-500">
                            @if($property['surface'])
                                <span>{{ $property['surface'] }}m2</span>
                            @endif
                            @if($property['rooms'])
                                <span>{{ $property['rooms'] }}p</span>
                            @endif
                            @if($property['bedrooms'])
                                <span>{{ $property['bedrooms'] }}ch</span>
                            @endif
                        </div>

                        {{-- Delai de vente (mandat -> compromis) --}}
                        @if($compromisDelay !== null)
                            <div class="mt-2">
                                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded {{ $compromisDelay <= 30 ? 'text-green-700 bg-green-50' : ($compromisDelay <= 90 ? 'text-yellow-700 bg-yellow-50' : 'text-gray-600 bg-gray-100') }}" title="Delai entre mandat et compromis">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Vendu en {{ $compromisDelay }} jours
                                </span>
                            </div>
                        @endif

                        <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs">
                            <span class="text-gray-500 truncate">{{ $property['advisor']['name'] }}</span>
                            <span class="text-gray-400">{{ $property['dates']['compromis'] ? \Carbon\Carbon::parse($property['dates']['compromis'])->format('d/m') : '-' }}</span>
                        </div>

                        {{-- Communication RS --}}
                        <div class="mt-2 pt-2 border-t border-gray-100">
                            @if($canCommunicate)
                                <button wire:click="openCommunicationModal('{{ $property['id'] }}', '{{ $property['reference'] }}')"
                                        class="w-full flex items-center justify-between text-left hover:bg-gray-50 rounded px-1 py-0.5 -mx-1 transition-colors">
                                    <div class="flex items-center gap-2">
                                        @if($communication)
                                            <span class="flex h-4 w-4 items-center justify-center rounded bg-green-500 text-white">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="flex h-4 w-4 items-center justify-center rounded border border-gray-300 bg-white">
                                            </span>
                                        @endif
                                        <span class="text-xs font-medium text-gray-700">Communication RS</span>
                                    </div>
                                    @if($communication)
                                        <span class="text-[10px] text-gray-400">{{ $communication->action_date->format('d/m/Y') }}</span>
                                    @endif
                                </button>
                            @endif
                        </div>

                        {{-- Generer Story --}}
                        <div class="mt-2 pt-2 border-t border-gray-100">
                            <button onclick="openPhotoSelector('{{ $property['id'] }}', '{{ $property['reference'] }}', {{ json_encode($property['photos'] ?? []) }}, 'sous-compromis')"
                                    class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-white bg-gradient-to-r from-pink-500 to-purple-500 rounded-lg hover:from-pink-600 hover:to-purple-600 transition-all">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Generer Story Instagram
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white shadow rounded-lg px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun bien en compromis</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if($search)
                                Aucun resultat pour "{{ $search }}".
                            @else
                                Il n'y a actuellement aucun bien sous compromis.
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
            @endif

            {{-- Onglet Vendus --}}
            @if($activeTab === 'vendus')
            @forelse($soldProperties as $property)
                @php
                    $communication = $communications[$property['id']] ?? null;
                    $saleDuration = $property['sale_duration_days'] ?? null;
                @endphp
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    {{-- Bandeau Vendu --}}
                    <div class="bg-green-600 px-3 py-1.5 text-center">
                        <p class="text-xs font-medium text-white">Vendu - Acte signe</p>
                    </div>

                    {{-- Photo principale --}}
                    <div class="relative h-32 bg-gray-100">
                        @if(!empty($property['photos']))
                            <img src="{{ $property['photos'][0] }}"
                                 alt="{{ $property['reference'] }}"
                                 class="h-full w-full object-cover" />
                            @if(count($property['photos']) > 1)
                                <span class="absolute bottom-1 right-1 bg-black/70 text-white text-[10px] px-1.5 py-0.5 rounded">
                                    {{ count($property['photos']) }} photos
                                </span>
                            @endif
                        @else
                            <div class="h-full w-full flex items-center justify-center">
                                <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                        @endif

                        {{-- Badge communication RS --}}
                        @if(in_array($property['id'], $communicatedIds))
                            <span class="absolute top-1 left-1 bg-green-500 text-white text-[10px] px-1.5 py-0.5 rounded-full flex items-center gap-0.5">
                                <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                RS
                            </span>
                        @endif
                    </div>

                    <div class="p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-keymex-red">{{ $property['reference'] }}</p>
                                <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $property['type'] }}</h3>
                                <p class="text-xs text-gray-500 truncate">{{ $property['address']['city'] }}</p>
                            </div>
                            @if($property['price'])
                                <p class="text-sm font-bold text-gray-900 whitespace-nowrap">
                                    {{ number_format($property['price'] / 1000, 0, ',', ' ') }}k
                                </p>
                            @endif
                        </div>

                        <div class="mt-2 flex flex-wrap items-center gap-1.5 text-xs text-gray-500">
                            @if($property['surface'])
                                <span>{{ $property['surface'] }}m2</span>
                            @endif
                            @if($property['rooms'])
                                <span>{{ $property['rooms'] }}p</span>
                            @endif
                            @if($property['bedrooms'])
                                <span>{{ $property['bedrooms'] }}ch</span>
                            @endif
                        </div>

                        {{-- Commission --}}
                        @if(isset($property['commission']) && $property['commission'] > 0)
                            <div class="mt-2">
                                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded text-green-700 bg-green-50">
                                    Commission: {{ number_format($property['commission'], 0, ',', ' ') }} EUR
                                </span>
                            </div>
                        @endif

                        {{-- Duree de vente --}}
                        @if($saleDuration !== null)
                            <div class="mt-2">
                                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded {{ $saleDuration <= 60 ? 'text-green-700 bg-green-50' : ($saleDuration <= 120 ? 'text-yellow-700 bg-yellow-50' : 'text-gray-600 bg-gray-100') }}" title="Duree entre mandat et acte">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Vendu en {{ $saleDuration }} jours
                                </span>
                            </div>
                        @endif

                        <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs">
                            <span class="text-gray-500 truncate">{{ $property['advisor']['name'] }}</span>
                            <span class="text-gray-400">Acte: {{ $property['dates']['sale'] ? \Carbon\Carbon::parse($property['dates']['sale'])->format('d/m/Y') : '-' }}</span>
                        </div>

                        {{-- Communication RS --}}
                        <div class="mt-2 pt-2 border-t border-gray-100">
                            <button wire:click="openCommunicationModal('{{ $property['id'] }}', '{{ $property['reference'] }}')"
                                    class="w-full flex items-center justify-between text-left hover:bg-gray-50 rounded px-1 py-0.5 -mx-1 transition-colors">
                                <div class="flex items-center gap-2">
                                    @if($communication)
                                        <span class="flex h-4 w-4 items-center justify-center rounded bg-green-500 text-white">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @else
                                        <span class="flex h-4 w-4 items-center justify-center rounded border border-gray-300 bg-white">
                                        </span>
                                    @endif
                                    <span class="text-xs font-medium text-gray-700">Communication RS</span>
                                </div>
                                @if($communication)
                                    <span class="text-[10px] text-gray-400">{{ $communication->action_date->format('d/m/Y') }}</span>
                                @endif
                            </button>
                        </div>

                        {{-- Generer Story --}}
                        <div class="mt-2 pt-2 border-t border-gray-100">
                            <button onclick="openPhotoSelector('{{ $property['id'] }}', '{{ $property['reference'] }}', {{ json_encode($property['photos'] ?? []) }}, 'vendu')"
                                    class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-white bg-gradient-to-r from-pink-500 to-purple-500 rounded-lg hover:from-pink-600 hover:to-purple-600 transition-all">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Generer Story Instagram
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white shadow rounded-lg px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun bien vendu</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if($search)
                                Aucun resultat pour "{{ $search }}".
                            @else
                                Il n'y a pas de biens vendus recemment.
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
            @endif
        </div>
    </div>

    {{-- Modal Communication RS --}}
    @if($showCommunicationModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="communication-modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center px-4 py-4 text-center">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="closeCommunicationModal"></div>

                <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all">
                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                        <h3 class="text-lg font-medium text-gray-900">Communication RS - {{ $communicationPropertyRef }}</h3>
                        <button wire:click="closeCommunicationModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-4">
                        <label for="communication-date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de communication
                        </label>
                        <input type="date"
                               id="communication-date"
                               wire:model.live="communicationDate"
                               class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-keymex-red sm:text-sm" />
                        <p class="mt-2 text-xs text-gray-500">
                            Selectionnez la date de publication sur les reseaux sociaux.
                        </p>
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 bg-gray-50">
                        @if($hasCommunication)
                            <button wire:click="deleteCommunication"
                                    class="inline-flex items-center gap-1.5 rounded-md bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Supprimer
                            </button>
                        @else
                            <div></div>
                        @endif
                        <div class="flex items-center gap-2">
                            <button wire:click="closeCommunicationModal"
                                    class="rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Annuler
                            </button>
                            <button wire:click="saveCommunication"
                                    class="rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-700">
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Photo Selector - Design moderne glassmorphism --}}
    <div id="photo-selector-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="photo-selector-title" role="dialog" aria-modal="true">
        {{-- Backdrop avec blur --}}
        <div class="fixed inset-0 bg-gradient-to-br from-purple-900/40 via-pink-900/40 to-orange-900/40 backdrop-blur-sm transition-opacity" onclick="closePhotoSelector()"></div>

        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="relative w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white/95 backdrop-blur-xl shadow-2xl ring-1 ring-white/20 transition-all">
                {{-- Header avec degrage --}}
                <div class="relative bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 backdrop-blur">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Choisir la photo</h3>
                                <p class="text-sm text-white/80"><span id="photo-selector-ref"></span></p>
                            </div>
                        </div>
                        <button onclick="closePhotoSelector()" class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4 flex items-center gap-2">
                        <svg class="h-4 w-4 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Cliquez sur une photo pour generer la story Instagram
                    </p>
                    <div id="photo-selector-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 max-h-[60vh] overflow-y-auto pr-2">
                    </div>
                </div>

                {{-- Footer --}}
                <div class="border-t border-gray-100 bg-gray-50/80 px-6 py-3">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span id="photo-count-text">0 photos disponibles</span>
                        <span class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/>
                            </svg>
                            Story Instagram
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Story Generated - Design moderne --}}
    <div id="story-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="story-modal-title" role="dialog" aria-modal="true">
        {{-- Backdrop avec blur --}}
        <div class="fixed inset-0 bg-gradient-to-br from-purple-900/50 via-pink-900/50 to-orange-900/50 backdrop-blur-md transition-opacity" onclick="closeStoryModal()"></div>

        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="relative w-full max-w-sm transform overflow-hidden rounded-2xl bg-white/95 backdrop-blur-xl shadow-2xl ring-1 ring-white/20 transition-all">
                {{-- Header avec degrage --}}
                <div class="relative bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 px-5 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/>
                            </svg>
                            <h3 class="text-base font-semibold text-white">Story Instagram</h3>
                        </div>
                        <button onclick="closeStoryModal()" class="flex h-7 w-7 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-5">
                    {{-- Loading state --}}
                    <div id="story-loading" class="flex flex-col items-center justify-center py-12">
                        <div class="relative">
                            <div class="h-16 w-16 rounded-full border-4 border-purple-100"></div>
                            <div class="absolute top-0 left-0 h-16 w-16 rounded-full border-4 border-transparent border-t-pink-500 animate-spin"></div>
                        </div>
                        <p class="mt-5 text-sm font-medium text-gray-700">Generation en cours...</p>
                        <p class="mt-1 text-xs text-gray-400">Preparation de votre story</p>
                    </div>

                    {{-- Result state --}}
                    <div id="story-result" class="hidden">
                        <div class="relative rounded-xl overflow-hidden shadow-lg ring-1 ring-black/5">
                            <img id="story-image" src="" alt="Story generee" class="w-full" />
                        </div>
                        <div class="mt-5 space-y-3">
                            <a id="story-download" href="" download class="flex w-full items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 rounded-xl hover:opacity-90 transition-opacity shadow-lg shadow-purple-500/25">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Telecharger la story
                            </a>
                            <button onclick="closeStoryModal()" class="flex w-full items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                                Fermer
                            </button>
                        </div>
                    </div>

                    {{-- Error state --}}
                    <div id="story-error" class="hidden text-center py-10">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                            <svg class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <p class="mt-4 text-sm font-medium text-gray-900">Erreur de generation</p>
                        <p id="story-error-message" class="mt-2 text-sm text-red-600"></p>
                        <button onclick="closeStoryModal()" class="mt-5 px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="{{ asset('js/property-story.js') }}"></script>
@endpush
