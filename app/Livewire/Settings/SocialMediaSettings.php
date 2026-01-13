<?php

namespace App\Livewire\Settings;

use App\Models\SocialMediaSetting;
use App\Services\MetaGraphApiService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Configuration Social Media')]
class SocialMediaSettings extends Component
{
    // Meta Graph API
    public string $meta_access_token = '';
    public string $meta_facebook_page_id = '';
    public string $meta_instagram_account_id = '';
    public string $meta_api_version = 'v21.0';

    // OpenAI
    public string $openai_api_key = '';
    public string $openai_model = 'gpt-4';

    // UI State
    public bool $is_active = false;
    public bool $showMetaToken = false;
    public bool $showOpenAiKey = false;
    public bool $testingMeta = false;
    public bool $testingOpenAi = false;

    // Token info
    public ?array $tokenInfo = null;
    public ?array $facebookPageInfo = null;
    public ?array $instagramInfo = null;

    protected function rules(): array
    {
        return [
            'meta_access_token' => 'nullable|string|max:500',
            'meta_facebook_page_id' => 'nullable|string|max:100',
            'meta_instagram_account_id' => 'nullable|string|max:100',
            'meta_api_version' => 'required|string|max:10',
            'openai_api_key' => 'nullable|string|max:500',
            'openai_model' => 'required|string|max:50',
        ];
    }

    protected function messages(): array
    {
        return [
            'meta_access_token.max' => 'Le token Meta est trop long.',
            'openai_api_key.max' => 'La cle OpenAI est trop longue.',
        ];
    }

    public function mount(): void
    {
        $settings = SocialMediaSetting::getInstance();

        if ($settings->exists) {
            $this->meta_access_token = $settings->meta_access_token ?? '';
            $this->meta_facebook_page_id = $settings->meta_facebook_page_id ?? '';
            $this->meta_instagram_account_id = $settings->meta_instagram_account_id ?? '';
            $this->meta_api_version = $settings->meta_api_version ?? 'v21.0';
            $this->openai_api_key = $settings->openai_api_key ?? '';
            $this->openai_model = $settings->openai_model ?? 'gpt-4';
            $this->is_active = $settings->is_active ?? false;
        }
    }

    public function save(): void
    {
        $this->validate();

        $settings = SocialMediaSetting::getInstance();

        $data = [
            'meta_facebook_page_id' => $this->meta_facebook_page_id,
            'meta_instagram_account_id' => $this->meta_instagram_account_id,
            'meta_api_version' => $this->meta_api_version,
            'openai_model' => $this->openai_model,
            'is_active' => $this->is_active,
        ];

        // Ne met a jour les credentials que s'ils sont renseignes
        if (!empty($this->meta_access_token)) {
            $data['meta_access_token'] = $this->meta_access_token;
        }
        if (!empty($this->openai_api_key)) {
            $data['openai_api_key'] = $this->openai_api_key;
        }

        if ($settings->exists) {
            $settings->update($data);
        } else {
            SocialMediaSetting::create($data);
        }

        session()->flash('success', 'Parametres Social Media enregistres avec succes.');
    }

    public function toggleActive(): void
    {
        $this->is_active = !$this->is_active;
    }

    public function toggleMetaToken(): void
    {
        $this->showMetaToken = !$this->showMetaToken;
    }

    public function toggleOpenAiKey(): void
    {
        $this->showOpenAiKey = !$this->showOpenAiKey;
    }

    public function testMetaConnection(): void
    {
        $this->testingMeta = true;
        $this->tokenInfo = null;
        $this->facebookPageInfo = null;
        $this->instagramInfo = null;

        try {
            $token = $this->meta_access_token ?: SocialMediaSetting::getInstance()->meta_access_token;
            $pageId = $this->meta_facebook_page_id ?: SocialMediaSetting::getInstance()->meta_facebook_page_id;
            $igId = $this->meta_instagram_account_id ?: SocialMediaSetting::getInstance()->meta_instagram_account_id;

            if (empty($token)) {
                session()->flash('error', 'Veuillez renseigner le token Meta.');
                $this->testingMeta = false;
                return;
            }

            // Test token validation
            $service = new MetaGraphApiService();
            $service->setCredentials($token, $pageId, $igId, $this->meta_api_version);

            $this->tokenInfo = $service->validateToken();

            if ($this->tokenInfo['valid']) {
                // Get Facebook page info
                if (!empty($pageId)) {
                    $this->facebookPageInfo = $service->getFacebookPageInfo();
                }

                // Get Instagram info
                if (!empty($igId)) {
                    $this->instagramInfo = $service->getInstagramAccountInfo();
                }

                session()->flash('success', 'Connexion Meta reussie !');
            } else {
                session()->flash('error', 'Token invalide ou expire.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur de connexion Meta: ' . $e->getMessage());
        }

        $this->testingMeta = false;
    }

    public function testOpenAiConnection(): void
    {
        $this->testingOpenAi = true;

        try {
            $apiKey = $this->openai_api_key ?: SocialMediaSetting::getInstance()->openai_api_key;

            if (empty($apiKey)) {
                session()->flash('error', 'Veuillez renseigner la cle API OpenAI.');
                $this->testingOpenAi = false;
                return;
            }

            // Simple test with OpenAI API
            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://api.openai.com/v1/models', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'timeout' => 10,
            ]);

            if ($response->getStatusCode() === 200) {
                session()->flash('success', 'Connexion OpenAI reussie ! Cle API valide.');
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 401) {
                session()->flash('error', 'Cle API OpenAI invalide.');
            } else {
                session()->flash('error', 'Erreur OpenAI: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur de connexion OpenAI: ' . $e->getMessage());
        }

        $this->testingOpenAi = false;
    }

    public function render()
    {
        $settings = SocialMediaSetting::getInstance();
        $hasExistingMetaToken = $settings->exists && !empty($settings->meta_access_token);
        $hasExistingOpenAiKey = $settings->exists && !empty($settings->openai_api_key);

        return view('livewire.settings.social-media-settings', [
            'hasExistingMetaToken' => $hasExistingMetaToken,
            'hasExistingOpenAiKey' => $hasExistingOpenAiKey,
        ]);
    }
}
