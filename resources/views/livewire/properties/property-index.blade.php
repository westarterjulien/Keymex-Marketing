<div>
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Biens immobiliers</h1>
            <p class="mt-1 text-sm text-gray-500">Suivi des biens en compromis et vendus</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('properties.for-sale') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-keymex-red">
                Voir biens a vendre
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
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
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button wire:click="setTab('compromis')" type="button"
                        class="{{ $activeTab === 'compromis' ? 'border-keymex-red text-keymex-red' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium flex items-center">
                    Compromis
                    @if($urgentCount > 0)
                        <span class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">
                            {{ $urgentCount }} urgent{{ $urgentCount > 1 ? 's' : '' }}
                        </span>
                    @endif
                    @if($nearDeadlineCount > 0)
                        <span class="ml-2 inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-700">
                            {{ $nearDeadlineCount }} bientot
                        </span>
                    @endif
                </button>
                <button wire:click="setTab('sold')" type="button"
                        class="{{ $activeTab === 'sold' ? 'border-keymex-red text-keymex-red' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                    Vendus recemment
                </button>
            </nav>
        </div>

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

                @if($activeTab === 'sold')
                    <div>
                        <select wire:model.live="soldDays"
                                class="block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-keymex-red sm:text-sm">
                            <option value="7">7 derniers jours</option>
                            <option value="15">15 derniers jours</option>
                            <option value="30">30 derniers jours</option>
                            <option value="60">60 derniers jours</option>
                            <option value="90">90 derniers jours</option>
                        </select>
                    </div>
                @endif
            </div>

            @if($activeTab === 'compromis')
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
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
                                         class="h-full w-full object-cover cursor-pointer"
                                         wire:click="openPhotoModal({{ json_encode($property['photos']) }}, '{{ $property['reference'] }}')" />
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

                                {{-- Délai de vente (mandat → compromis) --}}
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
                                        <label class="flex items-center justify-between cursor-pointer">
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox"
                                                       wire:click="toggleCommunication('{{ $property['id'] }}')"
                                                       {{ $communication ? 'checked' : '' }}
                                                       class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500" />
                                                <span class="text-xs font-medium text-gray-700">Communication RS</span>
                                            </div>
                                            @if($communication)
                                                <span class="text-[10px] text-gray-400">{{ $communication->action_date->format('d/m') }}</span>
                                            @endif
                                        </label>
                                    @endif
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
                </div>
            @else
                {{-- Tableau des biens vendus --}}
                <div class="bg-white shadow rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Bien</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Prix</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Commission</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Conseiller</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Vendu le</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sm:pr-6">Duree vente</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($soldProperties as $property)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                        <div class="flex items-center gap-3">
                                            @if(!empty($property['photos']))
                                                <img src="{{ $property['photos'][0] }}"
                                                     alt="{{ $property['reference'] }}"
                                                     class="h-12 w-12 rounded object-cover cursor-pointer"
                                                     wire:click="openPhotoModal({{ json_encode($property['photos']) }}, '{{ $property['reference'] }}')" />
                                            @else
                                                <div class="h-12 w-12 rounded bg-gray-100 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-medium text-keymex-red">{{ $property['reference'] }}</p>
                                                <p class="text-sm font-medium text-gray-900">{{ $property['type'] }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $property['address']['city'] }}
                                                    @if($property['surface']) - {{ $property['surface'] }} m2 @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 font-medium">
                                        @if($property['price'])
                                            {{ number_format($property['price'], 0, ',', ' ') }} EUR
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if($property['commission'] > 0)
                                            {{ number_format($property['commission'], 0, ',', ' ') }} EUR
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $property['advisor']['name'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $property['dates']['sale'] ? \Carbon\Carbon::parse($property['dates']['sale'])->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm sm:pr-6">
                                        @if($property['sale_duration_days'])
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $property['sale_duration_days'] <= 30 ? 'bg-green-100 text-green-700' : ($property['sale_duration_days'] <= 90 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                                {{ $property['sale_duration_days'] }} jours
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune vente recente</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            @if($search)
                                                Aucun resultat pour "{{ $search }}".
                                            @else
                                                Aucune vente dans les {{ $soldDays }} derniers jours.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Photos --}}
    @if($showPhotoModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="photo-modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center px-4 py-4 text-center">
                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" wire:click="closePhotoModal"></div>

                <div class="relative z-10 w-full max-w-4xl transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all">
                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                        <h3 class="text-lg font-medium text-gray-900">Photos - {{ $modalPropertyRef }}</h3>
                        <button wire:click="closePhotoModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-4 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($modalPhotos as $photo)
                                <a href="{{ $photo }}" target="_blank" class="block">
                                    <img src="{{ $photo }}" alt="Photo" class="w-full h-48 object-cover rounded-lg hover:opacity-90 transition-opacity" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
