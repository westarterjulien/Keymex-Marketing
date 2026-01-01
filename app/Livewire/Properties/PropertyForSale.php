<?php

namespace App\Livewire\Properties;

use App\Models\PropertyCommunication;
use App\Services\MongoPropertyService;
use Livewire\Component;

class PropertyForSale extends Component
{
    public string $search = '';
    public bool $mongoDbError = false;

    // Photo modal
    public bool $showPhotoModal = false;
    public array $modalPhotos = [];
    public string $modalPropertyRef = '';

    protected MongoPropertyService $propertyService;

    public function boot(MongoPropertyService $propertyService): void
    {
        $this->propertyService = $propertyService;
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
        $properties = collect();
        $this->mongoDbError = false;

        try {
            $properties = $this->propertyService->getPropertiesForSale();

            // Filtrer les biens sans photos
            $properties = $properties->filter(fn ($p) => !empty($p['photos']));

            if ($this->search) {
                $search = strtolower($this->search);
                $properties = $properties->filter(function ($property) use ($search) {
                    return str_contains(strtolower($property['reference'] ?? ''), $search)
                        || str_contains(strtolower($property['address']['city'] ?? ''), $search)
                        || str_contains(strtolower($property['advisor']['name'] ?? ''), $search);
                });
            }
        } catch (\Exception $e) {
            $this->mongoDbError = true;
            \Log::warning('MongoDB connection error in PropertyForSale: ' . $e->getMessage());
        }

        // Récupérer les communications RS avec leurs dates
        $propertyIds = $properties->pluck('id')->filter()->unique();
        $communications = PropertyCommunication::whereIn('property_mongo_id', $propertyIds)
            ->get()
            ->keyBy('property_mongo_id');
        $communicatedIds = $communications->keys()->toArray();

        return view('livewire.properties.property-for-sale', [
            'properties' => $properties,
            'mongoDbError' => $this->mongoDbError,
            'communicatedIds' => $communicatedIds,
            'communications' => $communications,
        ]);
    }
}
