<?php

namespace App\Models;

use App\Mail\BatValidationMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StandaloneBat extends Model
{
    protected $fillable = [
        'advisor_mongo_id',
        'advisor_name',
        'advisor_email',
        'advisor_agency',
        'support_type_id',
        'format_id',
        'category_id',
        'file_path',
        'file_name',
        'file_mime',
        'storage_disk',
        'title',
        'description',
        'grammage',
        'price',
        'delivery_time',
        'quantity',
        'status',
        'client_comment',
        'validation_token',
        'token_expires_at',
        'token_used_at',
        'sent_at',
        'responded_at',
        'order_id',
        'created_by',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'token_used_at' => 'datetime',
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bat) {
            if (empty($bat->validation_token)) {
                $bat->validation_token = Str::uuid();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function supportType(): BelongsTo
    {
        return $this->belongsTo(SupportType::class);
    }

    public function format(): BelongsTo
    {
        return $this->belongsTo(Format::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(StandaloneBatLog::class)->orderBy('created_at', 'desc');
    }

    public function logEvent(string $event, ?string $comment = null, array $extra = []): StandaloneBatLog
    {
        $actorType = 'system';
        $actorName = null;

        if (auth()->check()) {
            $actorType = 'staff';
            $actorName = auth()->user()->name;
        }

        return $this->logs()->create(array_merge([
            'event' => $event,
            'comment' => $comment,
            'actor_type' => $actorType,
            'actor_name' => $actorName,
        ], $extra));
    }

    public function generateNewToken(int $expirationDays = 30): void
    {
        $this->update([
            'validation_token' => Str::uuid(),
            'token_expires_at' => now()->addDays($expirationDays),
            'token_used_at' => null,
        ]);

        $this->logEvent('token_regenerated');
    }

    public function isTokenValid(): bool
    {
        return $this->token_expires_at
            && $this->token_expires_at->isFuture()
            && is_null($this->token_used_at);
    }

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }

    public function markTokenAsUsed(): void
    {
        $this->update(['token_used_at' => now()]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'token_expires_at' => now()->addDays(30),
        ]);

        // Envoyer l'email de validation au conseiller
        if ($this->advisor_email) {
            Mail::to($this->advisor_email)->send(new BatValidationMail($this));
        }

        $this->logEvent('sent');
    }

    public function validate(?string $comment = null): void
    {
        $this->update([
            'status' => 'validated',
            'client_comment' => $comment,
            'responded_at' => now(),
            'token_used_at' => now(),
        ]);

        $this->logs()->create([
            'event' => 'validated',
            'comment' => $comment,
            'actor_type' => 'client',
            'actor_name' => $this->advisor_name,
        ]);
    }

    public function refuse(?string $comment = null): void
    {
        $this->update([
            'status' => 'refused',
            'client_comment' => $comment,
            'responded_at' => now(),
            'token_used_at' => now(),
        ]);

        $this->logs()->create([
            'event' => 'refused',
            'comment' => $comment,
            'actor_type' => 'client',
            'actor_name' => $this->advisor_name,
        ]);
    }

    public function requestModifications(?string $comment = null): void
    {
        $this->update([
            'status' => 'modifications_requested',
            'client_comment' => $comment,
            'responded_at' => now(),
            'token_used_at' => now(),
        ]);

        $this->logs()->create([
            'event' => 'modifications_requested',
            'comment' => $comment,
            'actor_type' => 'client',
            'actor_name' => $this->advisor_name,
        ]);
    }

    public function canBeConvertedToOrder(): bool
    {
        return $this->status === 'validated' && is_null($this->order_id);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Brouillon',
            'sent' => 'Envoye',
            'validated' => 'Valide',
            'refused' => 'Refuse',
            'modifications_requested' => 'Modifications',
            'converted' => 'Converti',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'sent' => 'yellow',
            'validated' => 'green',
            'refused' => 'red',
            'modifications_requested' => 'orange',
            'converted' => 'violet',
            default => 'gray',
        };
    }

    public function getValidationUrlAttribute(): string
    {
        return route('standalone-bat.validate', $this->validation_token);
    }

    /**
     * Retourne l'URL du fichier BAT (URL signée pour S3 privé)
     */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        $disk = $this->storage_disk ?? 'public';

        if ($disk === 's3') {
            // Pour S3 privé, on utilise une URL signée temporaire (valide 60 minutes)
            return Storage::disk('s3')->temporaryUrl($this->file_path, now()->addMinutes(60));
        }

        // Pour le stockage local
        return asset('storage/' . $this->file_path);
    }

    /**
     * Retourne une URL temporaire signée (utile pour S3 privé)
     */
    public function getSignedFileUrl(int $minutes = 60): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        $disk = $this->storage_disk ?? 'public';

        if ($disk === 's3') {
            return Storage::disk('s3')->temporaryUrl($this->file_path, now()->addMinutes($minutes));
        }

        return $this->file_url;
    }

    /**
     * Vérifie si le fichier existe sur le storage
     */
    public function fileExists(): bool
    {
        if (!$this->file_path) {
            return false;
        }

        $disk = $this->storage_disk ?? 'public';

        return Storage::disk($disk)->exists($this->file_path);
    }

    /**
     * Supprime le fichier du storage
     */
    public function deleteFile(): bool
    {
        if (!$this->file_path) {
            return false;
        }

        $disk = $this->storage_disk ?? 'public';

        return Storage::disk($disk)->delete($this->file_path);
    }
}
