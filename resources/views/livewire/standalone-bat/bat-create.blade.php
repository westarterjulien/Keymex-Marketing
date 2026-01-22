<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouveau BAT</h1>
            <p class="text-sm text-gray-500 mt-1">
                Creer un bon a tirer pour validation client
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

    {{-- Form --}}
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Conseiller</h2>

            <div class="relative">
                @if($selectedAdvisor)
                    <div class="flex items-center justify-between p-4 bg-red-50 border border-keymex-red200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-keymex-red text-white font-semibold">
                                {{ strtoupper(substr($selectedAdvisor['fullname'], 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $selectedAdvisor['fullname'] }}</p>
                                <p class="text-sm text-gray-500">{{ $selectedAdvisor['email'] }}</p>
                                @if(!empty($selectedAdvisor['agency']))
                                    <p class="text-xs text-gray-400">{{ $selectedAdvisor['agency'] }}</p>
                                @endif
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="clearAdvisor"
                            class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                            title="Changer de conseiller"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @else
                    <div>
                        <label for="advisorSearch" class="block text-sm font-medium text-gray-700 mb-1">
                            Rechercher un conseiller
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="advisorSearch"
                                id="advisorSearch"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                                placeholder="Nom, email..."
                                autocomplete="off"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg wire:loading wire:target="advisorSearch" class="h-5 w-5 text-gray-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                        </div>
                        @error('advisorSearch')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        {{-- Dropdown results --}}
                        @if($showAdvisorDropdown && count($advisorResults) > 0)
                            <div class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                @foreach($advisorResults as $index => $advisor)
                                    <button
                                        type="button"
                                        wire:click="selectAdvisor({{ $index }})"
                                        class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 transition-colors {{ !$loop->last ? 'border-b border-gray-100' : '' }}"
                                    >
                                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-keymex-red text-sm font-semibold">
                                            {{ strtoupper(substr($advisor['fullname'], 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 text-sm">{{ $advisor['fullname'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $advisor['email'] }}</p>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @elseif($showAdvisorDropdown && strlen($advisorSearch) >= 2 && count($advisorResults) === 0)
                            <div class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-4">
                                <p class="text-sm text-gray-500 text-center">Aucun conseiller trouve</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Details du BAT</h2>

            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Titre (optionnel)
                    </label>
                    <input
                        type="text"
                        wire:model="title"
                        id="title"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                        placeholder="Ex: Panneau vitrine Mai 2025"
                    >
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description (optionnel)
                    </label>
                    <textarea
                        wire:model="description"
                        id="description"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                        placeholder="Instructions ou details supplementaires..."
                    ></textarea>
                </div>
            </div>
        </div>

        {{-- Print Details --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations d'impression</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Type de support --}}
                <div>
                    <label for="supportTypeId" class="block text-sm font-medium text-gray-700 mb-1">
                        Type de support
                    </label>
                    <select
                        wire:model.live="supportTypeId"
                        id="supportTypeId"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                    >
                        <option value="">Selectionner...</option>
                        @foreach($supportTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Format (depend du type de support) --}}
                <div>
                    <label for="formatId" class="block text-sm font-medium text-gray-700 mb-1">
                        Format
                    </label>
                    <select
                        wire:model="formatId"
                        id="formatId"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red disabled:bg-gray-100 disabled:cursor-not-allowed"
                        @if(!$supportTypeId) disabled @endif
                    >
                        <option value="">{{ $supportTypeId ? 'Selectionner...' : 'Choisir d\'abord un support' }}</option>
                        @foreach($availableFormats as $format)
                            <option value="{{ $format['id'] }}">{{ $format['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Categorie --}}
                <div>
                    <label for="categoryId" class="block text-sm font-medium text-gray-700 mb-1">
                        Categorie
                    </label>
                    <select
                        wire:model="categoryId"
                        id="categoryId"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                    >
                        <option value="">Selectionner...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Grammage --}}
                <div>
                    <label for="grammage" class="block text-sm font-medium text-gray-700 mb-1">
                        Grammage
                    </label>
                    <input
                        type="text"
                        wire:model="grammage"
                        id="grammage"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                        placeholder="Ex: 350g, 135g mat..."
                    >
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Fichier BAT</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Document BAT <span class="text-red-500">*</span>
                </label>

                @if($batFile)
                    <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-green-100">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $batFile->getClientOriginalName() }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($batFile->getSize() / 1024, 2) }} Ko</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="$set('batFile', null)"
                            class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                            title="Supprimer le fichier"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @else
                    <label
                        for="batFile"
                        class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-keymex-red hover:bg-red-50/50 transition-colors"
                    >
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500">
                                <span class="font-semibold">Cliquez pour telecharger</span> ou glissez-deposez
                            </p>
                            <p class="text-xs text-gray-400">PDF, JPG ou PNG (max. 20 Mo)</p>
                        </div>
                        <input
                            type="file"
                            wire:model="batFile"
                            id="batFile"
                            class="hidden"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >
                    </label>

                    <div wire:loading wire:target="batFile" class="mt-2">
                        <div class="flex items-center gap-2 text-sm text-keymex-red">
                            <svg class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Telechargement en cours...
                        </div>
                    </div>
                @endif

                @error('batFile')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a
                href="{{ route('standalone-bats.index') }}"
                wire:navigate
                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
            >
                Annuler
            </a>
            <button
                type="submit"
                class="px-6 py-2 bg-keymex-red text-white rounded-lg hover:bg-keymex-red-hover transition-colors inline-flex items-center gap-2"
            >
                <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Creer le BAT
            </button>
        </div>
    </form>
</div>
