<?php

namespace App\Console\Commands\Pages;

use App\Console\ArtisanExitCode;
use App\Support\Cms\RulesCharacteristicKrefReplacementCatalog;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Insère dans les Markdown des règles les shortcodes {@code [[kref:characteristic:…]]} selon le catalogue partagé.
 *
 * @example php artisan pages:rules-inject-characteristic-krefs --dry-run
 * @example php artisan pages:rules-inject-characteristic-krefs
 */
class RulesInjectCharacteristicKrefsInMarkdownCommand extends Command
{
    protected $signature = 'pages:rules-inject-characteristic-krefs
        {--path= : Répertoire racine des règles (défaut : private/game/rules)}
        {--dry-run : Affiche les fichiers modifiés sans écrire sur le disque}';

    protected $description = 'Injecte les références kref caractéristiques dans les .md des règles (liste : REFERENCE_KREF_CONVERSIONS_CARACTERISTIQUES.md).';

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

        $rootReal = realpath($root) ?: $root;
        $changed = 0;
        $files = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootReal, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || strtolower((string) $fileInfo->getExtension()) !== 'md') {
                continue;
            }

            $path = $fileInfo->getPathname();
            $basename = $fileInfo->getBasename();
            if (in_array($basename, self::EXCLUDED_BASENAMES, true)) {
                continue;
            }

            $original = file_get_contents($path);
            if (! is_string($original)) {
                continue;
            }

            $files++;
            $updated = RulesCharacteristicKrefReplacementCatalog::applyToMarkdown($original);
            if ($updated === $original) {
                continue;
            }

            $changed++;
            $rel = str_replace($rootReal.DIRECTORY_SEPARATOR, '', $path);
            $this->line($dryRun ? "[dry-run] {$rel}" : "Écrit : {$rel}");

            if (! $dryRun) {
                file_put_contents($path, $updated);
            }
        }

        $this->info(sprintf(
            'Fichiers .md parcourus : %d — modifiés : %d%s',
            $files,
            $changed,
            $dryRun ? ' (aucune écriture)' : ''
        ));

        return ArtisanExitCode::SUCCESS;
    }
}
