<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SsoGroupMapping;
use App\Models\User;
use App\Services\KeymexSSOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KeymexAuthController extends Controller
{
    public function __construct(
        protected KeymexSSOService $sso
    ) {}

    /**
     * Redirige vers Keymex SSO pour l'authentification
     */
    public function redirect()
    {
        return redirect($this->sso->getAuthorizationUrl());
    }

    /**
     * Gere le retour de Keymex SSO apres authentification
     */
    public function callback(Request $request)
    {
        // Verifier si c'est une auth pour la signature (page publique)
        $isSignatureAuth = session('auth_for_signature', false);
        session()->forget('auth_for_signature');

        // Gestion des erreurs retournees par le SSO
        if ($request->has('error')) {
            $redirectUrl = $isSignatureAuth ? route('signature.my') : '/login';
            return redirect($redirectUrl)->with('error',
                $request->error_description ?? 'Erreur d\'authentification'
            );
        }

        // Verification des parametres requis
        if (!$request->has('code') || !$request->has('state')) {
            $redirectUrl = $isSignatureAuth ? route('signature.my') : '/login';
            return redirect($redirectUrl)->with('error', 'Parametres manquants');
        }

        try {
            // Echange du code contre des tokens
            $tokens = $this->sso->handleCallback(
                $request->code,
                $request->state
            );

            // Recuperation des infos utilisateur
            $userInfo = $this->sso->getUserInfo($tokens['access_token']);

            // Si c'est une auth signature, stocker l'email en session et rediriger
            if ($isSignatureAuth) {
                Log::info('Signature Auth success', ['email' => $userInfo['email']]);

                session([
                    'signature_user_email' => $userInfo['email'],
                    'signature_user_name' => $userInfo['name'],
                    'signature_authenticated_at' => now(),
                ]);

                return redirect()->route('signature.my')
                    ->with('success', 'Authentification reussie');
            }

            // Auth normale : verifier les groupes et creer l'utilisateur
            $ssoGroups = $userInfo['groups'] ?? [];

            Log::info('SSO Login attempt', [
                'user' => $userInfo['email'],
                'groups' => $ssoGroups,
            ]);

            // Verifier si l'utilisateur appartient a un groupe autorise
            if (!SsoGroupMapping::isGroupAllowed($ssoGroups)) {
                Log::warning('SSO Login denied - no allowed group', [
                    'user' => $userInfo['email'],
                    'groups' => $ssoGroups,
                    'allowed_groups' => SsoGroupMapping::getAllowedGroupNames(),
                ]);

                return redirect('/login')->with('error',
                    'Acces refuse. Vous n\'appartenez a aucun groupe autorise a acceder a cette application.'
                );
            }

            // Creation ou mise a jour de l'utilisateur local
            $user = User::updateOrCreate(
                ['keymex_id' => $userInfo['sub']],
                [
                    'name' => $userInfo['name'],
                    'email' => $userInfo['email'],
                    'avatar' => $userInfo['picture'] ?? null,
                    'sso_groups' => $ssoGroups,
                    'password' => bcrypt(str()->random(32)), // Mot de passe aleatoire (non utilise avec SSO)
                ]
            );

            // Stockage des tokens en session
            session([
                'keymex_access_token' => $tokens['access_token'],
                'keymex_refresh_token' => $tokens['refresh_token'] ?? null,
                'keymex_token_expires_at' => now()->addSeconds($tokens['expires_in']),
            ]);

            // Connexion de l'utilisateur
            Auth::login($user, true);

            Log::info('SSO Login success', [
                'user' => $userInfo['email'],
            ]);

            return redirect()->intended(route('orders.index'));

        } catch (\Exception $e) {
            report($e);
            $redirectUrl = $isSignatureAuth ? route('signature.my') : '/login';
            return redirect($redirectUrl)->with('error', 'Erreur de connexion: ' . $e->getMessage());
        }
    }

    /**
     * Deconnexion
     */
    public function logout(Request $request)
    {
        // Revocation du token cote SSO (optionnel)
        if ($accessToken = session('keymex_access_token')) {
            $this->sso->revokeToken($accessToken);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
