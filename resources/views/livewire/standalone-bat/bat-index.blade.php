<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">BAT Independants</h1>
            <p class="text-sm text-gray-500 mt-1">
                Gerez vos BAT et convertissez-les en commandes apres validation
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('standalone-bats.history') }}"
               wire:navigate
               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Historique
            </a>
            <a href="{{ route('standalone-bats.create') }}"
               wire:navigate
               class="inline-flex items-center gap-2 px-4 py-2 bg-keymex-red hover:bg-keymex-red-hover text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau BAT
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="rounded-lg bg-green-50 p-4 border border-green-200">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-lg bg-red-50 p-4 border border-red-200">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            {{-- Search Input --}}
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Rechercher par conseiller, titre..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 focus:bg-white focus:border-keymex-red focus:ring-2 focus:ring-keymex-red/20 transition-all duration-200"
                >
                <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="h-4 w-4 text-keymex-red animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </div>

            {{-- Status Filter (Custom Dropdown) --}}
            @php
                $statusOptions = [
                    '' => ['label' => 'Tous les statuts', 'color' => 'bg-gray-100 text-gray-600', 'dot' => 'bg-gray-400'],
                    'draft' => ['label' => 'Brouillon', 'color' => 'bg-gray-100 text-gray-700', 'dot' => 'bg-gray-400'],
                    'sent' => ['label' => 'Envoye', 'color' => 'bg-yellow-100 text-yellow-700', 'dot' => 'bg-yellow-500'],
                    'validated' => ['label' => 'Valide', 'color' => 'bg-green-100 text-green-700', 'dot' => 'bg-green-500'],
                    'refused' => ['label' => 'Refuse', 'color' => 'bg-red-100 text-red-700', 'dot' => 'bg-red-500'],
                    'modifications_requested' => ['label' => 'Modifications', 'color' => 'bg-orange-100 text-orange-700', 'dot' => 'bg-orange-500'],
                    'converted' => ['label' => 'Converti', 'color' => 'bg-purple-100 text-purple-700', 'dot' => 'bg-purple-500'],
                ];
                $currentStatus = $statusOptions[$statusFilter] ?? $statusOptions[''];
            @endphp
            <div class="sm:w-56 relative" x-data="{ open: false }" @click.away="open = false">
                {{-- Trigger Button --}}
                <button
                    type="button"
                    @click="open = !open"
                    class="w-full flex items-center gap-2 pl-3 pr-10 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-700 hover:bg-white hover:border-gray-300 focus:bg-white focus:border-keymex-red focus:ring-2 focus:ring-keymex-red/20 transition-all duration-200 cursor-pointer text-left"
                >
                    <svg class="h-5 w-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span class="flex items-center gap-2 flex-1">
                        <span class="h-2 w-2 rounded-full {{ $currentStatus['dot'] }}"></span>
                        <span>{{ $currentStatus['label'] }}</span>
                    </span>
                </button>
                {{-- Chevron --}}
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                {{-- Dropdown Menu --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-lg border border-gray-200 py-1 overflow-hidden"
                    style="display: none;"
                >
                    @foreach($statusOptions as $value => $option)
                        <button
                            type="button"
                            wire:click="$set('statusFilter', '{{ $value }}')"
                            @click="open = false"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors {{ $statusFilter === $value ? 'bg-red-50' : '' }}"
                        >
                            <span class="h-2.5 w-2.5 rounded-full {{ $option['dot'] }}"></span>
                            <span class="flex-1 text-left {{ $statusFilter === $value ? 'font-medium text-keymex-red' : 'text-gray-700' }}">
                                {{ $option['label'] }}
                            </span>
                            @if($statusFilter === $value)
                                <svg class="h-4 w-4 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- BAT List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BAT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conseiller</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bats as $bat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        @if(str_contains($bat->file_mime, 'pdf'))
                                            <svg class="h-5 w-5 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('standalone-bats.show', $bat) }}" wire:navigate class="text-sm font-medium text-gray-900 hover:text-keymex-red transition-colors">
                                            {{ $bat->title ?: 'BAT #' . $bat->id }}
                                        </a>
                                        <div class="text-xs text-gray-500">{{ $bat->file_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $bat->advisor_name }}</div>
                                <div class="text-xs text-gray-500">{{ $bat->advisor_email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-700',
                                        'sent' => 'bg-yellow-100 text-yellow-700',
                                        'validated' => 'bg-green-100 text-green-700',
                                        'refused' => 'bg-red-100 text-red-700',
                                        'modifications_requested' => 'bg-orange-100 text-orange-700',
                                        'converted' => 'bg-red-100 text-keymex-red-hover',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$bat->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $bat->status_label }}
                                </span>
                                @if($bat->order_id)
                                    <a href="{{ route('orders.show', $bat->order_id) }}" class="ml-2 text-xs text-keymex-red hover:underline">
                                        Commande #{{ $bat->order_id }}
                                    </a>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $bat->created_at->format('d/m/Y H:i') }}
                                @if($bat->sent_at)
                                    <div class="text-xs">Envoye: {{ $bat->sent_at->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- View details --}}
                                    <a href="{{ route('standalone-bats.show', $bat) }}" wire:navigate
                                       class="p-2 text-gray-400 hover:text-keymex-red transition-colors" title="Voir les details">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    @if($bat->status === 'draft')
                                        {{-- Send --}}
                                        <button wire:click="sendBat({{ $bat->id }})"
                                                class="p-2 text-gray-400 hover:text-green-600 transition-colors" title="Envoyer pour validation">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                        </button>
                                    @endif

                                    @if(in_array($bat->status, ['sent', 'validated', 'refused', 'modifications_requested']))
                                        {{-- Copy link --}}
                                        <button wire:click="copyValidationLink({{ $bat->id }})"
                                                class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Copier le lien">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                            </svg>
                                        </button>

                                        {{-- Regenerate token --}}
                                        <button wire:click="regenerateToken({{ $bat->id }})"
                                                class="p-2 text-gray-400 hover:text-yellow-600 transition-colors" title="Regenerer le lien">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    @endif

                                    @if($bat->canBeConvertedToOrder())
                                        {{-- Convert to order --}}
                                        <button wire:click="openConvertModal({{ $bat->id }})"
                                                class="p-2 text-gray-400 hover:text-keymex-red transition-colors" title="Convertir en commande">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Delete (only for draft or sent) --}}
                                    @if(in_array($bat->status, ['draft', 'sent']))
                                        <button wire:click="confirmDelete({{ $bat->id }})"
                                                class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Supprimer">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Aucun BAT trouve.
                                <a href="{{ route('standalone-bats.create') }}" class="text-keymex-red hover:underline">Creer un nouveau BAT</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bats->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $bats->links() }}
            </div>
        @endif
    </div>

    {{-- Convert Modal --}}
    @if($showConvertModal && $convertingBat)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="closeConvertModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg leading-6 font-medium text-gray-900 text-center">
                            Convertir en commande
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 text-center">
                            Ajoutez les details pour creer la commande a partir du BAT valide.
                        </p>

                        <div class="mt-6 space-y-4">
                            {{-- Info conseiller --}}
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-sm font-medium text-gray-900">{{ $convertingBat->advisor_name }}</p>
                                <p class="text-xs text-gray-500">{{ $convertingBat->advisor_email }}</p>
                            </div>

                            {{-- Type de support --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Type de support *</label>
                                <select wire:model="selectedSupportTypeId" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-keymex-red focus:ring-keymex-red">
                                    <option value="">Selectionnez</option>
                                    @foreach($supportTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedSupportTypeId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Format --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Format *</label>
                                <select wire:model="selectedFormatId" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-keymex-red focus:ring-keymex-red">
                                    <option value="">Selectionnez</option>
                                    @foreach($formats as $format)
                                        <option value="{{ $format->id }}">{{ $format->name }} ({{ $format->supportType?->name }})</option>
                                    @endforeach
                                </select>
                                @error('selectedFormatId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Categorie --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Categorie *</label>
                                <select wire:model="selectedCategoryId" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-keymex-red focus:ring-keymex-red">
                                    <option value="">Selectionnez</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedCategoryId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Quantite --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quantite *</label>
                                <input type="number" wire:model="quantity" min="1" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-keymex-red focus:ring-keymex-red">
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea wire:model="orderNotes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-keymex-red focus:ring-keymex-red"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button wire:click="convertToOrder" type="button"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-keymex-red text-base font-medium text-white hover:bg-keymex-red-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-keymex-red sm:col-start-2 sm:text-sm">
                            Creer la commande
                        </button>
                        <button wire:click="closeConvertModal" type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div
                wire:click="closeDeleteModal"
                class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
            ></div>

            {{-- Modal Content --}}
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center gap-4 mb-5">
                    <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-br from-red-400 to-rose-500 shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Supprimer le BAT</h3>
                        <p class="text-sm text-gray-500">Cette action est irreversible.</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button
                        type="button"
                        wire:click="closeDeleteModal"
                        class="flex-1 px-4 py-3 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-sm"
                    >
                        Annuler
                    </button>
                    <button
                        type="button"
                        wire:click="deleteBat"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-wait"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm"
                    >
                        <span wire:loading.remove wire:target="deleteBat">Supprimer</span>
                        <span wire:loading wire:target="deleteBat" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Suppression...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('copy-to-clipboard', (data) => {
        navigator.clipboard.writeText(data.url).then(() => {
            alert('Lien copie dans le presse-papiers!');
        });
    });
</script>
@endscript
