<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\Concerns\NormalizesProjectSyncEntities;
use App\Console\Concerns\PromptsPrimarySuperAdmin;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Initialisation complète du projet : migrations, seeders, scrapping, capabilities.
 *
 * Transforme une base vide en un projet fonctionnel avec les données DofusDB.
 * Compatible exécution longue (set_time_limit(0), DB::reconnect entre phases).
 * Notifie les admin/super_admin à la fin (succès, durée, heure).
 *
 * @example php artisan project:init
 * @example php artisan project:init --fresh --noimage
 * @example php artisan project:init --skip-scrapping --entity=monster
 */
class ProjectInitCommand extends Command
{
    use NormalizesProjectSyncEntities;
    use PromptsPrimarySuperAdmin;

    protected $signature = 'project:init|init
        {--deps : Exécuter d’abord project:deps (apt/composer/pnpm + migrate + CSS)}
        {--fresh : migrate:fresh --force avant tout}
        {--skip-migrate : Ne pas lancer les migrations}
        {--skip-seeders : Ne pas exécuter les seeders (socle déjà fait)}
        {--skip-scrapping : Ne pas scraper}
        {--skip-capabilities : Ne pas importer les capabilities}
        {--skip-types : Ne pas extraire/seed les types (resources, consommables, équipements, races monstres)}
        {--noimage : Désactiver le téléchargement des images}
        {--skip-cache : Ignorer le cache HTTP pour le scrapping}
        {--entity= : Entités (virgules) : breed|class, spell, monster, resource, consumable, item, panoply}
        {--max-items=0 : Limite par entité (0=illimité)}
        {--update-mode=ignore : Mode remplacement existants: ignore|draft_raw_auto_update|auto_update|force (ignore=ne rien remplacer, reprise rapide)}
        {--simulate : Ne pas écrire en base (validation seule)}
        {--init-scheduler : Afficher la ligne cron pour le scheduler Laravel}
        {--skip-clear-queue : Ne pas vider la queue avant le scrapping}
        {--skip-notify : Ne pas notifier les admin à la fin}
        {--skip-super-admin-prompt : Ne pas demander la création du super_admin (CI / scripts)}';

    protected $description = 'Initialise le projet (migrations, seeders, types, scrapping, capabilities)';

    /** Ordre des entités scrapping (dépendances). */
    private const SCRAPPING_ENTITIES = [
        'class',      // breeds
        'spell',
        'monster',
        'resource',
        'consumable',
        'item',
        'panoply',
    ];

    /** Tranches de niveau pour monstres (éviter timeouts). */
    private const MONSTER_LEVEL_CHUNK = 50;

    public function handle(): int
    {
        set_time_limit(0);
        $startedAt = microtime(true);

        $this->info('=== Initialisation du projet KrosmozJDR ===');
        $this->newLine();

        if ((bool) $this->option('deps')) {
            $this->info('Phase 0 : dépendances (project:deps --all)');
            $code = Artisan::call('project:deps', ['--all' => true]);
            $this->output->write(Artisan::output());
            if ($code !== 0) {
                $this->error('Échec de project:deps.');

                return self::FAILURE;
            }
            $this->newLine();
        }

        $success = false;
        $lastError = null;

        try {
            if (! (bool) $this->option('skip-migrate')) {
                $this->runMigrations();
            } else {
                $this->warn('Migrations ignorées (--skip-migrate).');
            }
            $this->newLine();

            $this->runStorageLink();
            $this->newLine();

            if (! (bool) $this->option('skip-seeders')) {
                $this->runSeeders();
            } else {
                $this->warn('Seeders ignorés (--skip-seeders).');
            }
            $this->newLine();

            if (! (bool) $this->option('skip-types')) {
                $this->runTypesSetup();
            }
            $this->newLine();

            if (! (bool) $this->option('skip-scrapping')) {
                $this->runScrapping();
            } else {
                $this->warn('Scrapping ignoré (--skip-scrapping).');
            }
            $this->newLine();

            if (! (bool) $this->option('skip-capabilities')) {
                $this->runCapabilitiesImport();
            }
            $this->newLine();

            if ((bool) $this->option('init-scheduler')) {
                $this->runInitScheduler();
            }
            $this->newLine();

            $success = true;
        } catch (\Throwable $e) {
            $lastError = $e->getMessage();
            throw $e;
        } finally {
            $duration = microtime(true) - $startedAt;
            $finishedAt = now()->format('d/m/Y à H:i:s');
            if (! (bool) $this->option('skip-notify')) {
                NotificationService::notifyProjectMaintenance(
                    'init',
                    $success,
                    $duration,
                    $finishedAt,
                    $lastError,
                );
            }
        }

        $this->info('=== Initialisation terminée ===');

        return self::SUCCESS;
    }

