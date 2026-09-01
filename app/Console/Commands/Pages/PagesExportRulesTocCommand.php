<?php

declare(strict_types=1);

namespace App\Console\Commands\Pages;

use App\Console\ArtisanExitCode;
use App\Support\Cms\RulesPagesExporter;
use Illuminate\Console\Command;

/**
 * Exporte le contenu des pages règles CMS vers les fichiers Markdown du dépôt.
 *
 * Inverse de {@see PagesImportRulesTocCommand}. La base reste la source de vérité :
 * lancer cet export avant d’éditer les {@code .md}, puis réimporter avec
 * {@code pages:import-rules-toc --force-content}.
 *
 * @example php artisan pages:export-rules-toc --dry-run
 * @example php artisan pages:export-rules-toc
 */
class PagesExportRulesTocCommand extends Command
{
    protected $signature = 'pages:export-rules-toc
        {path? : Chemin du fichier TABLE_DES_MATIERES.md}
        {--dry-run : Affiche le plan sans écrire les fichiers}
        {--create-missing : Crée un fichier .md s’il n’existe pas encore pour une entrée TOC}';

    protected $description = 'Écrit les fichiers Markdown des règles depuis le contenu CMS (BDD → .md).';

    public function handle(RulesPagesExporter $exporter): int
    {
        $path = (string) ($this->argument('path') ?: base_path('private/game/rules/TABLE_DES_MATIERES.md'));
        $dryRun = (bool) $this->option('dry-run');
        $createMissing = (bool) $this->option('create-missing');

        if (! is_file($path)) {
            $this->error("Fichier introuvable: {$path}");

            return ArtisanExitCode::FAILURE;
        }

        $result = $exporter->export($path, $dryRun, $createMissing);

        foreach ($result['warnings'] as $warning) {
            $this->warn($warning);
        }

        foreach ($result['files'] as $file) {
            $relative = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file);
            $this->line($dryRun ? "[dry-run] {$relative}" : "Mis à jour : {$relative}");
        }

        $this->info(sprintf(
            '%s : %d fichier(s) %s, %d inchangé(s), %d sans section CMS, %d page(s) manquante(s), %d fichier(s) absent(s).',
            $dryRun ? 'Dry-run' : 'Export terminé',
            $result['written'],
            $dryRun ? 'à écrire' : 'écrit(s)',
            $result['unchanged'],
            $result['skipped'],
            $result['missing_page'],
            $result['missing_file'],
        ));

        return ArtisanExitCode::SUCCESS;
    }
}
