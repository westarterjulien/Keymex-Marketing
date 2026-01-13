<?php

namespace App\Livewire\SocialMedia;

use App\Services\MetaGraphApiService;
use App\Services\OpenAiAnalyticsService;
use App\Models\SocialMediaInsight;
use App\Models\SocialMediaMetric;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Analytics Social Media')]
class Dashboard extends Component
{
    public string $period = 'week';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public bool $apiError = false;
    public ?string $errorMessage = null;
    public bool $isLoading = false;
    public bool $isGeneratingAnalysis = false;

    protected MetaGraphApiService $metaService;
    protected OpenAiAnalyticsService $aiService;

    protected $queryString = [
        'period' => ['except' => 'week'],
        'dateFrom' => ['except' => null],
        'dateTo' => ['except' => null],
    ];

    public function boot(MetaGraphApiService $metaService, OpenAiAnalyticsService $aiService): void
    {
        $this->metaService = $metaService;
        $this->aiService = $aiService;
    }

    public function mount(): void
    {
        $this->applyPeriod();
    }

    public function updatedPeriod(): void
    {
        if ($this->period !== 'custom') {
            $this->applyPeriod();
        }
    }

    public function applyCustomDates(): void
    {
        $this->period = 'custom';
    }

    public function applyPeriod(): void
    {
        $now = Carbon::now();

        [$start, $end] = match ($this->period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'last_week' => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'quarter' => [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()],
            default => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
        };

        $this->dateFrom = $start->format('Y-m-d');
        $this->dateTo = $end->format('Y-m-d');
    }

    public function refreshData(): void
    {
        $this->metaService->clearCache();
        $this->dispatch('$refresh');
        $this->dispatch('notify', message: 'Donnees rafraichies');
    }

    public function syncMetrics(): void
    {
        $this->isLoading = true;

        try {
            [$start, $end] = $this->getDateRange();
            $count = $this->metaService->syncMetrics($start, $end);
            $this->dispatch('notify', message: "{$count} metriques synchronisees");
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->isLoading = false;
    }

    public function generateAnalysis(): void
    {
        $this->isGeneratingAnalysis = true;
        $this->errorMessage = null;

        try {
            [$start, $end] = $this->getDateRange();
            $this->aiService->analyzePerformance($start, $end, auth()->id());
            $this->dispatch('notify', message: 'Analyse generee avec succes');
        } catch (\Exception $e) {
            $this->errorMessage = "Erreur: " . $e->getMessage();
        }

        $this->isGeneratingAnalysis = false;
    }

    public function generateRecommendations(): void
    {
        $this->isGeneratingAnalysis = true;
        $this->errorMessage = null;

        try {
            [$start, $end] = $this->getDateRange();
            $this->aiService->generateRecommendations($start, $end, auth()->id());
            $this->dispatch('notify', message: 'Recommandations generees');
        } catch (\Exception $e) {
            $this->errorMessage = "Erreur: " . $e->getMessage();
        }

        $this->isGeneratingAnalysis = false;
    }

    protected function getDateRange(): array
    {
        return [
            Carbon::parse($this->dateFrom)->startOfDay(),
            Carbon::parse($this->dateTo)->endOfDay(),
        ];
    }

    protected function calculateVariation($current, $previous): ?float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : null;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    public function render()
    {
        $this->apiError = false;
        [$startDate, $endDate] = $this->getDateRange();

        // Previous period for comparison
        $daysDiff = $startDate->diffInDays($endDate);
        $previousStart = $startDate->copy()->subDays($daysDiff + 1);
        $previousEnd = $startDate->copy()->subDay();

        // Initialize data
        $facebookKpis = [];
        $instagramKpis = [];
        $recentPosts = ['facebook' => [], 'instagram' => []];
        $latestInsight = null;
        $latestRecommendations = null;
        $tokenInfo = ['valid' => false];
        $facebookPage = null;
        $instagramAccount = null;

        // Check if API is configured
        $isConfigured = $this->metaService->isConfigured();

        if ($isConfigured) {
            try {
                // Get page/account info
                $facebookPage = $this->metaService->getFacebookPageInfo();
                $instagramAccount = $this->metaService->getInstagramAccountInfo();

                // Get Facebook insights
                $fbCurrent = $this->metaService->getFacebookPageInsights($startDate, $endDate);
                $fbPrevious = $this->metaService->getFacebookPageInsights($previousStart, $previousEnd);

                // Get Instagram insights
                $igCurrent = $this->metaService->getInstagramInsights($startDate, $endDate);
                $igPrevious = $this->metaService->getInstagramInsights($previousStart, $previousEnd);

                // Calculate Facebook KPIs
                $facebookKpis = $this->calculatePlatformKpis($fbCurrent, $fbPrevious, [
                    'page_views_total' => 'Vues',
                    'page_post_engagements' => 'Engagements',
                    'page_fan_adds' => 'Nouveaux fans',
                    'page_follows' => 'Followers',
                ]);

                // Calculate Instagram KPIs
                $instagramKpis = $this->calculatePlatformKpis($igCurrent, $igPrevious, [
                    'reach' => 'Portee',
                    'impressions' => 'Impressions',
                    'accounts_engaged' => 'Comptes engages',
                    'total_interactions' => 'Interactions',
                ]);

                // Get recent posts
                $recentPosts['facebook'] = $this->metaService->getFacebookPosts(5);
                $recentPosts['instagram'] = $this->metaService->getInstagramMedia(5);

                // Token validation
                $tokenInfo = $this->metaService->validateToken();

            } catch (\Exception $e) {
                $this->apiError = true;
                $this->errorMessage = $e->getMessage();
            }
        }

        // Get latest AI insights
        $latestInsight = SocialMediaInsight::latestPerformance();
        $latestRecommendations = SocialMediaInsight::latestRecommendations();

        // Check if OpenAI is configured
        $aiConfigured = $this->aiService->isConfigured();

        return view('livewire.social-media.dashboard', [
            'isConfigured' => $isConfigured,
            'aiConfigured' => $aiConfigured,
            'facebookPage' => $facebookPage,
            'instagramAccount' => $instagramAccount,
            'facebookKpis' => $facebookKpis,
            'instagramKpis' => $instagramKpis,
            'recentPosts' => $recentPosts,
            'latestInsight' => $latestInsight,
            'latestRecommendations' => $latestRecommendations,
            'tokenInfo' => $tokenInfo,
        ]);
    }

    protected function calculatePlatformKpis(array $current, array $previous, array $metricLabels): array
    {
        $kpis = [];

        $sumMetric = function (array $data, string $metricName) {
            return collect($data)->where('metric_name', $metricName)->sum('value');
        };

        foreach ($metricLabels as $metricName => $label) {
            $currentValue = $sumMetric($current, $metricName);
            $previousValue = $sumMetric($previous, $metricName);
            $variation = $this->calculateVariation($currentValue, $previousValue);

            $kpis[] = [
                'name' => $metricName,
                'label' => $label,
                'value' => $currentValue,
                'previous' => $previousValue,
                'variation' => $variation,
            ];
        }

        return $kpis;
    }
}
