<x-layouts.app>
    <div x-data="{
        showUploadModal: false,
        showDeleteModal: false,
        mediaToDelete: null,
        deleteUrl: null,
        previewImage: null,
        previewName: ''
    }">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bibliothèque de médias</h1>
                <p class="mt-1 text-sm text-gray-500">Gérez vos logos, icônes et images pour les stories</p>
            </div>
            <button @click="showUploadModal = true"
                    class="inline-flex items-center gap-2 rounded-lg bg-keymex-red px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-keymex-red-hover transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Ajouter un média
            </button>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mt-4 rounded-lg bg-green-50 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">Erreur lors de l'upload</p>
                        <ul class="mt-1 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" action="{{ route('stories.media') }}" class="mt-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="sr-only">Rechercher</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="search"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           class="block w-full rounded-lg border-0 py-2.5 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-keymex-red sm:text-sm"
                           placeholder="Rechercher un média...">
                </div>
            </div>
            <div>
                <select name="category"
                        onchange="this.form.submit()"
                        class="block w-full rounded-lg border-0 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-keymex-red sm:text-sm">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="sm:hidden rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200">
                Rechercher
            </button>
        </form>

        {{-- Media grid --}}
        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            @forelse($medias as $media)
                <div class="group relative rounded-xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    {{-- Preview --}}
                    <div class="aspect-square bg-gray-100 relative">
                        @if($media->isImage())
                            <img src="{{ $media->getUrl() }}"
                                 alt="{{ $media->name }}"
                                 class="h-full w-full object-contain p-2"
                                 loading="lazy" />
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        @endif

                        {{-- Category badge --}}
                        <span class="absolute top-2 left-2 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium
                            {{ match($media->category) {
                                'logo' => 'bg-purple-100 text-purple-700',
                                'icon' => 'bg-blue-100 text-blue-700',
                                'background' => 'bg-green-100 text-green-700',
                                'decoration' => 'bg-orange-100 text-orange-700',
                                default => 'bg-gray-100 text-gray-700',
                            } }}">
                            {{ $categories[$media->category] ?? $media->category }}
                        </span>

                        {{-- Actions overlay --}}
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <button type="button"
                                    onclick="copyMediaUrl({{ $media->id }})"
                                    class="rounded-lg bg-white p-2 text-gray-700 hover:bg-gray-100 transition-colors"
                                    title="Copier le lien">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                            </button>
                            <button type="button"
                                    @click="mediaToDelete = {{ $media->id }}; deleteUrl = '{{ route('stories.media.destroy', $media) }}'; showDeleteModal = true"
                                    class="rounded-lg bg-red-500 p-2 text-white hover:bg-red-600 transition-colors"
                                    title="Supprimer">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-3">
                        <p class="text-sm font-medium text-gray-900 truncate" title="{{ $media->name }}">{{ $media->name }}</p>
                        <p class="text-xs text-gray-500">{{ $media->formatted_size }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="rounded-xl bg-gray-50 px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun média</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter un logo ou une icône.</p>
                        <button type="button" @click="showUploadModal = true" class="mt-4 text-sm font-medium text-keymex-red hover:text-keymex-red-hover">
                            Ajouter un média
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $medias->withQueryString()->links() }}
        </div>

        {{-- Upload Modal --}}
        <div x-show="showUploadModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="upload-modal"
             role="dialog"
             aria-modal="true">
            <div class="flex min-h-screen items-center justify-center px-4 py-4 text-center">
                <div x-show="showUploadModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-black/50 transition-opacity"
                     @click="showUploadModal = false"></div>

                <div x-show="showUploadModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative z-10 w-full max-w-lg transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all">
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Ajouter un média</h3>
                        <button type="button" @click="showUploadModal = false; previewImage = null; previewName = ''" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('stories.media.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                        @csrf
                        {{-- File upload --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fichier</label>
                            <div class="flex justify-center rounded-lg border border-dashed border-gray-300 px-6 py-8 hover:border-gray-400 transition-colors">
                                <div class="text-center">
                                    <template x-if="previewImage">
                                        <div class="mb-3">
                                            <img :src="previewImage" class="mx-auto h-24 w-24 object-contain rounded-lg" />
                                        </div>
                                    </template>
                                    <template x-if="!previewImage">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </template>
                                    <p x-show="previewName" x-text="previewName" class="text-sm text-gray-600 mb-2"></p>

                                    <div class="mt-4">
                                        <input type="file"
                                               name="file"
                                               id="file-upload"
                                               required
                                               @change="
                                                   const file = $event.target.files[0];
                                                   if (file) {
                                                       previewName = file.name;
                                                       if (file.type.startsWith('image/')) {
                                                           const reader = new FileReader();
                                                           reader.onload = (e) => previewImage = e.target.result;
                                                           reader.readAsDataURL(file);
                                                       } else {
                                                           previewImage = null;
                                                       }
                                                       // Auto-fill name field
                                                       const nameField = document.getElementById('media-name');
                                                       if (nameField && !nameField.value) {
                                                           nameField.value = file.name.replace(/\.[^/.]+$/, '');
                                                       }
                                                   }
                                               "
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-keymex-red file:text-white hover:file:bg-keymex-red-hover file:cursor-pointer cursor-pointer"
                                               accept=".png,.jpg,.jpeg,.gif,.svg,.webp">
                                    </div>

                                    <p class="mt-2 text-xs text-gray-500">PNG, JPG, GIF, SVG, WebP jusqu'à 10 Mo</p>
                                </div>
                            </div>
                        </div>

                        {{-- Name --}}
                        <div>
                            <label for="media-name" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text"
                                   id="media-name"
                                   name="name"
                                   required
                                   value="{{ old('name') }}"
                                   class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-keymex-red sm:text-sm"
                                   placeholder="Ex: Logo KEYMEX rouge">
                        </div>

                        {{-- Category --}}
                        <div>
                            <label for="media-category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                            <select id="media-category"
                                    name="category"
                                    required
                                    class="block w-full rounded-lg border-0 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-keymex-red sm:text-sm">
                                @foreach($categories as $value => $label)
                                    <option value="{{ $value }}" {{ old('category', 'other') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="media-description" class="block text-sm font-medium text-gray-700 mb-1">Description (optionnel)</label>
                            <textarea id="media-description"
                                      name="description"
                                      rows="2"
                                      class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-keymex-red sm:text-sm"
                                      placeholder="Courte description du média...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button"
                                    @click="showUploadModal = false; previewImage = null; previewName = ''"
                                    class="rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-keymex-red px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-keymex-red-hover transition-colors">
                                Uploader
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Delete Modal --}}
        <div x-show="showDeleteModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="delete-modal"
             role="dialog"
             aria-modal="true">
            <div class="flex min-h-screen items-center justify-center px-4 py-4 text-center">
                <div x-show="showDeleteModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-black/50 transition-opacity"
                     @click="showDeleteModal = false"></div>

                <div x-show="showDeleteModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative z-10 w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all">
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Supprimer ce média ?</h3>
                                <p class="mt-1 text-sm text-gray-500">Cette action est irréversible. Le fichier sera supprimé du serveur.</p>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3">
                            <button type="button" @click="showDeleteModal = false"
                                    class="rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                                Annuler
                            </button>
                            <form :action="deleteUrl" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition-colors">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast notification --}}
    <div id="toast" class="fixed bottom-4 right-4 bg-gray-900 text-white px-4 py-2 rounded-lg shadow-lg z-50 hidden transition-opacity duration-300">
        Lien copié dans le presse-papier !
    </div>

    <script>
        function copyMediaUrl(mediaId) {
            fetch(`/stories/medias/${mediaId}/url`)
                .then(response => response.json())
                .then(data => {
                    navigator.clipboard.writeText(data.url).then(() => {
                        const toast = document.getElementById('toast');
                        toast.classList.remove('hidden');
                        toast.classList.add('opacity-100');
                        setTimeout(() => {
                            toast.classList.remove('opacity-100');
                            toast.classList.add('opacity-0');
                            setTimeout(() => toast.classList.add('hidden'), 300);
                        }, 3000);
                    });
                })
                .catch(error => {
                    console.error('Error copying URL:', error);
                    alert('Erreur lors de la copie du lien');
                });
        }
    </script>
</x-layouts.app>
