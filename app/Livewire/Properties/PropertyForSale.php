<?php

namespace App\Livewire\Properties;

use App\Models\PropertyCommunication;
use App\Services\MongoPropertyService;
use Carbon\Carbon;
use Livewire\Component;

class PropertyForSale extends Component
{
    public string $search = '';
    public bool $mongoDbError = false;

    // Communication RS modal
    public bool $showCommunicationModal = false;
    public string $communicationPropertyId = '';
    public string $communicationPropertyRef = '';
    public string $communicationDate = '';
    public bool $hasCommunication = false;

    // Cached data
    protected ?object $cachedProperties = null;

    protected MongoPropertyService $propertyService;

    public function boot(MongoPropertyService $propertyService): void
    {
        $this->propertyService = $propertyService;
    }

    public function openCommunicationModal(string $propertyId, string $propertyRef): void
    {
        $this->communicationPropertyId = $propertyId;
        $this->communicationPropertyRef = $propertyRef;

        $existing = PropertyCommunication::where('property_mongo_id', $propertyId)->first();
        if ($existing) {
            $this->communicationDate = $existing->action_date->format('Y-m-d');
            $this->hasCommunication = true;
        } else {
            $this->communicationDate = now()->format('Y-m-d');
            $this->hasCommunication = false;
        }

        $this->showCommunicationModal = true;
    }

    public function closeCommunicationModal(): void
    {
        $this->showCommunicationModal = false;
        $this->communicationPropertyId = '';
        $this->communicationPropertyRef = '';
        $this->communicationDate = '';
        $this->hasCommunication = false;
    }

    public function saveCommunication(): void
    {
        $date = Carbon::parse($this->communicationDate);

        PropertyCommunication::updateOrCreate(
            ['property_mongo_id' => $this->communicationPropertyId],
            [
                'action_type' => 'rs',
                'action_date' => $date,
                'created_by' => auth()->id(),
            ]
        );

        $this->closeCommunicationModal();
    }

    public function deleteCommunication(): void
    {
        PropertyCommunication::where('property_mongo_id', $this->communicationPropertyId)->delete();
        $this->closeCommunicationModal();
    }

    public function render()
    {
        $properties = collect();
        $this->mongoDbError = false;

        try {
            // Only fetch from MongoDB if not cached
            if ($this->cachedProperties === null) {
                $this->cachedProperties = $this->propertyService->getPropertiesForSale();
                $this->cachedProperties = $this->cachedProperties->filter(fn ($p) => !empty($p['photos']));
            }

            $properties = $this->cachedProperties;

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
