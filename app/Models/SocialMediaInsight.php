<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaInsight extends Model
{
    protected $fillable = [
        'period_start',
        'period_end',
        'insight_type',
        'content',
        'metrics_used',
        'model_version',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'metrics_used' => 'array',
        ];
    }

    /**
     * User who generated this insight
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Get type label in French
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->insight_type) {
            'performance' => 'Analyse de performance',
            'recommendation' => 'Recommandations',
            'trend' => 'Tendances',
            'comparison' => 'Comparaison',
            default => $this->insight_type,
        };
    }

    /**
     * Get latest performance analysis
     */
    public static function latestPerformance(): ?static
    {
        return static::where('insight_type', 'performance')
            ->latest()
            ->first();
    }

    /**
     * Get latest recommendations
     */
    public static function latestRecommendations(): ?static
    {
        return static::where('insight_type', 'recommendation')
            ->latest()
            ->first();
    }
}
