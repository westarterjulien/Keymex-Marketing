<div class="space-y-8">
    {{-- Header avec gradient --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6 shadow-xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\" fill=\"rgba(255,255,255,0.07)\"%3E%3C/path%3E%3C/svg%3E')] opacity-60"></div>
        <div class="absolute top-0 right-0 -mt-16 -mr-16 h-64 w-64 rounded-full bg-white/5"></div>
        <div class="absolute bottom-0 left-0 -mb-16 -ml-16 h-48 w-48 rounded-full bg-white/5"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            {{-- Titre et description --}}
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">Analytics Social Media</h1>
                    <p class="mt-0.5 text-sm text-white/80">
                        Facebook & Instagram &bull; {{ \Carbon\Carbon::parse($dateFrom)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d M Y') }}
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('social-media.assistant') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-white/10 backdrop-blur-sm px-4 py-2.5 text-sm font-medium text-white hover:bg-white/20 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Assistant IA
                </a>
                <button wire:click="refreshData"
                        class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-gray-50 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Actualiser
                </button>
            </div>
        </div>
    </div>

    {{-- Filtres de periode --}}
    <div class="flex flex-wrap items-center justify-center gap-3">
        <div class="inline-flex items-center gap-2 rounded-xl bg-white p-1.5 shadow-sm ring-1 ring-gray-200">
            <select wire:model.live="period"
                    class="rounded-lg border-0 bg-transparent py-2 pl-3 pr-8 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-indigo-500">
                <option value="today">Aujourd'hui</option>
                <option value="yesterday">Hier</option>
                <option value="week">Cette semaine</option>
                <option value="last_week">Semaine derniere</option>
                <option value="month">Ce mois</option>
                <option value="last_month">Mois dernier</option>
                <option value="quarter">Ce trimestre</option>
                <option value="custom">Personnalise</option>
            </select>

            @if($period === 'custom')
                <input type="date" wire:model.live="dateFrom" wire:change="applyCustomDates"
                       class="rounded-lg border-0 bg-gray-100 py-2 px-3 text-sm text-gray-700 focus:ring-2 focus:ring-indigo-500">
                <span class="text-gray-400">-</span>
                <input type="date" wire:model.live="dateTo" wire:change="applyCustomDates"
                       class="rounded-lg border-0 bg-gray-100 py-2 px-3 text-sm text-gray-700 focus:ring-2 focus:ring-indigo-500">
            @endif
        </div>
    </div>

    {{-- Loading overlay --}}
    <div wire:loading.flex wire:target="refreshData, syncMetrics, generateAnalysis, generateRecommendations"
         class="fixed inset-0 z-50 items-center justify-center bg-black/30 backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-8 shadow-2xl flex flex-col items-center gap-4">
            <div class="w-14 h-14 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
            <span class="text-sm font-medium text-gray-700">Chargement...</span>
        </div>
    </div>

    {{-- Message d'erreur --}}
    @if($errorMessage)
        <div class="rounded-xl bg-red-50 border border-red-200 p-4">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-red-800">Erreur</h3>
                    <p class="mt-1 text-sm text-red-700">{{ $errorMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Configuration requise --}}
    @if(!$isConfigured)
        <div class="rounded-xl bg-amber-50 border border-amber-200 p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100">
                    <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-amber-800">Configuration requise</h3>
                    <p class="mt-1 text-sm text-amber-700">L'API Meta n'est pas configuree. Ajoutez les variables suivantes dans votre fichier .env :</p>
                    <pre class="mt-3 rounded-lg bg-amber-100 p-3 text-xs text-amber-900 overflow-x-auto">META_ACCESS_TOKEN=votre_token
META_FACEBOOK_PAGE_ID=votre_page_id
META_INSTAGRAM_ACCOUNT_ID=votre_ig_account_id</pre>
                </div>
            </div>
        </div>
    @else
        {{-- Infos des comptes --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Facebook Page Info --}}
            @if($facebookPage)
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center gap-4">
                        @if($facebookPage['picture'])
                            <img src="{{ $facebookPage['picture'] }}" alt="" class="h-14 w-14 rounded-xl object-cover">
                        @else
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-100">
                                <svg class="h-7 w-7 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $facebookPage['name'] }}</h3>
                            <div class="mt-1 flex items-center gap-4 text-sm text-gray-500">
                                <span><strong class="text-gray-900">{{ number_format($facebookPage['followers']) }}</strong> followers</span>
                                <span><strong class="text-gray-900">{{ number_format($facebookPage['fans']) }}</strong> fans</span>
                            </div>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50">
                            <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Instagram Account Info --}}
            @if($instagramAccount)
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center gap-4">
                        @if($instagramAccount['picture'])
                            <img src="{{ $instagramAccount['picture'] }}" alt="" class="h-14 w-14 rounded-xl object-cover">
                        @else
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-pink-500">
                                <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ '@' . $instagramAccount['username'] }}</h3>
                            <div class="mt-1 flex items-center gap-4 text-sm text-gray-500">
                                <span><strong class="text-gray-900">{{ number_format($instagramAccount['followers']) }}</strong> followers</span>
                                <span><strong class="text-gray-900">{{ number_format($instagramAccount['media_count']) }}</strong> posts</span>
                            </div>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-purple-50 to-pink-50">
                            <svg class="h-5 w-5 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- KPIs Facebook --}}
        @if(count($facebookKpis) > 0)
            <section>
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50">
                        <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Facebook</h2>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($facebookKpis as $kpi)
                        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-500">{{ $kpi['label'] }}</p>
                                @if($kpi['variation'] !== null)
                                    @if($kpi['variation'] >= 0)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-xs font-semibold text-green-700">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                                            +{{ $kpi['variation'] }}%
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                            {{ $kpi['variation'] }}%
                                        </span>
                                    @endif
                                @endif
                            </div>
                            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($kpi['value'], 0, ',', ' ') }}</p>
                            <p class="mt-1 text-xs text-gray-400">vs {{ number_format($kpi['previous'], 0, ',', ' ') }} periode prec.</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- KPIs Instagram --}}
        @if(count($instagramKpis) > 0)
            <section>
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-50 to-pink-50">
                        <svg class="h-5 w-5 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Instagram</h2>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($instagramKpis as $kpi)
                        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-500">{{ $kpi['label'] }}</p>
                                @if($kpi['variation'] !== null)
                                    @if($kpi['variation'] >= 0)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-xs font-semibold text-green-700">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                                            +{{ $kpi['variation'] }}%
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                            {{ $kpi['variation'] }}%
                                        </span>
                                    @endif
                                @endif
                            </div>
                            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($kpi['value'], 0, ',', ' ') }}</p>
                            <p class="mt-1 text-xs text-gray-400">vs {{ number_format($kpi['previous'], 0, ',', ' ') }} periode prec.</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Posts recents --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Facebook Posts --}}
            @if(count($recentPosts['facebook']) > 0)
                <section>
                    <div class="flex items-center gap-3 mb-5">
                        <h2 class="text-lg font-semibold text-gray-900">Publications Facebook recentes</h2>
                    </div>
                    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 divide-y divide-gray-100">
                        @foreach($recentPosts['facebook'] as $post)
                            <div class="p-4">
                                <p class="text-sm text-gray-700 line-clamp-2">{{ $post['message'] ?: '(Sans texte)' }}</p>
                                <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                                        {{ $post['reactions'] }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                        {{ $post['comments'] }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                                        {{ $post['shares'] }}
                                    </span>
                                    <span class="text-gray-400">{{ $post['created_at']->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Instagram Media --}}
            @if(count($recentPosts['instagram']) > 0)
                <section>
                    <div class="flex items-center gap-3 mb-5">
                        <h2 class="text-lg font-semibold text-gray-900">Publications Instagram recentes</h2>
                    </div>
                    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 divide-y divide-gray-100">
                        @foreach($recentPosts['instagram'] as $media)
                            <div class="p-4">
                                <p class="text-sm text-gray-700 line-clamp-2">{{ $media['caption'] ?: '(Sans legende)' }}</p>
                                <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                                        {{ $media['likes'] }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                        {{ $media['comments'] }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                                        {{ $media['type'] }}
                                    </span>
                                    <span class="text-gray-400">{{ $media['created_at']->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- Section Analyse IA --}}
        @if($aiConfigured)
            <section class="mt-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-50 to-purple-50">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Analyse IA</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="generateAnalysis"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors disabled:opacity-50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Analyser
                        </button>
                        <button wire:click="generateRecommendations"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors disabled:opacity-50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            Recommandations
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Derniere analyse --}}
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Derniere analyse de performance</h3>
                        @if($latestInsight)
                            <div class="prose prose-sm max-w-none text-gray-600">
                                {!! nl2br(e($latestInsight->content)) !!}
                            </div>
                            <p class="mt-4 text-xs text-gray-400">
                                Generee le {{ $latestInsight->created_at->translatedFormat('d M Y a H:i') }}
                            </p>
                        @else
                            <p class="text-sm text-gray-500">Aucune analyse disponible. Cliquez sur "Analyser" pour generer une analyse.</p>
                        @endif
                    </div>

                    {{-- Dernieres recommandations --}}
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Recommandations</h3>
                        @if($latestRecommendations)
                            <div class="prose prose-sm max-w-none text-gray-600">
                                {!! nl2br(e($latestRecommendations->content)) !!}
                            </div>
                            <p class="mt-4 text-xs text-gray-400">
                                Generees le {{ $latestRecommendations->created_at->translatedFormat('d M Y a H:i') }}
                            </p>
                        @else
                            <p class="text-sm text-gray-500">Aucune recommandation disponible. Cliquez sur "Recommandations" pour en generer.</p>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        {{-- Token Status --}}
        @if($tokenInfo['valid'] ?? false)
            <div class="rounded-xl bg-green-50 border border-green-200 p-4">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-800">Token Meta valide</p>
                        @if($tokenInfo['expires_at'] ?? null)
                            <p class="text-xs text-green-700">Expire le {{ $tokenInfo['expires_at']->translatedFormat('d M Y') }}</p>
                        @else
                            <p class="text-xs text-green-700">Token sans expiration</p>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($isConfigured)
            <div class="rounded-xl bg-red-50 border border-red-200 p-4">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-red-800">Token Meta invalide ou expire</p>
                        <p class="text-xs text-red-700">Veuillez regenerer votre token d'acces dans Meta Business Suite.</p>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
