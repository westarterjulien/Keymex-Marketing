<?php

namespace App\Livewire\Kpi;

use App\Services\MongoPropertyService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class HebdoBizYearly extends Component
{
    public bool $mongoDbError = false;
    public int $yearOffset = 0;

    protected MongoPropertyService $propertyService;

    protected $queryString = [
        'yearOffset' => ['except' => 0],
    ];

    // Cache duration: 10 minutes for yearly data
    protected int $cacheTtl = 600;

    public function boot(MongoPropertyService $propertyService): void
    {
        $this->propertyService = $propertyService;
    }

    public function previousYear(): void
    {
        $this->yearOffset--;
    }

    public function nextYear(): void
    {
        if ($this->yearOffset < 0) {
            $this->yearOffset++;
        }
    }

    public function currentYear(): void
    {
        $this->yearOffset = 0;
    }

    /**
     * Calcule les dates de l'annee selectionnee (du 1er janvier a aujourd'hui ou fin d'annee)
     */
    protected function getSelectedYearDates(): array
    {
        $year = Carbon::now()->addYears($this->yearOffset);
        $start = $year->copy()->startOfYear();

        // Si c'est l'annee en cours, on va jusqu'a aujourd'hui
        // Sinon on prend l'annee complete
        if ($this->yearOffset === 0) {
            $end = Carbon::now()->endOfDay();
        } else {
            $end = $year->copy()->endOfYear();
        }

        return [
            'start' => $start,
            'end' => $end,
            'year' => $year->year,
        ];
    }

    /**
     * Calcule les dates de l'annee precedente (meme periode)
     */
    protected function getSamePeriodLastYearDates(): array
    {
        $selectedYear = $this->getSelectedYearDates();
        $lastYearStart = $selectedYear['start']->copy()->subYear();

        // Pour la comparaison, on prend la meme periode de l'annee precedente
        if ($this->yearOffset === 0) {
            // Annee en cours: on compare du 1er janvier au meme jour l'annee derniere
            $lastYearEnd = Carbon::now()->subYear()->endOfDay();
        } else {
            // Annee passee: on compare l'annee complete
            $lastYearEnd = $selectedYear['end']->copy()->subYear();
        }

        return [
            'start' => $lastYearStart,
            'end' => $lastYearEnd,
            'year' => $lastYearStart->year,
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
     * Recupere les stats avec cache
     */
    protected function getCachedStats(string $type, Carbon $start, Carbon $end): array
    {
        $cacheKey = "kpi_yearly_{$type}_{$start->format('Y-m-d')}_{$end->format('Y-m-d')}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($type, $start, $end) {
            if ($type === 'compromis') {
                return $this->propertyService->getCompromisStats($start, $end);
            }
            return $this->propertyService->getMandatesExclusStats($start, $end);
        });
    }

    /**
     * Recupere le Top 10 CA Compromis par conseiller
     */
    protected function getTopCompromis(Carbon $start, Carbon $end): array
    {
        $cacheKey = "kpi_yearly_top_compromis_{$start->format('Y-m-d')}_{$end->format('Y-m-d')}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($start, $end) {
            return $this->propertyService->getTopCompromisParConseiller($start, $end, 10);
        });
    }

    /**
     * Recupere le Top 10 Mandats Exclusifs par conseiller
     */
    protected function getTopMandats(Carbon $start, Carbon $end): array
    {
        $cacheKey = "kpi_yearly_top_mandats_{$start->format('Y-m-d')}_{$end->format('Y-m-d')}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($start, $end) {
            return $this->propertyService->getTopMandatsExclusParConseiller($start, $end, 10);
        });
    }

    /**
     * Recupere les stats mensuelles pour le graphique
     */
    protected function getMonthlyStats(int $year): array
    {
        $cacheKey = "kpi_yearly_monthly_stats_{$year}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($year) {
            $monthlyData = [];
            $currentDate = Carbon::now();

            for ($month = 1; $month <= 12; $month++) {
                $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
                $monthEnd = Carbon::create($year, $month, 1)->endOfMonth();

                // Ne pas calculer les mois futurs
                if ($monthStart->isAfter($currentDate)) {
                    $monthlyData[] = [
                        'month' => $monthStart->translatedFormat('M'),
                        'compromis_count' => 0,
                        'compromis_ca' => 0,
                        'mandates_count' => 0,
                        'is_future' => true,
                    ];
                    continue;
                }

                // Si le mois est en cours, limiter a aujourd'hui
                if ($monthEnd->isAfter($currentDate)) {
                    $monthEnd = $currentDate->copy()->endOfDay();
                }

                try {
                    $compromisStats = $this->propertyService->getCompromisStats($monthStart, $monthEnd);
                    $mandatesStats = $this->propertyService->getMandatesExclusStats($monthStart, $monthEnd);

                    $monthlyData[] = [
                        'month' => $monthStart->translatedFormat('M'),
                        'compromis_count' => $compromisStats['count'] ?? 0,
                        'compromis_ca' => ($compromisStats['total_commission'] ?? 0) / 1.20 / 1000, // CA HT en k€
                        'mandates_count' => $mandatesStats['count'] ?? 0,
                        'is_future' => false,
                    ];
                } catch (\Exception $e) {
                    $monthlyData[] = [
                        'month' => $monthStart->translatedFormat('M'),
                        'compromis_count' => 0,
                        'compromis_ca' => 0,
                        'mandates_count' => 0,
                        'is_future' => false,
                    ];
                }
            }

            return $monthlyData;
        });
    }

    public function render()
    {
        $this->mongoDbError = false;

        // Periodes
        $selectedYear = $this->getSelectedYearDates();
        $lastYear = $this->getSamePeriodLastYearDates();

        // Nombre de jours de la periode
        $daysDiff = $selectedYear['start']->diffInDays($selectedYear['end']) + 1;

        // Initialisation des donnees
        $compromisData = [
            'current' => ['count' => 0, 'total_price' => 0, 'total_commission' => 0],
            'lastYear' => ['count' => 0, 'total_price' => 0, 'total_commission' => 0],
        ];

        $mandatesData = [
            'current' => ['count' => 0],
            'lastYear' => ['count' => 0],
        ];

        $topCompromis = [];
        $topMandats = [];
        $monthlyStats = [];

        try {
            // C.A Compromis (avec cache)
            $compromisData['current'] = $this->getCachedStats('compromis', $selectedYear['start'], $selectedYear['end']);
            $compromisData['lastYear'] = $this->getCachedStats('compromis', $lastYear['start'], $lastYear['end']);

            // Mandats Exclusifs (avec cache)
            $mandatesData['current'] = $this->getCachedStats('mandates', $selectedYear['start'], $selectedYear['end']);
            $mandatesData['lastYear'] = $this->getCachedStats('mandates', $lastYear['start'], $lastYear['end']);

            // Top 10 par conseiller
            $topCompromis = $this->getTopCompromis($selectedYear['start'], $selectedYear['end']);
            $topMandats = $this->getTopMandats($selectedYear['start'], $selectedYear['end']);

            // Stats mensuelles pour le graphique
            $monthlyStats = $this->getMonthlyStats($selectedYear['year']);
        } catch (\Exception $e) {
            $this->mongoDbError = true;
            \Log::warning('MongoDB connection error in HebdoBizYearly: ' . $e->getMessage());
        }

        // Calculer CA HT (TTC / 1.20)
        foreach (['current', 'lastYear'] as $period) {
            $compromisData[$period]['total_commission_ht'] = $compromisData[$period]['total_commission'] / 1.20;
        }

        // Calcul des variations (sur le CA HT)
        $variations = [
            'compromis_ca_vs_lastYear' => $this->calculateVariation($compromisData['current']['total_commission_ht'], $compromisData['lastYear']['total_commission_ht']),
            'compromis_count_vs_lastYear' => $this->calculateVariation($compromisData['current']['count'], $compromisData['lastYear']['count']),
            'mandates_vs_lastYear' => $this->calculateVariation($mandatesData['current']['count'], $mandatesData['lastYear']['count']),
        ];

        // Calculer la moyenne mensuelle
        $monthsElapsed = $selectedYear['start']->diffInMonths($selectedYear['end']) + 1;
        $avgMonthlyCA = $monthsElapsed > 0 ? $compromisData['current']['total_commission_ht'] / $monthsElapsed : 0;
        $avgMonthlyCompromis = $monthsElapsed > 0 ? $compromisData['current']['count'] / $monthsElapsed : 0;
        $avgMonthlyMandates = $monthsElapsed > 0 ? $mandatesData['current']['count'] / $monthsElapsed : 0;

        return view('livewire.kpi.hebdo-biz-yearly', [
            'selectedYear' => $selectedYear,
            'lastYear' => $lastYear,
            'daysDiff' => $daysDiff,
            'monthsElapsed' => $monthsElapsed,
            'compromisData' => $compromisData,
            'mandatesData' => $mandatesData,
            'variations' => $variations,
            'topCompromis' => $topCompromis,
            'topMandats' => $topMandats,
            'monthlyStats' => $monthlyStats,
            'avgMonthlyCA' => $avgMonthlyCA,
            'avgMonthlyCompromis' => $avgMonthlyCompromis,
            'avgMonthlyMandates' => $avgMonthlyMandates,
            'isCurrentYear' => $this->yearOffset === 0,
        ]);
    }
}
