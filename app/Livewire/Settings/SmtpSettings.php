<?php

namespace App\Livewire\Settings;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class SmtpSettings extends Component
{
    public string $mail_mailer = 'smtp';
    public string $mail_host = '';
    public int $mail_port = 587;
    public string $mail_username = '';
    public string $mail_password = '';
    public ?string $mail_encryption = 'tls';
    public string $mail_from_address = '';
    public string $mail_from_name = '';
    public bool $is_active = false;

    public bool $showPassword = false;
    public bool $testing = false;
    public bool $testingConnection = false;
    public string $testEmail = '';

    protected function rules(): array
    {
        return [
            'mail_mailer' => 'required|in:smtp,sendmail,mailgun,ses,postmark,log',
            'mail_host' => 'required_if:mail_mailer,smtp|nullable|string|max:255',
            'mail_port' => 'required_if:mail_mailer,smtp|nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|in:tls,ssl,null',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ];
    }

    protected function messages(): array
    {
        return [
            'mail_host.required_if' => 'Le serveur SMTP est obligatoire.',
            'mail_port.required_if' => 'Le port SMTP est obligatoire.',
            'mail_from_address.required' => 'L\'adresse d\'expedition est obligatoire.',
            'mail_from_address.email' => 'L\'adresse d\'expedition doit etre un email valide.',
            'mail_from_name.required' => 'Le nom d\'expedition est obligatoire.',
        ];
    }

    public function mount(): void
    {
        $settings = SmtpSetting::getInstance();

        if ($settings->exists) {
            $this->mail_mailer = $settings->mail_mailer ?? 'smtp';
            $this->mail_host = $settings->mail_host ?? '';
            $this->mail_port = $settings->mail_port ?? 587;
            $this->mail_username = $settings->mail_username ?? '';
            $this->mail_password = $settings->mail_password ?? '';
            $this->mail_encryption = $settings->mail_encryption;
            $this->mail_from_address = $settings->mail_from_address ?? '';
            $this->mail_from_name = $settings->mail_from_name ?? '';
            $this->is_active = $settings->is_active ?? false;
        }
    }

    public function save(): void
    {
        $this->validate();

        $settings = SmtpSetting::getInstance();

        $data = [
            'mail_mailer' => $this->mail_mailer,
            'mail_host' => $this->mail_host,
            'mail_port' => $this->mail_port,
            'mail_username' => $this->mail_username,
            'mail_encryption' => $this->mail_encryption === 'null' ? null : $this->mail_encryption,
            'mail_from_address' => $this->mail_from_address,
            'mail_from_name' => $this->mail_from_name,
            'is_active' => $this->is_active,
        ];

        // Ne met a jour le mot de passe que s'il est renseigne
        if (!empty($this->mail_password)) {
            $data['mail_password'] = $this->mail_password;
        }

        if ($settings->exists) {
            $settings->update($data);
        } else {
            if (!empty($this->mail_password)) {
                $data['mail_password'] = $this->mail_password;
            }
            SmtpSetting::create($data);
        }

        session()->flash('success', 'Parametres SMTP enregistres avec succes.');
    }

    public function toggleActive(): void
    {
        $this->is_active = !$this->is_active;
    }

    public function togglePassword(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function testSmtpConnection(): void
    {
        if (empty($this->mail_host) || empty($this->mail_port)) {
            session()->flash('error', 'Veuillez renseigner le serveur et le port SMTP.');
            return;
        }

        $this->testingConnection = true;

        try {
            $password = $this->mail_password ?: SmtpSetting::getInstance()->mail_password;
            $encryption = $this->mail_encryption === 'null' ? false : ($this->mail_encryption === 'ssl');

            $transport = new EsmtpTransport(
                $this->mail_host,
                $this->mail_port,
                $encryption
            );

            if (!empty($this->mail_username) && !empty($password)) {
                $transport->setUsername($this->mail_username);
                $transport->setPassword($password);
            }

            // Test la connexion
            $transport->start();
            $transport->stop();

            session()->flash('success', 'Connexion SMTP reussie ! Le serveur ' . $this->mail_host . ':' . $this->mail_port . ' est accessible.');
        } catch (\Exception $e) {
            session()->flash('error', 'Echec de connexion: ' . $e->getMessage());
        }

        $this->testingConnection = false;
    }

    public function testConnection(): void
    {
        $this->validate([
            'testEmail' => 'required|email',
        ], [
            'testEmail.required' => 'L\'email de test est obligatoire.',
            'testEmail.email' => 'L\'email de test doit etre valide.',
        ]);

        $this->testing = true;

        try {
            // Configure temporairement le mailer
            config([
                'mail.default' => $this->mail_mailer,
                'mail.mailers.smtp.host' => $this->mail_host,
                'mail.mailers.smtp.port' => $this->mail_port,
                'mail.mailers.smtp.username' => $this->mail_username,
                'mail.mailers.smtp.password' => $this->mail_password ?: SmtpSetting::getInstance()->mail_password,
                'mail.mailers.smtp.encryption' => $this->mail_encryption === 'null' ? null : $this->mail_encryption,
                'mail.from.address' => $this->mail_from_address,
                'mail.from.name' => $this->mail_from_name,
            ]);

            Mail::raw('Ceci est un email de test envoye depuis l\'application Marketing KEYMEX.', function ($message) {
                $message->to($this->testEmail)
                    ->subject('Test SMTP - Marketing KEYMEX');
            });

            session()->flash('success', 'Email de test envoye avec succes a ' . $this->testEmail);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'envoi: ' . $e->getMessage());
        }

        $this->testing = false;
        $this->testEmail = '';
    }

    public function render()
    {
        return view('livewire.settings.smtp-settings');
    }
}
