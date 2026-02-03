// Property Story Generator - JavaScript
var currentPropertyId = null;
var currentPhotos = [];
var currentStoryType = 'sous-compromis';

function openPhotoSelector(propertyId, reference, photos, storyType) {
    currentPropertyId = propertyId;
    currentPhotos = photos;
    currentStoryType = storyType;

    document.getElementById('photo-selector-ref').textContent = reference;

    var grid = document.getElementById('photo-selector-grid');
    grid.innerHTML = '';

    var countText = document.getElementById('photo-count-text');
    if (countText) {
        var count = photos ? photos.length : 0;
        countText.textContent = count + ' photo' + (count > 1 ? 's' : '') + ' disponible' + (count > 1 ? 's' : '');
    }

    if (!photos || photos.length === 0) {
        grid.innerHTML = '<div class="col-span-full flex flex-col items-center justify-center py-12 text-gray-400">' +
            '<svg class="h-12 w-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />' +
            '</svg>' +
            '<p class="text-sm font-medium">Aucune photo disponible</p>' +
            '<p class="text-xs mt-1">Ce bien ne possede pas de photos HD</p>' +
            '</div>';
    } else {
        for (var i = 0; i < photos.length; i++) {
            (function(index, photo) {
                var div = document.createElement('div');
                div.className = 'relative aspect-square rounded-xl overflow-hidden cursor-pointer bg-gradient-to-br from-gray-100 to-gray-200 group';
                div.id = 'photo-card-' + index;

                var spinnerHtml = '<div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100" id="spinner-' + index + '">' +
                    '<div class="h-8 w-8 rounded-full border-4 border-purple-200 border-t-pink-500 animate-spin"></div>' +
                    '</div>';

                var errorHtml = '<div class="absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-br from-red-50 to-red-100 text-red-400 hidden" id="error-' + index + '">' +
                    '<svg class="h-8 w-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />' +
                    '</svg>' +
                    '<span class="text-xs">Erreur</span>' +
                    '</div>';

                div.innerHTML = spinnerHtml + errorHtml +
                    '<img src="" alt="Photo ' + (index + 1) + '" class="hidden w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" data-src="' + photo + '" referrerpolicy="no-referrer" />' +
                    '<span class="absolute top-2 left-2 bg-white/90 backdrop-blur-sm rounded-md px-2 py-0.5 text-xs font-semibold text-gray-500">#' + (index + 1) + '</span>' +
                    '<div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-3">' +
                    '<span class="bg-gradient-to-r from-pink-500 to-purple-500 text-white text-xs font-semibold px-4 py-1.5 rounded-full transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">Selectionner</span>' +
                    '</div>';

                div.onclick = function() { selectPhotoAndGenerate(index); };
                grid.appendChild(div);

                var img = div.querySelector('img');
                img.onload = function() {
                    document.getElementById('spinner-' + index).classList.add('hidden');
                    img.classList.remove('hidden');
                };
                img.onerror = function() {
                    document.getElementById('spinner-' + index).classList.add('hidden');
                    document.getElementById('error-' + index).classList.remove('hidden');
                };
                img.src = photo;
            })(i, photos[i]);
        }
    }

    document.getElementById('photo-selector-modal').classList.remove('hidden');
}

function closePhotoSelector() {
    document.getElementById('photo-selector-modal').classList.add('hidden');
}

function selectPhotoAndGenerate(photoIndex) {
    closePhotoSelector();
    generateStory(currentPropertyId, currentStoryType, photoIndex);
}

function generateStory(propertyId, type, photoIndex) {
    document.getElementById('story-modal').classList.remove('hidden');
    document.getElementById('story-loading').classList.remove('hidden');
    document.getElementById('story-result').classList.add('hidden');
    document.getElementById('story-error').classList.add('hidden');

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/stories/generate/' + propertyId + '/' + type, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ photoIndex: photoIndex })
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        document.getElementById('story-loading').classList.add('hidden');

        if (data.success) {
            document.getElementById('story-image').src = data.url;
            document.getElementById('story-download').href = data.url;
            document.getElementById('story-result').classList.remove('hidden');
        } else {
            document.getElementById('story-error-message').textContent = data.error || 'Une erreur est survenue';
            document.getElementById('story-error').classList.remove('hidden');
        }
    })
    .catch(function(error) {
        document.getElementById('story-loading').classList.add('hidden');
        document.getElementById('story-error-message').textContent = 'Erreur de connexion';
        document.getElementById('story-error').classList.remove('hidden');
    });
}

function closeStoryModal() {
    document.getElementById('story-modal').classList.add('hidden');
}
