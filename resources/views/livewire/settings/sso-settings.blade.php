<div class="p-6 lg:p-8">
    <div class="max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-12 h-12 bg-keymex-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-keymex-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Configuration SSO</h1>
                    <p class="text-gray-600">
                        Gerez les groupes SSO autorises a acceder a cette application.
                    </p>
                </div>
            </div>
        </div>

        {{-- Messages --}}
        @if($syncMessage)
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3">
                <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-green-700 font-medium">{{ $syncMessage }}</span>
            </div>
        @endif
        @if($syncError)
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-center gap-3">
                <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-red-700 font-medium">{{ $syncError }}</span>
            </div>
        @endif

        {{-- Actions --}}
        <div class="mb-8 flex flex-wrap items-center gap-4">
            <button
                wire:click="syncGroups"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-3 px-6 py-3 bg-keymex-600 hover:bg-keymex-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50"
            >
                <svg wire:loading.remove wire:target="syncGroups" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <svg wire:loading wire:target="syncGroups" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="syncGroups">Synchroniser les groupes</span>
                <span wire:loading wire:target="syncGroups">Synchronisation...</span>
            </button>
            <p class="text-gray-500">
                Recupere la liste des groupes depuis le portail KEYMEX
            </p>
        </div>

        {{-- Table des groupes --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            @if(empty($groups))
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun groupe SSO configure</h3>
                    <p class="text-gray-500 max-w-md mx-auto">
                        Cliquez sur "Synchroniser les groupes" pour recuperer les groupes depuis le portail KEYMEX.
                    </p>
                </div>
            @else
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                Groupe SSO
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                Acces autorise
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($groups as $group)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-keymex-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6 text-keymex-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-base font-semibold text-gray-900">
                                                {{ $group['name'] }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ID: {{ $group['sso_group_id'] }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-gray-600">
                                        {{ $group['description'] ?: '-' }}
                                    </p>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <button
                                        wire:click="toggleAllowed({{ $group['id'] }})"
                                        class="relative inline-flex h-7 w-14 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-keymex-500 focus:ring-offset-2 {{ $group['is_allowed'] ? 'bg-green-500' : 'bg-gray-300' }}"
                                        role="switch"
                                        aria-checked="{{ $group['is_allowed'] ? 'true' : 'false' }}"
                                    >
                                        <span class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-lg ring-0 transition duration-300 ease-in-out {{ $group['is_allowed'] ? 'translate-x-7' : 'translate-x-0' }}"></span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Info --}}
        <div class="mt-8 p-5 bg-blue-50 rounded-xl border border-blue-100">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-1">Information</h4>
                    <p class="text-blue-700">
                        Seuls les utilisateurs appartenant a au moins un groupe autorise pourront se connecter a cette application.
                        Les autres utilisateurs verront un message d'erreur lors de la connexion SSO.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
