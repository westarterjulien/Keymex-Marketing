<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $bat->title ?: 'BAT #' . $bat->id }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Cree le {{ $bat->created_at->format('d/m/Y a H:i') }}
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
            Retour
        </a>
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

    {{-- Client Comment Alert (for modifications_requested) --}}
    @if($bat->status === 'modifications_requested' && $bat->client_comment)
        <div class="rounded-xl bg-orange-50 border-2 border-orange-200 p-5">
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-orange-100">
                        <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-bold text-orange-800">Modifications demandees par le client</h3>
                    <p class="text-sm text-orange-700 mt-1">{{ $bat->client_comment }}</p>
                    <p class="text-xs text-orange-500 mt-2">
                        Recu le {{ $bat->responded_at?->format('d/m/Y a H:i') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Refused Alert --}}
    @if($bat->status === 'refused' && $bat->client_comment)
        <div class="rounded-xl bg-red-50 border-2 border-red-200 p-5">
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-red-100">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-bold text-red-800">BAT refuse par le client</h3>
                    <p class="text-sm text-red-700 mt-1">{{ $bat->client_comment }}</p>
                    <p class="text-xs text-red-500 mt-2">
                        Recu le {{ $bat->responded_at?->format('d/m/Y a H:i') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Validated Alert --}}
    @if($bat->status === 'validated')
        <div class="rounded-xl bg-green-50 border-2 border-green-200 p-5">
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-green-100">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-bold text-green-800">BAT valide par le client</h3>
                    @if($bat->client_comment)
                        <p class="text-sm text-green-700 mt-1">{{ $bat->client_comment }}</p>
                    @endif
                    <p class="text-xs text-green-500 mt-2">
                        Valide le {{ $bat->responded_at?->format('d/m/Y a H:i') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column - File Preview --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Current File --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-keymex-red/10">
                            <svg class="h-5 w-5 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $bat->file_name }}</p>
                            <p class="text-xs text-gray-500">Fichier actuel</p>
                        </div>
                    </div>
                    <a
                        href="{{ asset('storage/' . $bat->file_path) }}"
                        download="{{ $bat->file_name }}"
                        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-keymex-red bg-keymex-red/10 rounded-lg hover:bg-keymex-red/20 transition-colors"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Telecharger
                    </a>
                </div>

                {{-- Preview --}}
                <div class="bg-gray-900">
                    @if(str_starts_with($bat->file_mime, 'image/'))
                        <div class="flex items-center justify-center p-4 min-h-[400px]">
                            <img
                                src="{{ asset('storage/' . $bat->file_path) }}"
                                alt="{{ $bat->file_name }}"
                                class="max-w-full h-auto max-h-[500px] rounded-lg shadow-2xl object-contain"
                            >
                        </div>
                    @else
                        <iframe
                            src="{{ asset('storage/' . $bat->file_path) }}#toolbar=0&navpanes=0&view=FitH"
                            class="w-full h-[500px] border-0"
                        ></iframe>
                    @endif
                </div>
            </div>

            {{-- Upload New File --}}
            @if(in_array($bat->status, ['draft', 'sent', 'modifications_requested', 'refused']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Mettre a jour le fichier</h2>
                        @if(!$showUploadForm)
                            <button
                                type="button"
                                wire:click="toggleUploadForm"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-keymex-red bg-keymex-red/10 rounded-lg hover:bg-keymex-red/20 transition-colors"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Nouveau fichier
                            </button>
                        @endif
                    </div>

                    @if($showUploadForm)
                        <form wire:submit="updateFile" class="space-y-4">
                            @if($newFile)
                                <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-green-100">
                                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $newFile->getClientOriginalName() }}</p>
                                            <p class="text-sm text-gray-500">{{ number_format($newFile->getSize() / 1024, 2) }} Ko</p>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="$set('newFile', null)"
                                        class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <label
                                    for="newFile"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-keymex-red hover:bg-red-50/50 transition-colors"
                                >
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="text-sm text-gray-500">
                                            <span class="font-semibold">Cliquez pour selectionner</span> un nouveau fichier
                                        </p>
                                        <p class="text-xs text-gray-400">PDF, JPG ou PNG (max. 20 Mo)</p>
                                    </div>
                                    <input
                                        type="file"
                                        wire:model="newFile"
                                        id="newFile"
                                        class="hidden"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                    >
                                </label>

                                <div wire:loading wire:target="newFile" class="text-center">
                                    <div class="inline-flex items-center gap-2 text-sm text-keymex-red">
                                        <svg class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Telechargement en cours...
                                    </div>
                                </div>
                            @endif

                            @error('newFile')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <div class="flex gap-3">
                                <button
                                    type="button"
                                    wire:click="toggleUploadForm"
                                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                                >
                                    Annuler
                                </button>
                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="flex-1 px-4 py-2 bg-keymex-red text-white rounded-lg hover:bg-keymex-red-hover transition-colors inline-flex items-center justify-center gap-2"
                                >
                                    <span wire:loading.remove wire:target="updateFile">Mettre a jour</span>
                                    <span wire:loading wire:target="updateFile" class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Mise a jour...
                                    </span>
                                </button>
                            </div>

                            <p class="text-xs text-gray-500 text-center">
                                Le fichier actuel sera remplace et le BAT repassera en brouillon.
                            </p>
                        </form>
                    @else
                        <p class="text-sm text-gray-500">
                            Vous pouvez remplacer le fichier actuel par une nouvelle version.
                            @if($bat->status === 'modifications_requested')
                                <span class="font-medium text-orange-600">Suite aux modifications demandees, mettez a jour le fichier puis renvoyez-le.</span>
                            @endif
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Right Column - Info & Actions --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900">Statut</h3>
                </div>
                <div class="p-4">
                    @php
                        $statusConfig = [
                            'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                            'sent' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8'],
                            'validated' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'M5 13l4 4L19 7'],
                            'refused' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'M6 18L18 6M6 6l12 12'],
                            'modifications_requested' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                            'converted' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ];
                        $config = $statusConfig[$bat->status] ?? $statusConfig['draft'];
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center h-10 w-10 rounded-lg {{ $config['bg'] }}">
                            <svg class="h-5 w-5 {{ $config['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold {{ $config['text'] }}">{{ $bat->status_label }}</p>
                            @if($bat->sent_at)
                                <p class="text-xs text-gray-500">Envoye le {{ $bat->sent_at->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Advisor Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900">Conseiller</h3>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-keymex-red text-white font-semibold">
                            {{ strtoupper(substr($bat->advisor_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $bat->advisor_name }}</p>
                            <p class="text-sm text-gray-500">{{ $bat->advisor_email }}</p>
                            @if($bat->advisor_agency)
                                <p class="text-xs text-gray-400">{{ $bat->advisor_agency }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Print Details Card --}}
            @if($bat->supportType || $bat->format || $bat->category || $bat->grammage || $bat->price || $bat->delivery_time || $bat->quantity)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-900">Informations d'impression</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @if($bat->supportType)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Type de support</span>
                                <span class="text-sm font-medium text-gray-900">{{ $bat->supportType->name }}</span>
                            </div>
                        @endif
                        @if($bat->format)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Format</span>
                                <span class="text-sm font-medium text-gray-900">{{ $bat->format->name }}</span>
                            </div>
                        @endif
                        @if($bat->category)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Categorie</span>
                                <span class="text-sm font-medium text-gray-900">{{ $bat->category->name }}</span>
                            </div>
                        @endif
                        @if($bat->grammage)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Grammage</span>
                                <span class="text-sm font-medium text-gray-900">{{ $bat->grammage }}</span>
                            </div>
                        @endif
                        @if($bat->quantity)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Quantite</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($bat->quantity, 0, ',', ' ') }}</span>
                            </div>
                        @endif
                        @if($bat->price)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Prix HT</span>
                                <span class="text-sm font-bold text-keymex-red">{{ number_format($bat->price, 2, ',', ' ') }} EUR</span>
                            </div>
                        @endif
                        @if($bat->delivery_time)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Delai</span>
                                <span class="text-sm font-medium text-gray-900">{{ $bat->delivery_time }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Description --}}
            @if($bat->description)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-900">Description</h3>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-gray-600">{{ $bat->description }}</p>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900">Actions</h3>
                </div>
                <div class="p-4 space-y-3">
                    @if($bat->status === 'draft')
                        <button
                            type="button"
                            wire:click="sendBat"
                            wire:loading.attr="disabled"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Envoyer pour validation
                        </button>
                    @endif

                    @if(in_array($bat->status, ['sent', 'validated', 'refused', 'modifications_requested']))
                        <button
                            type="button"
                            onclick="navigator.clipboard.writeText('{{ $bat->validation_url }}').then(() => alert('Lien copie!'))"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                            </svg>
                            Copier le lien de validation
                        </button>

                        <button
                            type="button"
                            wire:click="regenerateToken"
                            wire:loading.attr="disabled"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Regenerer le lien
                        </button>
                    @endif

                    @if($bat->canBeConvertedToOrder())
                        <button
                            type="button"
                            wire:click="openConvertModal"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Convertir en commande
                        </button>
                    @endif

                    @if($bat->order_id)
                        <a
                            href="{{ route('orders.show', $bat->order_id) }}"
                            wire:navigate
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-keymex-red text-white rounded-lg hover:bg-keymex-red-hover transition-colors font-medium"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Voir la commande #{{ $bat->order_id }}
                        </a>
                    @endif

                    @if($bat->token_expires_at)
                        <p class="text-xs text-center text-gray-400">
                            Lien expire le {{ $bat->token_expires_at->format('d/m/Y') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- History Timeline --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Historique
            </h3>
        </div>
        <div class="p-6">
            @if($bat->logs->count() > 0)
                <div class="flow-root">
                    <ul class="-mb-8">
                        @foreach($bat->logs as $log)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
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
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-4 ring-white {{ $colorClass }}">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $log->event_icon }}"/>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $log->event_label }}
                                                </p>
                                                <time class="text-xs text-gray-500">
                                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                                </time>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                @if($log->actor_type === 'client')
                                                    Par le client ({{ $log->actor_name }})
                                                @elseif($log->actor_type === 'staff')
                                                    Par {{ $log->actor_name }}
                                                @else
                                                    Systeme
                                                @endif
                                            </p>
                                            @if($log->comment)
                                                <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                                    <p class="text-sm text-gray-600">{{ $log->comment }}</p>
                                                </div>
                                            @endif
                                            @if($log->old_file_name && $log->new_file_name)
                                                <div class="mt-2 text-xs text-gray-500">
                                                    <span class="line-through">{{ $log->old_file_name }}</span>
                                                    <svg class="inline h-3 w-3 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                    </svg>
                                                    <span class="font-medium text-gray-700">{{ $log->new_file_name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-4">Aucun historique disponible</p>
            @endif
        </div>
    </div>

    {{-- Convert to Order Modal --}}
    @if($showConvertModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
                {{-- Background overlay --}}
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    wire:click="closeConvertModal"
                ></div>

                {{-- Modal panel --}}
                <div class="relative z-10 bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full mx-4 sm:mx-0">
                    <form wire:submit="convertToOrder">
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-emerald-100">
                                    <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                                        Convertir en commande
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Ce BAT sera transforme en commande
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                {{-- Order Date --}}
                                <div>
                                    <label for="orderedAt" class="block text-sm font-medium text-gray-700 mb-1">
                                        Date de commande <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        id="orderedAt"
                                        wire:model="orderedAt"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-keymex-red focus:ring-keymex-red"
                                    >
                                    @error('orderedAt')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Expected Delivery Date --}}
                                <div>
                                    <label for="expectedDeliveryAt" class="block text-sm font-medium text-gray-700 mb-1">
                                        Date de livraison prevue
                                    </label>
                                    <input
                                        type="date"
                                        id="expectedDeliveryAt"
                                        wire:model="expectedDeliveryAt"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-keymex-red focus:ring-keymex-red"
                                    >
                                    @error('expectedDeliveryAt')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Summary --}}
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <h4 class="text-sm font-medium text-gray-700">Resume de la commande</h4>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p><span class="font-medium">Conseiller :</span> {{ $bat->advisor_name }}</p>
                                        @if($bat->supportType)
                                            <p><span class="font-medium">Support :</span> {{ $bat->supportType->name }}</p>
                                        @endif
                                        @if($bat->format)
                                            <p><span class="font-medium">Format :</span> {{ $bat->format->name }}</p>
                                        @endif
                                        @if($bat->quantity)
                                            <p><span class="font-medium">Quantite :</span> {{ number_format($bat->quantity, 0, ',', ' ') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex gap-3">
                            <button
                                type="button"
                                wire:click="closeConvertModal"
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors font-medium"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium inline-flex items-center justify-center gap-2"
                            >
                                <span wire:loading.remove wire:target="convertToOrder">Creer la commande</span>
                                <span wire:loading wire:target="convertToOrder" class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Creation...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
