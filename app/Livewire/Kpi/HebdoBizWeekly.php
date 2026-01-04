<?php

namespace App\Livewire\Kpi;

use App\Services\MongoPropertyService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class HebdoBizWeekly extends Component
{
    public bool $mongoDbError = false;
    public int $weekOffset = 0;

    protected MongoPropertyService $propertyService;

    protected $queryString = [
        'weekOffset' => ['except' => 0],
    ];

    // Cache duration: 5 minutes
    protected int $cacheTtl = 300;

    public function boot(MongoPropertyService $propertyService): void
    {
        $this->propertyService = $propertyService;
    }

    public function previousWeek(): void
    {
        $this->weekOffset--;
    }

    public function nextWeek(): void
    {
        if ($this->weekOffset < 0) {
            $this->weekOffset++;
        }
    }

    public function currentWeek(): void
    {
        $this->weekOffset = 0;
    }

    /**
     * Calcule les dates de la semaine sélectionnée
     */
    protected function getSelectedWeekDates(): array
    {
        $date = Carbon::now()->addWeeks($this->weekOffset);
        return [
            'start' => $date->copy()->startOfWeek(Carbon::MONDAY),
            'end' => $date->copy()->endOfWeek(Carbon::SUNDAY),
        ];
    }

    /**
     * Calcule les dates de la semaine précédente (par rapport à la sélection)
     */
    protected function getPreviousWeekDates(): array
    {
        $date = Carbon::now()->addWeeks($this->weekOffset)->subWeek();
        return [
            'start' => $date->copy()->startOfWeek(Carbon::MONDAY),
            'end' => $date->copy()->endOfWeek(Carbon::SUNDAY),
        ];
    }

    /**
     * Calcule les dates de la même semaine l'année précédente
     */
    protected function getSameWeekLastYearDates(): array
    {
        $date = Carbon::now()->addWeeks($this->weekOffset);
        $weekNumber = $date->weekOfYear;
        $lastYear = $date->copy()->subYear();

        $lastYearWeekStart = $lastYear->copy()->setISODate($lastYear->year, $weekNumber)->startOfWeek(Carbon::MONDAY);

        return [
            'start' => $lastYearWeekStart,
            'end' => $lastYearWeekStart->copy()->endOfWeek(Carbon::SUNDAY),
        ];
    }

    /**
     * Calcule le pourcentage de variation
     */
    protected function calculateVariation($current, $previous): ?float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : null;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Récupère les stats avec cache
     */
    protected function getCachedStats(string $type, Carbon $start, Carbon $end): array
    {
        $cacheKey = "kpi_{$type}_{$start->format('Y-m-d')}_{$end->format('Y-m-d')}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($type, $start, $end) {
            if ($type === 'compromis') {
                return $this->propertyService->getCompromisStats($start, $end);
            }
            return $this->propertyService->getMandatesExclusStats($start, $end);
        });
    }

    /**
     * Récupère le Top 10 CA Compromis par conseiller
     */
    protected function getTopCompromis(Carbon $start, Carbon $end): array
    {
        $cacheKey = "kpi_top_compromis_{$start->format('Y-m-d')}_{$end->format('Y-m-d')}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($start, $end) {
            return $this->propertyService->getTopCompromisParConseiller($start, $end, 10);
        });
    }

    /**
     * Récupère le Top 10 Mandats Exclusifs par conseiller
     */
    protected function getTopMandats(Carbon $start, Carbon $end): array
    {
        $cacheKey = "kpi_top_mandats_{$start->format('Y-m-d')}_{$end->format('Y-m-d')}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($start, $end) {
            return $this->propertyService->getTopMandatsExclusParConseiller($start, $end, 10);
        });
    }

    public function render()
    {
        $this->mongoDbError = false;

        // Périodes
        $selectedWeek = $this->getSelectedWeekDates();
        $previousWeek = $this->getPreviousWeekDates();
        $lastYearWeek = $this->getSameWeekLastYearDates();

        // Initialisation des données
        $compromisData = [
            'current' => ['count' => 0, 'total_price' => 0, 'total_commission' => 0],
            'previous' => ['count' => 0, 'total_price' => 0, 'total_commission' => 0],
            'lastYear' => ['count' => 0, 'total_price' => 0, 'total_commission' => 0],
        ];

        $mandatesData = [
            'current' => ['count' => 0],
            'previous' => ['count' => 0],
            'lastYear' => ['count' => 0],
        ];

        $topCompromis = [];
        $topMandats = [];

        try {
            // C.A Compromis (avec cache)
            $compromisData['current'] = $this->getCachedStats('compromis', $selectedWeek['start'], $selectedWeek['end']);
            $compromisData['previous'] = $this->getCachedStats('compromis', $previousWeek['start'], $previousWeek['end']);
            $compromisData['lastYear'] = $this->getCachedStats('compromis', $lastYearWeek['start'], $lastYearWeek['end']);

            // Mandats Exclusifs (avec cache)
            $mandatesData['current'] = $this->getCachedStats('mandates', $selectedWeek['start'], $selectedWeek['end']);
            $mandatesData['previous'] = $this->getCachedStats('mandates', $previousWeek['start'], $previousWeek['end']);
            $mandatesData['lastYear'] = $this->getCachedStats('mandates', $lastYearWeek['start'], $lastYearWeek['end']);

            // Top 10 par conseiller
            $topCompromis = $this->getTopCompromis($selectedWeek['start'], $selectedWeek['end']);
            $topMandats = $this->getTopMandats($selectedWeek['start'], $selectedWeek['end']);
        } catch (\Exception $e) {
            $this->mongoDbError = true;
            \Log::warning('MongoDB connection error in HebdoBizWeekly: ' . $e->getMessage());
        }

        // Calculer CA HT (TTC / 1.20)
        foreach (['current', 'previous', 'lastYear'] as $period) {
            $compromisData[$period]['total_commission_ht'] = $compromisData[$period]['total_commission'] / 1.20;
        }

        // Calcul des variations (sur le CA HT)
        $variations = [
            'compromis_ca_vs_previous' => $this->calculateVariation($compromisData['current']['total_commission_ht'], $compromisData['previous']['total_commission_ht']),
            'compromis_ca_vs_lastYear' => $this->calculateVariation($compromisData['current']['total_commission_ht'], $compromisData['lastYear']['total_commission_ht']),
            'mandates_vs_previous' => $this->calculateVariation($mandatesData['current']['count'], $mandatesData['previous']['count']),
            'mandates_vs_lastYear' => $this->calculateVariation($mandatesData['current']['count'], $mandatesData['lastYear']['count']),
        ];

        return view('livewire.kpi.hebdo-biz-weekly', [
            'selectedWeek' => $selectedWeek,
            'previousWeek' => $previousWeek,
            'lastYearWeek' => $lastYearWeek,
            'compromisData' => $compromisData,
            'mandatesData' => $mandatesData,
            'variations' => $variations,
            'isCurrentWeek' => $this->weekOffset === 0,
            'topCompromis' => $topCompromis,
            'topMandats' => $topMandats,
        ]);
    }
}
