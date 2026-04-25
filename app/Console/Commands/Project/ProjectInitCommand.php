<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\Concerns\NormalizesProjectSyncEntities;
use App\Console\Concerns\PromptsPrimarySuperAdmin;
use App\Services\NotificationService;
use Database\Seeders\CriticalPagesSeeder;
use Database\Seeders\NavMenuSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\SectionSeeder;
use Database\Seeders\SubEffectSeeder;
use Database\Seeders\Type\SpellTypeSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Throwable;

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
        {--deps : Exécuter d’abord project:deps (composer update + pnpm up + project:optimize)}
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
        $phaseStatuses = [
            'migrations' => 'pending',
            'storage_link' => 'pending',
            'seeders' => 'pending',
            'rules_import' => 'pending',
            'types' => 'pending',
            'scrapping' => 'pending',
            'capabilities' => 'pending',
            'scheduler' => 'pending',
        ];

        $this->info('=== Initialisation du projet KrosmozJDR ===');
        $this->newLine();

        if ((bool) $this->option('deps')) {
            $this->info('Phase 0 : dépendances (project:deps — composer + pnpm + optimize)');
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
                $phaseStatuses['migrations'] = 'ok';
            } else {
                $this->warn('Migrations ignorées (--skip-migrate).');
                $phaseStatuses['migrations'] = 'skipped';
            }
            $this->newLine();

            $this->runStorageLink();
            $phaseStatuses['storage_link'] = $this->runStorageLink() ? 'ok' : 'warn';
            $this->newLine();

            if (! (bool) $this->option('skip-seeders')) {
                $phaseStatuses['seeders'] = $this->runSeeders() ? 'ok' : 'warn';
                $phaseStatuses['rules_import'] = $this->runRulesPagesImport() ? 'ok' : 'warn';
            } else {
                $this->warn('Seeders ignorés (--skip-seeders).');
                $this->warn('Import des règles ignoré (dépend de la création des pages/sections seedées).');
                $phaseStatuses['seeders'] = 'skipped';
                $phaseStatuses['rules_import'] = 'skipped';
            }
            $this->newLine();

            if (! (bool) $this->option('skip-types')) {
                $phaseStatuses['types'] = $this->runTypesSetup() ? 'ok' : 'warn';
            } else {
                $phaseStatuses['types'] = 'skipped';
            }
            $this->newLine();

            if (! (bool) $this->option('skip-scrapping')) {
                $phaseStatuses['scrapping'] = $this->runScrapping() ? 'ok' : 'warn';
            } else {
                $this->warn('Scrapping ignoré (--skip-scrapping).');
                $phaseStatuses['scrapping'] = 'skipped';
            }
            $this->newLine();

            if (! (bool) $this->option('skip-capabilities')) {
                $phaseStatuses['capabilities'] = $this->runCapabilitiesImport() ? 'ok' : 'warn';
            } else {
                $phaseStatuses['capabilities'] = 'skipped';
            }
            $this->newLine();

            if ((bool) $this->option('init-scheduler')) {
                $this->runInitScheduler();
                $phaseStatuses['scheduler'] = 'ok';
            } else {
                $phaseStatuses['scheduler'] = 'skipped';
            }
            $this->newLine();

            $success = true;
        } catch (Throwable $e) {
            foreach ($phaseStatuses as $phase => $status) {
                if ($status === 'pending') {
                    $phaseStatuses[$phase] = 'not_run';
                }
            }
            $lastError = $e->getMessage();
            throw $e;
        } finally {
            $duration = microtime(true) - $startedAt;
            $finishedAt = now()->format('d/m/Y à H:i:s');
            $this->printInitSummary($phaseStatuses, $success, $duration, $finishedAt, $lastError);
            if (! (bool) $this->option('skip-notify')) {
                try {
                    NotificationService::notifyProjectMaintenance(
                        'init',
                        $success,
                        $duration,
                        $finishedAt,
                        $lastError,
                    );
                } catch (Throwable $notifyError) {
                    $this->warn('Notification maintenance ignorée : '.$notifyError->getMessage());
                }
            }
        }

        $this->info('=== Initialisation terminée ===');

        return self::SUCCESS;
    }

    /**
     * @param array<string, string> $phaseStatuses
     */
    private function printInitSummary(
        array $phaseStatuses,
        bool $success,
        float $duration,
        string $finishedAt,
        ?string $lastError
    ): void {
        $this->info('=== Récapitulatif initialisation ===');
        $labels = [
            'migrations' => 'Migrations',
            'storage_link' => 'Storage link',
            'seeders' => 'Seeders',
            'rules_import' => 'Import règles CMS',
            'types' => 'Types',
            'scrapping' => 'Scrapping',
            'capabilities' => 'Capabilities',
            'scheduler' => 'Scheduler',
        ];

        foreach ($labels as $key => $label) {
            $status = $phaseStatuses[$key] ?? 'unknown';
            $badge = match ($status) {
                'ok' => '<info>OK</info>',
                'warn' => '<comment>WARN</comment>',
                'skipped' => '<comment>SKIP</comment>',
                'not_run' => '<fg=gray>NON LANCÉ</>',
                default => '<error>ERREUR</error>',
            };
            $this->line(" - {$label}: {$badge}");
        }

        $global = $success ? '<info>SUCCÈS</info>' : '<error>ÉCHEC</error>';
        $this->line('Statut global : '.$global);
        $this->line('Durée : '.number_format($duration, 1, ',', ' ').' s');
        $this->line('Fin : '.$finishedAt);
        if ($lastError !== null && trim($lastError) !== '') {
            $this->line('Dernière erreur : '.$lastError);
        }
        $this->newLine();
    }

    private function runStorageLink(): bool
    {
        $this->info('Phase 1b : Lien symbolique storage');
        $this->line('  → storage:link');
        $code = Artisan::call('storage:link');
        $this->output->write(Artisan::output());

        if ($code !== 0) {
            $this->warn('  Avertissement : storage:link a remonté un code non nul (souvent lien déjà existant).');

            return false;
        }

        return true;
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

    private function runSeeders(): bool
    {
        $this->info('Phase 2 : Seeders');
        $hasWarnings = false;

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
            UserSeeder::class,
            CriticalPagesSeeder::class,
            NavMenuSeeder::class,
            PageSeeder::class,
            SectionSeeder::class,
            SubEffectSeeder::class,
        ];
        foreach ($seeders as $seeder) {
            $this->line("  → {$seeder}");
            $code = Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
            $this->output->write(Artisan::output());
            if ($code !== 0) {
                $this->warn("  Avertissement : échec partiel de {$seeder}");
                $hasWarnings = true;
            } elseif ($seeder === UserSeeder::class) {
                $this->runPrimarySuperAdminPrompt();
            }
        }

        // MonsterRaceSeeder est inclus dans TypeSeeder (scrapping:setup)
        return ! $hasWarnings;
    }

    /**
     * Importe la table des matières des règles dans les pages CMS pour un projet initialisé "clé en main".
     */
    private function runRulesPagesImport(): bool
    {
        $this->info('Phase 2b : Import des règles (TABLE_DES_MATIERES.md → pages CMS)');
        $this->line('  → project:data:import-rules-toc (pages règles CMS)');
        $code = Artisan::call('project:data:import-rules-toc');
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : import des pages règles échoué.');
            $this->warn('  Vérifiez le fichier TABLE_DES_MATIERES.md et les logs de pages:import-rules-toc.');
            return false;
        }

        $this->info('  ✅ Import des règles terminé.');

        return true;
    }

    private function runTypesSetup(): bool
    {
        $this->info('Phase 3 : Récupération de tous les types depuis DofusDB');
        $hasWarnings = false;

        $typeArgs = ['--skip-cache' => (bool) $this->option('skip-cache')];

        $this->line('  → scrapping:types:seed (ressources, consommables, équipements)');
        $code = Artisan::call('scrapping:types:seed', $typeArgs);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : seed types item a échoué.');
            return false;
        }

        $this->line('  → scrapping:races:seed (races monstres)');
        $code = Artisan::call('scrapping:races:seed', $typeArgs);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : seed races monstres a échoué.');
            $hasWarnings = true;
        }

        $this->line('  → SpellTypeSeeder (types de sorts, référentiel métier)');
        $code = Artisan::call('db:seed', ['--class' => SpellTypeSeeder::class, '--force' => true]);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : seed types de sorts a échoué.');
            $hasWarnings = true;
        }

        $this->line('  Types récupérés : ressources, consommables, équipements, races monstres, types de sorts');

        return ! $hasWarnings;
    }

    private function runScrapping(): bool
    {
        $this->info('Phase 4 : Scrapping DofusDB');
        $hasWarnings = false;
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
                $hasWarnings = true;

                continue;
            }

            if ($entity === 'monster') {
                if (! $this->runScrappingMonsters($scrapArgs)) {
                    $hasWarnings = true;
                }
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
                $hasWarnings = true;
            }
            DB::reconnect();
            $this->newLine();
        }

        return ! $hasWarnings;
    }

    private function runScrappingMonsters(array $baseArgs): bool
    {
        $maxLevel = 250;
        $chunk = self::MONSTER_LEVEL_CHUNK;
        $hasWarnings = false;

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
                $hasWarnings = true;
            }
            DB::reconnect();
        }

        return ! $hasWarnings;
    }

    private function runCapabilitiesImport(): bool
    {
        $this->info('Phase 5 : Capabilities');
        $path = base_path('database/seeders/data/capability.json');
        if (! is_file($path)) {
            $this->line('  Fichier capability.json absent, import ignoré.');

            return true;
        }
        $this->line("  → capabilities:import-legacy {$path}");
        $code = Artisan::call('capabilities:import-legacy', [
            'file' => $path,
        ]);
        $this->output->write(Artisan::output());
        if ($code !== 0) {
            $this->warn('  Avertissement : import capabilities a échoué.');
            return false;
        }

        return true;
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
