<?php

namespace App\Livewire\Kpi;

use App\Services\MongoPropertyService;
use Carbon\Carbon;
use Livewire\Component;

class HebdoBizMonthly extends Component
{
    public bool $mongoDbError = false;
    public int $monthOffset = 0; // 0 = mois courant, -1 = mois precedent, etc.

    protected MongoPropertyService $propertyService;

    protected $queryString = [
        'monthOffset' => ['except' => 0],
    ];

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

        try {
            // C.A Compromis
            $compromisData['current'] = $this->propertyService->getCompromisStats($selectedMonth['start'], $selectedMonth['end']);
            $compromisData['previous'] = $this->propertyService->getCompromisStats($previousMonth['start'], $previousMonth['end']);
            $compromisData['lastYear'] = $this->propertyService->getCompromisStats($lastYearMonth['start'], $lastYearMonth['end']);

            // Mandats Exclusifs
            $mandatesData['current'] = $this->propertyService->getMandatesExclusStats($selectedMonth['start'], $selectedMonth['end']);
            $mandatesData['previous'] = $this->propertyService->getMandatesExclusStats($previousMonth['start'], $previousMonth['end']);
            $mandatesData['lastYear'] = $this->propertyService->getMandatesExclusStats($lastYearMonth['start'], $lastYearMonth['end']);
        } catch (\Exception $e) {
            $this->mongoDbError = true;
            \Log::warning('MongoDB connection error in HebdoBizMonthly: ' . $e->getMessage());
        }

        // Calcul des variations
        $variations = [
            'compromis_ca_vs_previous' => $this->calculateVariation($compromisData['current']['total_price'], $compromisData['previous']['total_price']),
            'compromis_ca_vs_lastYear' => $this->calculateVariation($compromisData['current']['total_price'], $compromisData['lastYear']['total_price']),
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
        ]);
    }
}
