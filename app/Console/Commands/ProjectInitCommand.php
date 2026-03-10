<?php

declare(strict_types=1);

namespace App\Console\Commands;

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
    protected $signature = 'project:init
        {--fresh : migrate:fresh --force avant tout}
        {--skip-migrate : Ne pas lancer les migrations}
        {--skip-seeders : Ne pas exécuter les seeders (socle déjà fait)}
        {--skip-scrapping : Ne pas scraper}
        {--skip-capabilities : Ne pas importer les capabilities}
        {--skip-types : Ne pas extraire/seed les types item (resource, consumable, equipment)}
        {--noimage : Désactiver le téléchargement des images}
        {--skip-cache : Ignorer le cache HTTP pour le scrapping}
        {--entity= : Limiter à une entité (class,spell,monster,resource,consumable,item,panoply)}
        {--max-items=0 : Limite par entité (0=illimité)}
        {--simulate : Ne pas écrire en base (validation seule)}
        {--init-scheduler : Afficher la ligne cron pour le scheduler Laravel}
        {--skip-clear-queue : Ne pas vider la queue avant le scrapping}
        {--skip-notify : Ne pas notifier les admin à la fin}';

    protected $description = 'Initialise le projet (migrations, seeders, scrapping, capabilities)';

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

        $success = false;
        $lastError = null;

        try {
            if (! (bool) $this->option('skip-migrate')) {
                $this->runMigrations();
            } else {
                $this->warn('Migrations ignorées (--skip-migrate).');
            }
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
            }
        }

        // MonsterRaceSeeder est inclus dans TypeSeeder (scrapping:setup)
    }

    private function runTypesSetup(): void
    {
        $this->info('Phase 3 : Types item (ressources, consommables, équipements)');

        $this->line('  → scrapping:types:extract');
        $code = Artisan::call('scrapping:types:extract');
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : extraction types a échoué.');
            return;
        }

        $this->line('  → scrapping:types:seed');
        $code = Artisan::call('scrapping:types:seed');
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : seed types a échoué.');
        }
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
            ? array_values(array_filter(array_map('trim', explode(',', $entityFilter))))
            : self::SCRAPPING_ENTITIES;

        $maxItems = max(0, (int) $this->option('max-items'));
        $noImage = (bool) $this->option('noimage');
        $simulate = (bool) $this->option('simulate');

        $scrapArgs = [
            '--max-items' => $maxItems,
            '--limit' => 100,
            '--max-pages' => 0,
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
                $this->line("  → scrapping:run --entity=resource --resource-types=allowed");
                $code = Artisan::call('scrapping:run', array_merge($scrapArgs, [
                    '--entity' => 'resource',
                    '--resource-types' => 'allowed',
                    '--max-pages' => 0,
                ]));
            } else {
                $this->line("  → scrapping:run --entity={$entity}");
                $code = Artisan::call('scrapping:run', array_merge($scrapArgs, [
                    '--entity' => $entity,
                ]));
            }

            $this->output->write(Artisan::output());
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
            $code = Artisan::call('scrapping:run', array_merge($baseArgs, [
                '--entity' => 'monster',
                '--levelMin' => (string) $min,
                '--levelMax' => (string) $max,
            ]));
            $this->output->write(Artisan::output());
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
