<?php

use App\Http\Controllers\Auth\KeymexAuthController;
use App\Livewire\Orders\OrderCreate;
use App\Livewire\Orders\OrderIndex;
use App\Livewire\Orders\OrderShow;
use App\Livewire\Properties\PropertyForSale;
use App\Livewire\Properties\PropertyIndex;
use App\Livewire\Settings\OrderSettings;
use App\Livewire\Settings\SignatureSettings;
use App\Livewire\Settings\SmtpSettings;
use App\Livewire\Settings\SocialMediaSettings;
use App\Livewire\Settings\SsoSettings;
use App\Livewire\Settings\StorageSettings;
use App\Livewire\StandaloneBat\BatCreate;
use App\Livewire\StandaloneBat\BatHistory;
use App\Livewire\StandaloneBat\BatIndex;
use App\Livewire\StandaloneBat\BatShow;
use App\Livewire\StandaloneBat\BatValidation;
use App\Livewire\Kpi\HebdoBizCustom;
use App\Livewire\Kpi\HebdoBizMonthly;
use App\Livewire\Kpi\HebdoBizWeekly;
use App\Livewire\Kpi\HebdoBizYearly;
use App\Livewire\Kpi\KeyPerformeurs;
use App\Livewire\Stats\Dashboard;
use App\Livewire\SocialMedia\Dashboard as SocialMediaDashboard;
use App\Livewire\SocialMedia\AiAssistant as SocialMediaAiAssistant;
use App\Livewire\Signature\MySignature;
use App\Http\Controllers\SignatureAuthController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('orders.index');
});

// Routes d'authentification Keymex SSO
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

// Dev login (uniquement si DEV_LOGIN_ENABLED=true)
Route::post('/dev-login', function () {
    if (!config('app.dev_login_enabled')) {
        abort(403);
    }

    $user = \App\Models\User::firstOrCreate(
        ['email' => 'admin@keymex.fr'],
        [
            'name' => 'Admin Test',
            'keymex_id' => 'dev-test-id',
            'password' => bcrypt('dev-password'),
        ]
    );

    auth()->login($user);

    return redirect()->route('orders.index');
})->name('dev.login');

// Redirection vers Keymex SSO
Route::get('/auth/keymex', [KeymexAuthController::class, 'redirect'])
    ->name('auth.keymex')
    ->middleware('guest');

// Callback apres authentification SSO
Route::get('/auth/callback', [KeymexAuthController::class, 'callback'])
    ->name('auth.callback')
    ->middleware('guest');

// Deconnexion
Route::post('/logout', [KeymexAuthController::class, 'logout'])->name('logout');

// Routes protégées (staff)
Route::middleware(['auth'])->group(function () {
    // Module Commandes
    Route::get('/commandes', OrderIndex::class)->name('orders.index');
    Route::get('/commandes/creer', OrderCreate::class)->name('orders.create');
    Route::get('/commandes/{order}', OrderShow::class)->name('orders.show');

    // Module Biens
    Route::get('/biens', PropertyIndex::class)->name('properties.index');
    Route::get('/biens/a-vendre', PropertyForSale::class)->name('properties.for-sale');

    // Module Statistiques
    Route::get('/statistiques', Dashboard::class)->name('stats.dashboard');

    // Module KPI Hebdo Biz
    Route::get('/kpi/hebdomadaire', HebdoBizWeekly::class)->name('kpi.weekly');
    Route::get('/kpi/mensuel', HebdoBizMonthly::class)->name('kpi.monthly');
    Route::get('/kpi/annuel', HebdoBizYearly::class)->name('kpi.yearly');
    Route::get('/kpi/personnalise', HebdoBizCustom::class)->name('kpi.custom');
    Route::get('/kpi/key-performeurs', KeyPerformeurs::class)->name('kpi.performeurs');

    // Configuration
    Route::get('/configuration/commandes', OrderSettings::class)->name('settings.orders');
    Route::get('/configuration/signatures', SignatureSettings::class)->name('settings.signatures');
    Route::get('/configuration/sso', SsoSettings::class)->name('settings.sso');
    Route::get('/configuration/smtp', SmtpSettings::class)->name('settings.smtp');
    Route::get('/configuration/stockage', StorageSettings::class)->name('settings.storage');
    Route::get('/configuration/social-media', SocialMediaSettings::class)->name('settings.social-media');

    // Module BAT standalone
    Route::get('/bats', BatIndex::class)->name('standalone-bats.index');
    Route::get('/bats/creer', BatCreate::class)->name('standalone-bats.create');
    Route::get('/bats/historique', BatHistory::class)->name('standalone-bats.history');
    Route::get('/bats/{bat}', BatShow::class)->name('standalone-bats.show');

    // Module Social Media Analytics
    Route::get('/social-media', SocialMediaDashboard::class)->name('social-media.dashboard');
    Route::get('/social-media/assistant', SocialMediaAiAssistant::class)->name('social-media.assistant');

    // Story Generator
    Route::post('/stories/generate/{propertyId}/{type}', [StoryController::class, 'generate'])->name('stories.generate');
    Route::get('/stories/preview/{type}', [StoryController::class, 'preview'])->name('stories.preview');
    Route::get('/stories/download/{filename}', [StoryController::class, 'download'])->name('stories.download');

    // Media Library
    Route::get('/stories/medias', [MediaController::class, 'index'])->name('stories.media');
    Route::post('/stories/medias', [MediaController::class, 'store'])->name('stories.media.store');
    Route::delete('/stories/medias/{media}', [MediaController::class, 'destroy'])->name('stories.media.destroy');
    Route::get('/stories/medias/{media}/url', [MediaController::class, 'copyUrl'])->name('stories.media.url');
});

// Route publique pour validation BAT (avec token)
Route::get('/bat/validation/{token}', BatValidation::class)->name('standalone-bat.validate');

// Routes publiques pour la signature email
Route::get('/ma-signature', MySignature::class)->name('signature.my');
Route::get('/ma-signature/auth', [SignatureAuthController::class, 'redirect'])->name('signature.auth');
Route::get('/ma-signature/callback', [SignatureAuthController::class, 'callback'])->name('signature.callback');
Route::post('/ma-signature/logout', [SignatureAuthController::class, 'logout'])->name('signature.logout');
