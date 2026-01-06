<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedSignature extends Model
{
    protected $fillable = [
        'advisor_email',
        'advisor_mongo_id',
        'signature_template_id',
        'brand_id',
        'firstname',
        'lastname',
        'job_title',
        'phone',
        'mobile_phone',
        'picture_url',
        'linkedin_url',
        'facebook_url',
        'instagram_url',
        'cached_html',
        'cached_at',
    ];

    protected $casts = [
        'cached_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(SignatureTemplate::class, 'signature_template_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get saved signature by email
     */
    public static function findByEmail(string $email): ?self
    {
        return static::where('advisor_email', $email)->first();
    }

    /**
     * Merge MongoDB advisor data with saved customizations
     * Saved data takes precedence over MongoDB data
     */
    public function mergeWithMongoData(array $mongoAdvisor): array
    {
        return [
            'firstname' => $this->firstname ?? ($mongoAdvisor['firstname'] ?? ''),
            'lastname' => $this->lastname ?? ($mongoAdvisor['lastname'] ?? ''),
            'email' => $this->advisor_email,
            'job_title' => $this->job_title ?? ($mongoAdvisor['job_title'] ?? 'Conseiller Immobilier'),
            'phone' => $this->phone ?? ($mongoAdvisor['phone'] ?? ''),
            'mobile_phone' => $this->mobile_phone ?? ($mongoAdvisor['mobile_phone'] ?? ''),
            'picture' => $this->picture_url ?? ($mongoAdvisor['picture'] ?? ''),
            'linkedin_url' => $this->linkedin_url ?? ($mongoAdvisor['linkedin_url'] ?? ''),
            'facebook_url' => $this->facebook_url ?? ($mongoAdvisor['facebook_url'] ?? ''),
            'instagram_url' => $this->instagram_url ?? ($mongoAdvisor['instagram_url'] ?? ''),
        ];
    }

    /**
     * Check if cached HTML is still valid (less than 24h old)
     */
    public function isCacheValid(): bool
    {
        if (empty($this->cached_html) || empty($this->cached_at)) {
            return false;
        }

        return $this->cached_at->diffInHours(now()) < 24;
    }

    /**
     * Update cached HTML
     */
    public function updateCache(string $html): void
    {
        $this->update([
            'cached_html' => $html,
            'cached_at' => now(),
        ]);
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        $this->update([
            'cached_html' => null,
            'cached_at' => null,
        ]);
    }
}
