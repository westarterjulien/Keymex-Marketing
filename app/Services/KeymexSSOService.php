<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class KeymexSSOService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;

    public function __construct()
    {
        $this->baseUrl = config('services.keymex.url');
        $this->clientId = config('services.keymex.client_id');
        $this->clientSecret = config('services.keymex.client_secret');
        $this->redirectUri = config('services.keymex.redirect_uri');
    }

    /**
     * Genere l'URL d'autorisation pour rediriger l'utilisateur
     */
    public function getAuthorizationUrl(array $scopes = ['openid', 'profile', 'email', 'groups']): string
    {
        $state = Str::random(40);
        session(['keymex_sso_state' => $state]);

        return $this->baseUrl . '/api/oauth/authorize?' . http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'state' => $state,
        ]);
    }

    /**
     * Gere le callback et echange le code contre des tokens
     */
    public function handleCallback(string $code, string $state): array
    {
        // Verification du state pour prevenir CSRF
        if ($state !== session('keymex_sso_state')) {
            throw new \Exception('Invalid state parameter - possible CSRF attack');
        }

        session()->forget('keymex_sso_state');

        // Echange du code contre des tokens
        $response = Http::asForm()->post($this->baseUrl . '/api/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Token exchange failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Recupere les informations de l'utilisateur connecte
     */
    public function getUserInfo(string $accessToken): array
    {
        $response = Http::withToken($accessToken)
            ->get($this->baseUrl . '/api/oauth/userinfo');

        if (!$response->successful()) {
            throw new \Exception('Failed to get user info: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Valide un token et retourne les infos utilisateur
     */
    public function validateToken(string $accessToken): ?array
    {
        $response = Http::post($this->baseUrl . '/api/oauth/validate', [
            'token' => $accessToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();
        return $data['active'] ? $data : null;
    }

    /**
     * Rafraichit un access token expire
     */
    public function refreshToken(string $refreshToken): array
    {
        $response = Http::asForm()->post($this->baseUrl . '/api/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Token refresh failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Revoque un token (logout cote SSO)
     */
    public function revokeToken(string $token): bool
    {
        $response = Http::post($this->baseUrl . '/api/oauth/revoke', [
            'token' => $token,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        return $response->successful();
    }
}
