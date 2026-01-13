<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class SocialMediaMetric extends Model
{
    protected $fillable = [
        'platform',
        'page_id',
        'date',
        'metric_name',
        'metric_period',
        'value',
        'breakdown',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'value' => 'decimal:2',
            'breakdown' => 'array',
        ];
    }

    /**
     * Scope: Facebook metrics only
     */
    public function scopeFacebook(Builder $query): Builder
    {
        return $query->where('platform', 'facebook');
    }

    /**
     * Scope: Instagram metrics only
     */
    public function scopeInstagram(Builder $query): Builder
    {
        return $query->where('platform', 'instagram');
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeForPeriod(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
    }

    /**
     * Scope: Filter by metric name
     */
    public function scopeMetric(Builder $query, string $metricName): Builder
    {
        return $query->where('metric_name', $metricName);
    }

    /**
     * Get aggregated sum for a metric over a period
     */
    public static function sumForPeriod(
        string $platform,
        string $metricName,
        Carbon $start,
        Carbon $end
    ): float {
        return static::where('platform', $platform)
            ->where('metric_name', $metricName)
            ->forPeriod($start, $end)
            ->sum('value');
    }

    /**
     * Get daily values for charts
     */
    public static function dailyValues(
        string $platform,
        string $metricName,
        Carbon $start,
        Carbon $end
    ): array {
        return static::where('platform', $platform)
            ->where('metric_name', $metricName)
            ->forPeriod($start, $end)
            ->orderBy('date')
            ->pluck('value', 'date')
            ->toArray();
    }

    /**
     * Upsert metric (create or update)
     */
    public static function upsertMetric(array $data): static
    {
        return static::updateOrCreate(
            [
                'platform' => $data['platform'],
                'page_id' => $data['page_id'],
                'date' => $data['date'],
                'metric_name' => $data['metric_name'],
                'metric_period' => $data['metric_period'] ?? 'day',
            ],
            [
                'value' => $data['value'],
                'breakdown' => $data['breakdown'] ?? null,
            ]
        );
    }
}
