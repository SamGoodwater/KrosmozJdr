<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\NormalizesProjectSyncEntities;
use App\Console\Concerns\RunsBibliothequeEntityPagesSync;
use Database\Seeders\Type\SpellTypeSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Point d’entrée unique pour les flux « données DofusDB » (sync / init catalogue / complétion).
 *
 * Délègue aux commandes existantes pour rester DRY.
 */
class ProjectDataCommand extends Command
{
    use NormalizesProjectSyncEntities;
    use RunsBibliothequeEntityPagesSync;

    protected $signature = 'project:data
        {action : sync (maj auto_update), init (équivalent project:init données), fill (guide complétion)}
        {--fresh : (init) migrate:fresh avant le pipeline}
        {--noimage : (init|sync) pas de téléchargement d’images}
        {--skip-cache : (init|sync|catalogue) ignorer le cache HTTP scrapping}
        {--simulate : (init) ne pas écrire en base}
        {--entity= : (sync) Entités (virgules) : breed|class, spell, monster, panoply, resource, item, consumable — sans --type/--races : sync seul ; avec catalogue : exige ce filtre pour lancer aussi le sync entités}
        {--type= : (sync) L\'Essentiels catalogue (virgules) : all | monster (races) | resource | consumable | item | equipment | spell (types de sorts en BDD)}
        {--races : (sync) Raccourci pour --type=monster (races monstres DofusDB)}
        {--lang=fr : (sync catalogue) langue DofusDB pour types/races}
        {--skip-scrapping : (init)}
        {--skip-seeders : (init)}
        {--skip-types : (init)}
        {--skip-capabilities : (init)}
        {--skip-super-admin-prompt : (init)}
        {--max-items=0 : (init)}
        {--update-mode=ignore : (init)}
        {--dry-run : (sync)}
        {--skip-clear-queue : (sync|init)}
        {--skip-notify : (sync|init)}';

    protected $description = 'Données DofusDB : sync (auto_update), init (pipeline complet), fill (guide — non automatisé)';

    public function handle(): int
    {
        $action = strtolower(trim((string) $this->argument('action')));

        return match ($action) {
            'sync', 'updates' => $this->runSync(),
            'init' => $this->runInit(),
            'fill', 'upgrade' => $this->runFill(),
            default => $this->invalidAction($action),
        };
    }

    private function runSync(): int
    {
        $catalogCode = $this->runCatalogSync();
        if ($catalogCode !== ArtisanExitCode::SUCCESS) {
            return $catalogCode;
        }

        if ($this->shouldRunEntitySyncAfterCatalog()) {
            $params = $this->buildEntitySyncParams();
            $syncCode = $this->call('project:data:sync', $params);
            if ($syncCode !== ArtisanExitCode::SUCCESS) {
                return $syncCode;
            }
        }

        if (! (bool) $this->option('dry-run')) {
            $this->newLine();
            $this->info('Synchronisation menu Bibliothèques (classes / spécialisations)');
            if (! $this->runBibliothequeEntityPagesSync()) {
                return ArtisanExitCode::FAILURE;
            }
        }

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildEntitySyncParams(): array
    {
        $params = [];
        $entityCsv = trim((string) $this->option('entity'));
        if ($entityCsv !== '') {
            $params['--entity'] = $this->normalizeEntityCsvToOptionString($entityCsv);
        }
        if ($this->option('noimage')) {
            $params['--noimage'] = true;
        }
        if ($this->option('skip-cache')) {
            $params['--skip-cache'] = true;
        }
        if ($this->option('dry-run')) {
            $params['--dry-run'] = true;
        }
        if ($this->option('skip-clear-queue')) {
            $params['--skip-clear-queue'] = true;
        }
        if ($this->option('skip-notify')) {
            $params['--skip-notify'] = true;
        }

        return $params;
    }

    /**
     * Si un catalogue (--type / --races) est demandé sans --entity, on n’exécute pas le sync entités.
     * Sinon (pas de catalogue, ou catalogue + --entity, ou sync seul) : sync entités.
     */
    private function shouldRunEntitySyncAfterCatalog(): bool
    {
        $hasCatalog = $this->hasCatalogOptions();
        $entityCsv = trim((string) $this->option('entity'));

        if (! $hasCatalog) {
            return true;
        }

        return $entityCsv !== '';
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
                $this->error('Valeurs catalogue inconnues. Utilisez : all, monster, spell, resource, consumable, item, equipment (ou --races pour les races).');

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

    private function runInit(): int
    {
        $params = [];
        if ($this->option('fresh')) {
            $params['--fresh'] = true;
        }
        if ($this->option('noimage')) {
            $params['--noimage'] = true;
        }
        if ($this->option('skip-cache')) {
            $params['--skip-cache'] = true;
        }
        if ($this->option('simulate')) {
            $params['--simulate'] = true;
        }
        if ($this->option('entity')) {
            $params['--entity'] = $this->normalizeEntityCsvToOptionString((string) $this->option('entity'));
        }
        if ($this->option('skip-scrapping')) {
            $params['--skip-scrapping'] = true;
        }
        if ($this->option('skip-seeders')) {
            $params['--skip-seeders'] = true;
        }
        if ($this->option('skip-types')) {
            $params['--skip-types'] = true;
        }
        if ($this->option('skip-capabilities')) {
            $params['--skip-capabilities'] = true;
        }
        if ($this->option('skip-super-admin-prompt')) {
            $params['--skip-super-admin-prompt'] = true;
        }
        if ($this->option('skip-clear-queue')) {
            $params['--skip-clear-queue'] = true;
        }
        if ($this->option('skip-notify')) {
            $params['--skip-notify'] = true;
        }

        $params['--max-items'] = $this->option('max-items');

        $updateMode = (string) $this->option('update-mode');
        if ($updateMode !== '' && $updateMode !== 'ignore') {
            $params['--update-mode'] = $updateMode;
        }

        return $this->call('project:init', $params);
    }

    private function runFill(): int
    {
        $this->warn('Mode « fill / upgrade » : import ciblé des fiches absentes en base (catalogue DofusDB vs dofusdb_id locaux).');
        $this->line('Ce mode n’est pas encore encapsulé : utilisez `scrapping:run` par entité sans `--skip-existing`, ou des filtres `--idMin` / `--levelMin` / `--limit`.');
        $this->line('Pour mettre à jour les entités déjà présentes avec auto_update, enchaînez avec `php artisan project:data sync`.');
        $this->newLine();
        $this->line('Documentation : docs/operations/README.md');

        return ArtisanExitCode::SUCCESS;
    }

    private function invalidAction(string $action): int
    {
        $this->error("Action inconnue « {$action} ». Utilisez : sync, init ou fill.");

        return ArtisanExitCode::FAILURE;
    }
}
