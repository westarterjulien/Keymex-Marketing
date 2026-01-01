<?php

namespace App\Livewire\Kpi;

use App\Services\MongoPropertyService;
use Carbon\Carbon;
use Livewire\Component;

class HebdoBizWeekly extends Component
{
    public bool $mongoDbError = false;
    public int $weekOffset = 0; // 0 = semaine courante, -1 = semaine précédente, etc.

    protected MongoPropertyService $propertyService;

    protected $queryString = [
        'weekOffset' => ['except' => 0],
    ];

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

        try {
            // C.A Compromis
            $compromisData['current'] = $this->propertyService->getCompromisStats($selectedWeek['start'], $selectedWeek['end']);
            $compromisData['previous'] = $this->propertyService->getCompromisStats($previousWeek['start'], $previousWeek['end']);
            $compromisData['lastYear'] = $this->propertyService->getCompromisStats($lastYearWeek['start'], $lastYearWeek['end']);

            // Mandats Exclusifs
            $mandatesData['current'] = $this->propertyService->getMandatesExclusStats($selectedWeek['start'], $selectedWeek['end']);
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
            'selectedWeek' => $selectedWeek,
            'previousWeek' => $previousWeek,
            'lastYearWeek' => $lastYearWeek,
            'compromisData' => $compromisData,
            'mandatesData' => $mandatesData,
            'variations' => $variations,
            'isCurrentWeek' => $this->weekOffset === 0,
        ]);
    }
}
