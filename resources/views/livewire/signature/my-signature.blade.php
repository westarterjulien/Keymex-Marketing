<div class="max-w-4xl mx-auto py-8">
    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Ma Signature Email</h1>
        <p class="mt-2 text-gray-600">Generez et personnalisez votre signature email professionnelle KEYMEX</p>
    </div>

    {{-- Messages flash --}}
    @if ($showSavedMessage)
        <div class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200"
             x-data="{ show: true }"
             x-init="setTimeout(() => { show = false; $wire.hideSavedMessage() }, 3000)"
             x-show="show"
             x-transition>
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-green-700">Votre signature a ete sauvegardee avec succes !</p>
            </div>
        </div>
    @endif

    @if ($error)
        <div class="mb-6 rounded-lg bg-amber-50 p-4 border border-amber-200">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-amber-700">{{ $error }}</p>
            </div>
        </div>
    @endif

    {{-- Non authentifie --}}
    @if (!$isAuthenticated)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <div class="text-center">
                <div class="mx-auto w-16 h-16 bg-keymex-red/10 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>

                <h2 class="text-xl font-semibold text-gray-900 mb-2">Authentification requise</h2>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    Connectez-vous avec votre compte Microsoft 365 KEYMEX pour generer votre signature email personnalisee.
                </p>

                <a href="{{ route('signature.auth') }}"
                   class="inline-flex items-center gap-3 px-6 py-3 bg-[#2F2F2F] text-white font-medium rounded-xl hover:bg-[#1F1F1F] transition-colors shadow-lg">
                    {{-- Microsoft Logo --}}
                    <svg class="w-5 h-5" viewBox="0 0 23 23" fill="none">
                        <path fill="#f25022" d="M1 1h10v10H1z"/>
                        <path fill="#00a4ef" d="M1 12h10v10H1z"/>
                        <path fill="#7fba00" d="M12 1h10v10H12z"/>
                        <path fill="#ffb900" d="M12 12h10v10H12z"/>
                    </svg>
                    Se connecter avec Microsoft
                </a>
            </div>
        </div>

    {{-- Authentifie --}}
    @else
        <div class="space-y-6">
            {{-- Info utilisateur + deconnexion --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if ($advisor && !empty($advisor['picture']))
                            <img src="{{ $advisor['picture'] }}" alt="Photo" class="w-10 h-10 rounded-full object-cover border-2 border-keymex-red">
                        @else
                            <div class="w-10 h-10 rounded-full bg-keymex-red/10 flex items-center justify-center">
                                <span class="text-keymex-red font-semibold">{{ substr($advisor['firstname'] ?? 'U', 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-900">{{ $advisor['firstname'] ?? '' }} {{ $advisor['lastname'] ?? '' }}</p>
                            <p class="text-sm text-gray-500">{{ $userEmail }}</p>
                        </div>
                        @if ($savedSignature)
                            <span class="ml-2 px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Signature sauvegardee</span>
                        @endif
                    </div>
                    <button wire:click="logout"
                            class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Deconnecter
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Panneau de configuration (gauche) --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Selection Template/Marque --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                            </svg>
                            Template & Marque
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Template</label>
                                <select wire:model.live="selectedTemplateId"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red">
                                    <option value="">-- Selectionner --</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Marque</label>
                                <select wire:model.live="selectedBrandId"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red">
                                    <option value="">-- Selectionner --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Edition des donnees --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Mes informations
                            </h3>
                            @if (!$isEditing)
                                <button wire:click="startEditing"
                                        class="text-sm text-keymex-red hover:text-keymex-red/80 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Modifier
                                </button>
                            @endif
                        </div>

                        @if ($isEditing)
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Prenom</label>
                                        <input type="text" wire:model="editFirstname"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Nom</label>
                                        <input type="text" wire:model="editLastname"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Fonction</label>
                                    <input type="text" wire:model="editJobTitle"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                                           placeholder="Conseiller Immobilier">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Tel. fixe</label>
                                        <input type="text" wire:model="editPhone"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                                               placeholder="01 23 45 67 89">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Mobile</label>
                                        <input type="text" wire:model="editMobilePhone"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                                               placeholder="06 12 34 56 78">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">URL Photo</label>
                                    <input type="url" wire:model="editPictureUrl"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                                           placeholder="https://example.com/photo.jpg">
                                    <p class="mt-1 text-xs text-gray-500">Entrez l'URL d'une image hebergee</p>
                                </div>

                                <div class="border-t border-gray-200 pt-4">
                                    <p class="text-xs font-medium text-gray-600 mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                        </svg>
                                        Mes reseaux sociaux
                                    </p>
                                    <div class="space-y-3">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="w-4 h-4 text-[#0A66C2]" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                                </svg>
                                            </div>
                                            <input type="url" wire:model="editLinkedin"
                                                   class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                                                   placeholder="https://linkedin.com/in/votre-profil">
                                        </div>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="w-4 h-4 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                </svg>
                                            </div>
                                            <input type="url" wire:model="editFacebook"
                                                   class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                                                   placeholder="https://facebook.com/votre-page">
                                        </div>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="w-4 h-4 text-[#E4405F]" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                                                </svg>
                                            </div>
                                            <input type="url" wire:model="editInstagram"
                                                   class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                                                   placeholder="https://instagram.com/votre-compte">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2 pt-2">
                                    <button wire:click="saveChanges"
                                            class="flex-1 px-4 py-2 bg-keymex-red text-white text-sm font-medium rounded-lg hover:bg-keymex-red/90 transition-colors">
                                        Sauvegarder
                                    </button>
                                    <button wire:click="cancelEditing"
                                            class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                        Annuler
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Prenom</span>
                                    <span class="font-medium text-gray-900">{{ $advisor['firstname'] ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Nom</span>
                                    <span class="font-medium text-gray-900">{{ $advisor['lastname'] ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Fonction</span>
                                    <span class="font-medium text-gray-900">{{ $advisor['job_title'] ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Mobile</span>
                                    <span class="font-medium text-gray-900">{{ $advisor['mobile_phone'] ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Tel. fixe</span>
                                    <span class="font-medium text-gray-900">{{ $advisor['phone'] ?? '-' }}</span>
                                </div>

                                {{-- Réseaux sociaux --}}
                                @if(!empty($advisor['linkedin_url']) || !empty($advisor['facebook_url']) || !empty($advisor['instagram_url']))
                                    <div class="border-t border-gray-200 pt-3 mt-3">
                                        <span class="text-gray-500 text-xs block mb-2">Reseaux sociaux</span>
                                        <div class="flex gap-3">
                                            @if(!empty($advisor['linkedin_url']))
                                                <a href="{{ $advisor['linkedin_url'] }}" target="_blank" class="text-[#0A66C2] hover:opacity-80" title="LinkedIn">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if(!empty($advisor['facebook_url']))
                                                <a href="{{ $advisor['facebook_url'] }}" target="_blank" class="text-[#1877F2] hover:opacity-80" title="Facebook">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if(!empty($advisor['instagram_url']))
                                                <a href="{{ $advisor['instagram_url'] }}" target="_blank" class="text-[#E4405F] hover:opacity-80" title="Instagram">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="border-t border-gray-200 pt-3 mt-3">
                                        <p class="text-xs text-gray-400 italic">Aucun reseau social configure. Cliquez sur "Modifier" pour en ajouter.</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Preview signature (droite) --}}
                <div class="lg:col-span-2">
                    @if ($signatureHtml)
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                                <h2 class="font-semibold text-gray-900">Apercu de votre signature</h2>
                            </div>

                            <div class="p-6">
                                {{-- Signature preview --}}
                                <div id="signature-preview" class="bg-white border border-gray-200 rounded-lg p-4 overflow-x-auto">
                                    {!! $signatureHtml !!}
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-wrap gap-3 justify-center">
                                <button onclick="copySignatureHTML()"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-keymex-red text-white font-medium rounded-xl hover:bg-keymex-red/90 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                    </svg>
                                    Copier le HTML
                                </button>

                                <button onclick="downloadSignatureHTML()"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Telecharger .html
                                </button>

                                <button onclick="copySignatureRich()"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Copier (formatte)
                                </button>
                            </div>
                        </div>

                        {{-- Instructions --}}
                        <div class="mt-6 bg-blue-50 rounded-xl p-6 border border-blue-100">
                            <h3 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Comment utiliser votre signature
                            </h3>
                            <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800">
                                <li>Cliquez sur <strong>"Copier (formatte)"</strong> pour copier la signature avec sa mise en forme</li>
                                <li>Ouvrez les parametres de signature dans votre client mail (Outlook, Gmail, etc.)</li>
                                <li>Collez la signature dans l'editeur</li>
                                <li>Enregistrez vos modifications</li>
                            </ol>
                            <p class="mt-4 text-sm text-blue-700 bg-blue-100 rounded-lg p-3">
                                <strong>Astuce :</strong> Votre signature est sauvegardee automatiquement. Vous pouvez revenir a tout moment pour la recuperer ou la modifier.
                            </p>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
                            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun apercu disponible</h3>
                            <p class="text-gray-500">Selectionnez un template et remplissez vos informations pour generer votre signature.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Notification toast --}}
    <div id="toast" class="fixed bottom-4 right-4 bg-gray-900 text-white px-4 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300">
        <span id="toast-message"></span>
    </div>
</div>

@push('scripts')
<script>
    // Copier le HTML brut
    function copySignatureHTML() {
        const html = document.getElementById('signature-preview').innerHTML;
        navigator.clipboard.writeText(html).then(() => {
            showToast('HTML copie dans le presse-papier !');
        }).catch(err => {
            showToast('Erreur lors de la copie', true);
        });
    }

    // Copier avec formatage (rich text)
    function copySignatureRich() {
        const preview = document.getElementById('signature-preview');
        const range = document.createRange();
        range.selectNodeContents(preview);

        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);

        try {
            document.execCommand('copy');
            showToast('Signature copiee avec formatage !');
        } catch (err) {
            showToast('Erreur lors de la copie', true);
        }

        selection.removeAllRanges();
    }

    // Telecharger le fichier HTML
    function downloadSignatureHTML() {
        const html = `<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ma Signature KEYMEX</title>
</head>
<body>
${document.getElementById('signature-preview').innerHTML}
</body>
</html>`;

        const blob = new Blob([html], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'signature-keymex.html';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        showToast('Fichier telecharge !');
    }

    // Afficher une notification toast
    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');

        toastMessage.textContent = message;
        toast.classList.toggle('bg-red-600', isError);
        toast.classList.toggle('bg-gray-900', !isError);

        toast.classList.remove('translate-y-full', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');

        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
            toast.classList.remove('translate-y-0', 'opacity-100');
        }, 3000);
    }
</script>
@endpush
