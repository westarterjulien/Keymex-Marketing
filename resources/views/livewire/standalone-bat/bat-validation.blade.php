<div class="min-h-screen" x-data="{ pdfLoading: true }">

    @if($alreadyResponded)
        {{-- Already responded state --}}
        <div class="max-w-lg mx-auto">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 text-center animate-fade-in">
                <div class="flex justify-center mb-6">
                    @if($bat->status === 'validated')
                        <div class="flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-green-400 to-green-600 shadow-lg">
                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    @elseif($bat->status === 'refused')
                        <div class="flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-red-400 to-red-600 shadow-lg">
                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    @elseif($bat->status === 'modifications_requested')
                        <div class="flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 shadow-lg">
                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-keymex-red to-keymex-red-hover shadow-lg">
                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    @if($bat->status === 'validated')
                        BAT Valide !
                    @elseif($bat->status === 'refused')
                        BAT Refuse
                    @elseif($bat->status === 'modifications_requested')
                        Modifications demandees
                    @else
                        Reponse enregistree
                    @endif
                </h2>

                <p class="text-gray-500 mb-6">
                    Votre reponse a ete enregistree le <span class="font-medium text-gray-700">{{ $bat->responded_at?->format('d/m/Y') }}</span> a <span class="font-medium text-gray-700">{{ $bat->responded_at?->format('H:i') }}</span>
                </p>

                @if($bat->client_comment)
                    <div class="mt-6 p-4 bg-gray-50 rounded-xl text-left border border-gray-100">
                        <p class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Votre commentaire
                        </p>
                        <p class="text-gray-600">{{ $bat->client_comment }}</p>
                    </div>
                @endif

                <div class="mt-8 pt-6 border-t border-gray-100">
                    <p class="text-sm text-gray-400">Merci pour votre retour !</p>
                </div>
            </div>
        </div>

    @elseif(!$tokenValid)
        {{-- Token expired or invalid --}}
        <div class="max-w-lg mx-auto">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 text-center animate-fade-in">
                <div class="flex justify-center mb-6">
                    <div class="flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 shadow-lg">
                        <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-3">Lien expire</h2>
                <p class="text-gray-500">
                    Ce lien de validation a expire.<br>
                    Veuillez contacter l'equipe marketing pour obtenir un nouveau lien.
                </p>

                <div class="mt-8 p-4 bg-keymex-red/5 rounded-xl border border-keymex-red/10">
                    <p class="text-sm text-keymex-red font-medium">
                        Contact : marketing@keymeximmo.fr
                    </p>
                </div>
            </div>
        </div>

    @else
        {{-- Main validation interface - Two columns layout --}}
        <div class="flex flex-col lg:flex-row gap-6 animate-fade-in">

            {{-- LEFT COLUMN - PDF Viewer --}}
            <div class="lg:flex-1 lg:min-w-0">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden h-full flex flex-col">
                    {{-- Document header --}}
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-xl bg-keymex-red/10">
                                <svg class="h-5 w-5 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ $bat->file_name }}</p>
                                <p class="text-xs text-gray-500">Scrollez pour naviguer</p>
                            </div>
                        </div>
                        <a
                            href="{{ asset('storage/' . $bat->file_path) }}"
                            download="{{ $bat->file_name }}"
                            class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-keymex-red bg-keymex-red/10 rounded-lg hover:bg-keymex-red/20 transition-colors"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span class="hidden sm:inline">Telecharger</span>
                        </a>
                    </div>

                    {{-- Document Preview --}}
                    <div class="flex-1 relative bg-gray-900">
                        @if(str_starts_with($bat->file_mime, 'image/'))
                            {{-- Image Preview --}}
                            <div class="flex items-center justify-center p-4 h-full min-h-[60vh] lg:min-h-[75vh]">
                                <img
                                    src="{{ asset('storage/' . $bat->file_path) }}"
                                    alt="{{ $bat->file_name }}"
                                    class="max-w-full h-auto max-h-full rounded-lg shadow-2xl object-contain"
                                >
                            </div>
                        @else
                            {{-- PDF Viewer --}}
                            <div class="relative h-[60vh] lg:h-[75vh]">
                                {{-- Loading state --}}
                                <div
                                    x-show="pdfLoading"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="absolute inset-0 flex items-center justify-center bg-gray-900 z-10"
                                >
                                    <div class="text-center">
                                        <svg class="animate-spin h-10 w-10 text-keymex-red mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="text-gray-400 font-medium">Chargement du document...</p>
                                    </div>
                                </div>

                                {{-- PDF iframe --}}
                                <iframe
                                    src="{{ asset('storage/' . $bat->file_path) }}#toolbar=0&navpanes=0&view=FitH"
                                    class="w-full h-full border-0"
                                    style="background: #1f2937;"
                                    x-on:load="pdfLoading = false"
                                ></iframe>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN - Info & Actions --}}
            <div class="lg:w-80 xl:w-96 flex-shrink-0 space-y-4">
                {{-- BAT Info Card --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-keymex-red to-keymex-red-hover p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="text-white min-w-0">
                                @if($bat->title)
                                    <h2 class="text-lg font-bold truncate">{{ $bat->title }}</h2>
                                @else
                                    <h2 class="text-lg font-bold">Document #{{ $bat->id }}</h2>
                                @endif
                                <p class="text-white/80 text-sm mt-1 flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="truncate">{{ $bat->advisor_name }}</span>
                                </p>
                            </div>
                            <span class="flex-shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white/20 text-white">
                                <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1.5 animate-pulse"></span>
                                En attente
                            </span>
                        </div>
                    </div>

                    @if($bat->description)
                        <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                            <p class="text-gray-600 text-sm">{{ $bat->description }}</p>
                        </div>
                    @endif

                    <div class="px-5 py-3 flex items-center gap-2 text-xs text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Expire le {{ $bat->token_expires_at?->format('d/m/Y') }}
                    </div>
                </div>

                {{-- Print Details Card --}}
                @if($bat->supportType || $bat->format || $bat->category || $bat->grammage || $bat->price || $bat->delivery_time || $bat->quantity)
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Informations d'impression
                            </h3>
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

                {{-- Actions Card --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
                    <h3 class="text-base font-bold text-gray-900 mb-1">Votre decision</h3>
                    <p class="text-xs text-gray-500 mb-4">Choisissez une action apres examen</p>

                    <div class="space-y-3">
                        {{-- Validate Button --}}
                        <button
                            type="button"
                            wire:click="openConfirm('validate')"
                            wire:loading.attr="disabled"
                            class="w-full flex items-center gap-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl hover:border-green-400 hover:shadow-md hover:shadow-green-100 transition-all duration-200 group"
                        >
                            <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 shadow-lg group-hover:scale-105 transition-transform">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <span class="font-bold text-green-700 block">Valider</span>
                                <span class="text-xs text-gray-500">J'approuve ce BAT</span>
                            </div>
                        </button>

                        {{-- Modifications Button --}}
                        <button
                            type="button"
                            wire:click="openConfirm('modifications')"
                            wire:loading.attr="disabled"
                            class="w-full flex items-center gap-4 p-4 bg-gradient-to-r from-orange-50 to-amber-50 border-2 border-orange-200 rounded-xl hover:border-orange-400 hover:shadow-md hover:shadow-orange-100 transition-all duration-200 group"
                        >
                            <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-br from-orange-400 to-amber-500 shadow-lg group-hover:scale-105 transition-transform">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <span class="font-bold text-orange-700 block">Modifications</span>
                                <span class="text-xs text-gray-500">Ajustements necessaires</span>
                            </div>
                        </button>

                        {{-- Refuse Button --}}
                        <button
                            type="button"
                            wire:click="openConfirm('refuse')"
                            wire:loading.attr="disabled"
                            class="w-full flex items-center gap-4 p-4 bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-200 rounded-xl hover:border-red-400 hover:shadow-md hover:shadow-red-100 transition-all duration-200 group"
                        >
                            <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-br from-red-400 to-rose-500 shadow-lg group-hover:scale-105 transition-transform">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <span class="font-bold text-red-700 block">Refuser</span>
                                <span class="text-xs text-gray-500">Je refuse ce BAT</span>
                            </div>
                        </button>
                    </div>

                    {{-- Loading indicator --}}
                    <div wire:loading class="mt-4 flex items-center justify-center gap-2 text-gray-500">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="text-sm">Traitement...</span>
                    </div>
                </div>

                {{-- Help notice --}}
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">
                                Examinez attentivement le document avant de valider. En cas de doute, demandez des modifications.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Confirmation Modal --}}
    @if($showConfirmModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-data
            x-init="$nextTick(() => $refs.commentInput?.focus())"
        >
            {{-- Backdrop --}}
            <div
                wire:click="closeConfirm"
                class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
            ></div>

            {{-- Modal Content --}}
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 animate-slide-in-up">
                {{-- Header with icon --}}
                <div class="flex items-center gap-4 mb-5">
                    <div class="flex items-center justify-center h-12 w-12 rounded-xl shadow-lg
                        @if($action === 'validate') bg-gradient-to-br from-green-400 to-emerald-500
                        @elseif($action === 'modifications') bg-gradient-to-br from-orange-400 to-amber-500
                        @else bg-gradient-to-br from-red-400 to-rose-500 @endif"
                    >
                        @if($action === 'validate')
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        @elseif($action === 'modifications')
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        @else
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">
                            @if($action === 'validate')
                                Confirmer la validation
                            @elseif($action === 'modifications')
                                Demander des modifications
                            @else
                                Confirmer le refus
                            @endif
                        </h3>
                        <p class="text-sm text-gray-500">
                            @if($action === 'validate')
                                Le BAT sera envoye en production
                            @elseif($action === 'modifications')
                                Decrivez les changements souhaites
                            @else
                                Indiquez la raison du refus
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Comment field --}}
                <div class="mb-5">
                    <label for="comment" class="block text-sm font-semibold text-gray-700 mb-2">
                        Commentaire
                        @if($action !== 'validate')
                            <span class="text-keymex-red">*</span>
                        @else
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        @endif
                    </label>
                    <textarea
                        x-ref="commentInput"
                        wire:model="comment"
                        id="comment"
                        rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-keymex-red/20 focus:border-keymex-red transition-colors resize-none text-sm"
                        placeholder="@if($action === 'validate')Ajoutez un commentaire si vous le souhaitez...@elseif($action === 'modifications')Decrivez precisement les modifications souhaitees...@else Indiquez la raison du refus...@endif"
                    ></textarea>
                </div>

                {{-- Action buttons --}}
                <div class="flex gap-3">
                    <button
                        type="button"
                        wire:click="closeConfirm"
                        class="flex-1 px-4 py-3 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-sm"
                    >
                        Annuler
                    </button>
                    <button
                        type="button"
                        wire:click="confirm"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-wait"
                        class="flex-1 px-4 py-3 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm
                            @if($action === 'validate') bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700
                            @elseif($action === 'modifications') bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700
                            @else bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 @endif"
                    >
                        <span wire:loading.remove wire:target="confirm">Confirmer</span>
                        <span wire:loading wire:target="confirm" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Envoi...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
