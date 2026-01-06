<?php

namespace App\Livewire\Signature;

use App\Models\Brand;
use App\Models\SavedSignature;
use App\Models\SignatureTemplate;
use App\Services\MongoPropertyService;
use App\Services\SignatureGeneratorService;
use Livewire\Component;
use Livewire\WithFileUploads;

class MySignature extends Component
{
    use WithFileUploads;

    public bool $isAuthenticated = false;
    public ?string $userEmail = null;
    public ?string $userName = null;
    public ?array $advisor = null;
    public ?string $signatureHtml = null;
    public ?string $error = null;
    public bool $advisorNotFound = false;

    // Mode edition
    public bool $isEditing = false;
    public bool $showSavedMessage = false;

    // Donnees editables
    public string $editFirstname = '';
    public string $editLastname = '';
    public string $editJobTitle = '';
    public string $editPhone = '';
    public string $editMobilePhone = '';
    public string $editPictureUrl = '';
    public string $editLinkedin = '';
    public string $editFacebook = '';
    public string $editInstagram = '';

    // Selection template/brand
    public ?int $selectedTemplateId = null;
    public ?int $selectedBrandId = null;

    // Upload photo
    public $photoUpload;

    // Signature sauvegardee
    public ?SavedSignature $savedSignature = null;

    protected MongoPropertyService $mongoService;
    protected SignatureGeneratorService $signatureService;

    protected $rules = [
        'editFirstname' => 'nullable|string|max:100',
        'editLastname' => 'nullable|string|max:100',
        'editJobTitle' => 'nullable|string|max:100',
        'editPhone' => 'nullable|string|max:30',
        'editMobilePhone' => 'nullable|string|max:30',
        'editPictureUrl' => 'nullable|url|max:500',
        'editLinkedin' => 'nullable|url|max:500',
        'editFacebook' => 'nullable|url|max:500',
        'editInstagram' => 'nullable|url|max:500',
        'photoUpload' => 'nullable|image|max:2048',
    ];

    public function boot(MongoPropertyService $mongoService, SignatureGeneratorService $signatureService): void
    {
        $this->mongoService = $mongoService;
        $this->signatureService = $signatureService;
    }

    public function mount(): void
    {
        // Verifier si l'utilisateur est authentifie via la session
        $this->userEmail = session('signature_user_email');
        $this->userName = session('signature_user_name');
        $this->isAuthenticated = !empty($this->userEmail);

        if ($this->isAuthenticated) {
            $this->loadData();
        }
    }

    protected function loadData(): void
    {
        try {
            // Verifier s'il y a une signature sauvegardee
            $this->savedSignature = SavedSignature::findByEmail($this->userEmail);

            // Rechercher le conseiller dans MongoDB par email
            $mongoAdvisor = $this->mongoService->getAdvisorByEmail($this->userEmail);

            if (!$mongoAdvisor && !$this->savedSignature) {
                $this->advisorNotFound = true;
                $this->error = "Aucun conseiller trouve avec l'email {$this->userEmail}. Vous pouvez creer votre signature manuellement.";

                // Permettre la creation manuelle
                $this->initializeEmptyAdvisor();
                return;
            }

            // Si une signature sauvegardee existe, fusionner avec les donnees MongoDB
            if ($this->savedSignature) {
                $this->advisor = $this->savedSignature->mergeWithMongoData($mongoAdvisor ?? []);
                $this->selectedTemplateId = $this->savedSignature->signature_template_id;
                $this->selectedBrandId = $this->savedSignature->brand_id;
            } else {
                $this->advisor = $mongoAdvisor;

                // Template et brand par defaut
                $defaultTemplate = SignatureTemplate::getDefault();
                if ($defaultTemplate) {
                    $this->selectedTemplateId = $defaultTemplate->id;
                    $this->selectedBrandId = $defaultTemplate->brand_id;
                }
            }

            // Remplir les champs d'edition avec les donnees actuelles
            $this->fillEditFields();

            // Activer le mode edition par defaut pour permettre les modifications
            $this->isEditing = true;

            // Generer la signature
            $this->generateSignature();

        } catch (\Exception $e) {
            $this->error = 'Erreur lors du chargement des donnees: ' . $e->getMessage();
            report($e);
        }
    }

    protected function initializeEmptyAdvisor(): void
    {
        $this->advisor = [
            'firstname' => '',
            'lastname' => '',
            'email' => $this->userEmail,
            'job_title' => 'Conseiller Immobilier',
            'phone' => '',
            'mobile_phone' => '',
            'picture' => '',
            'linkedin_url' => '',
            'facebook_url' => '',
            'instagram_url' => '',
        ];

        $defaultTemplate = SignatureTemplate::getDefault();
        if ($defaultTemplate) {
            $this->selectedTemplateId = $defaultTemplate->id;
            $this->selectedBrandId = $defaultTemplate->brand_id;
        }

        $this->fillEditFields();
        $this->isEditing = true;
    }

