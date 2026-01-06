<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SmtpSetting extends Model
{
    protected $fillable = [
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'is_active',
    ];

    protected $casts = [
        'mail_port' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'mail_password',
    ];

    /**
     * Recupere les parametres SMTP actifs (singleton pattern)
     */
    public static function getSettings(): ?self
    {
        return Cache::remember('smtp_settings', 3600, function () {
            return self::where('is_active', true)->first();
        });
    }

    /**
     * Vide le cache des parametres SMTP
     */
    public static function clearCache(): void
    {
        Cache::forget('smtp_settings');
    }

    /**
     * Recupere ou cree les parametres (singleton)
     */
    public static function getInstance(): self
    {
        return self::first() ?? new self();
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
