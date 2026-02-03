<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StoryMedia extends Model
{
    protected $table = 'story_media';

    protected $fillable = [
        'name',
        'filename',
        'path',
        'disk',
        'mime_type',
        'size',
        'category',
        'description',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Get the user who uploaded this media.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get a signed URL for the media (for private S3 buckets).
     */
    public function getSignedUrl(int $expirationMinutes = 60): string
    {
        return Storage::disk($this->disk)->temporaryUrl(
            $this->path,
            now()->addMinutes($expirationMinutes)
        );
    }

    /**
     * Get the public URL (for public buckets or local storage).
     */
    public function getPublicUrl(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Get the URL (signed for S3, public for local).
     */
    public function getUrl(): string
    {
        if ($this->disk === 's3') {
            return $this->getSignedUrl();
        }

        return $this->getPublicUrl();
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }

    /**
     * Check if media is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Delete the file from storage when the model is deleted.
     */
    protected static function booted(): void
    {
        static::deleting(function (StoryMedia $media) {
            Storage::disk($media->disk)->delete($media->path);
        });
    }
}
