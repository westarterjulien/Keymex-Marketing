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
                                    <p class="text-xs font-medium text-gray-600 mb-2">Reseaux sociaux (optionnel)</p>
                                    <div class="space-y-3">
                                        <div>
                                            <input type="url" wire:model="editLinkedin"
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                                                   placeholder="URL LinkedIn">
                                        </div>
                                        <div>
                                            <input type="url" wire:model="editFacebook"
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                                                   placeholder="URL Facebook">
                                        </div>
                                        <div>
                                            <input type="url" wire:model="editInstagram"
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-keymex-red focus:border-keymex-red"
                                                   placeholder="URL Instagram">
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
