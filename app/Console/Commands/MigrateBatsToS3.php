<?php

namespace App\Console\Commands;

use App\Models\StandaloneBat;
use App\Models\StorageSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateBatsToS3 extends Command
{
    protected $signature = 'bats:migrate-to-s3 {--dry-run : Afficher les fichiers sans les migrer}';

    protected $description = 'Migre les fichiers BAT du stockage local vers S3';

    public function handle(): int
    {
        // Vérifier que S3 est actif
        if (StorageSetting::getDisk() !== 's3') {
            $this->error('Le stockage S3 n\'est pas actif. Activez-le dans les paramètres.');
            return Command::FAILURE;
        }

        $dryRun = $this->option('dry-run');

        // Récupérer les BAT stockés en local
        $bats = StandaloneBat::where('storage_disk', 'public')
            ->orWhereNull('storage_disk')
            ->get();

        if ($bats->isEmpty()) {
            $this->info('Aucun BAT à migrer. Tous les fichiers sont déjà sur S3.');
            return Command::SUCCESS;
        }

        $this->info("Trouvé {$bats->count()} BAT(s) à migrer vers S3.");

        if ($dryRun) {
            $this->warn('Mode dry-run activé - aucune modification ne sera effectuée.');
        }

        $migrated = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($bats->count());
        $bar->start();

        foreach ($bats as $bat) {
            try {
                $localDisk = $bat->storage_disk ?? 'public';

                // Vérifier si le fichier existe en local
                if (!Storage::disk($localDisk)->exists($bat->file_path)) {
                    $this->newLine();
                    $this->warn("  Fichier non trouvé: {$bat->file_path}");
                    $errors++;
                    $bar->advance();
                    continue;
                }

                if (!$dryRun) {
                    // Lire le contenu du fichier local
                    $content = Storage::disk($localDisk)->get($bat->file_path);

                    // Uploader vers S3
                    Storage::disk('s3')->put($bat->file_path, $content);

                    // Mettre à jour le BAT
                    $bat->update(['storage_disk' => 's3']);

                    // Supprimer le fichier local
                    Storage::disk($localDisk)->delete($bat->file_path);
                }

                $migrated++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  Erreur pour BAT #{$bat->id}: {$e->getMessage()}");
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("Dry-run terminé. {$migrated} fichier(s) seraient migrés.");
        } else {
            $this->info("Migration terminée. {$migrated} fichier(s) migré(s) vers S3.");
        }

        if ($errors > 0) {
            $this->warn("{$errors} erreur(s) rencontrée(s).");
        }

        return Command::SUCCESS;
    }
}
