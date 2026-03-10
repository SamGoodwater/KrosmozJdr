<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Entity\Breed;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Mise à jour des entités en base dont auto_update = true.
 *
 * Ne crée pas de nouvelles entités ; met à jour uniquement celles déjà présentes
 * et marquées pour mise à jour automatique.
 *
 * Compatible exécution longue (set_time_limit(0), DB::reconnect entre chunks).
 * Vide la queue avant l'update. Notifie les admin/super_admin à la fin.
 *
 * @example php artisan project:update
 * @example php artisan project:update --entity=monster --dry-run
 */
class ProjectUpdateCommand extends Command
{
    protected $signature = 'project:update
        {--entity= : Limiter à une entité (class,spell,monster,resource,consumable,item)}
        {--noimage : Ne pas télécharger les images}
        {--skip-cache : Ignorer le cache HTTP}
        {--dry-run : Simuler sans écrire}
        {--skip-clear-queue : Ne pas vider la queue avant la mise à jour}
        {--skip-notify : Ne pas notifier les admin à la fin}';

    protected $description = 'Met à jour les entités en base avec auto_update=true depuis DofusDB';

    /** Mapping entité Krosmoz → alias scrapping:run et modèle. */
    private const ENTITY_CONFIG = [
        'class' => [
            'alias' => 'class',
            'model' => Breed::class,
            'idColumn' => 'dofusdb_id',
        ],
        'spell' => [
            'alias' => 'spell',
            'model' => Spell::class,
            'idColumn' => 'dofusdb_id',
        ],
        'monster' => [
            'alias' => 'monster',
            'model' => Monster::class,
            'idColumn' => 'dofusdb_id',
        ],
        'resource' => [
            'alias' => 'resource',
            'model' => Resource::class,
            'idColumn' => 'dofusdb_id',
        ],
        'consumable' => [
            'alias' => 'consumable',
            'model' => Consumable::class,
            'idColumn' => 'dofusdb_id',
        ],
        'item' => [
            'alias' => 'item',
            'model' => Item::class,
            'idColumn' => 'dofusdb_id',
        ],
    ];

    private const IDS_CHUNK_SIZE = 100;

    public function handle(): int
    {
        set_time_limit(0);
        $startedAt = microtime(true);

        $this->info('=== Mise à jour des données (auto_update) ===');
        $this->newLine();

        if (! (bool) $this->option('skip-clear-queue')) {
            $this->clearQueue();
        }

        $dryRun = (bool) $this->option('dry-run');
        if (! $dryRun) {
            $this->line('  → effects:rebuild-signatures (avant update)');
            Artisan::call('effects:rebuild-signatures');
            $this->output->write(Artisan::output());
        }

        $entityFilter = (string) $this->option('entity');
        $entities = $entityFilter !== ''
            ? array_values(array_filter(array_map('trim', explode(',', strtolower($entityFilter)))))
            : array_keys(self::ENTITY_CONFIG);

        $updated = 0;
        $errors = 0;

        foreach ($entities as $entity) {
            if (! isset(self::ENTITY_CONFIG[$entity])) {
                $this->warn("Entité inconnue : {$entity}");
                continue;
            }

            $config = self::ENTITY_CONFIG[$entity];
            $ids = $this->getAutoUpdateIds($config);
            if ($ids === []) {
                $this->line("  {$entity} : aucun ID à mettre à jour.");
                continue;
            }

            $this->line("  {$entity} : " . count($ids) . ' entité(s) à mettre à jour.');
            $chunks = array_chunk($ids, self::IDS_CHUNK_SIZE);

            foreach ($chunks as $i => $chunk) {
                $idsStr = implode(',', $chunk);
                $scrapArgs = [
                    '--entity' => $config['alias'],
                    '--ids' => $idsStr,
                    '--update-mode' => 'auto_update',
                    '--skip-existing' => true,
                ];
                if ((bool) $this->option('noimage')) {
                    $scrapArgs['--noimage'] = true;
                }
                if ((bool) $this->option('skip-cache')) {
                    $scrapArgs['--skip-cache'] = true;
                }
                if ((bool) $this->option('dry-run')) {
                    $scrapArgs['--simulate'] = true;
                }

                $code = Artisan::call('scrapping:run', $scrapArgs);
                $this->output->write(Artisan::output());
                if ($code !== 0) {
                    $errors++;
                    $this->warn("  Avertissement : chunk " . ($i + 1) . " de {$entity} a échoué.");
                } else {
                    $updated += count($chunk);
                }
                DB::reconnect();
            }
            $this->newLine();
        }

        if (! $dryRun) {
            $this->line('  → effects:rebuild-signatures (après update)');
            Artisan::call('effects:rebuild-signatures');
            $this->output->write(Artisan::output());
        }

        $duration = microtime(true) - $startedAt;
        $finishedAt = now()->format('d/m/Y à H:i:s');
        $success = $errors === 0;

        if (! (bool) $this->option('skip-notify')) {
            $message = $success
                ? "{$updated} entité(s) mise(s) à jour."
                : "{$errors} erreur(s), {$updated} entité(s) traité(es).";
            NotificationService::notifyProjectMaintenance(
                'update',
                $success,
                $duration,
                $finishedAt,
                $message,
            );
        }

        $this->info("=== Mise à jour terminée : {$updated} entité(s) traité(es) ===");
        if ($errors > 0) {
            $this->warn("{$errors} erreur(s) rencontrée(s).");
        }

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return list<int>
     */
    private function getAutoUpdateIds(array $config): array
    {
        $model = $config['model'];
        $idCol = $config['idColumn'];

        $query = $model::query()
            ->where('auto_update', true)
            ->whereNotNull($idCol)
            ->where($idCol, '!=', '');

        $ids = $query->pluck($idCol)->map(function ($v) {
            $n = (int) $v;
            return $n > 0 ? $n : null;
        })->filter()->unique()->values()->all();

        return array_values(array_map('intval', $ids));
    }

    private function clearQueue(): void
    {
        $connection = Config::get('queue.default');
        if ($connection === 'sync') {
            return;
        }
        $this->line('  → Nettoyage de la queue (jobs en attente + failed)');
        Artisan::call('queue:clear', [$connection, '--force' => true]);
        Artisan::call('queue:flush');
        $this->output->write(Artisan::output());
    }
}
