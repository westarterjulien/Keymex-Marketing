<div>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Biens a vendre</h1>
        <p class="mt-1 text-sm text-gray-500">Mandats exclusifs en cours de commercialisation</p>
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
            <div class="flex items-center text-sm text-gray-500">
                {{ $properties->count() }} bien{{ $properties->count() > 1 ? 's' : '' }}
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($properties as $property)
                @php
                    $communication = $communications[$property['id']] ?? null;
                @endphp
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    {{-- Bandeau mandat exclusif --}}
                    <div class="bg-keymex-red px-3 py-1.5 text-center">
                        <p class="text-xs font-medium text-white">Mandat exclusif</p>
                    </div>

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

                        <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs">
                            <span class="text-gray-500 truncate">{{ $property['advisor']['name'] }}</span>
                            <span class="text-gray-400">{{ $property['dates']['creation'] ? \Carbon\Carbon::parse($property['dates']['creation'])->format('d/m') : '-' }}</span>
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
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white shadow rounded-lg px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun bien a vendre</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if($search)
                                Aucun resultat pour "{{ $search }}".
                            @else
                                Aucun bien en cours de commercialisation.
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
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
                               wire:model="communicationDate"
                               class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-keymex-red sm:text-sm" />
                        <p class="mt-2 text-xs text-gray-500">
                            Selectionnez la date de publication sur les reseaux sociaux (passee, aujourd'hui ou future).
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
</div>
