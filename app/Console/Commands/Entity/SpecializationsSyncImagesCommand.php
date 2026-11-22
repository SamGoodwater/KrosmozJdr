<?php

declare(strict_types=1);

namespace App\Console\Commands\Entity;

use App\Console\ArtisanExitCode;
use App\Models\Entity\Specialization;
use App\Services\PageService;
use App\Support\SpecializationImagePaths;
use Illuminate\Console\Command;

/**
 * Aligne `specializations.image` sur `storage/app/public/images/specializations/{slug}.png`.
 */
final class SpecializationsSyncImagesCommand extends Command
{
    protected $signature = 'specializations:sync-images
                            {--dry-run : N\'écrit pas en base, affiche seulement le volume}';

    protected $description = 'Aligne les icônes des spécialisations sur les fichiers locaux';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $scanned = 0;
        $updated = 0;
        $unchanged = 0;
        $skipped = 0;

        Specialization::query()->orderBy('id')->each(function (Specialization $specialization) use ($dryRun, &$scanned, &$updated, &$unchanged, &$skipped): void {
            $scanned++;
            if (SpecializationImagePaths::slugFromName((string) $specialization->name) === null) {
                $skipped++;

                return;
            }

            if (! SpecializationImagePaths::assignIfMissing($specialization)) {
                $unchanged++;

                return;
            }

            if (! $dryRun) {
                $specialization->save();
            }
            $updated++;
            $this->line($specialization->name.' → '.$specialization->image);
        });

        $this->info(sprintf(
            'Spécialisations scannées : %d. Mises à jour : %d. Inchangées : %d. Sans icône connue : %d.',
            $scanned,
            $updated,
            $unchanged,
            $skipped
        ));

        if ($dryRun) {
            $this->warn('Mode --dry-run : aucune écriture. Relancez sans --dry-run pour appliquer.');

            return ArtisanExitCode::SUCCESS;
        }

        if ($updated > 0) {
            PageService::clearMenuCache();
            $this->info('Cache du menu invalidé.');
        }

        return ArtisanExitCode::SUCCESS;
    }
}
