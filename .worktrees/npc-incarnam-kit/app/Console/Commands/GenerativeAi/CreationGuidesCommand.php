<?php

declare(strict_types=1);

namespace App\Console\Commands\GenerativeAi;

use App\Console\ArtisanExitCode;
use App\Services\GenerativeAi\CreationGuideCatalog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Dump les fiches de bonne pratique (atelier Création) pour un futur prompt de conversion.
 *
 * @example php artisan ia:creation-guides
 * @example php artisan ia:creation-guides spell --json=storage/logs/creation-guide-spell.json
 */
final class CreationGuidesCommand extends Command
{
    protected $signature = 'ia:creation-guides
        {entity? : Type (spell, monster, item, consumable, capability, trait, resource)}
        {--json= : Écrit le payload JSON (chemin fichier)}';

    protected $description = 'Fiches de bonne pratique Création (philosophie, points, limites, conseils, exemples) pour la conversion IA — sans LLM';

    public function handle(CreationGuideCatalog $catalog): int
    {
        $entityRaw = $this->argument('entity');
        $entity = is_string($entityRaw) && $entityRaw !== '' ? strtolower(trim($entityRaw)) : null;

        if ($entity !== null && ! in_array($entity, CreationGuideCatalog::CONVERSION_ENTITIES, true)) {
            $this->error('Type inconnu. Attendus : '.implode(', ', CreationGuideCatalog::CONVERSION_ENTITIES));

            return ArtisanExitCode::FAILURE;
        }

        $bundle = $entity === null
            ? $catalog->promptBundle()
            : [$entity => (string) $catalog->promptFor($entity)];

        foreach ($bundle as $key => $prompt) {
            $this->info(sprintf('%s : %d caractères', $key, mb_strlen($prompt)));
        }

        $jsonPath = $this->option('json');
        if (is_string($jsonPath) && $jsonPath !== '') {
            $dir = dirname($jsonPath);
            if ($dir !== '.' && $dir !== '' && ! is_dir($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            File::put($jsonPath, json_encode($bundle, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
            $this->info('JSON : '.$jsonPath);
        } elseif ($entity !== null) {
            $this->newLine();
            $this->line($bundle[$entity] ?? '');
        }

        return ArtisanExitCode::SUCCESS;
    }
}
