<?php

namespace App\Console\Commands;

use App\Models\SsoGroupMapping;
use Illuminate\Console\Command;

class MapSSOGroup extends Command
{
    protected $signature = 'sso:map-group
                            {group? : Nom du groupe SSO}
                            {--role= : Role local a attribuer (super-admin, admin, editor, viewer)}
                            {--priority= : Priorite du mapping (0-100)}
                            {--list : Afficher tous les mappings}';

    protected $description = 'Configure le mapping entre un groupe SSO et un role local';

    public function handle(): int
    {
        // Mode liste
        if ($this->option('list')) {
            return $this->listMappings();
        }

        $groupName = $this->argument('group');
        $role = $this->option('role');
        $priority = $this->option('priority');

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

        // Demander le role si non specifie
        if (!$role) {
            $role = $this->choice(
                'Quel role attribuer a ce groupe?',
                array_keys(SsoGroupMapping::ROLES),
                'viewer'
            );
        }

        // Valider le role
        if (!array_key_exists($role, SsoGroupMapping::ROLES)) {
            $this->error("Role invalide. Roles disponibles: " . implode(', ', array_keys(SsoGroupMapping::ROLES)));
            return 1;
        }

        // Definir la priorite par defaut si non specifiee
        if ($priority === null) {
            $priority = SsoGroupMapping::ROLES[$role];
        }

        $mapping->update([
            'local_role' => $role,
            'priority' => (int) $priority,
        ]);

        $this->info("Groupe '$groupName' mappe au role '$role' avec priorite $priority");

        return 0;
    }

    protected function listMappings(): int
    {
        $mappings = SsoGroupMapping::orderByDesc('priority')->get();

        if ($mappings->isEmpty()) {
            $this->warn('Aucun mapping configure. Executez sso:sync-groups d\'abord.');
            return 0;
        }

        $this->table(
            ['ID SSO', 'Nom du groupe', 'Description', 'Role local', 'Priorite'],
            $mappings->map(fn($m) => [
                $m->sso_group_id,
                $m->sso_group_name,
                substr($m->sso_group_description ?? '-', 0, 30),
                $m->local_role ?? '(non configure)',
                $m->priority,
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

        $role = $this->choice(
            'Quel role attribuer?',
            array_keys(SsoGroupMapping::ROLES),
            'viewer'
        );

        $priority = $this->ask('Priorite (defaut: ' . SsoGroupMapping::ROLES[$role] . ')', SsoGroupMapping::ROLES[$role]);

        $mapping = SsoGroupMapping::where('sso_group_name', $groupName)->first();
        $mapping->update([
            'local_role' => $role,
            'priority' => (int) $priority,
        ]);

        $this->info("Groupe '$groupName' mappe au role '$role' avec priorite $priority");

        return 0;
    }
}
