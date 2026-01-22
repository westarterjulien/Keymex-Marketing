<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Historique des BATs</h1>
            <p class="text-sm text-gray-500 mt-1">
                BATs convertis en commandes
            </p>
        </div>
        <a
            href="{{ route('standalone-bats.index') }}"
            wire:navigate
            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux BATs en cours
        </a>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-keymex-red focus:border-keymex-red"
                placeholder="Rechercher par conseiller, titre, numero de commande..."
            >
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($bats->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BAT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conseiller</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commande</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Converti le</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bats as $bat)
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- BAT Info --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-purple-100">
                                            <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $bat->title ?: 'BAT #' . $bat->id }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $bat->file_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Conseiller --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $bat->advisor_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $bat->advisor_email }}</div>
                                </td>

                                {{-- Commande --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($bat->order)
                                        <a
                                            href="{{ route('orders.show', $bat->order_id) }}"
                                            wire:navigate
                                            class="inline-flex items-center gap-1 px-2 py-1 bg-keymex-red/10 text-keymex-red rounded-md text-sm font-medium hover:bg-keymex-red/20 transition-colors"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            Commande #{{ $bat->order_id }}
                                        </a>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Details --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 space-y-1">
                                        @if($bat->quantity)
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-400">Qte:</span>
                                                <span class="font-medium">{{ number_format($bat->quantity, 0, ',', ' ') }}</span>
                                            </div>
                                        @endif
                                        @if($bat->price)
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-400">Prix:</span>
                                                <span class="font-medium text-keymex-red">{{ number_format($bat->price, 2, ',', ' ') }} EUR</span>
                                            </div>
                                        @endif
                                        @if($bat->delivery_time)
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-400">Delai:</span>
                                                <span>{{ $bat->delivery_time }}</span>
                                            </div>
                                        @endif
                                        @if(!$bat->quantity && !$bat->price && !$bat->delivery_time)
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Date conversion --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $bat->updated_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $bat->updated_at->format('H:i') }}</div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a
                                        href="{{ route('standalone-bats.show', $bat) }}"
                                        wire:navigate
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-500 hover:text-keymex-red hover:bg-red-50 transition-colors"
                                        title="Voir le BAT"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($bats->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $bats->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun BAT converti</h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if($search)
                        Aucun resultat pour "{{ $search }}"
                    @else
                        Les BATs convertis en commandes apparaitront ici.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
