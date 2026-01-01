<?php

namespace App\Livewire\StandaloneBat;

use App\Models\Category;
use App\Models\Format;
use App\Models\StandaloneBat;
use App\Models\SupportType;
use App\Services\MongoAdvisorService;
use Livewire\Component;
use Livewire\WithFileUploads;

class BatCreate extends Component
{
    use WithFileUploads;

    // Advisor
    public string $advisorSearch = '';
    public array $advisorResults = [];
    public ?array $selectedAdvisor = null;
    public bool $showAdvisorDropdown = false;

    // BAT details
    public string $title = '';
    public string $description = '';
    public ?int $supportTypeId = null;
    public ?int $formatId = null;
    public ?int $categoryId = null;
    public string $grammage = '';
    public ?string $price = null;
    public string $deliveryTime = '';
    public ?int $quantity = null;
    public $batFile;

    // Formats disponibles selon le type de support
    public array $availableFormats = [];

    protected MongoAdvisorService $advisorService;

    public function boot(MongoAdvisorService $advisorService): void
    {
        $this->advisorService = $advisorService;
    }

    public function updatedAdvisorSearch(): void
    {
        if (strlen($this->advisorSearch) >= 2) {
            try {
                $this->advisorResults = $this->advisorService->search($this->advisorSearch, 10)->toArray();
                $this->showAdvisorDropdown = true;
            } catch (\Exception $e) {
                $this->advisorResults = [];
            }
        } else {
            $this->advisorResults = [];
            $this->showAdvisorDropdown = false;
        }
    }

    public function selectAdvisor(int $index): void
    {
        if (isset($this->advisorResults[$index])) {
            $this->selectedAdvisor = $this->advisorResults[$index];
            $this->advisorSearch = $this->selectedAdvisor['fullname'];
            $this->showAdvisorDropdown = false;
            $this->advisorResults = [];
        }
    }

    public function clearAdvisor(): void
    {
        $this->selectedAdvisor = null;
        $this->advisorSearch = '';
        $this->advisorResults = [];
    }

    public function updatedSupportTypeId(): void
    {
        $this->formatId = null;
        $this->availableFormats = [];

        if ($this->supportTypeId) {
            $this->availableFormats = Format::where('support_type_id', $this->supportTypeId)
                ->active()
                ->orderBy('sort_order')
                ->get()
                ->toArray();
        }
    }

    public function save()
    {
        $this->validate([
            'batFile' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ], [
            'batFile.required' => 'Le fichier BAT est obligatoire.',
            'batFile.mimes' => 'Le fichier doit etre un PDF ou une image (JPG, PNG).',
            'batFile.max' => 'Le fichier ne doit pas depasser 20 Mo.',
        ]);

        if (!$this->selectedAdvisor) {
            $this->addError('advisorSearch', 'Veuillez selectionner un conseiller.');
            return;
        }

        // Store file
        $fileName = 'bat_' . time() . '_' . uniqid() . '.' . $this->batFile->extension();
        $filePath = $this->batFile->storeAs('standalone-bats', $fileName, 'public');

        // Create BAT
        $bat = StandaloneBat::create([
            'advisor_mongo_id' => $this->selectedAdvisor['id'],
            'advisor_name' => $this->selectedAdvisor['fullname'],
            'advisor_email' => $this->selectedAdvisor['email'],
            'advisor_agency' => $this->selectedAdvisor['agency'] ?? null,
            'support_type_id' => $this->supportTypeId ?: null,
            'format_id' => $this->formatId ?: null,
            'category_id' => $this->categoryId ?: null,
            'file_path' => $filePath,
            'file_name' => $this->batFile->getClientOriginalName(),
            'file_mime' => $this->batFile->getMimeType(),
            'title' => $this->title ?: null,
            'description' => $this->description ?: null,
            'grammage' => $this->grammage ?: null,
            'price' => $this->price ? (float) $this->price : null,
            'delivery_time' => $this->deliveryTime ?: null,
            'quantity' => $this->quantity ?: null,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        // Log creation
        $bat->logEvent('created');

        session()->flash('success', 'BAT cree avec succes. Vous pouvez maintenant l\'envoyer pour validation.');

        return $this->redirect(route('standalone-bats.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.standalone-bat.bat-create', [
            'supportTypes' => SupportType::active()->orderBy('sort_order')->get(),
            'categories' => Category::active()->orderBy('sort_order')->get(),
        ]);
    }
}
