<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuration SMTP</h1>
            <p class="text-sm text-gray-500 mt-1">
                Configurez les parametres d'envoi d'emails de l'application
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
        {{-- Serveur SMTP --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                    Serveur de messagerie
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Mailer --}}
                    <div>
                        <label for="mail_mailer" class="block text-sm font-medium text-gray-700 mb-1">Type de mailer</label>
                        <select
                            wire:model.live="mail_mailer"
                            id="mail_mailer"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                        >
                            <option value="smtp">SMTP</option>
                            <option value="sendmail">Sendmail</option>
                            <option value="log">Log (developpement)</option>
                        </select>
                    </div>

                    {{-- Host --}}
                    <div>
                        <label for="mail_host" class="block text-sm font-medium text-gray-700 mb-1">Serveur SMTP</label>
                        <input
                            type="text"
                            wire:model="mail_host"
                            id="mail_host"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                            placeholder="smtp.example.com"
                        >
                        @error('mail_host')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Port --}}
                    <div>
                        <label for="mail_port" class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                        <input
                            type="number"
                            wire:model="mail_port"
                            id="mail_port"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                            placeholder="587"
                        >
                        @error('mail_port')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Encryption --}}
                    <div>
                        <label for="mail_encryption" class="block text-sm font-medium text-gray-700 mb-1">Chiffrement</label>
                        <select
                            wire:model="mail_encryption"
                            id="mail_encryption"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                        >
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                            <option value="null">Aucun</option>
                        </select>
                    </div>
                </div>

                {{-- Bouton test connexion --}}
                <div class="pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        wire:click="testSmtpConnection"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        wire:target="testSmtpConnection"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors border border-gray-300"
                    >
                        <svg wire:loading wire:target="testSmtpConnection" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg wire:loading.remove wire:target="testSmtpConnection" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span wire:loading.remove wire:target="testSmtpConnection">Tester la connexion</span>
                        <span wire:loading wire:target="testSmtpConnection">Test en cours...</span>
                    </button>
                    <p class="mt-2 text-xs text-gray-500">Verifie que le serveur SMTP est accessible (sans envoyer d'email)</p>
                </div>
            </div>
        </div>

        {{-- Authentification --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Authentification
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Username --}}
                    <div>
                        <label for="mail_username" class="block text-sm font-medium text-gray-700 mb-1">Nom d'utilisateur</label>
                        <input
                            type="text"
                            wire:model="mail_username"
                            id="mail_username"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                            placeholder="user@example.com"
                        >
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="mail_password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                        <div class="relative">
                            <input
                                type="{{ $showPassword ? 'text' : 'password' }}"
                                wire:model="mail_password"
                                id="mail_password"
                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                                placeholder="Laisser vide pour ne pas modifier"
                            >
                            <button
                                type="button"
                                wire:click="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                            >
                                @if($showPassword)
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
                        <p class="mt-1 text-xs text-gray-500">Laissez vide pour conserver le mot de passe actuel</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Expediteur --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Expediteur par defaut
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- From Address --}}
                    <div>
                        <label for="mail_from_address" class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                        <input
                            type="email"
                            wire:model="mail_from_address"
                            id="mail_from_address"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                            placeholder="noreply@keymex.fr"
                        >
                        @error('mail_from_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- From Name --}}
                    <div>
                        <label for="mail_from_name" class="block text-sm font-medium text-gray-700 mb-1">Nom d'affichage</label>
                        <input
                            type="text"
                            wire:model="mail_from_name"
                            id="mail_from_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                            placeholder="KEYMEX Marketing"
                        >
                        @error('mail_from_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
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
        </div>
    </form>

    {{-- Test de connexion --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-keymex-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tester la configuration
            </h2>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">
                Envoyez un email de test pour verifier que la configuration SMTP fonctionne correctement.
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input
                        type="email"
                        wire:model="testEmail"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                        placeholder="Email de destination pour le test"
                    >
                    @error('testEmail')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button
                    type="button"
                    wire:click="testConnection"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <svg wire:loading wire:target="testConnection" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="testConnection">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </span>
                    Envoyer un test
                </button>
            </div>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="rounded-lg bg-blue-50 p-4 border border-blue-200">
        <div class="flex gap-3">
            <svg class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-700">
                <p class="font-medium mb-1">Ports SMTP courants :</p>
                <ul class="list-disc list-inside space-y-1 text-blue-600">
                    <li><strong>587</strong> - Port standard avec STARTTLS (recommande)</li>
                    <li><strong>465</strong> - Port SSL/TLS implicite</li>
                    <li><strong>25</strong> - Port standard non securise (deconseille)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
