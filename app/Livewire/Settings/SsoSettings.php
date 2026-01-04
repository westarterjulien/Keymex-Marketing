<?php

namespace App\Livewire\Settings;

use App\Models\SsoGroupMapping;
use App\Services\KeymexSSOService;
use Livewire\Component;

class SsoSettings extends Component
{
    public array $groups = [];
    public bool $syncing = false;
    public ?string $syncMessage = null;
    public ?string $syncError = null;

    public function mount()
    {
        $this->loadGroups();
    }

    public function loadGroups()
    {
        $this->groups = SsoGroupMapping::orderBy('sso_group_name')
            ->get()
            ->map(fn($g) => [
                'id' => $g->id,
                'sso_group_id' => $g->sso_group_id,
                'name' => $g->sso_group_name,
                'description' => $g->sso_group_description,
                'is_allowed' => $g->is_allowed,
            ])
            ->toArray();
    }

    public function syncGroups()
    {
        $this->syncing = true;
        $this->syncMessage = null;
        $this->syncError = null;

        try {
            $sso = app(KeymexSSOService::class);
            $groups = $sso->getGroups();

            if (empty($groups)) {
                $this->syncError = 'Aucun groupe recupere depuis le portail SSO.';
                $this->syncing = false;
                return;
            }

            $syncedCount = 0;
            foreach ($groups as $group) {
                SsoGroupMapping::updateOrCreate(
                    ['sso_group_id' => $group['id']],
                    [
                        'sso_group_name' => $group['name'],
                        'sso_group_description' => $group['description'] ?? null,
                    ]
                );
                $syncedCount++;
            }

            $this->syncMessage = "$syncedCount groupe(s) synchronise(s) depuis le portail SSO.";
            $this->loadGroups();
        } catch (\Exception $e) {
            $this->syncError = 'Erreur de synchronisation: ' . $e->getMessage();
        }

        $this->syncing = false;
    }

    public function toggleAllowed(int $groupId)
    {
        $mapping = SsoGroupMapping::find($groupId);
        if ($mapping) {
            $mapping->update(['is_allowed' => !$mapping->is_allowed]);
            $this->loadGroups();
        }
    }

    public function render()
    {
        return view('livewire.settings.sso-settings');
    }
}
