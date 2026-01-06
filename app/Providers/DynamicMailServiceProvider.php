<?php

namespace App\Providers;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class DynamicMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Evite les erreurs si la table n'existe pas encore (pendant les migrations)
        if (!$this->app->runningInConsole() || $this->app->runningUnitTests()) {
            $this->configureMailFromDatabase();
        }
    }

    /**
     * Configure le mailer depuis la base de donnees
     */
    protected function configureMailFromDatabase(): void
    {
        try {
            // Verifie que la table existe
            if (!Schema::hasTable('smtp_settings')) {
                return;
            }

            $settings = SmtpSetting::getSettings();

            if ($settings && $settings->is_active) {
                config([
                    'mail.default' => $settings->mail_mailer ?? 'smtp',
                    'mail.mailers.smtp.host' => $settings->mail_host,
                    'mail.mailers.smtp.port' => $settings->mail_port,
                    'mail.mailers.smtp.username' => $settings->mail_username,
                    'mail.mailers.smtp.password' => $settings->mail_password,
                    'mail.mailers.smtp.encryption' => $settings->mail_encryption,
                    'mail.from.address' => $settings->mail_from_address,
                    'mail.from.name' => $settings->mail_from_name,
                ]);
            }
        } catch (\Exception $e) {
            // En cas d'erreur (table inexistante, etc.), on continue avec la config par defaut
            report($e);
        }
    }
}
