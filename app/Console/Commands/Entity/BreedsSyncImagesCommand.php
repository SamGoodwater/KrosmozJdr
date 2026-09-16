<?php

declare(strict_types=1);

namespace App\Console\Commands\Entity;

use App\Console\ArtisanExitCode;
use App\Models\Entity\Breed;
use App\Services\BibliothequeEntityPageService;
use App\Support\BreedImagePaths;
use Illuminate\Console\Command;

/**
 * Aligne les colonnes image des classes sur `storage/app/public/images/breeds/{slug}/`.
 */
final class BreedsSyncImagesCommand extends Command
{
    protected $signature = 'breeds:sync-images
                            {--dry-run : N\'écrit pas en base, affiche seulement le volume}
                            {--skip-pages : Ne pas resynchroniser les icônes du menu Bibliothèques}';

    protected $description = 'Aligne les visuels des classes (symboles, logos, full) sur les fichiers locaux';

    public function handle(BibliothequeEntityPageService $pages): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $scanned = 0;
        $updated = 0;
        $unchanged = 0;
        $skipped = 0;

        Breed::query()->orderBy('id')->each(function (Breed $breed) use ($dryRun, &$scanned, &$updated, &$unchanged, &$skipped): void {
            $scanned++;
            $slug = BreedImagePaths::slugFor($breed);
            if ($slug === null) {
                $skipped++;

                return;
            }

            $next = BreedImagePaths::columnValuesForSlug($slug);
            $dirty = false;
            foreach ($next as $column => $value) {
                $current = $breed->getAttribute($column);
                $currentStr = is_string($current) ? $current : null;
                if ($currentStr !== $value) {
                    $dirty = true;
                    break;
                }
            }

            if (! $dirty) {
                $unchanged++;

                return;
            }

            if (! $dryRun) {
                $breed->fill($next);
                $breed->save();
            }
            $updated++;
        });

        $this->info(sprintf(
            'Classes scannées : %d. Mises à jour : %d. Inchangées : %d. Sans dossier : %d.',
            $scanned,
            $updated,
            $unchanged,
            $skipped
        ));

        if ($dryRun) {
            $this->warn('Mode --dry-run : aucune écriture. Relancez sans --dry-run pour appliquer.');

            return ArtisanExitCode::SUCCESS;
        }

        if (! $this->option('skip-pages') && $updated > 0) {
            $pages->syncAll();
            $this->info('Menu Bibliothèques resynchronisé.');
        }

        return ArtisanExitCode::SUCCESS;
    }
}
