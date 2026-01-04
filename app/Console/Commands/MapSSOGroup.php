<?php

namespace App\Console\Commands;

use App\Models\SsoGroupMapping;
use Illuminate\Console\Command;

class MapSSOGroup extends Command
{
    protected $signature = 'sso:allow-group
                            {group? : Nom du groupe SSO}
                            {--disallow : Retirer l\'acces au groupe}
                            {--list : Afficher tous les groupes}';

    protected $description = 'Configure l\'acces d\'un groupe SSO a cette application';

    public function handle(): int
    {
        // Mode liste
        if ($this->option('list')) {
            return $this->listGroups();
        }

        $groupName = $this->argument('group');

        // Mode interactif si pas de groupe specifie
        if (!$groupName) {
            return $this->interactiveMode();
        }

        // Trouver le groupe
        $mapping = SsoGroupMapping::where('sso_group_name', $groupName)->first();

        if (!$mapping) {
            $this->error("Groupe '$groupName' non trouve. Executez sso:sync-groups d'abord.");
            return 1;
        }

        $isAllowed = !$this->option('disallow');
        $mapping->update(['is_allowed' => $isAllowed]);

        if ($isAllowed) {
            $this->info("Groupe '$groupName' autorise a se connecter.");
        } else {
            $this->warn("Groupe '$groupName' n'est plus autorise a se connecter.");
        }

        return 0;
    }

    protected function listGroups(): int
    {
        $mappings = SsoGroupMapping::orderBy('sso_group_name')->get();

        if ($mappings->isEmpty()) {
            $this->warn('Aucun groupe configure. Executez sso:sync-groups d\'abord.');
            return 0;
        }

        $this->table(
            ['ID SSO', 'Nom du groupe', 'Description', 'Acces autorise'],
            $mappings->map(fn($m) => [
                $m->sso_group_id,
                $m->sso_group_name,
                substr($m->sso_group_description ?? '-', 0, 40),
                $m->is_allowed ? 'Oui' : 'Non',
            ])
        );

        return 0;
    }

    protected function interactiveMode(): int
    {
        $mappings = SsoGroupMapping::orderBy('sso_group_name')->get();

        if ($mappings->isEmpty()) {
            $this->warn('Aucun groupe disponible. Executez sso:sync-groups d\'abord.');
            return 1;
        }

        $groupName = $this->choice(
            'Quel groupe voulez-vous configurer?',
            $mappings->pluck('sso_group_name')->toArray()
        );

        $action = $this->choice(
            'Que voulez-vous faire?',
            ['Autoriser l\'acces', 'Retirer l\'acces'],
            0
        );

        $isAllowed = $action === 'Autoriser l\'acces';

        $mapping = SsoGroupMapping::where('sso_group_name', $groupName)->first();
        $mapping->update(['is_allowed' => $isAllowed]);

        if ($isAllowed) {
            $this->info("Groupe '$groupName' autorise a se connecter.");
        } else {
            $this->warn("Groupe '$groupName' n'est plus autorise a se connecter.");
        }

        return 0;
    }
}
