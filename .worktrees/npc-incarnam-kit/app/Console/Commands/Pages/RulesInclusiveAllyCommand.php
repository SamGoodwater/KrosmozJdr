<?php

namespace App\Console\Commands\Pages;

use App\Console\ArtisanExitCode;
use App\Support\Cms\RulesMarkdownInclusiveText;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Normalise le ton inclusif (allié·e, personnage, en-têtes) dans les Markdown des règles.
 *
 * @example php artisan pages:rules-inclusive-ally --dry-run
 */
class RulesInclusiveAllyCommand extends Command
{
    protected $signature = 'pages:rules-inclusive-ally
        {--path= : Répertoire racine des règles (défaut : private/game/rules)}
        {--dry-run : Affiche les fichiers modifiés sans écrire sur le disque}';

    protected $description = 'Applique l\'écriture inclusive (allié·e, personnage, en-têtes) dans les .md des règles.';

    /** @var array<int, string> */
    private const EXCLUDED_BASENAMES = [
        'TABLE_DES_MATIERES.md',
        'INDEX.md',
        'FORMAT_REGLES.md',
        'REFERENCE_CLES_CARACTERISTIQUES.md',
        'REFERENCE_KREF_CONVERSIONS_CARACTERISTIQUES.md',
        'AUDIT_REGLES_PUBLICATION.md',
        'RECAP.md',
        'COHERENCE_SEEDER_REGLES.md',
    ];

    public function handle(): int
    {
        $root = (string) ($this->option('path') ?: base_path('private/game/rules'));
        $dryRun = (bool) $this->option('dry-run');

        if (! is_dir($root)) {
            $this->error("Répertoire introuvable : {$root}");

            return ArtisanExitCode::FAILURE;
        }

        $changed = 0;
        $files = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || strtolower((string) $fileInfo->getExtension()) !== 'md') {
                continue;
            }

            $basename = pathinfo((string) $fileInfo->getPathname(), PATHINFO_BASENAME);
            if (in_array($basename, self::EXCLUDED_BASENAMES, true)) {
                continue;
            }

            if (! preg_match('/^\d+(?:\.\d+){0,2}-/u', $basename)) {
                continue;
            }

            $files++;
            $path = (string) $fileInfo->getPathname();
            $original = file_get_contents($path);
            if (! is_string($original)) {
                continue;
            }

            $updated = RulesMarkdownInclusiveText::apply($original);
            if ($updated === $original) {
                continue;
            }

            $changed++;
            $relative = str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
            $this->line($dryRun ? "[dry-run] {$relative}" : "Mis à jour : {$relative}");

            if (! $dryRun) {
                file_put_contents($path, $updated);
            }
        }

        $this->info(sprintf(
            '%d fichier(s) %s sur %d parcouru(s).',
            $changed,
            $dryRun ? 'à modifier' : 'modifié(s)',
            $files
        ));

        return ArtisanExitCode::SUCCESS;
    }
}
