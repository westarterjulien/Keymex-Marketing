<?php

namespace App\Livewire\Properties;

use App\Models\PropertyCommunication;
use App\Services\MongoPropertyService;
use Livewire\Component;

class PropertyIndex extends Component
{
    public string $activeTab = 'compromis';
    public string $search = '';
    public int $soldDays = 30;
    public bool $mongoDbError = false;

    // Photo modal
    public bool $showPhotoModal = false;
    public array $modalPhotos = [];
    public string $modalPropertyRef = '';

    protected MongoPropertyService $propertyService;

    protected $queryString = [
        'activeTab' => ['except' => 'compromis'],
        'soldDays' => ['except' => 30],
    ];

    public function boot(MongoPropertyService $propertyService): void
    {
        $this->propertyService = $propertyService;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->search = '';
    }

    public function toggleCommunication(string $propertyId): void
    {
        $existing = PropertyCommunication::where('property_mongo_id', $propertyId)->first();

        if ($existing) {
            $existing->delete();
        } else {
            PropertyCommunication::create([
                'property_mongo_id' => $propertyId,
                'action_type' => 'rs',
                'action_date' => now(),
                'created_by' => auth()->id(),
            ]);
        }
    }

    public function openPhotoModal(array $photos, string $propertyRef): void
    {
        $this->modalPhotos = $photos;
        $this->modalPropertyRef = $propertyRef;
        $this->showPhotoModal = true;
    }

    public function closePhotoModal(): void
    {
        $this->showPhotoModal = false;
        $this->modalPhotos = [];
        $this->modalPropertyRef = '';
    }

    public function render()
    {
        $compromisProperties = collect();
        $soldProperties = collect();
        $this->mongoDbError = false;

        try {
            if ($this->activeTab === 'compromis') {
                $compromisProperties = $this->propertyService->getCompromisProperties();

                // Filtrer les biens sans photos
                $compromisProperties = $compromisProperties->filter(fn ($p) => !empty($p['photos']));

                if ($this->search) {
                    $search = strtolower($this->search);
                    $compromisProperties = $compromisProperties->filter(function ($property) use ($search) {
                        return str_contains(strtolower($property['reference'] ?? ''), $search)
                            || str_contains(strtolower($property['address']['city'] ?? ''), $search)
                            || str_contains(strtolower($property['advisor']['name'] ?? ''), $search);
                    });
                }
            } else {
                $soldProperties = $this->propertyService->getRecentlySold($this->soldDays);

                // Filtrer les biens sans photos
                $soldProperties = $soldProperties->filter(fn ($p) => !empty($p['photos']));

                if ($this->search) {
                    $search = strtolower($this->search);
                    $soldProperties = $soldProperties->filter(function ($property) use ($search) {
                        return str_contains(strtolower($property['reference'] ?? ''), $search)
                            || str_contains(strtolower($property['address']['city'] ?? ''), $search)
                            || str_contains(strtolower($property['advisor']['name'] ?? ''), $search);
                    });
                }
            }
        } catch (\Exception $e) {
            $this->mongoDbError = true;
            \Log::warning('MongoDB connection error in PropertyIndex: ' . $e->getMessage());
        }

        // Récupérer les communications RS avec leurs dates
        $propertyIds = $compromisProperties->pluck('id')->merge($soldProperties->pluck('id'))->filter()->unique();
        $communications = PropertyCommunication::whereIn('property_mongo_id', $propertyIds)
            ->get()
            ->keyBy('property_mongo_id');
        $communicatedIds = $communications->keys()->toArray();

        $urgentCount = $compromisProperties->filter(fn ($p) => $p['is_legal_deadline_passed'])->count();
        $nearDeadlineCount = $compromisProperties->filter(function ($p) {
            if (!isset($p['dates']['legal_deadline'])) return false;
            $deadline = \Carbon\Carbon::parse($p['dates']['legal_deadline']);
            return !$p['is_legal_deadline_passed'] && $deadline->diffInDays(now()) <= 3;
        })->count();

        return view('livewire.properties.property-index', [
            'compromisProperties' => $compromisProperties,
            'soldProperties' => $soldProperties,
            'urgentCount' => $urgentCount,
            'nearDeadlineCount' => $nearDeadlineCount,
            'mongoDbError' => $this->mongoDbError,
            'communicatedIds' => $communicatedIds,
            'communications' => $communications,
        ]);
    }
}
