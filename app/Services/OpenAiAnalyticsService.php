<?php

namespace App\Services;

use App\Models\SocialMediaMetric;
use App\Models\SocialMediaInsight;
use App\Models\SocialMediaChat;
use App\Models\SocialMediaSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiAnalyticsService
{
    protected const API_URL = 'https://api.openai.com/v1/chat/completions';

    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->loadCredentials();
    }

    /**
     * Load credentials from database or fallback to config
     */
    protected function loadCredentials(): void
    {
        $settings = SocialMediaSetting::getSettings();

        if ($settings && $settings->is_active) {
            // Load from database
            $this->apiKey = $settings->openai_api_key;
            $this->model = $settings->openai_model ?? 'gpt-4';
        } else {
            // Fallback to config
            $this->apiKey = config('services.openai.api_key');
            $this->model = config('services.openai.model', 'gpt-4');
        }
    }

    /**
     * Check if the service is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Generate performance analysis for a period
     */
    public function analyzePerformance(Carbon $periodStart, Carbon $periodEnd, ?int $userId = null): SocialMediaInsight
    {
        $metrics = $this->getMetricsForPeriod($periodStart, $periodEnd);

        // Get previous period for comparison
        $daysDiff = $periodStart->diffInDays($periodEnd);
        $previousStart = $periodStart->copy()->subDays($daysDiff + 1);
        $previousEnd = $periodStart->copy()->subDay();
        $previousMetrics = $this->getMetricsForPeriod($previousStart, $previousEnd);

        $prompt = $this->buildAnalysisPrompt($metrics, $previousMetrics, $periodStart, $periodEnd);

        $systemPrompt = "Tu es un expert en marketing digital et reseaux sociaux pour KEYMEX, une agence immobiliere. " .
            "Analyse les performances de maniere professionnelle et actionnable. " .
            "Reponds toujours en francais.";

        $response = $this->callOpenAI($prompt, $systemPrompt);

        return SocialMediaInsight::create([
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'insight_type' => 'performance',
            'content' => $response['content'],
            'metrics_used' => $metrics->pluck('metric_name')->unique()->values()->toArray(),
            'model_version' => $this->model,
            'generated_by' => $userId,
        ]);
    }

    /**
     * Generate recommendations
     */
    public function generateRecommendations(Carbon $periodStart, Carbon $periodEnd, ?int $userId = null): SocialMediaInsight
    {
        $metrics = $this->getMetricsForPeriod($periodStart, $periodEnd);

        $prompt = $this->buildRecommendationsPrompt($metrics);

        $systemPrompt = "Tu es un consultant en strategie social media specialise dans le secteur immobilier. " .
            "Fournis des recommandations concretes et applicables. " .
            "Reponds toujours en francais.";

        $response = $this->callOpenAI($prompt, $systemPrompt);

        return SocialMediaInsight::create([
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'insight_type' => 'recommendation',
            'content' => $response['content'],
            'metrics_used' => $metrics->pluck('metric_name')->unique()->values()->toArray(),
            'model_version' => $this->model,
            'generated_by' => $userId,
        ]);
    }

    /**
     * Chat with AI about the stats
     */
    public function chat(int $userId, string $question): array
    {
        // Get recent metrics for context
        $recentMetrics = $this->getMetricsForPeriod(
            Carbon::now()->subDays(30),
            Carbon::now()
        );

        // Build context
        $context = $this->buildChatContext($recentMetrics);

        $systemPrompt = "Tu es un assistant analytique pour les reseaux sociaux de KEYMEX, une agence immobiliere. " .
            "Voici les statistiques des 30 derniers jours:\n\n" . $context . "\n\n" .
            "Reponds de maniere concise et actionnable en francais. Utilise les donnees fournies pour etayer tes reponses.";

        $response = $this->callOpenAI($question, $systemPrompt);

        // Save user message
        SocialMediaChat::create([
            'user_id' => $userId,
            'role' => 'user',
            'message' => $question,
            'context' => ['metrics_count' => $recentMetrics->count()],
        ]);

        // Save assistant response
        SocialMediaChat::create([
            'user_id' => $userId,
            'role' => 'assistant',
            'message' => $response['content'],
            'tokens_used' => $response['tokens_used'] ?? null,
        ]);

        return $response;
    }

    /**
     * Call OpenAI API
     */
    protected function callOpenAI(string $userMessage, string $systemPrompt): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('OpenAI API key is not configured');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post(self::API_URL, [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'max_tokens' => 1500,
            'temperature' => 0.7,
        ]);

        if (!$response->successful()) {
            Log::error('OpenAI API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Erreur API OpenAI: ' . $response->status());
        }

        $data = $response->json();

        return [
            'content' => $data['choices'][0]['message']['content'] ?? '',
            'tokens_used' => $data['usage']['total_tokens'] ?? null,
            'model' => $data['model'] ?? $this->model,
        ];
    }

    /**
     * Get metrics for a period
     */
    protected function getMetricsForPeriod(Carbon $start, Carbon $end)
    {
        return SocialMediaMetric::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->get();
    }

    /**
     * Build analysis prompt
     */
    protected function buildAnalysisPrompt($currentMetrics, $previousMetrics, Carbon $start, Carbon $end): string
    {
        $current = $this->summarizeMetrics($currentMetrics);
        $previous = $this->summarizeMetrics($previousMetrics);

        return "Analyse les performances social media de KEYMEX du {$start->format('d/m/Y')} au {$end->format('d/m/Y')}.\n\n" .
            "METRIQUES PERIODE ACTUELLE:\n" . json_encode($current, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n" .
            "METRIQUES PERIODE PRECEDENTE:\n" . json_encode($previous, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n" .
            "Fournis:\n" .
            "1. **Resume des performances** (2-3 phrases)\n" .
            "2. **Points forts** identifies\n" .
            "3. **Points d'attention**\n" .
            "4. **Evolution** vs periode precedente (en pourcentage pour chaque metrique cle)";
    }

    /**
     * Build recommendations prompt
     */
    protected function buildRecommendationsPrompt($metrics): string
    {
        $summary = $this->summarizeMetrics($metrics);

        return "Base sur ces metriques social media:\n" .
            json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n" .
            "Fournis 3 a 5 recommandations concretes pour ameliorer:\n" .
            "- L'engagement (likes, commentaires, partages)\n" .
            "- La portee (impressions, reach)\n" .
            "- La croissance de l'audience (followers)\n\n" .
            "Contexte: agence immobiliere, contenu typique = biens vendus, estimations gratuites, conseils achat/vente, actualites marche local.\n\n" .
            "Pour chaque recommandation, indique:\n" .
            "- L'action concrete a mettre en place\n" .
            "- Le resultat attendu\n" .
            "- La priorite (haute/moyenne/basse)";
    }

    /**
     * Build chat context from metrics
     */
    protected function buildChatContext($metrics): string
    {
        $summary = $this->summarizeMetrics($metrics);
        $lines = [];

        foreach ($summary as $platform => $platformMetrics) {
            $lines[] = "## {$platform}";
            foreach ($platformMetrics as $metric => $values) {
                $lines[] = "- {$metric}: total={$values['total']}, moyenne/jour={$values['average']}";
            }
            $lines[] = "";
        }

        return implode("\n", $lines);
    }

    /**
     * Summarize metrics by platform and metric name
     */
    protected function summarizeMetrics($metrics): array
    {
        return $metrics->groupBy('platform')
            ->map(function ($platformMetrics) {
                return $platformMetrics->groupBy('metric_name')
                    ->map(fn($items) => [
                        'total' => round($items->sum('value'), 2),
                        'average' => round($items->avg('value'), 2),
                        'min' => round($items->min('value'), 2),
                        'max' => round($items->max('value'), 2),
                        'count' => $items->count(),
                    ]);
            })
            ->toArray();
    }
}
