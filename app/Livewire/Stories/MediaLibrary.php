<?php

namespace App\Livewire\Stories;

use App\Models\StoryMedia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MediaLibrary extends Component
{
    use WithFileUploads, WithPagination;

    public $file;
    public string $name = '';
    public string $category = 'other';
    public string $description = '';

    public string $filterCategory = '';
    public string $search = '';

    public bool $showUploadModal = false;
    public bool $showDeleteModal = false;
    public ?int $mediaToDelete = null;

    public ?string $copiedUrl = null;

    protected $rules = [
        'file' => 'required|file|mimes:png,jpg,jpeg,gif,svg,webp|max:10240',
        'name' => 'required|string|max:255',
        'category' => 'required|in:logo,icon,background,decoration,other',
        'description' => 'nullable|string|max:1000',
    ];

    public function updatedFile()
    {
        $this->validateOnly('file');

        // Auto-fill name from filename if empty
        if (empty($this->name) && $this->file) {
            $this->name = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        }
    }

    public function openUploadModal()
    {
        $this->reset(['file', 'name', 'category', 'description']);
        $this->category = 'other';
        $this->showUploadModal = true;
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->reset(['file', 'name', 'category', 'description']);
    }

    public function upload()
    {
        $this->validate();

        $filename = Str::random(20) . '.' . $this->file->getClientOriginalExtension();
        $path = 'story-media/' . $filename;

        // Upload to S3
        Storage::disk('s3')->put($path, file_get_contents($this->file->getRealPath()));

        StoryMedia::create([
            'name' => $this->name,
            'filename' => $this->file->getClientOriginalName(),
            'path' => $path,
            'disk' => 's3',
            'mime_type' => $this->file->getMimeType(),
            'size' => $this->file->getSize(),
            'category' => $this->category,
            'description' => $this->description ?: null,
            'uploaded_by' => auth()->id(),
        ]);

        $this->closeUploadModal();
        session()->flash('success', 'Media uploade avec succes.');
    }

    public function confirmDelete(int $id)
    {
        $this->mediaToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->mediaToDelete = null;
        $this->showDeleteModal = false;
    }

    public function delete()
    {
        if ($this->mediaToDelete) {
            $media = StoryMedia::find($this->mediaToDelete);
            if ($media) {
                $media->delete();
                session()->flash('success', 'Media supprime.');
            }
        }
        $this->cancelDelete();
    }

    public function copyUrl(int $id)
    {
        $media = StoryMedia::find($id);
        if ($media) {
            $this->copiedUrl = $media->getSignedUrl(1440); // 24h validity
            $this->dispatch('copy-to-clipboard', url: $this->copiedUrl);
        }
    }

    public function render()
    {
        $query = StoryMedia::query()->latest();

        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('filename', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.stories.media-library', [
            'medias' => $query->paginate(20),
            'categories' => [
                'logo' => 'Logos',
                'icon' => 'Icones',
                'background' => 'Fonds',
                'decoration' => 'Decorations',
                'other' => 'Autres',
            ],
        ]);
    }
}
