<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SocialMediaSetting extends Model
{
    protected $fillable = [
        'meta_access_token',
        'meta_facebook_page_id',
        'meta_instagram_account_id',
        'meta_api_version',
        'openai_api_key',
        'openai_model',
        'is_active',
        'meta_token_expires_at',
        'last_sync_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta_token_expires_at' => 'datetime',
        'last_sync_at' => 'datetime',
    ];

    protected $hidden = [
        'meta_access_token',
        'openai_api_key',
    ];

    /**
     * Recupere les parametres actifs (singleton pattern avec cache)
     */
    public static function getSettings(): ?self
    {
        return Cache::remember('social_media_settings', 3600, function () {
            return self::where('is_active', true)->first();
        });
    }

    /**
     * Vide le cache des parametres
     */
    public static function clearCache(): void
    {
        Cache::forget('social_media_settings');
    }

    /**
     * Recupere ou cree les parametres (singleton)
     */
    public static function getInstance(): self
    {
        return self::first() ?? new self();
    }

    /**
     * Verifie si Meta API est configure
     */
    public function isMetaConfigured(): bool
    {
        return !empty($this->meta_access_token)
            && !empty($this->meta_facebook_page_id);
    }

    /**
     * Verifie si OpenAI est configure
     */
    public function isOpenAiConfigured(): bool
    {
        return !empty($this->openai_api_key);
    }

    /**
     * Verifie si le token Meta est expire
     */
    public function isMetaTokenExpired(): bool
    {
        if (!$this->meta_token_expires_at) {
            return false;
        }
        return $this->meta_token_expires_at->isPast();
    }

    /**
     * Boot method pour vider le cache apres modification
     */
    protected static function booted(): void
    {
        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}