    protected function fillEditFields(): void
    {
        if (!$this->advisor) {
            return;
        }

        $this->editFirstname = $this->advisor['firstname'] ?? '';
        $this->editLastname = $this->advisor['lastname'] ?? '';
        $this->editJobTitle = $this->advisor['job_title'] ?? 'Conseiller Immobilier';
        $this->editPhone = $this->advisor['phone'] ?? '';
        $this->editMobilePhone = $this->advisor['mobile_phone'] ?? '';
        $this->editPictureUrl = $this->advisor['picture'] ?? '';
        $this->editLinkedin = $this->advisor['linkedin_url'] ?? '';
        $this->editFacebook = $this->advisor['facebook_url'] ?? '';
        $this->editInstagram = $this->advisor['instagram_url'] ?? '';
    }

    public function startEditing(): void
    {
        $this->isEditing = true;
        $this->fillEditFields();
    }

    public function cancelEditing(): void
    {
        $this->isEditing = false;
        $this->fillEditFields();
    }

    public function saveChanges(): void
    {
        $this->validate();

        try {
            // Mettre a jour les donnees du conseiller
            $this->advisor['firstname'] = $this->editFirstname;
            $this->advisor['lastname'] = $this->editLastname;
            $this->advisor['job_title'] = $this->editJobTitle;
            $this->advisor['phone'] = $this->editPhone;
            $this->advisor['mobile_phone'] = $this->editMobilePhone;
            $this->advisor['picture'] = $this->editPictureUrl;
            $this->advisor['linkedin_url'] = $this->editLinkedin;
            $this->advisor['facebook_url'] = $this->editFacebook;
            $this->advisor['instagram_url'] = $this->editInstagram;

            // Sauvegarder en base de donnees
            $this->saveSignatureToDatabase();

            // Regenerer la signature
            $this->generateSignature();

            $this->isEditing = false;
            $this->showSavedMessage = true;

            // Cacher le message apres 3 secondes
            $this->dispatch('saved-message-timeout');

        } catch (\Exception $e) {
            $this->error = 'Erreur lors de la sauvegarde: ' . $e->getMessage();
            report($e);
        }
    }

    protected function saveSignatureToDatabase(): void
    {
        if (!$this->selectedTemplateId) {
            throw new \Exception('Veuillez selectionner un template.');
        }

        $data = [
            'advisor_email' => $this->userEmail,
            'signature_template_id' => $this->selectedTemplateId,
            'brand_id' => $this->selectedBrandId,
            'firstname' => $this->editFirstname ?: null,
            'lastname' => $this->editLastname ?: null,
            'job_title' => $this->editJobTitle ?: null,
            'phone' => $this->editPhone ?: null,
            'mobile_phone' => $this->editMobilePhone ?: null,
            'picture_url' => $this->editPictureUrl ?: null,
            'linkedin_url' => $this->editLinkedin ?: null,
            'facebook_url' => $this->editFacebook ?: null,
            'instagram_url' => $this->editInstagram ?: null,
        ];

        if ($this->savedSignature) {
            $this->savedSignature->update($data);
            $this->savedSignature->clearCache();
        } else {
            $this->savedSignature = SavedSignature::create($data);
        }
    }

    public function updatedSelectedTemplateId(): void
    {
        $template = SignatureTemplate::find($this->selectedTemplateId);
        if ($template && $template->brand_id) {
            $this->selectedBrandId = $template->brand_id;
        }
        $this->generateSignature();
    }

    public function updatedSelectedBrandId(): void
    {
        $this->generateSignature();
    }

    protected function generateSignature(): void
    {
        if (!$this->advisor || !$this->selectedTemplateId) {
            return;
        }

        try {
            $template = SignatureTemplate::with('brand')->find($this->selectedTemplateId);

            if (!$template) {
                $this->error = 'Template non trouve.';
                return;
            }

            // Utiliser la marque selectionnee ou celle du template
            if ($this->selectedBrandId && $this->selectedBrandId !== $template->brand_id) {
                $brand = Brand::find($this->selectedBrandId);
                if ($brand) {
                    $template->setRelation('brand', $brand);
                }
            }

            $this->signatureHtml = $this->signatureService->generateForAdvisor($template, $this->advisor, false);

            // Mettre a jour le cache si signature sauvegardee
            if ($this->savedSignature && $this->signatureHtml) {
                $this->savedSignature->updateCache($this->signatureHtml);
            }

        } catch (\Exception $e) {
            $this->error = 'Erreur lors de la generation de la signature: ' . $e->getMessage();
            report($e);
        }
    }

    public function logout(): void
    {
        session()->forget([
            'signature_user_email',
            'signature_user_name',
            'signature_authenticated_at',
        ]);

        $this->reset();
    }

    public function hideSavedMessage(): void
    {
        $this->showSavedMessage = false;
    }

    public function render()
    {
        return view('livewire.signature.my-signature', [
            'templates' => SignatureTemplate::where('is_active', true)->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
        ])->layout('components.layouts.public', ['title' => 'Ma Signature KEYMEX']);
    }
}
