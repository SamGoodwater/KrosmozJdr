<?php

declare(strict_types=1);

namespace App\Console\Commands\Scrapping\Effects;

use App\Console\ArtisanExitCode;
use App\Services\Scrapping\Core\Conversion\SpellEffects\SpellAutreMappingReapplyService;
use Illuminate\Console\Command;

/**
 * Réapplique les mappings DofusDB sur les sous-effets « autre » déjà en base (sans re-import).
 *
 * @example php artisan scrapping:effects:reapply-mappings --dry-run
 * @example php artisan scrapping:effects:reapply-mappings
 */
final class ScrappingEffectsReapplyMappingsCommand extends Command
{
    protected $signature = 'scrapping:effects:reapply-mappings
                            {--dry-run : Affiche le volume corrigé sans écrire}
                            {--json : Sortie JSON}
                            {--sample-limit=20 : Nombre d\'exemples listés}';

    protected $description = 'Réapplique les mappings effectId → sous-effet sur les lignes « autre » importées';

    public function handle(SpellAutreMappingReapplyService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $asJson = (bool) $this->option('json');
        $sampleLimit = max(1, (int) $this->option('sample-limit'));

        $result = $service->reapply($dryRun, $sampleLimit);

        if ($asJson) {
            $this->line(json_encode([
                'dry_run' => $dryRun,
                ...$result,
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            return ArtisanExitCode::SUCCESS;
        }

        $prefix = $dryRun ? '[DRY-RUN] ' : '';
        $this->info($prefix.'Lignes « autre » scannées (avec effectId) : '.$result['scanned']);
        $this->info($prefix.'Lignes à mettre à jour / mises à jour : '.$result['updated']);
        if ($result['skipped_unknown_slug'] > 0) {
            $this->warn('Slug cible inconnu en BDD : '.$result['skipped_unknown_slug']);
        }
        foreach ($result['by_slug'] as $slug => $count) {
            $this->line("  → {$slug}: {$count}");
        }

        return ArtisanExitCode::SUCCESS;
    }
}
