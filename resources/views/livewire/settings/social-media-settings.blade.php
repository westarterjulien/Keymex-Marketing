<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuration Social Media</h1>
            <p class="text-sm text-gray-500 mt-1">
                Configurez les API Meta (Facebook/Instagram) et OpenAI pour l'analyse des performances
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-600">Configuration active</span>
            <button
                wire:click="toggleActive"
                type="button"
                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $is_active ? 'bg-green-500' : 'bg-gray-200' }}"
            >
                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
            </button>
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

    {{-- Main Form --}}
    <form wire:submit="save" class="space-y-6">
        {{-- Meta Graph API --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Meta Graph API (Facebook / Instagram)
                </h2>
            </div>
            <div class="p-6 space-y-4">
                {{-- Access Token --}}
                <div>
                    <label for="meta_access_token" class="block text-sm font-medium text-gray-700 mb-1">
                        Access Token
                        @if($hasExistingMetaToken)
                            <span class="text-green-600 text-xs ml-2">(configure)</span>
                        @endif
                    </label>
                    <div class="relative">
                        <input
                            type="{{ $showMetaToken ? 'text' : 'password' }}"
                            wire:model="meta_access_token"
                            id="meta_access_token"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="{{ $hasExistingMetaToken ? 'Laisser vide pour conserver le token actuel' : 'Collez votre Long-Lived Access Token' }}"
                        >
                        <button
                            type="button"
                            wire:click="toggleMetaToken"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                        >
                            @if($showMetaToken)
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                    @error('meta_access_token')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Facebook Page ID --}}
                    <div>
                        <label for="meta_facebook_page_id" class="block text-sm font-medium text-gray-700 mb-1">Facebook Page ID</label>
                        <input
                            type="text"
                            wire:model="meta_facebook_page_id"
                            id="meta_facebook_page_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="123456789012345"
                        >
                        @error('meta_facebook_page_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Instagram Account ID --}}
                    <div>
                        <label for="meta_instagram_account_id" class="block text-sm font-medium text-gray-700 mb-1">Instagram Business Account ID</label>
                        <input
                            type="text"
                            wire:model="meta_instagram_account_id"
                            id="meta_instagram_account_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="17841400000000000"
                        >
                        @error('meta_instagram_account_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- API Version --}}
                <div class="max-w-xs">
                    <label for="meta_api_version" class="block text-sm font-medium text-gray-700 mb-1">Version API</label>
                    <select
                        wire:model="meta_api_version"
                        id="meta_api_version"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="v21.0">v21.0 (Recommande)</option>
                        <option value="v20.0">v20.0</option>
                        <option value="v19.0">v19.0</option>
                    </select>
                </div>

                {{-- Test Meta Connection --}}
                <div class="pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        wire:click="testMetaConnection"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        wire:target="testMetaConnection"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-medium rounded-lg transition-colors border border-blue-300"
                    >
                        <svg wire:loading wire:target="testMetaConnection" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg wire:loading.remove wire:target="testMetaConnection" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span wire:loading.remove wire:target="testMetaConnection">Tester la connexion Meta</span>
                        <span wire:loading wire:target="testMetaConnection">Test en cours...</span>
                    </button>
                </div>

                {{-- Token Info --}}
                @if($tokenInfo)
                    <div class="rounded-lg {{ $tokenInfo['valid'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} p-4 border">
                        <h4 class="font-medium {{ $tokenInfo['valid'] ? 'text-green-800' : 'text-red-800' }} mb-2">
                            {{ $tokenInfo['valid'] ? 'Token valide' : 'Token invalide' }}
                        </h4>
                        @if($tokenInfo['valid'])
                            <div class="text-sm text-green-700 space-y-1">
                                @if(isset($tokenInfo['expires_at']))
                                    <p>Expire le : {{ \Carbon\Carbon::parse($tokenInfo['expires_at'])->format('d/m/Y H:i') }}</p>
                                @endif
                                @if(isset($tokenInfo['scopes']))
                                    <p>Permissions : {{ implode(', ', $tokenInfo['scopes']) }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Facebook Page Info --}}
                @if($facebookPageInfo)
                    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $facebookPageInfo['name'] ?? 'Page Facebook' }}</p>
                                <p class="text-sm text-gray-600">{{ number_format($facebookPageInfo['followers_count'] ?? 0) }} followers</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Instagram Info --}}
                @if($instagramInfo)
                    <div class="rounded-lg bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $instagramInfo['username'] ?? 'Instagram' }}</p>
                                <p class="text-sm text-gray-600">{{ number_format($instagramInfo['followers_count'] ?? 0) }} followers</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- OpenAI API --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M22.282 9.821a5.985 5.985 0 0 0-.516-4.91 6.046 6.046 0 0 0-6.51-2.9A6.065 6.065 0 0 0 4.981 4.18a5.985 5.985 0 0 0-3.998 2.9 6.046 6.046 0 0 0 .743 7.097 5.98 5.98 0 0 0 .51 4.911 6.051 6.051 0 0 0 6.515 2.9A5.985 5.985 0 0 0 13.26 24a6.056 6.056 0 0 0 5.772-4.206 5.99 5.99 0 0 0 3.997-2.9 6.056 6.056 0 0 0-.747-7.073zM13.26 22.43a4.476 4.476 0 0 1-2.876-1.04l.141-.081 4.779-2.758a.795.795 0 0 0 .392-.681v-6.737l2.02 1.168a.071.071 0 0 1 .038.052v5.583a4.504 4.504 0 0 1-4.494 4.494zM3.6 18.304a4.47 4.47 0 0 1-.535-3.014l.142.085 4.783 2.759a.771.771 0 0 0 .78 0l5.843-3.369v2.332a.08.08 0 0 1-.033.062L9.74 19.95a4.5 4.5 0 0 1-6.14-1.646zM2.34 7.896a4.485 4.485 0 0 1 2.366-1.973V11.6a.766.766 0 0 0 .388.676l5.815 3.355-2.02 1.168a.076.076 0 0 1-.071 0l-4.83-2.786A4.504 4.504 0 0 1 2.34 7.872zm16.597 3.855l-5.833-3.387L15.119 7.2a.076.076 0 0 1 .071 0l4.83 2.791a4.494 4.494 0 0 1-.676 8.105v-5.678a.79.79 0 0 0-.407-.667zm2.01-3.023l-.141-.085-4.774-2.782a.776.776 0 0 0-.785 0L9.409 9.23V6.897a.066.066 0 0 1 .028-.061l4.83-2.787a4.5 4.5 0 0 1 6.68 4.66zm-12.64 4.135l-2.02-1.164a.08.08 0 0 1-.038-.057V6.075a4.5 4.5 0 0 1 7.375-3.453l-.142.08L8.704 5.46a.795.795 0 0 0-.393.681zm1.097-2.365l2.602-1.5 2.607 1.5v2.999l-2.597 1.5-2.607-1.5z"/>
                    </svg>
                    OpenAI (Analyse IA)
                </h2>
            </div>
            <div class="p-6 space-y-4">
                {{-- API Key --}}
                <div>
                    <label for="openai_api_key" class="block text-sm font-medium text-gray-700 mb-1">
                        Cle API
                        @if($hasExistingOpenAiKey)
                            <span class="text-green-600 text-xs ml-2">(configuree)</span>
                        @endif
                    </label>
                    <div class="relative">
                        <input
                            type="{{ $showOpenAiKey ? 'text' : 'password' }}"
                            wire:model="openai_api_key"
                            id="openai_api_key"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="{{ $hasExistingOpenAiKey ? 'Laisser vide pour conserver la cle actuelle' : 'sk-...' }}"
                        >
                        <button
                            type="button"
                            wire:click="toggleOpenAiKey"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                        >
                            @if($showOpenAiKey)
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                    @error('openai_api_key')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Model Selection --}}
                <div class="max-w-xs">
                    <label for="openai_model" class="block text-sm font-medium text-gray-700 mb-1">Modele</label>
                    <select
                        wire:model="openai_model"
                        id="openai_model"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    >
                        <option value="gpt-4">GPT-4 (Recommande)</option>
                        <option value="gpt-4-turbo">GPT-4 Turbo</option>
                        <option value="gpt-4o">GPT-4o</option>
                        <option value="gpt-3.5-turbo">GPT-3.5 Turbo (Economique)</option>
                    </select>
                </div>

                {{-- Test OpenAI Connection --}}
                <div class="pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        wire:click="testOpenAiConnection"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        wire:target="testOpenAiConnection"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 text-sm font-medium rounded-lg transition-colors border border-green-300"
                    >
                        <svg wire:loading wire:target="testOpenAiConnection" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg wire:loading.remove wire:target="testOpenAiConnection" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span wire:loading.remove wire:target="testOpenAiConnection">Tester la connexion OpenAI</span>
                        <span wire:loading wire:target="testOpenAiConnection">Test en cours...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-between">
            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-keymex-red hover:bg-keymex-red-hover text-white text-sm font-medium rounded-lg transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer les parametres
            </button>

            <a href="{{ route('social-media.dashboard') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors border border-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Voir le dashboard
            </a>
        </div>
    </form>

    {{-- Info Box --}}
    <div class="rounded-lg bg-blue-50 p-4 border border-blue-200">
        <div class="flex gap-3">
            <svg class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-700">
                <p class="font-medium mb-2">Comment obtenir les credentials :</p>
                <ul class="list-disc list-inside space-y-1 text-blue-600">
                    <li><strong>Meta Access Token</strong> - Depuis <a href="https://developers.facebook.com/tools/explorer" target="_blank" class="underline">Meta Graph API Explorer</a></li>
                    <li><strong>Facebook Page ID</strong> - Dans les parametres de votre page Facebook (A propos)</li>
                    <li><strong>Instagram Business ID</strong> - Via l'API Graph ou dans Meta Business Suite</li>
                    <li><strong>OpenAI API Key</strong> - Depuis <a href="https://platform.openai.com/api-keys" target="_blank" class="underline">OpenAI Platform</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