    private function runStorageLink(): void
    {
        $this->info('Phase 1b : Lien symbolique storage');
        $this->line('  → storage:link');
        Artisan::call('storage:link');
        $this->output->write(Artisan::output());
    }

    private function runMigrations(): void
    {
        $this->info('Phase 1 : Migrations');
        $cmd = (bool) $this->option('fresh') ? 'migrate:fresh' : 'migrate';
        $this->line("  → php artisan {$cmd} --force");
        $code = Artisan::call($cmd, ['--force' => true]);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            throw new \RuntimeException("Échec de {$cmd}.");
        }
    }

    private function runSeeders(): void
    {
        $this->info('Phase 2 : Seeders');

        $this->line('  → scrapping:setup (socle scrapping)');
        $code = Artisan::call('scrapping:setup', [
            '--skip-migrate' => true,
            '--fresh' => false,
        ]);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            throw new \RuntimeException('Échec de scrapping:setup.');
        }

        $seeders = [
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\CriticalPagesSeeder::class,
            \Database\Seeders\NavMenuSeeder::class,
            \Database\Seeders\PageSeeder::class,
            \Database\Seeders\SectionSeeder::class,
            \Database\Seeders\SubEffectSeeder::class,
        ];
        foreach ($seeders as $seeder) {
            $this->line("  → {$seeder}");
            $code = Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
            $this->output->write(Artisan::output());
            if ($code !== 0) {
                $this->warn("  Avertissement : échec partiel de {$seeder}");
            } elseif ($seeder === \Database\Seeders\UserSeeder::class) {
                $this->runPrimarySuperAdminPrompt();
            }
        }

        // MonsterRaceSeeder est inclus dans TypeSeeder (scrapping:setup)
    }

    private function runTypesSetup(): void
    {
        $this->info('Phase 3 : Récupération de tous les types depuis DofusDB');

        $typeArgs = ['--skip-cache' => (bool) $this->option('skip-cache')];

        $this->line('  → scrapping:types:seed (ressources, consommables, équipements)');
        $code = Artisan::call('scrapping:types:seed', $typeArgs);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : seed types item a échoué.');

            return;
        }

        $this->line('  → scrapping:races:seed (races monstres)');
        $code = Artisan::call('scrapping:races:seed', $typeArgs);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : seed races monstres a échoué.');
        }

        $this->line('  → SpellTypeSeeder (types de sorts, référentiel métier)');
        $code = Artisan::call('db:seed', ['--class' => \Database\Seeders\Type\SpellTypeSeeder::class, '--force' => true]);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : seed types de sorts a échoué.');
        }

        $this->line('  Types récupérés : ressources, consommables, équipements, races monstres, types de sorts');
    }

    private function runScrapping(): void
    {
        $this->info('Phase 4 : Scrapping DofusDB');
        DB::reconnect();

        if (! (bool) $this->option('skip-clear-queue')) {
            $this->clearQueue();
        }

        $entityFilter = (string) $this->option('entity');
        $entities = $entityFilter !== ''
            ? $this->normalizeEntityCsvToList($entityFilter)
            : self::SCRAPPING_ENTITIES;

        $maxItems = max(0, (int) $this->option('max-items'));
        $noImage = (bool) $this->option('noimage');
        $simulate = (bool) $this->option('simulate');

        $scrapArgs = [
            '--max-items' => $maxItems,
            '--limit' => 100,
            '--max-pages' => 0,
            '--update-mode' => (string) $this->option('update-mode'),
            '--skip-existing' => true,
        ];
        if ($noImage) {
            $scrapArgs['--noimage'] = true;
        }
        if ($simulate) {
            $scrapArgs['--simulate'] = true;
        }
        if ((bool) $this->option('skip-cache')) {
            $scrapArgs['--skip-cache'] = true;
        }

        foreach ($entities as $entity) {
            $entity = strtolower(trim($entity));
            if (! in_array($entity, self::SCRAPPING_ENTITIES, true)) {
                $this->warn("  Entité inconnue ignorée : {$entity}");

                continue;
            }

            if ($entity === 'monster') {
                $this->runScrappingMonsters($scrapArgs);
                $this->newLine();

                continue;
            }
            if ($entity === 'resource') {
                $this->line('  → scrapping:run --entity=resource --resource-types=allowed');
                $code = $this->call('scrapping:run', array_merge($scrapArgs, [
                    '--entity' => 'resource',
                    '--resource-types' => 'allowed',
                    '--max-pages' => 0,
                ]));
            } else {
                $this->line("  → scrapping:run --entity={$entity}");
                $code = $this->call('scrapping:run', array_merge($scrapArgs, [
                    '--entity' => $entity,
                ]));
            }
            if ($code !== 0) {
                $this->warn("  Avertissement : scrapping {$entity} a échoué.");
            }
            DB::reconnect();
            $this->newLine();
        }
    }

    private function runScrappingMonsters(array $baseArgs): void
    {
        $maxLevel = 250;
        $chunk = self::MONSTER_LEVEL_CHUNK;

        for ($min = 1; $min <= $maxLevel; $min += $chunk) {
            $max = min($min + $chunk - 1, $maxLevel);
            $this->line("  → scrapping:run --entity=monster --levelMin={$min} --levelMax={$max}");
            $code = $this->call('scrapping:run', array_merge($baseArgs, [
                '--entity' => 'monster',
                '--levelMin' => (string) $min,
                '--levelMax' => (string) $max,
            ]));
            if ($code !== 0) {
                $this->warn("  Avertissement : scrapping monster niveau {$min}-{$max} a échoué.");
            }
            DB::reconnect();
        }
    }

    private function runCapabilitiesImport(): void
    {
        $this->info('Phase 5 : Capabilities');
        $path = base_path('database/seeders/data/capability.json');
        if (! is_file($path)) {
            $this->line('  Fichier capability.json absent, import ignoré.');

            return;
        }
        $this->line("  → capabilities:import-legacy {$path}");
        $code = Artisan::call('capabilities:import-legacy', [
            'file' => $path,
        ]);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : import capabilities a échoué.');
        }
    }

    private function runInitScheduler(): void
    {
        $this->info('Phase 6 : Initialisation du scheduler (cron)');

        $path = base_path();
        $php = defined('PHP_BINARY') ? PHP_BINARY : 'php';
        $cronLine = "* * * * * cd {$path} && {$php} artisan schedule:run >> /dev/null 2>&1";

        $this->line('  Pour que le scheduler Laravel soit exécuté, ajoutez cette ligne à la crontab :');
        $this->newLine();
        $this->line("    <fg=green>{$cronLine}</>");
        $this->newLine();
        $this->line('  Commande : <fg=cyan>crontab -e</> puis coller la ligne ci-dessus.');
        $this->line('  Pour project:update planifié : définissez PROJECT_UPDATE_AUTO_ENABLED=true et PROJECT_UPDATE_CRON dans .env');
        $this->newLine();
        $this->line('  Tâches planifiées actuelles :');
        Artisan::call('schedule:list');
        $this->output->write(Artisan::output());
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
