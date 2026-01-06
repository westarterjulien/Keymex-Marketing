<?php

namespace App\Livewire\Settings;

use App\Models\Brand;
use App\Models\SignatureTemplate;
use App\Services\SignatureGeneratorService;
use Livewire\Component;

class SignatureSettings extends Component
{
    public string $activeTab = 'templates';

    // Templates
    public bool $showTemplateModal = false;
    public ?int $editingTemplateId = null;
    public string $templateName = '';
    public string $templateDescription = '';
    public ?int $templateBrandId = null;
    public string $templateHtmlContent = '';
    public bool $templateActive = true;
    public bool $templateDefault = false;

    // Brands
    public bool $showBrandModal = false;
    public ?int $editingBrandId = null;
    public string $brandName = '';
    public string $brandDescription = '';
    public string $brandPrimaryColor = '#8B5CF6';
    public string $brandSecondaryColor = '#6c757d';
    public string $brandWebsite = '';
    public string $brandEmail = '';
    public string $brandPhone = '';
    public bool $brandActive = true;
    public bool $brandDefault = false;

    // Preview
    public bool $showPreviewModal = false;
    public string $previewHtml = '';

    // Delete confirmation
    public bool $showDeleteModal = false;
    public string $deleteType = '';
    public ?int $deleteId = null;

    protected $queryString = [
        'activeTab' => ['except' => 'templates'],
    ];

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ================== Templates ==================

    public function openTemplateModal(?int $id = null): void
    {
        $this->resetTemplateForm();

        if ($id) {
            $template = SignatureTemplate::find($id);
            if ($template) {
                $this->editingTemplateId = $id;
                $this->templateName = $template->name;
                $this->templateDescription = $template->description ?? '';
                $this->templateBrandId = $template->brand_id;
                $this->templateHtmlContent = $template->html_content;
                $this->templateActive = $template->is_active;
                $this->templateDefault = $template->is_default;
            }
        } else {
            // Default template HTML
            $this->templateHtmlContent = $this->getDefaultTemplateHtml();
        }

        $this->showTemplateModal = true;
    }

