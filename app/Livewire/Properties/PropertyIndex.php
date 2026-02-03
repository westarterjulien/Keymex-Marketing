<?php

namespace App\Livewire\Properties;

use App\Models\PropertyCommunication;
use App\Services\MongoPropertyService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class PropertyIndex extends Component
{
    public string $activeTab = 'compromis';
    public string $search = '';
    public int $soldDays = 30;
    public bool $mongoDbError = false;

    // Communication RS modal
    public bool $showCommunicationModal = false;
    public string $communicationPropertyId = '';
    public string $communicationPropertyRef = '';
    public string $communicationDate = '';
    public bool $hasCommunication = false;

    // Cached data to avoid refetching on every render
    protected ?object $cachedCompromisProperties = null;
    protected ?object $cachedSoldProperties = null;
    protected ?string $cachedTab = null;

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
        \Log::info('saveCommunication called', [
            'propertyId' => $this->communicationPropertyId,
            'date' => $this->communicationDate,
        ]);

        if (empty($this->communicationPropertyId)) {
            \Log::error('communicationPropertyId is empty!');
            return;
        }

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
        $compromisProperties = collect();
        $soldProperties = collect();
        $this->mongoDbError = false;

        // Only fetch from MongoDB if tab changed or data not cached
        $needsFetch = $this->cachedTab !== $this->activeTab;

        try {
            if ($this->activeTab === 'compromis') {
                if ($needsFetch || $this->cachedCompromisProperties === null) {
                    $this->cachedCompromisProperties = $this->propertyService->getCompromisProperties();
                    $this->cachedCompromisProperties = $this->cachedCompromisProperties->filter(fn ($p) => !empty($p['photos']));
                    $this->cachedTab = $this->activeTab;
                }

                $compromisProperties = $this->cachedCompromisProperties;

                if ($this->search) {
                    $search = strtolower($this->search);
                    $compromisProperties = $compromisProperties->filter(function ($property) use ($search) {
                        return str_contains(strtolower($property['reference'] ?? ''), $search)
                            || str_contains(strtolower($property['address']['city'] ?? ''), $search)
                            || str_contains(strtolower($property['advisor']['name'] ?? ''), $search);
                    });
                }
            } elseif ($this->activeTab === 'vendus') {
                if ($needsFetch || $this->cachedSoldProperties === null) {
                    $this->cachedSoldProperties = $this->propertyService->getSoldProperties(100);
                    $this->cachedSoldProperties = $this->cachedSoldProperties->filter(fn ($p) => !empty($p['photos']));
                    $this->cachedTab = $this->activeTab;
                }

                $soldProperties = $this->cachedSoldProperties;

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
