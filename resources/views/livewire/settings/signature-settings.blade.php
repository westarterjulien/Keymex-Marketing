<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuration Signatures</h1>
            <p class="text-sm text-gray-500 mt-1">
                Gerez les templates de signature email et les marques
            </p>
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

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex gap-6" aria-label="Tabs">
            <button
                wire:click="setTab('templates')"
                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'templates' ? 'border-keymex-violet text-keymex-violet' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                Templates
                <span class="ml-2 rounded-full bg-gray-100 px-2 py-0.5 text-xs">
                    {{ $templates->count() }}
                </span>
            </button>
            <button
                wire:click="setTab('brands')"
                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'brands' ? 'border-keymex-violet text-keymex-violet' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                Marques
                <span class="ml-2 rounded-full bg-gray-100 px-2 py-0.5 text-xs">
                    {{ $brands->count() }}
                </span>
            </button>
            <button
                wire:click="setTab('campaigns')"
                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'campaigns' ? 'border-keymex-violet text-keymex-violet' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                Campagnes
                <span class="ml-2 rounded-full bg-gray-100 px-2 py-0.5 text-xs">
                    {{ $campaigns->count() }}
                </span>
            </button>
            <button
                wire:click="setTab('communication')"
                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'communication' ? 'border-keymex-violet text-keymex-violet' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <svg class="inline-block h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
                Communication
            </button>
        </nav>
    </div>

    {{-- Tab Content --}}
    @if($activeTab === 'templates')
        {{-- Templates --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Templates de signature</h2>
                <button
                    wire:click="openTemplateModal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-keymex-violet hover:bg-keymex-violet-dark text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter
                </button>
            </div>

            {{-- Variables disponibles --}}
            <div class="p-4 bg-violet-50 border-b border-gray-200">
                <p class="text-sm font-medium text-gray-700 mb-2">Variables disponibles :</p>
                <div class="flex flex-wrap gap-2 text-xs">
                    <code class="px-2 py-1 bg-white rounded border border-violet-200 text-violet-700">@{{contact.firstName}}</code>
                    <code class="px-2 py-1 bg-white rounded border border-violet-200 text-violet-700">@{{contact.lastName}}</code>
                    <code class="px-2 py-1 bg-white rounded border border-violet-200 text-violet-700">@{{contact.email}}</code>
                    <code class="px-2 py-1 bg-white rounded border border-violet-200 text-violet-700">@{{contact.mobile}}</code>
                    <code class="px-2 py-1 bg-white rounded border border-violet-200 text-violet-700">@{{contact.phone}}</code>
                    <code class="px-2 py-1 bg-white rounded border border-violet-200 text-violet-700">@{{contact.photoUrl}}</code>
                    <code class="px-2 py-1 bg-white rounded border border-violet-200 text-violet-700">@{{contact.jobTitle}}</code>
                    <code class="px-2 py-1 bg-white rounded border border-violet-200 text-violet-700">@{{brand.name}}</code>
                    <code class="px-2 py-1 bg-white rounded border border-violet-200 text-violet-700">@{{brand.logoUrl}}</code>
                    <code class="px-2 py-1 bg-white rounded border border-violet-200 text-violet-700">@{{brand.website}}</code>
                    <code class="px-2 py-1 bg-white rounded border border-violet-200 text-violet-700">@{{brand.primaryColor}}</code>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($templates as $template)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <span class="flex items-center justify-center h-10 w-10 rounded-lg bg-violet-100 text-keymex-violet">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-medium text-gray-900">{{ $template->name }}</p>
                                    @if($template->is_default)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-keymex-violet">
                                            Par defaut
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500">
                                    Marque: {{ $template->brand?->name ?? 'Aucune' }}
                                    @if($template->description)
                                        - {{ Str::limit($template->description, 50) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button
                                wire:click="previewTemplate({{ $template->id }})"
                                class="p-2 text-gray-400 hover:text-keymex-violet transition-colors"
                                title="Apercu"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button
                                wire:click="toggleTemplateActive({{ $template->id }})"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $template->is_active ? 'bg-keymex-violet' : 'bg-gray-200' }}"
                                title="{{ $template->is_active ? 'Actif' : 'Inactif' }}"
                            >
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $template->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <button
                                wire:click="openTemplateModal({{ $template->id }})"
                                class="p-2 text-gray-400 hover:text-keymex-violet transition-colors"
                                title="Modifier"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button
                                wire:click="confirmDelete('template', {{ $template->id }})"
                                class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                title="Supprimer"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        Aucun template. Cliquez sur "Ajouter" pour en creer un.
                    </div>
                @endforelse
            </div>
        </div>
    @elseif($activeTab === 'brands')
        {{-- Brands --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Marques</h2>
                <button
                    wire:click="openBrandModal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-keymex-violet hover:bg-keymex-violet-dark text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter
                </button>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($brands as $brand)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <span
                                class="flex items-center justify-center h-10 w-10 rounded-lg text-white font-bold text-sm"
                                style="background-color: {{ $brand->primary_color ?? '#8B5CF6' }}"
                            >
                                {{ strtoupper(substr($brand->name, 0, 2)) }}
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-medium text-gray-900">{{ $brand->name }}</p>
                                    @if($brand->is_default)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-keymex-violet">
                                            Par defaut
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <span class="inline-block w-3 h-3 rounded-full" style="background-color: {{ $brand->primary_color ?? '#8B5CF6' }}"></span>
                                        {{ $brand->primary_color ?? '#8B5CF6' }}
                                    </span>
                                    @if($brand->website)
                                        <span>{{ parse_url($brand->website, PHP_URL_HOST) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button
                                wire:click="toggleBrandActive({{ $brand->id }})"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $brand->is_active ? 'bg-keymex-violet' : 'bg-gray-200' }}"
                                title="{{ $brand->is_active ? 'Actif' : 'Inactif' }}"
                            >
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $brand->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <button
                                wire:click="openBrandModal({{ $brand->id }})"
                                class="p-2 text-gray-400 hover:text-keymex-violet transition-colors"
                                title="Modifier"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button
                                wire:click="confirmDelete('brand', {{ $brand->id }})"
                                class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                title="Supprimer"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        Aucune marque. Cliquez sur "Ajouter" pour en creer une.
                    </div>
                @endforelse
            </div>
        </div>
    @elseif($activeTab === 'campaigns')
        {{-- Campaigns --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Campagnes / Bannieres</h2>
                <button
                    wire:click="openCampaignModal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-keymex-violet hover:bg-keymex-violet-dark text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter
                </button>
            </div>
            <div class="p-4 bg-blue-50 border-b border-gray-200">
                <p class="text-sm text-blue-700">
                    <strong>Info :</strong> Les bannieres actives sont automatiquement ajoutees en bas des signatures email.
                    Vous pouvez definir une periode de validite pour les campagnes temporaires.
                </p>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($campaigns as $campaign)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            @if($campaign->banner_url)
                                <img
                                    src="{{ $campaign->banner_url }}"
                                    alt="{{ $campaign->name }}"
                                    class="h-12 w-auto rounded object-cover border border-gray-200"
                                    style="max-width: 120px;"
                                >
                            @else
                                <span class="flex items-center justify-center h-12 w-20 rounded bg-gray-100 text-gray-400">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                            @endif
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-medium text-gray-900">{{ $campaign->name }}</p>
                                    @if($campaign->brand)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            {{ $campaign->brand->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-keymex-violet">
                                            Globale
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                    @if($campaign->start_date || $campaign->end_date)
                                        <span>
                                            {{ $campaign->start_date?->format('d/m/Y') ?? 'Debut' }}
                                            -
                                            {{ $campaign->end_date?->format('d/m/Y') ?? 'Indefinie' }}
                                        </span>
                                    @else
                                        <span>Permanente</span>
                                    @endif
                                    @if($campaign->link_url)
                                        <span class="text-keymex-violet">Cliquable</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button
                                wire:click="toggleCampaignActive({{ $campaign->id }})"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $campaign->is_active ? 'bg-keymex-violet' : 'bg-gray-200' }}"
                                title="{{ $campaign->is_active ? 'Actif' : 'Inactif' }}"
                            >
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $campaign->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <button
                                wire:click="openCampaignModal({{ $campaign->id }})"
                                class="p-2 text-gray-400 hover:text-keymex-violet transition-colors"
                                title="Modifier"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button
                                wire:click="confirmDelete('campaign', {{ $campaign->id }})"
                                class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                title="Supprimer"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        Aucune campagne. Cliquez sur "Ajouter" pour en creer une.
                    </div>
                @endforelse
            </div>
        </div>
    @elseif($activeTab === 'communication')
        {{-- Communication --}}
        <div class="space-y-6">
            {{-- Lien de partage --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Lien de partage</h2>
                    <p class="text-sm text-gray-500 mt-1">Partagez ce lien avec vos conseillers pour qu'ils puissent generer leur signature email.</p>
                </div>
                <div class="p-6">
                    @php
                        $signatureUrl = rtrim(config('app.url'), '/') . '/ma-signature';
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="flex-1 relative">
                            <input
                                type="text"
                                value="{{ $signatureUrl }}"
                                readonly
                                id="signatureLink"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 font-mono text-sm"
                            >
                        </div>
                        <button
                            onclick="copyToClipboard('{{ $signatureUrl }}')"
                            class="inline-flex items-center gap-2 px-4 py-3 bg-keymex-violet hover:bg-keymex-violet-dark text-white text-sm font-medium rounded-lg transition-colors"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Copier
                        </button>
                        <a
                            href="{{ $signatureUrl }}"
                            target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Ouvrir
                        </a>
                    </div>
                    <p class="mt-3 text-xs text-gray-500">
                        Ce lien permet aux conseillers de se connecter avec leur compte Microsoft 365 et de generer automatiquement leur signature personnalisee.
                    </p>
                </div>
            </div>

            {{-- QR Code --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">QR Code</h2>
                    <p class="text-sm text-gray-500 mt-1">Scannez ou telechargez ce QR code pour acceder a la page signature.</p>
                </div>
                <div class="p-6">
                    <div class="flex flex-col md:flex-row items-start gap-6">
                        <div class="bg-white p-4 rounded-lg border-2 border-gray-200 shadow-sm">
                            <img
                                src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($signatureUrl) }}&bgcolor=ffffff&color=8B5CF6"
                                alt="QR Code Signature"
                                class="w-48 h-48"
                                id="qrCodeImage"
                            >
                        </div>
                        <div class="flex-1 space-y-4">
                            <div>
                                <h3 class="font-medium text-gray-900 mb-2">Utilisation du QR Code</h3>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li class="flex items-start gap-2">
                                        <svg class="h-4 w-4 text-keymex-violet mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Affichez-le lors de vos reunions d'equipe
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="h-4 w-4 text-keymex-violet mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Imprimez-le et affichez-le dans vos locaux
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="h-4 w-4 text-keymex-violet mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Integrez-le dans vos presentations
                                    </li>
                                </ul>
                            </div>
                            <div class="flex gap-3">
                                <a
                                    href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode($signatureUrl) }}&bgcolor=ffffff&color=8B5CF6&format=png"
                                    download="qrcode-signature-keymex.png"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-keymex-violet hover:bg-keymex-violet-dark text-white text-sm font-medium rounded-lg transition-colors"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Telecharger PNG
                                </a>
                                <a
                                    href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode($signatureUrl) }}&bgcolor=ffffff&color=8B5CF6&format=svg"
                                    download="qrcode-signature-keymex.svg"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Telecharger SVG
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Email aux conseillers --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Notifier les conseillers</h2>
                    <p class="text-sm text-gray-500 mt-1">Envoyez un email a vos conseillers pour les informer de la nouvelle signature.</p>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Email template preview --}}
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Apercu du message</h4>
                        <div class="bg-white rounded-lg p-4 border border-gray-200 text-sm">
                            <p class="font-semibold text-gray-900 mb-3">Objet : Votre nouvelle signature email KEYMEX</p>
                            <div class="text-gray-700 space-y-3">
                                <p>Bonjour,</p>
                                <p>Nous avons mis en place un nouvel outil pour generer votre signature email professionnelle KEYMEX.</p>
                                <p>Pour obtenir votre signature personnalisee :</p>
                                <ol class="list-decimal list-inside ml-2 space-y-1">
                                    <li>Cliquez sur le lien ci-dessous</li>
                                    <li>Connectez-vous avec votre compte Microsoft 365</li>
                                    <li>Votre signature sera automatiquement generee</li>
                                    <li>Copiez-la et collez-la dans les parametres de votre messagerie</li>
                                </ol>
                                <p class="mt-3">
                                    <strong>Lien :</strong>
                                    <a href="{{ $signatureUrl }}" class="text-keymex-violet hover:underline">{{ $signatureUrl }}</a>
                                </p>
                                <p class="mt-3">Cordialement,<br>L'equipe Marketing KEYMEX</p>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap gap-3">
                        <button
                            onclick="copyEmailTemplate()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-keymex-violet hover:bg-keymex-violet-dark text-white text-sm font-medium rounded-lg transition-colors"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Copier le texte
                        </button>
                        <a
                            href="mailto:?subject=Votre%20nouvelle%20signature%20email%20KEYMEX&body=Bonjour%2C%0A%0ANous%20avons%20mis%20en%20place%20un%20nouvel%20outil%20pour%20g%C3%A9n%C3%A9rer%20votre%20signature%20email%20professionnelle%20KEYMEX.%0A%0APour%20obtenir%20votre%20signature%20personnalis%C3%A9e%20%3A%0A1.%20Cliquez%20sur%20le%20lien%20ci-dessous%0A2.%20Connectez-vous%20avec%20votre%20compte%20Microsoft%20365%0A3.%20Votre%20signature%20sera%20automatiquement%20g%C3%A9n%C3%A9r%C3%A9e%0A4.%20Copiez-la%20et%20collez-la%20dans%20les%20param%C3%A8tres%20de%20votre%20messagerie%0A%0ALien%20%3A%20{{ urlencode($signatureUrl) }}%0A%0ACordialement%2C%0AL%27%C3%A9quipe%20Marketing%20KEYMEX"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Ouvrir dans ma messagerie
                        </a>
                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        Conseil : Vous pouvez aussi partager ce message via Teams, Slack ou tout autre outil de communication interne.
                    </p>
                </div>
            </div>

            {{-- Statistiques (placeholder) --}}
            <div class="bg-gradient-to-r from-keymex-violet/10 to-purple-100 rounded-xl border border-keymex-violet/20 p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 p-3 bg-keymex-violet/20 rounded-lg">
                        <svg class="h-6 w-6 text-keymex-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Astuce</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Encouragez vos conseillers a utiliser la nouvelle signature en leur expliquant les avantages :
                            uniformite de l'image de marque, mise a jour automatique des informations, et integration des campagnes promotionnelles.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Template --}}
    @if($showTemplateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div wire:click="closeTemplateModal" class="absolute inset-0 bg-gray-900/50"></div>

            {{-- Modal Content --}}
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-4xl p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ $editingTemplateId ? 'Modifier le template' : 'Nouveau template' }}
                </h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="templateName" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                            <input
                                type="text"
                                wire:model="templateName"
                                id="templateName"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                                placeholder="Ex: Template KEYMEX Standard"
                            >
                            @error('templateName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="templateBrandId" class="block text-sm font-medium text-gray-700 mb-1">Marque *</label>
                            <select
                                wire:model="templateBrandId"
                                id="templateBrandId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                            >
                                <option value="">Selectionnez une marque</option>
                                @foreach($brandsForSelect as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('templateBrandId')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="templateDescription" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input
                            type="text"
                            wire:model="templateDescription"
                            id="templateDescription"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                            placeholder="Description du template"
                        >
                    </div>

                    <div>
                        <label for="templateHtmlContent" class="block text-sm font-medium text-gray-700 mb-1">Contenu HTML *</label>
                        <textarea
                            wire:model="templateHtmlContent"
                            id="templateHtmlContent"
                            rows="15"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet font-mono text-sm"
                            placeholder="<table>...</table>"
                        ></textarea>
                        @error('templateHtmlContent')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                wire:click="$toggle('templateActive')"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $templateActive ? 'bg-keymex-violet' : 'bg-gray-200' }}"
                            >
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $templateActive ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <span class="text-sm text-gray-700">Actif</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                wire:click="$toggle('templateDefault')"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $templateDefault ? 'bg-keymex-violet' : 'bg-gray-200' }}"
                            >
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $templateDefault ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <span class="text-sm text-gray-700">Template par defaut</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button
                        wire:click="closeTemplateModal"
                        type="button"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Annuler
                    </button>
                    <button
                        wire:click="saveTemplate"
                        type="button"
                        class="flex-1 px-4 py-2 bg-keymex-violet text-white rounded-lg hover:bg-keymex-violet-dark transition-colors"
                    >
                        {{ $editingTemplateId ? 'Modifier' : 'Creer' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Brand --}}
    @if($showBrandModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div wire:click="closeBrandModal" class="absolute inset-0 bg-gray-900/50"></div>

            {{-- Modal Content --}}
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ $editingBrandId ? 'Modifier la marque' : 'Nouvelle marque' }}
                </h3>

                <div class="space-y-4">
                    <div>
                        <label for="brandName" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                        <input
                            type="text"
                            wire:model="brandName"
                            id="brandName"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                            placeholder="Ex: KEYMEX"
                        >
                        @error('brandName')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="brandDescription" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input
                            type="text"
                            wire:model="brandDescription"
                            id="brandDescription"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                            placeholder="Description de la marque"
                        >
                    </div>

                    {{-- Logo Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        <div class="flex items-start gap-4">
                            @if($brandLogoPreview || $brandLogoPath)
                                <div class="flex-shrink-0">
                                    <img
                                        src="{{ $brandLogoPreview ?? asset('storage/' . $brandLogoPath) }}"
                                        alt="Logo preview"
                                        class="h-16 w-auto rounded border border-gray-200 bg-white p-1"
                                    >
                                </div>
                            @endif
                            <div class="flex-1">
                                <input
                                    type="file"
                                    wire:model="brandLogoUpload"
                                    id="brandLogoUpload"
                                    accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-keymex-violet file:text-white hover:file:bg-keymex-violet-dark file:cursor-pointer"
                                >
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG ou SVG. Max 2MB.</p>
                                @error('brandLogoUpload')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="brandPrimaryColor" class="block text-sm font-medium text-gray-700 mb-1">Couleur principale *</label>
                            <div class="flex items-center gap-2">
                                <input
                                    type="color"
                                    wire:model="brandPrimaryColor"
                                    id="brandPrimaryColor"
                                    class="h-10 w-14 rounded cursor-pointer border border-gray-300"
                                >
                                <input
                                    type="text"
                                    wire:model="brandPrimaryColor"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet font-mono text-sm"
                                    placeholder="#8B5CF6"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="brandSecondaryColor" class="block text-sm font-medium text-gray-700 mb-1">Couleur secondaire</label>
                            <div class="flex items-center gap-2">
                                <input
                                    type="color"
                                    wire:model="brandSecondaryColor"
                                    id="brandSecondaryColor"
                                    class="h-10 w-14 rounded cursor-pointer border border-gray-300"
                                >
                                <input
                                    type="text"
                                    wire:model="brandSecondaryColor"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet font-mono text-sm"
                                    placeholder="#6c757d"
                                >
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="brandWebsite" class="block text-sm font-medium text-gray-700 mb-1">Site web</label>
                        <input
                            type="url"
                            wire:model="brandWebsite"
                            id="brandWebsite"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                            placeholder="https://keymex.fr"
                        >
                        @error('brandWebsite')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="brandEmail" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input
                                type="email"
                                wire:model="brandEmail"
                                id="brandEmail"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                                placeholder="contact@keymex.fr"
                            >
                            @error('brandEmail')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="brandPhone" class="block text-sm font-medium text-gray-700 mb-1">Telephone</label>
                            <input
                                type="text"
                                wire:model="brandPhone"
                                id="brandPhone"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                                placeholder="01 23 45 67 89"
                            >
                        </div>
                    </div>

                    {{-- Adresses --}}
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Adresses</h4>

                        <div class="space-y-4">
                            <div>
                                <label for="brandAddress" class="block text-sm font-medium text-gray-700 mb-1">Bureau principal (adresse)</label>
                                <textarea
                                    wire:model="brandAddress"
                                    id="brandAddress"
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                                    placeholder="123 rue de la Paix, 75001 Paris"
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label for="brandOffice2Name" class="block text-sm font-medium text-gray-700 mb-1">Bureau 2 (nom)</label>
                                    <input
                                        type="text"
                                        wire:model="brandOffice2Name"
                                        id="brandOffice2Name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                                        placeholder="Ex: Agence Sud"
                                    >
                                </div>
                                <div class="col-span-2">
                                    <label for="brandOffice2Address" class="block text-sm font-medium text-gray-700 mb-1">Bureau 2 (adresse)</label>
                                    <input
                                        type="text"
                                        wire:model="brandOffice2Address"
                                        id="brandOffice2Address"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                                        placeholder="456 avenue du Soleil, 13000 Marseille"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reseaux sociaux --}}
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Reseaux sociaux</h4>

                        <div class="space-y-4">
                            <div>
                                <label for="brandLinkedinUrl" class="block text-sm font-medium text-gray-700 mb-1">LinkedIn</label>
                                <input
                                    type="url"
                                    wire:model="brandLinkedinUrl"
                                    id="brandLinkedinUrl"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                                    placeholder="https://linkedin.com/company/keymex"
                                >
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="brandFacebookUrl" class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
                                    <input
                                        type="url"
                                        wire:model="brandFacebookUrl"
                                        id="brandFacebookUrl"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                                        placeholder="https://facebook.com/keymex"
                                    >
                                </div>
                                <div>
                                    <label for="brandInstagramUrl" class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                                    <input
                                        type="url"
                                        wire:model="brandInstagramUrl"
                                        id="brandInstagramUrl"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                                        placeholder="https://instagram.com/keymex"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                wire:click="$toggle('brandActive')"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $brandActive ? 'bg-keymex-violet' : 'bg-gray-200' }}"
                            >
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $brandActive ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <span class="text-sm text-gray-700">Actif</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                wire:click="$toggle('brandDefault')"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $brandDefault ? 'bg-keymex-violet' : 'bg-gray-200' }}"
                            >
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $brandDefault ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <span class="text-sm text-gray-700">Marque par defaut</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button
                        wire:click="closeBrandModal"
                        type="button"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Annuler
                    </button>
                    <button
                        wire:click="saveBrand"
                        type="button"
                        class="flex-1 px-4 py-2 bg-keymex-violet text-white rounded-lg hover:bg-keymex-violet-dark transition-colors"
                    >
                        {{ $editingBrandId ? 'Modifier' : 'Creer' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Preview Modal --}}
    @if($showPreviewModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div wire:click="closePreviewModal" class="absolute inset-0 bg-gray-900/50"></div>

            {{-- Modal Content --}}
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Apercu du template</h3>
                    <button
                        wire:click="closePreviewModal"
                        class="p-2 text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <p class="text-xs text-gray-500 mb-4">Donnees de demonstration :</p>
                    <div class="bg-white p-4 rounded border border-gray-100">
                        {!! $previewHtml !!}
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button
                        wire:click="closePreviewModal"
                        type="button"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                    >
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div wire:click="closeDeleteModal" class="absolute inset-0 bg-gray-900/50"></div>

            {{-- Modal Content --}}
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Confirmer la suppression
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Etes-vous sur de vouloir supprimer cet element ? Cette action est irreversible.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button
                        wire:click="closeDeleteModal"
                        type="button"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Annuler
                    </button>
                    <button
                        wire:click="delete"
                        type="button"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Campaign --}}
    @if($showCampaignModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div wire:click="closeCampaignModal" class="absolute inset-0 bg-gray-900/50"></div>

            {{-- Modal Content --}}
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ $editingCampaignId ? 'Modifier la campagne' : 'Nouvelle campagne' }}
                </h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="campaignName" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                            <input
                                type="text"
                                wire:model="campaignName"
                                id="campaignName"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                                placeholder="Ex: Promo Janvier 2026"
                            >
                            @error('campaignName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="campaignBrandId" class="block text-sm font-medium text-gray-700 mb-1">Marque</label>
                            <select
                                wire:model="campaignBrandId"
                                id="campaignBrandId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                            >
                                <option value="">Toutes les marques (globale)</option>
                                @foreach($brandsForSelect as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Laissez vide pour appliquer a toutes les marques</p>
                        </div>
                    </div>

                    {{-- Banner: Upload ou URL --}}
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Banniere *</label>

                        {{-- Upload de fichier --}}
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Telecharger une image</label>
                            <input
                                type="file"
                                wire:model="campaignBannerUpload"
                                id="campaignBannerUpload"
                                accept="image/*"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-keymex-violet file:text-white hover:file:bg-keymex-violet-dark file:cursor-pointer"
                            >
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG ou GIF. Max 5MB. Largeur recommandee : 750px.</p>
                            @error('campaignBannerUpload')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4 my-3">
                            <div class="flex-1 border-t border-gray-300"></div>
                            <span class="text-xs text-gray-500 font-medium">OU</span>
                            <div class="flex-1 border-t border-gray-300"></div>
                        </div>

                        {{-- URL externe --}}
                        <div>
                            <label for="campaignBannerUrl" class="block text-xs font-medium text-gray-600 mb-1">URL externe</label>
                            <input
                                type="url"
                                wire:model="campaignBannerUrl"
                                id="campaignBannerUrl"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet text-sm"
                                placeholder="https://example.com/banner.jpg"
                            >
                            @error('campaignBannerUrl')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Preview --}}
                        @if($campaignBannerPreview || $campaignBannerUrl)
                            <div class="mt-3 p-2 bg-white rounded-lg border border-gray-200">
                                <p class="text-xs text-gray-500 mb-1">Apercu :</p>
                                <img
                                    src="{{ $campaignBannerPreview ?? $campaignBannerUrl }}"
                                    alt="Apercu"
                                    class="max-h-24 rounded border border-gray-200"
                                >
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="campaignLinkUrl" class="block text-sm font-medium text-gray-700 mb-1">URL de destination (clic)</label>
                        <input
                            type="url"
                            wire:model="campaignLinkUrl"
                            id="campaignLinkUrl"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                            placeholder="https://example.com/promo"
                        >
                        @error('campaignLinkUrl')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="campaignDescription" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input
                            type="text"
                            wire:model="campaignDescription"
                            id="campaignDescription"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                            placeholder="Description de la campagne"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="campaignStartDate" class="block text-sm font-medium text-gray-700 mb-1">Date de debut</label>
                            <input
                                type="date"
                                wire:model="campaignStartDate"
                                id="campaignStartDate"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                            >
                        </div>
                        <div>
                            <label for="campaignEndDate" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                            <input
                                type="date"
                                wire:model="campaignEndDate"
                                id="campaignEndDate"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                            >
                            @error('campaignEndDate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Laissez vide pour une campagne permanente</p>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="campaignBannerWidth" class="block text-sm font-medium text-gray-700 mb-1">Largeur (px)</label>
                            <input
                                type="number"
                                wire:model="campaignBannerWidth"
                                id="campaignBannerWidth"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                                min="100"
                                max="1000"
                            >
                        </div>
                        <div>
                            <label for="campaignPriority" class="block text-sm font-medium text-gray-700 mb-1">Priorite</label>
                            <input
                                type="number"
                                wire:model="campaignPriority"
                                id="campaignPriority"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-violet focus:border-keymex-violet"
                                min="0"
                                max="100"
                            >
                            <p class="mt-1 text-xs text-gray-500">Plus la priorite est haute, plus la campagne est prioritaire</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            wire:click="$toggle('campaignActive')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $campaignActive ? 'bg-keymex-violet' : 'bg-gray-200' }}"
                        >
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $campaignActive ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                        <span class="text-sm text-gray-700">Campagne active</span>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button
                        wire:click="closeCampaignModal"
                        type="button"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Annuler
                    </button>
                    <button
                        wire:click="saveCampaign"
                        type="button"
                        class="flex-1 px-4 py-2 bg-keymex-violet text-white rounded-lg hover:bg-keymex-violet-dark transition-colors"
                    >
                        {{ $editingCampaignId ? 'Modifier' : 'Creer' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- JavaScript for communication features --}}
    @if($activeTab === 'communication')
        <script>
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    // Show success notification
                    showNotification('Lien copie dans le presse-papiers !', 'success');
                }).catch(err => {
                    console.error('Erreur lors de la copie:', err);
                    showNotification('Erreur lors de la copie', 'error');
                });
            }

            function copyEmailTemplate() {
                const emailText = `Bonjour,

Nous avons mis en place un nouvel outil pour générer votre signature email professionnelle KEYMEX.

Pour obtenir votre signature personnalisée :
1. Cliquez sur le lien ci-dessous
2. Connectez-vous avec votre compte Microsoft 365
3. Votre signature sera automatiquement générée
4. Copiez-la et collez-la dans les paramètres de votre messagerie

Lien : {{ rtrim(config('app.url'), '/') }}/ma-signature

Cordialement,
L'équipe Marketing KEYMEX`;

                navigator.clipboard.writeText(emailText).then(() => {
                    showNotification('Texte de l\'email copie !', 'success');
                }).catch(err => {
                    console.error('Erreur lors de la copie:', err);
                    showNotification('Erreur lors de la copie', 'error');
                });
            }

            function showNotification(message, type) {
                // Create notification element
                const notification = document.createElement('div');
                notification.className = `fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2 animate-slide-in-up ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
                notification.innerHTML = `
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success'
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'}
                    </svg>
                    <span>${message}</span>
                `;
                document.body.appendChild(notification);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
        </script>
    @endif
</div>
