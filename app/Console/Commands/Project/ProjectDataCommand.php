<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\NormalizesProjectSyncEntities;
use App\Console\Concerns\RunsBibliothequeEntityPagesSync;
use App\Models\Entity\Breed;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Services\NotificationService;
use Database\Seeders\Type\SpellTypeSeeder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sync DofusDB : catalogue (types / races) et fiches en base avec auto_update=true.
 *
 * @example php artisan project:data sync
 * @example php artisan project:data sync --entity=monster --dry-run
 */
class ProjectDataCommand extends Command
{
    use NormalizesProjectSyncEntities;
    use RunsBibliothequeEntityPagesSync;

    protected $signature = 'project:data
        {action : sync (catalogue et/ou entités auto_update)}
        {--noimage : Pas de téléchargement d’images}
        {--skip-cache : Ignorer le cache HTTP scrapping}
        {--entity= : Entités (virgules) : breed|class, spell, monster, panoply, resource, item, consumable}
        {--type= : Catalogue (virgules) : all | monster | resource | consumable | item | equipment | spell}
        {--races : Raccourci pour --type=monster}
        {--lang=fr : Langue DofusDB pour types/races}
        {--dry-run : Simuler sans écrire}
        {--skip-clear-queue : Ne pas vider la queue avant sync}
        {--skip-notify : Ne pas notifier les admin}';

    protected $description = 'Données DofusDB : sync catalogue et/ou fiches auto_update';

    /** @var array<string, array{alias: string, model: class-string<Model>, idColumn: string}> */
    private const ENTITY_CONFIG = [
        'class' => ['alias' => 'class', 'model' => Breed::class, 'idColumn' => 'dofusdb_id'],
        'spell' => ['alias' => 'spell', 'model' => Spell::class, 'idColumn' => 'dofusdb_id'],
        'monster' => ['alias' => 'monster', 'model' => Monster::class, 'idColumn' => 'dofusdb_id'],
        'resource' => ['alias' => 'resource', 'model' => Resource::class, 'idColumn' => 'dofusdb_id'],
        'consumable' => ['alias' => 'consumable', 'model' => Consumable::class, 'idColumn' => 'dofusdb_id'],
        'item' => ['alias' => 'item', 'model' => Item::class, 'idColumn' => 'dofusdb_id'],
        'panoply' => ['alias' => 'panoply', 'model' => Panoply::class, 'idColumn' => 'dofusdb_id'],
    ];

    private const IDS_CHUNK_SIZE = 100;

    public function handle(): int
    {
        $action = strtolower(trim((string) $this->argument('action')));

        return match ($action) {
            'sync' => $this->runSync(),
            default => $this->invalidAction($action),
        };
    }

