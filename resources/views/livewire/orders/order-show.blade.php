<div class="space-y-6">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="rounded-2xl bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 p-4">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 p-4">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Header with Pattern Background --}}
    <div class="relative bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 rounded-2xl overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.4&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        <div class="relative px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('orders.index') }}"
                       wire:navigate
                       class="flex items-center justify-center h-10 w-10 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div class="relative">
                        <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-keymex-red to-red-600 flex items-center justify-center shadow-lg">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="absolute -bottom-1 -right-1 h-6 min-w-6 px-1.5 rounded-lg bg-white text-gray-900 text-xs font-bold flex items-center justify-center shadow-sm">
                            #{{ $order->id }}
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Commande #{{ $order->id }}</h1>
                        <p class="text-sm text-gray-300 mt-1">Creee le {{ $order->created_at->format('d/m/Y a H:i') }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button wire:click="openStatusModal" type="button"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 backdrop-blur-sm text-white rounded-xl hover:bg-white/20 transition-all border border-white/20">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="font-medium">Changer statut</span>
                    </button>
                    <button wire:click="$set('showUploadModal', true)" type="button"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-keymex-red to-red-600 text-white rounded-xl hover:from-keymex-red-hover hover:to-red-700 transition-all shadow-lg shadow-red-500/25">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span class="font-medium">Envoyer BAT</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Advisor Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-keymex-red/10 to-red-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">Conseiller</h3>
                        </div>
                        <button wire:click="openEditOrderModal" type="button"
                                class="text-xs font-medium text-keymex-red hover:text-keymex-red-hover">
                            Modifier
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-keymex-red to-red-600 flex items-center justify-center shadow-lg shadow-red-500/20">
                            <span class="text-lg font-bold text-white">
                                {{ strtoupper(substr($order->advisor_name, 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900">{{ $order->advisor_name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->advisor_email }}</p>
                            @if($order->advisor_agency)
                                <p class="text-sm text-gray-400 mt-0.5">{{ $order->advisor_agency }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Articles commandes</h3>
                        <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            {{ $order->items->count() }} article{{ $order->items->count() > 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Support</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Format</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categorie</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Quantite</th>
                                <th scope="col" class="px-3 py-3.5 pr-6 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 pl-6 pr-3">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $item->supportType->name }}</p>
                                                @if($item->notes)
                                                    <p class="text-xs text-gray-400 mt-0.5">{{ $item->notes }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4">
                                        @if($item->format)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                                {{ $item->format->name }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4">
                                        @if($item->category)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                                {{ $item->category->name }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-right">
                                        <span class="inline-flex items-center justify-center h-8 min-w-8 px-2 rounded-lg bg-gray-900 text-white text-sm font-bold">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 pr-6">
                                        <div class="flex items-center justify-center gap-1">
                                            <button wire:click="openEditItemModal({{ $item->id }})" type="button"
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-500 hover:text-keymex-red hover:bg-red-50 transition-colors"
                                                    title="Modifier">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            @if($order->items->count() > 1)
                                                <button wire:click="deleteItem({{ $item->id }})"
                                                        wire:confirm="Etes-vous sur de vouloir supprimer cet article ?"
                                                        type="button"
                                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                        title="Supprimer">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($order->notes)
                    <div class="px-6 py-4 border-t border-gray-100 bg-gradient-to-r from-amber-50 to-yellow-50">
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-amber-100 to-yellow-100 flex items-center justify-center flex-shrink-0">
                                <svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-amber-800">Notes</p>
                                <p class="text-sm text-amber-700 mt-1">{{ $order->notes }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- BAT History --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-emerald-100 to-green-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Historique des BAT</h3>
                        <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                            {{ $order->batVersions->count() }} version{{ $order->batVersions->count() > 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>

                {{-- Standalone BAT Timeline (if order was created from a BAT) --}}
                @if($order->standaloneBat && $order->standaloneBat->logs->count() > 0)
                    <div class="px-6 py-5 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-purple-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-sm">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-purple-900">Historique BAT source</h4>
                                <p class="text-xs text-purple-600">#{{ $order->standaloneBat->id }}</p>
                            </div>
                        </div>
                        <div class="flow-root">
                            <ul class="-mb-4">
                                @foreach($order->standaloneBat->logs as $log)
                                    <li class="relative pb-4">
                                        @if(!$loop->last)
                                            <span class="absolute left-3 top-6 -ml-px h-full w-0.5 bg-purple-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex gap-3">
                                            <div>
                                                @php
                                                    $gradients = [
                                                        'gray' => 'from-gray-400 to-gray-500',
                                                        'blue' => 'from-blue-500 to-blue-600',
                                                        'green' => 'from-emerald-500 to-green-600',
                                                        'red' => 'from-red-500 to-red-600',
                                                        'orange' => 'from-orange-500 to-amber-600',
                                                        'purple' => 'from-purple-500 to-indigo-600',
                                                        'yellow' => 'from-yellow-500 to-amber-500',
                                                        'emerald' => 'from-emerald-500 to-teal-600',
                                                    ];
                                                    $gradient = $gradients[$log->event_color] ?? $gradients['gray'];
                                                @endphp
                                                <span class="h-6 w-6 rounded-full flex items-center justify-center bg-gradient-to-br {{ $gradient }} ring-2 ring-white shadow-sm">
                                                    <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $log->event_icon }}"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="flex-1 min-w-0 bg-white rounded-xl border border-purple-100 p-3 shadow-sm">
                                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                                    <p class="text-sm font-medium text-gray-900">{{ $log->event_label }}</p>
                                                    <time class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ $log->created_at->format('d/m/Y H:i') }}</time>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    @if($log->actor_type === 'client')
                                                        Par le client
                                                    @elseif($log->actor_type === 'staff' && $log->actor_name)
                                                        Par {{ $log->actor_name }}
                                                    @else
                                                        Systeme
                                                    @endif
                                                </p>
                                                @if($log->comment)
                                                    <div class="mt-2 p-2 bg-purple-50 rounded-lg border border-purple-100">
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

                <div class="divide-y divide-gray-100">
                    @forelse($order->batVersions as $bat)
                        <div class="p-6 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center shadow-lg">
                                            <span class="text-sm font-bold text-white">v{{ $bat->version_number }}</span>
                                        </div>
                                        @php
                                            $statusDot = [
                                                'pending' => 'bg-yellow-400',
                                                'validated' => 'bg-emerald-400',
                                                'refused' => 'bg-red-400',
                                                'modifications_requested' => 'bg-orange-400',
                                            ];
                                        @endphp
                                        <span class="absolute -bottom-0.5 -right-0.5 h-4 w-4 rounded-full {{ $statusDot[$bat->status] ?? 'bg-gray-400' }} ring-2 ring-white"></span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $bat->file_name }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Envoye le {{ $bat->sent_at->format('d/m/Y a H:i') }}
                                            @if($bat->sentBy)
                                                par {{ $bat->sentBy->name }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    @php
                                        $statusStyles = [
                                            'pending' => 'bg-gradient-to-r from-yellow-50 to-amber-50 text-yellow-700 border-yellow-200',
                                            'validated' => 'bg-gradient-to-r from-emerald-50 to-green-50 text-emerald-700 border-emerald-200',
                                            'refused' => 'bg-gradient-to-r from-red-50 to-rose-50 text-red-700 border-red-200',
                                            'modifications_requested' => 'bg-gradient-to-r from-orange-50 to-amber-50 text-orange-700 border-orange-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold border {{ $statusStyles[$bat->status] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $statusDot[$bat->status] ?? 'bg-gray-400' }}"></span>
                                        {{ $bat->status_label }}
                                    </span>
                                </div>
                            </div>

                            @if($bat->comment)
                                <div class="mt-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 p-4 border border-gray-200">
                                    <div class="flex items-start gap-3">
                                        <svg class="h-5 w-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <p class="text-sm text-gray-600">{{ $bat->comment }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ Storage::url($bat->file_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-keymex-red/10 to-red-100 text-keymex-red rounded-lg text-xs font-medium hover:from-keymex-red/20 hover:to-red-200 transition-colors border border-keymex-red/20">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Telecharger
                                </a>

                                @if($bat->activeToken)
                                    <button wire:click="copyValidationLink({{ $bat->id }})" type="button"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-600 rounded-lg text-xs font-medium hover:from-blue-100 hover:to-indigo-100 transition-colors border border-blue-200"
                                            x-data
                                            @copy-to-clipboard.window="navigator.clipboard.writeText($event.detail.url); alert('Lien copie !')">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                        </svg>
                                        Copier lien validation
                                    </button>

                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 text-gray-500 rounded-lg text-xs border border-gray-200">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Expire le {{ $bat->activeToken->expires_at->format('d/m/Y') }}
                                    </span>
                                @else
                                    <button wire:click="regenerateToken({{ $bat->id }})" type="button"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-orange-50 to-amber-50 text-orange-600 rounded-lg text-xs font-medium hover:from-orange-100 hover:to-amber-100 transition-colors border border-orange-200">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Generer nouveau lien
                                    </button>
                                @endif

                                <button wire:click="deleteBatVersion({{ $bat->id }})"
                                        wire:confirm="Etes-vous sur de vouloir supprimer ce BAT ?"
                                        type="button"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-red-50 to-rose-50 text-red-600 rounded-lg text-xs font-medium hover:from-red-100 hover:to-rose-100 transition-colors border border-red-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Aucun BAT</h3>
                            <p class="mt-2 text-sm text-gray-500">Envoyez le premier BAT pour cette commande.</p>
                            <button wire:click="$set('showUploadModal', true)" type="button"
                                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-keymex-red to-red-600 text-white rounded-xl hover:from-keymex-red-hover hover:to-red-700 transition-all text-sm font-medium shadow-lg shadow-red-500/20">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Envoyer un BAT
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50">
                    <h3 class="text-base font-semibold text-gray-900">Statut</h3>
                </div>
                <div class="p-6">
                    @php
                        $statusConfig = [
                            'pending' => [
                                'bg' => 'from-gray-100 to-slate-100',
                                'text' => 'text-gray-700',
                                'dot' => 'bg-gray-400',
                                'border' => 'border-gray-200',
                            ],
                            'in_progress' => [
                                'bg' => 'from-blue-100 to-indigo-100',
                                'text' => 'text-blue-700',
                                'dot' => 'bg-blue-500 animate-pulse',
                                'border' => 'border-blue-200',
                            ],
                            'bat_sent' => [
                                'bg' => 'from-yellow-100 to-amber-100',
                                'text' => 'text-yellow-700',
                                'dot' => 'bg-yellow-500 animate-pulse',
                                'border' => 'border-yellow-200',
                            ],
                            'validated' => [
                                'bg' => 'from-emerald-100 to-green-100',
                                'text' => 'text-emerald-700',
                                'dot' => 'bg-emerald-500',
                                'border' => 'border-emerald-200',
                            ],
                            'refused' => [
                                'bg' => 'from-red-100 to-rose-100',
                                'text' => 'text-red-700',
                                'dot' => 'bg-red-500',
                                'border' => 'border-red-200',
                            ],
                            'modifications_requested' => [
                                'bg' => 'from-orange-100 to-amber-100',
                                'text' => 'text-orange-700',
                                'dot' => 'bg-orange-500 animate-pulse',
                                'border' => 'border-orange-200',
                            ],
                            'completed' => [
                                'bg' => 'from-emerald-100 to-teal-100',
                                'text' => 'text-emerald-700',
                                'dot' => 'bg-emerald-500',
                                'border' => 'border-emerald-200',
                            ],
                        ];
                        $config = $statusConfig[$order->status] ?? $statusConfig['pending'];
                    @endphp
                    <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r {{ $config['bg'] }} border {{ $config['border'] }}">
                        <span class="h-2 w-2 rounded-full {{ $config['dot'] }}"></span>
                        <span class="text-sm font-semibold {{ $config['text'] }}">{{ $order->status_label }}</span>
                    </div>
                </div>
            </div>

            {{-- BAT Source --}}
            @if($order->standaloneBat)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-purple-100 bg-gradient-to-r from-purple-50 to-indigo-50">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-sm">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-purple-900">BAT source</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">
                            Cette commande a ete creee depuis un BAT autonome.
                        </p>
                        <a href="{{ route('standalone-bats.show', $order->standaloneBat) }}"
                           wire:navigate
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-xl text-sm font-medium hover:from-purple-600 hover:to-indigo-700 transition-all shadow-lg shadow-purple-500/20">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Voir le BAT #{{ $order->standaloneBat->id }}
                        </a>
                    </div>

                    {{-- File Preview --}}
                    @if($order->standaloneBat->file_path)
                        <div class="border-t border-gray-100">
                            @if(str_starts_with($order->standaloneBat->file_mime, 'image/'))
                                <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-4">
                                    <img
                                        src="{{ $order->standaloneBat->file_url }}"
                                        alt="{{ $order->standaloneBat->file_name }}"
                                        class="max-w-full h-auto max-h-48 mx-auto rounded-lg shadow-lg"
                                    >
                                </div>
                            @else
                                <iframe
                                    src="{{ $order->standaloneBat->file_url }}#toolbar=0&navpanes=0&view=FitH"
                                    class="w-full h-48 border-0"
                                ></iframe>
                            @endif
                            <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 flex items-center justify-between">
                                <span class="text-xs text-gray-500 truncate">{{ $order->standaloneBat->file_name }}</span>
                                <a href="{{ $order->standaloneBat->file_url }}"
                                   download="{{ $order->standaloneBat->file_name }}"
                                   class="text-xs font-medium text-keymex-red hover:text-keymex-red-hover">
                                    Telecharger
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Tracking URL --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">Suivi de livraison</h3>
                        </div>
                        <button wire:click="openTrackingModal" type="button"
                                class="text-xs font-medium text-keymex-red hover:text-keymex-red-hover">
                            {{ $order->tracking_url ? 'Modifier' : 'Ajouter' }}
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    @if($order->tracking_url)
                        <a href="{{ $order->tracking_url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="group flex items-center gap-3 p-3 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 hover:from-blue-100 hover:to-indigo-100 transition-colors">
                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-blue-700">Suivre le colis</p>
                                <p class="text-xs text-blue-500 truncate">{{ $order->tracking_url }}</p>
                            </div>
                        </a>
                    @else
                        <div class="text-center py-4">
                            <div class="mx-auto h-12 w-12 rounded-xl bg-gray-100 flex items-center justify-center mb-3">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-400">Aucun lien de suivi</p>
                            <button wire:click="openTrackingModal" type="button"
                                    class="mt-2 text-sm font-medium text-keymex-red hover:text-keymex-red-hover">
                                Ajouter un lien
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-gray-100 to-slate-200 flex items-center justify-center">
                            <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Informations</h3>
                    </div>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Creee par</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $order->creator?->name ?? 'Systeme' }}</dd>
                        </div>
                        @if($order->ordered_at)
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-500">Date de commande</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $order->ordered_at->format('d/m/Y') }}</dd>
                            </div>
                        @endif
                        @if($order->expected_delivery_at)
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-500">Livraison prevue</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $order->expected_delivery_at->format('d/m/Y') }}</dd>
                            </div>
                        @endif
                        <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-500">Date de creation</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Derniere modification</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $order->updated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Nombre de BAT</dt>
                            <dd>
                                <span class="inline-flex items-center justify-center h-6 min-w-6 px-2 rounded-lg bg-emerald-100 text-emerald-700 text-sm font-bold">
                                    {{ $order->batVersions->count() }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Upload Modal --}}
    @if($showUploadModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showUploadModal', false)"></div>

                <div class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all w-full max-w-lg">
                    <form wire:submit="uploadBat">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-keymex-red to-red-600 flex items-center justify-center shadow-lg shadow-red-500/20">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">Envoyer un BAT</h3>
                                    <p class="text-sm text-gray-500">Un lien de validation sera automatiquement genere</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <label class="block">
                                <span class="text-sm font-medium text-gray-700">Fichier BAT</span>
                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-keymex-red/50 transition-colors">
                                    <div class="space-y-2 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <div class="text-sm text-gray-600">
                                            <label for="batFile" class="relative cursor-pointer rounded-md font-medium text-keymex-red hover:text-keymex-red-hover">
                                                <span>Choisir un fichier</span>
                                                <input wire:model="batFile"
                                                       type="file"
                                                       id="batFile"
                                                       accept=".pdf,.jpg,.jpeg,.png"
                                                       class="sr-only">
                                            </label>
                                            <span class="pl-1">ou glisser-deposer</span>
                                        </div>
                                        <p class="text-xs text-gray-500">PDF, JPG, JPEG ou PNG</p>
                                    </div>
                                </div>
                            </label>
                            @error('batFile')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror

                            <div wire:loading wire:target="batFile" class="mt-3 flex items-center gap-2 text-sm text-gray-500">
                                <svg class="animate-spin h-4 w-4 text-keymex-red" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Upload en cours...
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                            <button wire:click="$set('showUploadModal', false)" type="button"
                                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-keymex-red to-red-600 text-white rounded-xl hover:from-keymex-red-hover hover:to-red-700 transition-all text-sm font-medium shadow-lg shadow-red-500/20"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed">
                                <span wire:loading.remove wire:target="uploadBat">Envoyer</span>
                                <span wire:loading wire:target="uploadBat" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Envoi...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Status Modal --}}
    @if($showStatusModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showStatusModal', false)"></div>

                <div class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all w-full max-w-lg">
                    <form wire:submit="updateOrderStatus">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">Changer le statut</h3>
                                    <p class="text-sm text-gray-500">Modifiez le statut de la commande</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-2">Nouveau statut</label>
                            <select wire:model="newStatus"
                                    id="newStatus"
                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-keymex-red focus:border-keymex-red text-sm">
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                            <button wire:click="$set('showStatusModal', false)" type="button"
                                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all text-sm font-medium shadow-lg shadow-blue-500/20">
                                Mettre a jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Edit Item Modal --}}
    @if($showEditItemModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeEditItemModal"></div>

                <div class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all w-full max-w-lg">
                    <form wire:submit="updateItem">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/20">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">Modifier l'article</h3>
                                    <p class="text-sm text-gray-500">Modifiez les details de cet article</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            {{-- Support Type --}}
                            <div>
                                <label for="editItemSupportTypeId" class="block text-sm font-medium text-gray-700 mb-2">Type de support</label>
                                <select wire:model="editItemSupportTypeId"
                                        id="editItemSupportTypeId"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-keymex-red focus:border-keymex-red text-sm">
                                    @foreach($supportTypes as $supportType)
                                        <option value="{{ $supportType->id }}">{{ $supportType->name }}</option>
                                    @endforeach
                                </select>
                                @error('editItemSupportTypeId')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Format --}}
                            <div>
                                <label for="editItemFormatId" class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                                <select wire:model="editItemFormatId"
                                        id="editItemFormatId"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-keymex-red focus:border-keymex-red text-sm">
                                    <option value="">-- Aucun --</option>
                                    @foreach($formats as $format)
                                        <option value="{{ $format->id }}">{{ $format->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Category --}}
                            <div>
                                <label for="editItemCategoryId" class="block text-sm font-medium text-gray-700 mb-2">Categorie</label>
                                <select wire:model="editItemCategoryId"
                                        id="editItemCategoryId"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-keymex-red focus:border-keymex-red text-sm">
                                    <option value="">-- Aucune --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Quantity --}}
                            <div>
                                <label for="editItemQuantity" class="block text-sm font-medium text-gray-700 mb-2">Quantite</label>
                                <input wire:model="editItemQuantity"
                                       type="number"
                                       id="editItemQuantity"
                                       min="1"
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-keymex-red focus:border-keymex-red text-sm">
                                @error('editItemQuantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label for="editItemNotes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <textarea wire:model="editItemNotes"
                                          id="editItemNotes"
                                          rows="2"
                                          class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-keymex-red focus:border-keymex-red text-sm"
                                          placeholder="Notes optionnelles..."></textarea>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                            <button wire:click="closeEditItemModal" type="button"
                                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all text-sm font-medium shadow-lg shadow-amber-500/20">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Edit Order Modal --}}
    @if($showEditOrderModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeEditOrderModal"></div>

                <div class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all w-full max-w-lg">
                    <form wire:submit="updateOrder">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-keymex-red to-red-600 flex items-center justify-center shadow-lg shadow-red-500/20">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">Modifier la commande</h3>
                                    <p class="text-sm text-gray-500">Modifiez les informations du conseiller</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            {{-- Advisor Name --}}
                            <div>
                                <label for="editAdvisorName" class="block text-sm font-medium text-gray-700 mb-2">Nom du conseiller</label>
                                <input wire:model="editAdvisorName"
                                       type="text"
                                       id="editAdvisorName"
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-keymex-red focus:border-keymex-red text-sm">
                                @error('editAdvisorName')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Advisor Email --}}
                            <div>
                                <label for="editAdvisorEmail" class="block text-sm font-medium text-gray-700 mb-2">Email du conseiller</label>
                                <input wire:model="editAdvisorEmail"
                                       type="email"
                                       id="editAdvisorEmail"
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-keymex-red focus:border-keymex-red text-sm">
                                @error('editAdvisorEmail')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Advisor Agency --}}
                            <div>
                                <label for="editAdvisorAgency" class="block text-sm font-medium text-gray-700 mb-2">Agence</label>
                                <input wire:model="editAdvisorAgency"
                                       type="text"
                                       id="editAdvisorAgency"
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-keymex-red focus:border-keymex-red text-sm"
                                       placeholder="Nom de l'agence (optionnel)">
                            </div>

                            {{-- Order Notes --}}
                            <div>
                                <label for="editOrderNotes" class="block text-sm font-medium text-gray-700 mb-2">Notes de la commande</label>
                                <textarea wire:model="editOrderNotes"
                                          id="editOrderNotes"
                                          rows="3"
                                          class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-keymex-red focus:border-keymex-red text-sm"
                                          placeholder="Notes optionnelles..."></textarea>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                            <button wire:click="closeEditOrderModal" type="button"
                                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-keymex-red to-red-600 text-white rounded-xl hover:from-keymex-red-hover hover:to-red-700 transition-all text-sm font-medium shadow-lg shadow-red-500/20">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Tracking Modal --}}
    @if($showTrackingModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showTrackingModal', false)"></div>

                <div class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all w-full max-w-lg">
                    <form wire:submit="updateTrackingUrl">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">Lien de suivi</h3>
                                    <p class="text-sm text-gray-500">Ajoutez le lien de suivi du transporteur</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <label for="trackingUrl" class="block text-sm font-medium text-gray-700 mb-2">URL de suivi</label>
                            <input wire:model="trackingUrl"
                                   type="url"
                                   id="trackingUrl"
                                   placeholder="https://..."
                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-keymex-red focus:border-keymex-red text-sm placeholder-gray-400">
                            @error('trackingUrl')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                            <button wire:click="$set('showTrackingModal', false)" type="button"
                                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all text-sm font-medium shadow-lg shadow-emerald-500/20">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
