<div>
    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('orders.index') }}"
               class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                <svg class="mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                </svg>
                Retour
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Commande #{{ $order->id }}</h1>
                <p class="mt-1 text-sm text-gray-500">Créée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-2">
            <button wire:click="openStatusModal" type="button"
                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd" />
                </svg>
                Changer statut
            </button>
            <button wire:click="$set('showUploadModal', true)" type="button"
                    class="inline-flex items-center rounded-md bg-keymex-red px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-keymex-red-hover">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.25 13.25a.75.75 0 001.5 0V4.636l2.955 3.129a.75.75 0 001.09-1.03l-4.25-4.5a.75.75 0 00-1.09 0l-4.25 4.5a.75.75 0 101.09 1.03L9.25 4.636v8.614z" />
                    <path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z" />
                </svg>
                Envoyer BAT
            </button>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Conseiller</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-keymex-red/10">
                            <span class="text-lg font-medium text-keymex-red">
                                {{ substr($order->advisor_name, 0, 2) }}
                            </span>
                        </span>
                        <div class="ml-4">
                            <p class="text-lg font-medium text-gray-900">{{ $order->advisor_name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->advisor_email }}</p>
                            @if($order->advisor_agency)
                                <p class="text-sm text-gray-400">{{ $order->advisor_agency }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Articles commandés</h3>
                </div>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Support</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Format</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Catégorie</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:pr-6">Quantité</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        {{ $item->supportType->name }}
                                        @if($item->notes)
                                            <p class="text-xs text-gray-400 mt-1">{{ $item->notes }}</p>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $item->format?->name ?? '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $item->category?->name ?? '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 text-right sm:pr-6">
                                        {{ $item->quantity }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($order->notes)
                    <div class="px-4 py-4 sm:px-6 border-t border-gray-200 bg-gray-50">
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Notes :</span> {{ $order->notes }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Historique des BAT</h3>
                </div>

                {{-- Standalone BAT Timeline (if order was created from a BAT) --}}
                @if($order->standaloneBat && $order->standaloneBat->logs->count() > 0)
                    <div class="px-4 py-4 sm:px-6 bg-purple-50 border-b border-purple-100">
                        <h4 class="text-sm font-semibold text-purple-800 mb-3 flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Historique BAT source #{{ $order->standaloneBat->id }}
                        </h4>
                        <div class="flow-root">
                            <ul class="-mb-4">
                                @foreach($order->standaloneBat->logs as $log)
                                    <li class="relative pb-4">
                                        @if(!$loop->last)
                                            <span class="absolute left-3 top-3 -ml-px h-full w-0.5 bg-purple-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                @php
                                                    $colors = [
                                                        'gray' => 'bg-gray-100 text-gray-600',
                                                        'blue' => 'bg-blue-100 text-blue-600',
                                                        'green' => 'bg-green-100 text-green-600',
                                                        'red' => 'bg-red-100 text-red-600',
                                                        'orange' => 'bg-orange-100 text-orange-600',
                                                        'purple' => 'bg-purple-100 text-purple-600',
                                                        'yellow' => 'bg-yellow-100 text-yellow-600',
                                                        'emerald' => 'bg-emerald-100 text-emerald-600',
                                                    ];
                                                    $colorClass = $colors[$log->event_color] ?? $colors['gray'];
                                                @endphp
                                                <span class="h-6 w-6 rounded-full flex items-center justify-center ring-2 ring-purple-50 {{ $colorClass }}">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $log->event_icon }}"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900">{{ $log->event_label }}</p>
                                                    <time class="text-xs text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</time>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    @if($log->actor_type === 'client')
                                                        Par le client
                                                    @elseif($log->actor_type === 'staff' && $log->actor_name)
                                                        Par {{ $log->actor_name }}
                                                    @else
                                                        Système
                                                    @endif
                                                </p>
                                                @if($log->comment)
                                                    <div class="mt-2 p-2 bg-white rounded border border-purple-100">
                                                        <p class="text-sm text-gray-600">{{ $log->comment }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="divide-y divide-gray-200">
                    @forelse($order->batVersions as $bat)
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-100 text-sm font-medium text-gray-600">
                                        v{{ $bat->version_number }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bat->file_name }}</p>
                                        <p class="text-xs text-gray-500">
                                            Envoyé le {{ $bat->sent_at->format('d/m/Y à H:i') }}
                                            @if($bat->sentBy)
                                                par {{ $bat->sentBy->name }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'validated' => 'bg-green-100 text-green-700',
                                            'refused' => 'bg-red-100 text-red-700',
                                            'modifications_requested' => 'bg-orange-100 text-orange-700',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$bat->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $bat->status_label }}
                                    </span>
                                </div>
                            </div>

                            @if($bat->comment)
                                <div class="mt-3 rounded-md bg-gray-50 p-3">
                                    <p class="text-sm text-gray-600">{{ $bat->comment }}</p>
                                </div>
                            @endif

                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ Storage::url($bat->file_path) }}" target="_blank"
                                   class="inline-flex items-center text-xs font-medium text-keymex-red hover:text-keymex-red-hover">
                                    <svg class="mr-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.75 2.75a.75.75 0 00-1.5 0v8.614L6.295 8.235a.75.75 0 10-1.09 1.03l4.25 4.5a.75.75 0 001.09 0l4.25-4.5a.75.75 0 00-1.09-1.03l-2.955 3.129V2.75z" />
                                        <path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z" />
                                    </svg>
                                    Télécharger
                                </a>

                                @if($bat->activeToken)
                                    <button wire:click="copyValidationLink({{ $bat->id }})" type="button"
                                            class="inline-flex items-center text-xs font-medium text-keymex-red hover:text-keymex-red-hover"
                                            x-data
                                            @copy-to-clipboard.window="navigator.clipboard.writeText($event.detail.url); alert('Lien copié !')">
                                        <svg class="mr-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.887 3.182c.396.037.79.08 1.183.128C16.194 3.45 17 4.414 17 5.517V16.75A2.25 2.25 0 0114.75 19h-9.5A2.25 2.25 0 013 16.75V5.517c0-1.103.806-2.068 1.93-2.207.393-.048.787-.09 1.183-.128A3.001 3.001 0 019 1h2c1.373 0 2.531.923 2.887 2.182zM7.5 4A1.5 1.5 0 019 2.5h2A1.5 1.5 0 0112.5 4v.5h-5V4z" />
                                        </svg>
                                        Copier lien validation
                                    </button>

                                    <span class="text-xs text-gray-400">
                                        Expire le {{ $bat->activeToken->expires_at->format('d/m/Y') }}
                                    </span>
                                @else
                                    <button wire:click="regenerateToken({{ $bat->id }})" type="button"
                                            class="inline-flex items-center text-xs font-medium text-orange-600 hover:text-orange-500">
                                        <svg class="mr-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd" />
                                        </svg>
                                        Générer nouveau lien
                                    </button>
                                @endif

                                <button wire:click="deleteBatVersion({{ $bat->id }})"
                                        wire:confirm="Êtes-vous sûr de vouloir supprimer ce BAT ?"
                                        type="button"
                                        class="inline-flex items-center text-xs font-medium text-red-600 hover:text-red-500">
                                    <svg class="mr-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.519.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                    </svg>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun BAT</h3>
                            <p class="mt-1 text-sm text-gray-500">Envoyez le premier BAT pour cette commande.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Statut</h3>
                    @php
                        $statusColors = [
                            'pending' => 'bg-gray-100 text-gray-700',
                            'in_progress' => 'bg-blue-100 text-blue-700',
                            'bat_sent' => 'bg-yellow-100 text-yellow-700',
                            'validated' => 'bg-green-100 text-green-700',
                            'refused' => 'bg-red-100 text-red-700',
                            'modifications_requested' => 'bg-orange-100 text-orange-700',
                            'completed' => 'bg-emerald-100 text-emerald-700',
                        ];
                    @endphp
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $order->status_label }}
                    </span>
                </div>
            </div>

            {{-- BAT Source --}}
            @if($order->standaloneBat)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-purple-50">
                        <h3 class="text-base font-semibold text-purple-900 flex items-center gap-2">
                            <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            BAT source
                        </h3>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-gray-600 mb-3">
                            Cette commande a été créée depuis un BAT autonome.
                        </p>
                        <a href="{{ route('standalone-bats.show', $order->standaloneBat) }}"
                           wire:navigate
                           class="inline-flex items-center gap-2 text-sm font-medium text-purple-600 hover:text-purple-800">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Voir le BAT #{{ $order->standaloneBat->id }}
                        </a>
                    </div>

                    {{-- File Preview --}}
                    @if($order->standaloneBat->file_path)
                        <div class="border-t border-gray-200">
                            @if(str_starts_with($order->standaloneBat->file_mime, 'image/'))
                                <div class="bg-gray-100 p-4">
                                    <img
                                        src="{{ asset('storage/' . $order->standaloneBat->file_path) }}"
                                        alt="{{ $order->standaloneBat->file_name }}"
                                        class="max-w-full h-auto max-h-64 mx-auto rounded-lg shadow"
                                    >
                                </div>
                            @else
                                <iframe
                                    src="{{ asset('storage/' . $order->standaloneBat->file_path) }}#toolbar=0&navpanes=0&view=FitH"
                                    class="w-full h-64 border-0"
                                ></iframe>
                            @endif
                            <div class="px-4 py-3 bg-gray-50 flex items-center justify-between">
                                <span class="text-sm text-gray-500">{{ $order->standaloneBat->file_name }}</span>
                                <a href="{{ asset('storage/' . $order->standaloneBat->file_path) }}"
                                   download="{{ $order->standaloneBat->file_name }}"
                                   class="text-sm font-medium text-keymex-red hover:text-keymex-red-hover">
                                    Télécharger
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Tracking URL --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-gray-900">Suivi de livraison</h3>
                        <button wire:click="openTrackingModal" type="button"
                                class="text-sm font-medium text-keymex-red hover:text-keymex-red-hover">
                            {{ $order->tracking_url ? 'Modifier' : 'Ajouter' }}
                        </button>
                    </div>
                    @if($order->tracking_url)
                        <a href="{{ $order->tracking_url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 break-all">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Suivre le colis
                        </a>
                    @else
                        <p class="text-sm text-gray-400 italic">Aucun lien de suivi</p>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Informations</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Créée par</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->creator?->name ?? 'Système' }}</dd>
                        </div>
                        @if($order->ordered_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date de commande</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $order->ordered_at->format('d/m/Y') }}</dd>
                            </div>
                        @endif
                        @if($order->expected_delivery_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Livraison prévue</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $order->expected_delivery_at->format('d/m/Y') }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dernière modification</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->updated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre de BAT</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->batVersions->count() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @if($showUploadModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 transition-opacity" wire:click="$set('showUploadModal', false)"></div>

                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <form wire:submit="uploadBat">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Envoyer un BAT</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Uploadez le fichier BAT (PDF ou image). Un lien de validation sera automatiquement généré.
                            </p>
                        </div>

                        <div class="mt-4">
                            <label for="batFile" class="block text-sm font-medium text-gray-700">Fichier BAT</label>
                            <div class="mt-2">
                                <input wire:model="batFile"
                                       type="file"
                                       id="batFile"
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-keymex-red/10 file:text-keymex-red hover:file:bg-keymex-red/20">
                            </div>
                            @error('batFile')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <div wire:loading wire:target="batFile" class="mt-2 text-sm text-gray-500">
                                Upload en cours...
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button wire:click="$set('showUploadModal', false)" type="button"
                                    class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center rounded-md bg-keymex-red px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-keymex-red-hover"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed">
                                <span wire:loading.remove wire:target="uploadBat">Envoyer</span>
                                <span wire:loading wire:target="uploadBat">Envoi...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showStatusModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 transition-opacity" wire:click="$set('showStatusModal', false)"></div>

                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <form wire:submit="updateOrderStatus">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Changer le statut</h3>
                        </div>

                        <div class="mt-4">
                            <label for="newStatus" class="block text-sm font-medium text-gray-700">Nouveau statut</label>
                            <select wire:model="newStatus"
                                    id="newStatus"
                                    class="mt-2 block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-keymex-red sm:text-sm">
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button wire:click="$set('showStatusModal', false)" type="button"
                                    class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="rounded-md bg-keymex-red px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-keymex-red-hover">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showTrackingModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 transition-opacity" wire:click="$set('showTrackingModal', false)"></div>

                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <form wire:submit="updateTrackingUrl">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Lien de suivi</h3>
                                    <p class="text-sm text-gray-500">Ajoutez le lien de suivi du transporteur</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="trackingUrl" class="block text-sm font-medium text-gray-700">URL de suivi</label>
                            <input wire:model="trackingUrl"
                                   type="url"
                                   id="trackingUrl"
                                   placeholder="https://..."
                                   class="mt-2 block w-full rounded-md border-0 py-2 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-keymex-red sm:text-sm">
                            @error('trackingUrl')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button wire:click="$set('showTrackingModal', false)" type="button"
                                    class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="rounded-md bg-keymex-red px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-keymex-red-hover">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
