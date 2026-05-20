<?php

declare(strict_types=1);

namespace App\Console\Commands\Characteristics;

use App\Console\ArtisanExitCode;
use App\Services\Characteristics\CharacteristicDefinitionQualityService;
use App\Services\Characteristics\CharacteristicDefinitionReader;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Vérifie la cohérence des fichiers JSON `characteristic-definitions/` (nommage, groupe, entités).
 *
 * @example php artisan characteristics:audit-definitions
 */
class AuditCharacteristicDefinitionsCommand extends Command
{
    protected $signature = 'characteristics:audit-definitions
        {--report= : Écrit un rapport Markdown (chemin fichier)}';

    protected $description = 'Audit des définitions JSON caractéristiques (nommage, groupe, entités, qualité)';

    public function handle(CharacteristicDefinitionQualityService $quality): int
    {
        $paths = CharacteristicDefinitionReader::allDefinitionAbsolutePaths();
        $errors = [];
        $qualityRows = [];

        foreach ($paths as $path) {
            try {
                $def = CharacteristicDefinitionReader::load($path);
            } catch (\Throwable $e) {
                $errors[] = basename($path).': '.$e->getMessage();

                continue;
            }

            $key = $def['characteristic']['key'] ?? '';
            if (! is_string($key) || $key === '') {
                $errors[] = basename($path).': clé caractéristique manquante';

                continue;
            }

            $parsed = CharacteristicDefinitionNaming::parseCharacteristicKey($key);
            if ($parsed === null) {
                $errors[] = $key.': clé non parsable (suffixe _creature/_object/_spell attendu)';

                continue;
            }

            $expectedFile = CharacteristicDefinitionNaming::definitionFilename($parsed['stem'], $parsed['group']);
            if ($expectedFile !== basename($path)) {
                $errors[] = $key.': fichier attendu '.$expectedFile.', trouvé '.basename($path);
            }

            if (($def['characteristic']['group'] ?? null) !== $parsed['group']) {
                $errors[] = $key.': groupe JSON incohérent avec le suffixe de la clé';
            }

            $entities = $def['entities'] ?? null;
            if (! is_array($entities)) {
                $errors[] = $key.': bloc entities manquant ou invalide';

                continue;
            }

            if ($entities === [] && empty($def['characteristic']['linked_to_key'] ?? null)) {
                $errors[] = $key.': entities vide sans linked_to_key';
            }

            $qIssues = $quality->qualityIssues($path, $def);
            if ($qIssues !== []) {
                $qualityRows[] = ['key' => $key, 'issues' => implode('; ', $qIssues)];
            }
        }

        $reportPath = $this->option('report');
        if (is_string($reportPath) && $reportPath !== '') {
            $this->writeReport($reportPath, $errors, $qualityRows, count($paths));
        }

        $count = count($paths);
        if ($errors === []) {
            $this->info("Audit structurel OK — {$count} définition(s) JSON.");
            if ($qualityRows !== []) {
                $this->warn(count($qualityRows).' définition(s) avec écarts qualité (voir --report).');
            }

            return ArtisanExitCode::SUCCESS;
        }

        $this->error(count($errors).' problème(s) sur '.$count.' fichier(s) :');
        foreach ($errors as $line) {
            $this->line('  • '.$line);
        }

        return ArtisanExitCode::FAILURE;
    }

    /**
     * @param  list<string>  $errors
     * @param  list<array{key: string, issues: string}>  $qualityRows
     */
    private function writeReport(string $path, array $errors, array $qualityRows, int $total): void
    {
        $abs = str_starts_with($path, '/') ? $path : base_path($path);
        $lines = [
            '# Audit characteristic-definitions',
            '',
            '- **Total** : '.$total,
            '- **Erreurs structurelles** : '.count($errors),
            '- **Écarts qualité** : '.count($qualityRows),
            '',
        ];
        if ($errors !== []) {
            $lines[] = '## Erreurs structurelles';
            foreach ($errors as $e) {
                $lines[] = '- '.$e;
            }
            $lines[] = '';
        }
        if ($qualityRows !== []) {
            $lines[] = '## Écarts qualité';
            $lines[] = '| Clé | Problèmes |';
            $lines[] = '| --- | --- |';
            foreach ($qualityRows as $row) {
                $lines[] = '| '.$row['key'].' | '.$row['issues'].' |';
            }
        }
        File::ensureDirectoryExists(dirname($abs));
        File::put($abs, implode("\n", $lines)."\n");
        $this->info('Rapport écrit : '.$abs);
    }
}