    private function runSync(): int
    {
        set_time_limit(0);
        $startedAt = microtime(true);

        $catalogCode = $this->runCatalogSync();
        if ($catalogCode !== ArtisanExitCode::SUCCESS) {
            return $catalogCode;
        }

        $errors = 0;
        $updated = 0;

        if ($this->shouldRunEntitySyncAfterCatalog()) {
            $entityResult = $this->runEntitySync();
            $errors += $entityResult['errors'];
            $updated += $entityResult['updated'];
            if ($entityResult['fatal']) {
                return ArtisanExitCode::FAILURE;
            }
        }

        $dryRun = (bool) $this->option('dry-run');
        if (! $dryRun) {
            $this->newLine();
            $this->info('Synchronisation menu Bibliothèques (classes / spécialisations)');
            if (! $this->runBibliothequeEntityPagesSync()) {
                $errors++;
            }
        }

        $duration = microtime(true) - $startedAt;
        $finishedAt = now()->format('d/m/Y à H:i:s');
        $success = $errors === 0;

        if (! (bool) $this->option('skip-notify') && $this->shouldRunEntitySyncAfterCatalog()) {
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

        return $errors > 0 ? ArtisanExitCode::FAILURE : ArtisanExitCode::SUCCESS;
    }

    /**
     * @return array{updated: int, errors: int, fatal: bool}
     */
    private function runEntitySync(): array
    {
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
            ? $this->normalizeEntityCsvToList($entityFilter)
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

            $this->line("  {$entity} : ".count($ids).' entité(s) à mettre à jour.');
            $chunks = array_chunk($ids, self::IDS_CHUNK_SIZE);

            foreach ($chunks as $i => $chunk) {
                $scrapArgs = [
                    '--entity' => $config['alias'],
                    '--ids' => implode(',', $chunk),
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

                $code = $this->call('scrapping:run', $scrapArgs);
                if ($code !== 0) {
                    $errors++;
                    $this->warn('  Avertissement : chunk '.($i + 1)." de {$entity} a échoué.");
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

        $this->info("=== Mise à jour terminée : {$updated} entité(s) traité(es) ===");

        return ['updated' => $updated, 'errors' => $errors, 'fatal' => false];
    }

    /**
     * @param  array{alias: string, model: class-string<Model>, idColumn: string}  $config
     * @return list<int>
     */
    private function getAutoUpdateIds(array $config): array
    {
        $model = $config['model'];
        $instance = new $model;
        $table = $instance->getTable();
        $idCol = $config['idColumn'];

        if (! Schema::hasColumn($table, 'auto_update')) {
            $this->warn("  Table « {$table} » : pas de colonne auto_update — aucun ID pour ce sync.");

            return [];
        }

        $ids = $model::query()
            ->where('auto_update', true)
            ->whereNotNull($idCol)
            ->where($idCol, '!=', '')
            ->pluck($idCol)
            ->map(function ($v) {
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

    private function shouldRunEntitySyncAfterCatalog(): bool
    {
        if (! $this->hasCatalogOptions()) {
            return true;
        }

        return trim((string) $this->option('entity')) !== '';
    }

    private function hasCatalogOptions(): bool
    {
        if ((bool) $this->option('races')) {
            return true;
        }

        return trim((string) $this->option('type')) !== '';
    }

    private function runCatalogSync(): int
    {
        $tokens = $this->collectCatalogTokens();
        if ($tokens === []) {
            return ArtisanExitCode::SUCCESS;
        }

        if (! in_array('all', $tokens, true)) {
            $validCatalog = ['monster', 'spell', 'resource', 'consumable', 'item', 'equipment'];
            $hasKnown = false;
            foreach ($tokens as $t) {
                if (in_array($t, $validCatalog, true)) {
                    $hasKnown = true;
                    break;
                }
            }
            if (! $hasKnown) {
                $this->error('Valeurs catalogue inconnues. Utilisez : all, monster, spell, resource, consumable, item, equipment (ou --races).');

                return ArtisanExitCode::FAILURE;
            }
        }

        $skipCache = (bool) $this->option('skip-cache');
        $lang = (string) $this->option('lang');
        $catalogArgs = [
            '--skip-cache' => $skipCache,
            '--lang' => $lang,
        ];

        if (in_array('all', $tokens, true)) {
            $code = $this->call('scrapping:types:seed', $catalogArgs);
            if ($code !== ArtisanExitCode::SUCCESS) {
                return $code;
            }
            $code = $this->call('scrapping:races:seed', $catalogArgs);
            if ($code !== ArtisanExitCode::SUCCESS) {
                return $code;
            }
            $seedCode = Artisan::call('db:seed', [
                '--class' => SpellTypeSeeder::class,
                '--force' => true,
            ]);
            $this->output->write(Artisan::output());

            return $seedCode === 0 ? ArtisanExitCode::SUCCESS : ArtisanExitCode::FAILURE;
        }

        $wantMonster = in_array('monster', $tokens, true);
        $wantSpellType = in_array('spell', $tokens, true);

        $itemKeys = [];
        foreach ($tokens as $t) {
            if (in_array($t, ['resource', 'consumable', 'item', 'equipment'], true)) {
                $k = $t === 'equipment' ? 'item' : $t;
                if (! in_array($k, $itemKeys, true)) {
                    $itemKeys[] = $k;
                }
            }
        }

        if ($itemKeys !== []) {
            $code = $this->call('scrapping:types:seed', array_merge($catalogArgs, [
                '--only' => implode(',', $itemKeys),
            ]));
            if ($code !== ArtisanExitCode::SUCCESS) {
                return $code;
            }
        }

        if ($wantMonster) {
            $code = $this->call('scrapping:races:seed', $catalogArgs);
            if ($code !== ArtisanExitCode::SUCCESS) {
                return $code;
            }
        }

        if ($wantSpellType) {
            $code = Artisan::call('db:seed', [
                '--class' => SpellTypeSeeder::class,
                '--force' => true,
            ]);
            $this->output->write(Artisan::output());
            if ($code !== 0) {
                return ArtisanExitCode::FAILURE;
            }
        }

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function collectCatalogTokens(): array
    {
        $raw = [];
        if ((bool) $this->option('races')) {
            $raw[] = 'monster';
        }
        $typeCsv = trim((string) $this->option('type'));
        if ($typeCsv !== '') {
            foreach (explode(',', $typeCsv) as $p) {
                $t = strtolower(trim($p));
                if ($t !== '') {
                    $raw[] = $t;
                }
            }
        }

        return array_values(array_unique($raw));
    }

    private function invalidAction(string $action): int
    {
        $this->error("Action inconnue « {$action} ». Utilisez : sync.");

        return ArtisanExitCode::FAILURE;
    }
}