    public function saveTemplate(): void
    {
        $this->validate([
            'templateName' => 'required|string|max:255',
            'templateBrandId' => 'required|exists:brands,id',
            'templateHtmlContent' => 'required|string',
        ], [
            'templateName.required' => 'Le nom est obligatoire.',
            'templateBrandId.required' => 'La marque est obligatoire.',
            'templateHtmlContent.required' => 'Le contenu HTML est obligatoire.',
        ]);

        // If setting as default, unset other defaults
        if ($this->templateDefault) {
            SignatureTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        $data = [
            'name' => $this->templateName,
            'description' => $this->templateDescription ?: null,
            'brand_id' => $this->templateBrandId,
            'html_content' => $this->templateHtmlContent,
            'is_active' => $this->templateActive,
            'is_default' => $this->templateDefault,
        ];

        if ($this->editingTemplateId) {
            $template = SignatureTemplate::find($this->editingTemplateId);
            $template->update($data);
            session()->flash('success', 'Template modifie avec succes.');
        } else {
            SignatureTemplate::create($data);
            session()->flash('success', 'Template cree avec succes.');
        }

        $this->closeTemplateModal();
    }

    public function closeTemplateModal(): void
    {
        $this->showTemplateModal = false;
        $this->resetTemplateForm();
    }

    protected function resetTemplateForm(): void
    {
        $this->editingTemplateId = null;
        $this->templateName = '';
        $this->templateDescription = '';
        $this->templateBrandId = null;
        $this->templateHtmlContent = '';
        $this->templateActive = true;
        $this->templateDefault = false;
    }

    public function toggleTemplateActive(int $id): void
    {
        $template = SignatureTemplate::find($id);
        if ($template) {
            $template->update(['is_active' => !$template->is_active]);
        }
    }

    public function previewTemplate(int $id): void
    {
        $template = SignatureTemplate::with('brand')->find($id);
        if ($template) {
            $generator = app(SignatureGeneratorService::class);
            $sampleAdvisor = [
                'firstname' => 'Jean',
                'lastname' => 'DUPONT',
                'email' => 'jean.dupont@keymex.fr',
                'mobile_phone' => '06 12 34 56 78',
                'phone' => '01 23 45 67 89',
                'picture' => 'https://ui-avatars.com/api/?name=Jean+Dupont&size=200&background=8B5CF6&color=fff',
                'job_title' => 'Conseiller Immobilier',
            ];

            $this->previewHtml = $generator->generateForAdvisor($template, $sampleAdvisor, false);
            $this->showPreviewModal = true;
        }
    }

    public function closePreviewModal(): void
    {
        $this->showPreviewModal = false;
        $this->previewHtml = '';
    }

    // ================== Brands ==================

    public function openBrandModal(?int $id = null): void
    {
        $this->resetBrandForm();

        if ($id) {
            $brand = Brand::find($id);
            if ($brand) {
                $this->editingBrandId = $id;
                $this->brandName = $brand->name;
                $this->brandDescription = $brand->description ?? '';
                $this->brandPrimaryColor = $brand->primary_color ?? '#8B5CF6';
                $this->brandSecondaryColor = $brand->secondary_color ?? '#6c757d';
                $this->brandWebsite = $brand->website ?? '';
                $this->brandEmail = $brand->email ?? '';
                $this->brandPhone = $brand->phone ?? '';
                $this->brandActive = $brand->is_active;
                $this->brandDefault = $brand->is_default;
            }
        }

        $this->showBrandModal = true;
    }

    public function saveBrand(): void
    {
        $this->validate([
            'brandName' => 'required|string|max:255',
            'brandPrimaryColor' => 'required|string|max:20',
            'brandWebsite' => 'nullable|url|max:255',
            'brandEmail' => 'nullable|email|max:255',
        ], [
            'brandName.required' => 'Le nom est obligatoire.',
            'brandWebsite.url' => 'L\'URL du site web n\'est pas valide.',
            'brandEmail.email' => 'L\'email n\'est pas valide.',
        ]);

        // If setting as default, unset other defaults
        if ($this->brandDefault) {
            Brand::where('is_default', true)->update(['is_default' => false]);
        }

        $data = [
            'name' => $this->brandName,
            'description' => $this->brandDescription ?: null,
            'primary_color' => $this->brandPrimaryColor,
            'secondary_color' => $this->brandSecondaryColor ?: null,
            'website' => $this->brandWebsite ?: null,
            'email' => $this->brandEmail ?: null,
            'phone' => $this->brandPhone ?: null,
            'is_active' => $this->brandActive,
            'is_default' => $this->brandDefault,
        ];

        if ($this->editingBrandId) {
            $brand = Brand::find($this->editingBrandId);
            $brand->update($data);
            session()->flash('success', 'Marque modifiee avec succes.');
        } else {
            Brand::create($data);
            session()->flash('success', 'Marque creee avec succes.');
        }

        $this->closeBrandModal();
    }

    public function closeBrandModal(): void
    {
        $this->showBrandModal = false;
        $this->resetBrandForm();
    }

    protected function resetBrandForm(): void
    {
        $this->editingBrandId = null;
        $this->brandName = '';
        $this->brandDescription = '';
        $this->brandPrimaryColor = '#8B5CF6';
        $this->brandSecondaryColor = '#6c757d';
        $this->brandWebsite = '';
        $this->brandEmail = '';
        $this->brandPhone = '';
        $this->brandActive = true;
        $this->brandDefault = false;
    }

    public function toggleBrandActive(int $id): void
    {
        $brand = Brand::find($id);
        if ($brand) {
            $brand->update(['is_active' => !$brand->is_active]);
        }
    }

    // ================== Delete ==================

    public function confirmDelete(string $type, int $id): void
    {
        $this->deleteType = $type;
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (!$this->deleteId) {
            return;
        }

        $model = match ($this->deleteType) {
            'template' => SignatureTemplate::find($this->deleteId),
            'brand' => Brand::find($this->deleteId),
            default => null,
        };

        if ($model) {
            $model->delete();
            session()->flash('success', 'Element supprime avec succes.');
        }

        $this->closeDeleteModal();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteType = '';
        $this->deleteId = null;
    }

    protected function getDefaultTemplateHtml(): string
    {
        return <<<'HTML'
<table cellpadding="0" cellspacing="0" border="0" style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.4; color: #333333;">
    <tr>
        <td style="padding-right: 20px; vertical-align: top;">
            <img src="{{contact.photoUrl}}" alt="Photo" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid {{brand.primaryColor}};">
        </td>
        <td style="vertical-align: top;">
            <p style="margin: 0 0 5px 0; font-size: 18px; font-weight: bold; color: #1a1a1a;">
                {{contact.firstName}} {{contact.lastName}}
            </p>
            <p style="margin: 0 0 10px 0; font-size: 13px; color: {{brand.primaryColor}}; font-weight: 500;">
                {{contact.jobTitle}}
            </p>
            <div style="width: 60px; height: 2px; background: {{brand.primaryColor}}; margin-bottom: 10px;"></div>
            <p style="margin: 0 0 5px 0; font-size: 13px;">
                <span style="color: #666;">Tel:</span>
                <a href="tel:{{contact.mobile}}" style="color: #333; text-decoration: none;">{{contact.mobile}}</a>
            </p>
            <p style="margin: 0 0 12px 0; font-size: 13px;">
                <span style="color: #666;">Email:</span>
                <a href="mailto:{{contact.email}}" style="color: {{brand.primaryColor}}; text-decoration: none;">{{contact.email}}</a>
            </p>
            <div style="margin-top: 10px;">
                <img src="{{brand.logoUrl}}" alt="Logo" style="height: 35px; width: auto;">
            </div>
            <p style="margin: 8px 0 0 0; font-size: 11px;">
                <a href="{{brand.website}}" style="color: {{brand.primaryColor}}; text-decoration: none;">{{brand.website}}</a>
            </p>
        </td>
    </tr>
</table>
HTML;
    }

    public function render()
    {
        return view('livewire.settings.signature-settings', [
            'templates' => SignatureTemplate::with('brand')->orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'brandsForSelect' => Brand::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
