<?php

namespace App\Livewire\Kpi;

use App\Services\MongoPropertyService;
use Carbon\Carbon;
use Livewire\Component;

class HebdoBizMonthly extends Component
{
    public bool $mongoDbError = false;

    protected MongoPropertyService $propertyService;

    public function boot(MongoPropertyService $propertyService): void
    {
        $this->propertyService = $propertyService;
    }

    /**
     * Calcule les dates du mois en cours
     */
    protected function getCurrentMonthDates(): array
    {
        $now = Carbon::now();
        return [
            'start' => $now->copy()->startOfMonth(),
            'end' => $now->copy()->endOfMonth(),
        ];
    }

    /**
     * Calcule les dates du mois précédent
     */
    protected function getPreviousMonthDates(): array
    {
        $now = Carbon::now();
        return [
            'start' => $now->copy()->subMonth()->startOfMonth(),
            'end' => $now->copy()->subMonth()->endOfMonth(),
        ];
    }

    /**
     * Calcule les dates du même mois l'année précédente
     */
    protected function getSameMonthLastYearDates(): array
    {
        $now = Carbon::now();
        return [
            'start' => $now->copy()->subYear()->startOfMonth(),
            'end' => $now->copy()->subYear()->endOfMonth(),
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

        // Périodes
        $currentMonth = $this->getCurrentMonthDates();
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
            $compromisData['current'] = $this->propertyService->getCompromisStats($currentMonth['start'], $currentMonth['end']);
            $compromisData['previous'] = $this->propertyService->getCompromisStats($previousMonth['start'], $previousMonth['end']);
            $compromisData['lastYear'] = $this->propertyService->getCompromisStats($lastYearMonth['start'], $lastYearMonth['end']);

            // Mandats Exclusifs
            $mandatesData['current'] = $this->propertyService->getMandatesExclusStats($currentMonth['start'], $currentMonth['end']);
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
            'currentMonth' => $currentMonth,
            'previousMonth' => $previousMonth,
            'lastYearMonth' => $lastYearMonth,
            'compromisData' => $compromisData,
            'mandatesData' => $mandatesData,
            'variations' => $variations,
        ]);
    }
}
