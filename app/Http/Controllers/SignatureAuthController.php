<?php

namespace App\Http\Controllers;

use App\Services\KeymexSSOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SignatureAuthController extends Controller
{
    public function __construct(
        protected KeymexSSOService $sso
    ) {}

    /**
     * Redirige vers Keymex SSO pour l'authentification signature
     * Utilise le callback principal avec un flag en session
     */
    public function redirect()
    {
        // Stocker un flag pour indiquer que c'est une auth signature
        session(['auth_for_signature' => true]);

        // Utiliser le callback principal (autorise dans le SSO)
        return redirect($this->sso->getAuthorizationUrl(['openid', 'profile', 'email']));
    }

    /**
     * Gere le retour de Keymex SSO (callback alternatif si URI autorisee)
     */
    public function callback(Request $request)
    {
        // Gestion des erreurs retournees par le SSO
        if ($request->has('error')) {
            return redirect()->route('signature.my')
                ->with('error', $request->error_description ?? 'Erreur d\'authentification');
        }

        // Verification des parametres requis
        if (!$request->has('code') || !$request->has('state')) {
            return redirect()->route('signature.my')
                ->with('error', 'Parametres manquants');
        }

        try {
            // Echange du code contre des tokens
            $tokens = $this->sso->handleCallback(
                $request->code,
                $request->state
            );

            // Recuperation des infos utilisateur
            $userInfo = $this->sso->getUserInfo($tokens['access_token']);

            Log::info('Signature Auth - User authenticated', [
                'email' => $userInfo['email'],
                'name' => $userInfo['name'],
            ]);

            // Stocker l'email en session pour la page signature
            session([
                'signature_user_email' => $userInfo['email'],
                'signature_user_name' => $userInfo['name'],
                'signature_authenticated_at' => now(),
            ]);

            return redirect()->route('signature.my')
                ->with('success', 'Authentification reussie');

        } catch (\Exception $e) {
            report($e);
            return redirect()->route('signature.my')
                ->with('error', 'Erreur de connexion: ' . $e->getMessage());
        }
    }

    /**
     * Deconnexion de la page signature
     */
    public function logout()
    {
        session()->forget([
            'signature_user_email',
            'signature_user_name',
            'signature_authenticated_at',
        ]);

        return redirect()->route('signature.my')
            ->with('success', 'Deconnexion reussie');
    }
}
