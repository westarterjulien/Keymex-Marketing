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
</div>
