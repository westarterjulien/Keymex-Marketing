<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SignatureCampaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'brand_id',
        'name',
        'description',
        'banner_url',
        'link_url',
        'alt_text',
        'banner_width',
        'banner_height',
        'start_date',
        'end_date',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'banner_width' => 'integer',
        'banner_height' => 'integer',
        'priority' => 'integer',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Scope pour les campagnes actives et dans leur periode de validite
     */
    public function scopeActive($query)
    {
        $now = Carbon::now()->toDateString();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            });
    }

    /**
     * Recupere la campagne active pour une marque donnee (ou globale)
     */
    public static function getActiveForBrand(?int $brandId): ?self
    {
        return static::active()
            ->where(function ($q) use ($brandId) {
                $q->where('brand_id', $brandId)
                    ->orWhereNull('brand_id');
            })
            ->orderByRaw('brand_id IS NULL ASC')
            ->orderBy('priority', 'desc')
            ->first();
    }

    /**
     * Genere le HTML de la banniere
     */
    public function generateBannerHtml(): string
    {
        if (empty($this->banner_url)) {
            return '';
        }

        $width = $this->banner_width ?? 750;
        $height = $this->banner_height ? "height=\"{$this->banner_height}\"" : '';
        $alt = htmlspecialchars($this->alt_text ?? $this->name);

        $img = "<img src=\"{$this->banner_url}\" alt=\"{$alt}\" width=\"{$width}\" {$height} style=\"display: block; width: {$width}px; max-width: 100%; height: auto;\" />";

        if (!empty($this->link_url)) {
            return "<a href=\"{$this->link_url}\" target=\"_blank\" style=\"text-decoration: none;\">{$img}</a>";
        }

        return $img;
    }
}
