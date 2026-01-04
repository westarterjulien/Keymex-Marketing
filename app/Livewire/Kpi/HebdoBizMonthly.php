<?php

namespace App\Livewire\Kpi;

use App\Services\MongoPropertyService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class HebdoBizMonthly extends Component
{
    public bool $mongoDbError = false;
    public int $monthOffset = 0;

    protected MongoPropertyService $propertyService;

    protected $queryString = [
        'monthOffset' => ['except' => 0],
    ];

    // Cache duration: 5 minutes
    protected int $cacheTtl = 300;

    public function boot(MongoPropertyService $propertyService): void
    {
        $this->propertyService = $propertyService;
    }

    public function previousMonth(): void
    {
        $this->monthOffset--;
    }

    public function nextMonth(): void
    {
        if ($this->monthOffset < 0) {
            $this->monthOffset++;
        }
    }

    public function currentMonth(): void
    {
        $this->monthOffset = 0;
    }

    /**
     * Calcule les dates du mois selectionne
     */
    protected function getSelectedMonthDates(): array
    {
        $date = Carbon::now()->addMonths($this->monthOffset);
        return [
            'start' => $date->copy()->startOfMonth(),
            'end' => $date->copy()->endOfMonth(),
        ];
    }

    /**
     * Calcule les dates du mois precedent (par rapport a la selection)
     */
    protected function getPreviousMonthDates(): array
    {
        $date = Carbon::now()->addMonths($this->monthOffset)->subMonth();
        return [
            'start' => $date->copy()->startOfMonth(),
            'end' => $date->copy()->endOfMonth(),
        ];
    }

    /**
     * Calcule les dates du meme mois l'annee precedente
     */
    protected function getSameMonthLastYearDates(): array
    {
        $date = Carbon::now()->addMonths($this->monthOffset)->subYear();
        return [
            'start' => $date->copy()->startOfMonth(),
            'end' => $date->copy()->endOfMonth(),
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
        $cacheKey = "kpi_monthly_{$type}_{$start->format('Y-m-d')}_{$end->format('Y-m-d')}";

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
        $cacheKey = "kpi_monthly_top_compromis_{$start->format('Y-m-d')}_{$end->format('Y-m-d')}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($start, $end) {
            return $this->propertyService->getTopCompromisParConseiller($start, $end, 10);
        });
    }

    /**
     * Récupère le Top 10 Mandats Exclusifs par conseiller
     */
    protected function getTopMandats(Carbon $start, Carbon $end): array
    {
        $cacheKey = "kpi_monthly_top_mandats_{$start->format('Y-m-d')}_{$end->format('Y-m-d')}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($start, $end) {
            return $this->propertyService->getTopMandatsExclusParConseiller($start, $end, 10);
        });
    }

    public function render()
    {
        $this->mongoDbError = false;

        // Periodes
        $selectedMonth = $this->getSelectedMonthDates();
        $previousMonth = $this->getPreviousMonthDates();
        $lastYearMonth = $this->getSameMonthLastYearDates();

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
            $compromisData['current'] = $this->getCachedStats('compromis', $selectedMonth['start'], $selectedMonth['end']);
            $compromisData['previous'] = $this->getCachedStats('compromis', $previousMonth['start'], $previousMonth['end']);
            $compromisData['lastYear'] = $this->getCachedStats('compromis', $lastYearMonth['start'], $lastYearMonth['end']);

            // Mandats Exclusifs (avec cache)
            $mandatesData['current'] = $this->getCachedStats('mandates', $selectedMonth['start'], $selectedMonth['end']);
            $mandatesData['previous'] = $this->getCachedStats('mandates', $previousMonth['start'], $previousMonth['end']);
            $mandatesData['lastYear'] = $this->getCachedStats('mandates', $lastYearMonth['start'], $lastYearMonth['end']);

            // Top 10 par conseiller
            $topCompromis = $this->getTopCompromis($selectedMonth['start'], $selectedMonth['end']);
            $topMandats = $this->getTopMandats($selectedMonth['start'], $selectedMonth['end']);
        } catch (\Exception $e) {
            $this->mongoDbError = true;
            \Log::warning('MongoDB connection error in HebdoBizMonthly: ' . $e->getMessage());
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

        return view('livewire.kpi.hebdo-biz-monthly', [
            'selectedMonth' => $selectedMonth,
            'previousMonth' => $previousMonth,
            'lastYearMonth' => $lastYearMonth,
            'compromisData' => $compromisData,
            'mandatesData' => $mandatesData,
            'variations' => $variations,
            'isCurrentMonth' => $this->monthOffset === 0,
            'topCompromis' => $topCompromis,
            'topMandats' => $topMandats,
        ]);
    }
}
