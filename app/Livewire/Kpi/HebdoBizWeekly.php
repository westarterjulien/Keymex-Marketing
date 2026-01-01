<?php

namespace App\Livewire\Kpi;

use App\Services\MongoPropertyService;
use Carbon\Carbon;
use Livewire\Component;

class HebdoBizWeekly extends Component
{
    public bool $mongoDbError = false;

    protected MongoPropertyService $propertyService;

    public function boot(MongoPropertyService $propertyService): void
    {
        $this->propertyService = $propertyService;
    }

    /**
     * Calcule les dates de la semaine en cours (lundi à dimanche)
     */
    protected function getCurrentWeekDates(): array
    {
        $now = Carbon::now();
        return [
            'start' => $now->copy()->startOfWeek(Carbon::MONDAY),
            'end' => $now->copy()->endOfWeek(Carbon::SUNDAY),
        ];
    }

    /**
     * Calcule les dates de la semaine précédente
     */
    protected function getPreviousWeekDates(): array
    {
        $now = Carbon::now();
        return [
            'start' => $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY),
            'end' => $now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY),
        ];
    }

    /**
     * Calcule les dates de la même semaine l'année précédente
     */
    protected function getSameWeekLastYearDates(): array
    {
        $now = Carbon::now();
        $weekNumber = $now->weekOfYear;
        $lastYear = $now->copy()->subYear();

        // Trouver la même semaine numérotée dans l'année précédente
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

    public function render()
    {
        $this->mongoDbError = false;

        // Périodes
        $currentWeek = $this->getCurrentWeekDates();
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

        try {
            // C.A Compromis
            $compromisData['current'] = $this->propertyService->getCompromisStats($currentWeek['start'], $currentWeek['end']);
            $compromisData['previous'] = $this->propertyService->getCompromisStats($previousWeek['start'], $previousWeek['end']);
            $compromisData['lastYear'] = $this->propertyService->getCompromisStats($lastYearWeek['start'], $lastYearWeek['end']);

            // Mandats Exclusifs
            $mandatesData['current'] = $this->propertyService->getMandatesExclusStats($currentWeek['start'], $currentWeek['end']);
            $mandatesData['previous'] = $this->propertyService->getMandatesExclusStats($previousWeek['start'], $previousWeek['end']);
            $mandatesData['lastYear'] = $this->propertyService->getMandatesExclusStats($lastYearWeek['start'], $lastYearWeek['end']);
        } catch (\Exception $e) {
            $this->mongoDbError = true;
            \Log::warning('MongoDB connection error in HebdoBizWeekly: ' . $e->getMessage());
        }

        // Calcul des variations
        $variations = [
            'compromis_ca_vs_previous' => $this->calculateVariation($compromisData['current']['total_price'], $compromisData['previous']['total_price']),
            'compromis_ca_vs_lastYear' => $this->calculateVariation($compromisData['current']['total_price'], $compromisData['lastYear']['total_price']),
            'mandates_vs_previous' => $this->calculateVariation($mandatesData['current']['count'], $mandatesData['previous']['count']),
            'mandates_vs_lastYear' => $this->calculateVariation($mandatesData['current']['count'], $mandatesData['lastYear']['count']),
        ];

        return view('livewire.kpi.hebdo-biz-weekly', [
            'currentWeek' => $currentWeek,
            'previousWeek' => $previousWeek,
            'lastYearWeek' => $lastYearWeek,
            'compromisData' => $compromisData,
            'mandatesData' => $mandatesData,
            'variations' => $variations,
        ]);
    }
}
