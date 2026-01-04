<?php

namespace App\Console\Commands;

use App\Models\SsoGroupMapping;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncSSOGroups extends Command
{
    protected $signature = 'sso:sync-groups';
    protected $description = 'Synchronise les groupes SSO disponibles depuis le portail KEYMEX';

    public function handle(): int
    {
        $this->info('Recuperation des groupes depuis le portail SSO...');

        $response = Http::get(config('services.keymex.url') . '/api/oauth/groups', [
            'client_id' => config('services.keymex.client_id'),
            'client_secret' => config('services.keymex.client_secret'),
        ]);

        if (!$response->successful()) {
            $this->error('Erreur lors de la recuperation des groupes: ' . $response->body());
            return 1;
        }

        $data = $response->json();
        $groups = $data['groups'] ?? [];

        if (empty($groups)) {
            $this->warn('Aucun groupe trouve');
            return 0;
        }

        $this->info('Synchronisation de ' . count($groups) . ' groupes...');

        $bar = $this->output->createProgressBar(count($groups));
        $bar->start();

        foreach ($groups as $group) {
            SsoGroupMapping::updateOrCreate(
                ['sso_group_id' => $group['id']],
                [
                    'sso_group_name' => $group['name'],
                    'sso_group_description' => $group['description'] ?? null,
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Afficher le tableau des groupes
        $this->newLine();
        $this->info('Groupes synchronises:');

        $mappings = SsoGroupMapping::orderBy('sso_group_name')->get();
        $this->table(
            ['ID SSO', 'Nom', 'Acces autorise'],
            $mappings->map(fn($m) => [
                $m->sso_group_id,
                $m->sso_group_name,
                $m->is_allowed ? 'Oui' : 'Non',
            ])
        );

        $this->newLine();
        $this->info('Utilisez la commande sso:allow-group pour autoriser un groupe.');

        return 0;
    }
}
