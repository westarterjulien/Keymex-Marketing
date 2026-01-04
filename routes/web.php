<?php

use App\Http\Controllers\Auth\KeymexAuthController;
use App\Livewire\Orders\OrderCreate;
use App\Livewire\Orders\OrderIndex;
use App\Livewire\Orders\OrderShow;
use App\Livewire\Properties\PropertyForSale;
use App\Livewire\Properties\PropertyIndex;
use App\Livewire\Settings\OrderSettings;
use App\Livewire\Settings\SsoSettings;
use App\Livewire\StandaloneBat\BatCreate;
use App\Livewire\StandaloneBat\BatIndex;
use App\Livewire\StandaloneBat\BatShow;
use App\Livewire\StandaloneBat\BatValidation;
use App\Livewire\Kpi\HebdoBizMonthly;
use App\Livewire\Kpi\HebdoBizWeekly;
use App\Livewire\Stats\Dashboard;
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

    // Configuration
    Route::get('/configuration/commandes', OrderSettings::class)->name('settings.orders');
    Route::get('/configuration/sso', SsoSettings::class)->name('settings.sso');

    // Module BAT standalone
    Route::get('/bats', BatIndex::class)->name('standalone-bats.index');
    Route::get('/bats/creer', BatCreate::class)->name('standalone-bats.create');
    Route::get('/bats/{bat}', BatShow::class)->name('standalone-bats.show');
});

// Route publique pour validation BAT (avec token)
Route::get('/bat/validation/{token}', BatValidation::class)->name('standalone-bat.validate');
