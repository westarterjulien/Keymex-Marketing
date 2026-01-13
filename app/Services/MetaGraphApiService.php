<?php

namespace App\Services;

use App\Models\SocialMediaMetric;
use App\Models\SocialMediaSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaGraphApiService
{
    protected const BASE_URL = 'https://graph.facebook.com';
    protected const CACHE_TTL = 600; // 10 minutes

    protected ?string $accessToken;
    protected ?string $facebookPageId;
    protected ?string $instagramAccountId;
    protected string $apiVersion;

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
            $this->accessToken = $settings->meta_access_token;
            $this->facebookPageId = $settings->meta_facebook_page_id;
            $this->instagramAccountId = $settings->meta_instagram_account_id;
            $this->apiVersion = $settings->meta_api_version ?? 'v21.0';
        } else {
            // Fallback to config
            $this->accessToken = config('services.meta.access_token');
            $this->facebookPageId = config('services.meta.facebook_page_id');
            $this->instagramAccountId = config('services.meta.instagram_account_id');
            $this->apiVersion = config('services.meta.api_version', 'v21.0');
        }
    }

    /**
     * Set credentials manually (for testing)
     */
    public function setCredentials(?string $token, ?string $pageId, ?string $igId, string $apiVersion = 'v21.0'): void
    {
        $this->accessToken = $token;
        $this->facebookPageId = $pageId;
        $this->instagramAccountId = $igId;
        $this->apiVersion = $apiVersion;
    }

    /**
     * Check if the service is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->accessToken) &&
               (!empty($this->facebookPageId) || !empty($this->instagramAccountId));
    }

    /**
     * Get Facebook page insights
     */
    public function getFacebookPageInsights(Carbon $since, Carbon $until): array
    {
        if (empty($this->facebookPageId)) {
            return [];
        }

        $cacheKey = "fb_insights_{$this->facebookPageId}_{$since->format('Y-m-d')}_{$until->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($since, $until) {
            $metrics = [
                'page_views_total',
                'page_post_engagements',
                'page_fan_adds',
                'page_follows',
                'page_actions_post_reactions_total',
            ];

            $response = Http::get($this->buildUrl("{$this->facebookPageId}/insights"), [
                'access_token' => $this->accessToken,
                'metric' => implode(',', $metrics),
                'period' => 'day',
                'since' => $since->timestamp,
                'until' => $until->timestamp,
            ]);

            if (!$response->successful()) {
                Log::error('Meta Graph API Error (Facebook)', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            return $this->parseInsightsResponse($response->json(), 'facebook');
        });
    }

    /**
     * Get Instagram insights
     */
    public function getInstagramInsights(Carbon $since, Carbon $until): array
    {
        if (empty($this->instagramAccountId)) {
            return [];
        }

        $cacheKey = "ig_insights_{$this->instagramAccountId}_{$since->format('Y-m-d')}_{$until->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($since, $until) {
            $metrics = [
                'impressions',
                'reach',
                'follower_count',
                'accounts_engaged',
                'total_interactions',
            ];

            $response = Http::get($this->buildUrl("{$this->instagramAccountId}/insights"), [
                'access_token' => $this->accessToken,
                'metric' => implode(',', $metrics),
                'period' => 'day',
                'since' => $since->timestamp,
                'until' => $until->timestamp,
            ]);

            if (!$response->successful()) {
                Log::error('Meta Graph API Error (Instagram)', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            return $this->parseInsightsResponse($response->json(), 'instagram');
        });
    }

    /**
     * Get recent Facebook posts with stats
     */
    public function getFacebookPosts(int $limit = 10): array
    {
        if (empty($this->facebookPageId)) {
            return [];
        }

        $cacheKey = "fb_posts_{$this->facebookPageId}_{$limit}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($limit) {
            $response = Http::get($this->buildUrl("{$this->facebookPageId}/posts"), [
                'access_token' => $this->accessToken,
                'fields' => 'id,message,created_time,shares,reactions.summary(true),comments.summary(true)',
                'limit' => $limit,
            ]);

            if (!$response->successful()) {
                return [];
            }

            return collect($response->json()['data'] ?? [])->map(function ($post) {
                return [
                    'id' => $post['id'],
                    'message' => $post['message'] ?? '',
                    'created_at' => Carbon::parse($post['created_time']),
                    'shares' => $post['shares']['count'] ?? 0,
                    'reactions' => $post['reactions']['summary']['total_count'] ?? 0,
                    'comments' => $post['comments']['summary']['total_count'] ?? 0,
                ];
            })->toArray();
        });
    }

    /**
     * Get recent Instagram media with stats
     */
    public function getInstagramMedia(int $limit = 10): array
    {
        if (empty($this->instagramAccountId)) {
            return [];
        }

        $cacheKey = "ig_media_{$this->instagramAccountId}_{$limit}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($limit) {
            $response = Http::get($this->buildUrl("{$this->instagramAccountId}/media"), [
                'access_token' => $this->accessToken,
                'fields' => 'id,caption,timestamp,like_count,comments_count,media_type,thumbnail_url,permalink',
                'limit' => $limit,
            ]);

            if (!$response->successful()) {
                return [];
            }

            return collect($response->json()['data'] ?? [])->map(function ($media) {
                return [
                    'id' => $media['id'],
                    'caption' => $media['caption'] ?? '',
                    'created_at' => Carbon::parse($media['timestamp']),
                    'likes' => $media['like_count'] ?? 0,
                    'comments' => $media['comments_count'] ?? 0,
                    'type' => $media['media_type'] ?? 'IMAGE',
                    'thumbnail' => $media['thumbnail_url'] ?? null,
                    'permalink' => $media['permalink'] ?? null,
                ];
            })->toArray();
        });
    }

    /**
     * Validate access token and get info
     */
    public function validateToken(): array
    {
        if (empty($this->accessToken)) {
            return ['valid' => false, 'error' => 'No access token configured'];
        }

        $cacheKey = 'meta_token_validation';

        return Cache::remember($cacheKey, 300, function () {
            $response = Http::get($this->buildUrl('debug_token'), [
                'input_token' => $this->accessToken,
                'access_token' => $this->accessToken,
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                return [
                    'valid' => $data['is_valid'] ?? false,
                    'expires_at' => isset($data['expires_at']) && $data['expires_at'] > 0
                        ? Carbon::createFromTimestamp($data['expires_at'])
                        : null,
                    'scopes' => $data['scopes'] ?? [],
                    'app_id' => $data['app_id'] ?? null,
                ];
            }

            return ['valid' => false, 'error' => $response->body()];
        });
    }

    /**
     * Get Facebook page info
     */
    public function getFacebookPageInfo(): ?array
    {
        if (empty($this->facebookPageId)) {
            return null;
        }

        $cacheKey = "fb_page_info_{$this->facebookPageId}";

        return Cache::remember($cacheKey, 3600, function () {
            $response = Http::get($this->buildUrl($this->facebookPageId), [
                'access_token' => $this->accessToken,
                'fields' => 'id,name,fan_count,followers_count,picture{url}',
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            return [
                'id' => $data['id'] ?? null,
                'name' => $data['name'] ?? null,
                'fans' => $data['fan_count'] ?? 0,
                'followers' => $data['followers_count'] ?? 0,
                'picture' => $data['picture']['data']['url'] ?? null,
            ];
        });
    }

    /**
     * Get Instagram account info
     */
    public function getInstagramAccountInfo(): ?array
    {
        if (empty($this->instagramAccountId)) {
            return null;
        }

        $cacheKey = "ig_account_info_{$this->instagramAccountId}";

        return Cache::remember($cacheKey, 3600, function () {
            $response = Http::get($this->buildUrl($this->instagramAccountId), [
                'access_token' => $this->accessToken,
                'fields' => 'id,username,name,profile_picture_url,followers_count,follows_count,media_count',
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            return [
                'id' => $data['id'] ?? null,
                'username' => $data['username'] ?? null,
                'name' => $data['name'] ?? null,
                'picture' => $data['profile_picture_url'] ?? null,
                'followers' => $data['followers_count'] ?? 0,
                'following' => $data['follows_count'] ?? 0,
                'media_count' => $data['media_count'] ?? 0,
            ];
        });
    }

    /**
     * Sync metrics to database for a period
     */
    public function syncMetrics(Carbon $since, Carbon $until): int
    {
        $count = 0;

        // Facebook
        $fbInsights = $this->getFacebookPageInsights($since, $until);
        foreach ($fbInsights as $insight) {
            SocialMediaMetric::upsertMetric([
                'platform' => 'facebook',
                'page_id' => $this->facebookPageId,
                'date' => Carbon::parse($insight['end_time'])->toDateString(),
                'metric_name' => $insight['metric_name'],
                'metric_period' => 'day',
                'value' => $insight['value'],
            ]);
            $count++;
        }

        // Instagram
        $igInsights = $this->getInstagramInsights($since, $until);
        foreach ($igInsights as $insight) {
            SocialMediaMetric::upsertMetric([
                'platform' => 'instagram',
                'page_id' => $this->instagramAccountId,
                'date' => Carbon::parse($insight['end_time'])->toDateString(),
                'metric_name' => $insight['metric_name'],
                'metric_period' => 'day',
                'value' => $insight['value'],
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Build API URL
     */
    protected function buildUrl(string $endpoint): string
    {
        return self::BASE_URL . '/' . $this->apiVersion . '/' . $endpoint;
    }

    /**
     * Parse insights API response
     */
    protected function parseInsightsResponse(array $response, string $platform): array
    {
        $insights = [];

        foreach ($response['data'] ?? [] as $metric) {
            $metricName = $metric['name'] ?? '';
            foreach ($metric['values'] ?? [] as $value) {
                $insights[] = [
                    'platform' => $platform,
                    'metric_name' => $metricName,
                    'value' => $value['value'] ?? 0,
                    'end_time' => $value['end_time'] ?? null,
                ];
            }
        }

        return $insights;
    }

    /**
     * Clear all cached data
     */
    public function clearCache(): void
    {
        Cache::forget("fb_insights_{$this->facebookPageId}_*");
        Cache::forget("ig_insights_{$this->instagramAccountId}_*");
        Cache::forget("fb_posts_{$this->facebookPageId}_*");
        Cache::forget("ig_media_{$this->instagramAccountId}_*");
        Cache::forget("fb_page_info_{$this->facebookPageId}");
        Cache::forget("ig_account_info_{$this->instagramAccountId}");
        Cache::forget('meta_token_validation');
    }
}
