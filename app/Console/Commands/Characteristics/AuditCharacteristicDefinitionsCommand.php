<?php

declare(strict_types=1);

namespace App\Console\Commands\Characteristics;

use App\Console\ArtisanExitCode;
use App\Services\Characteristics\CharacteristicDefinitionReader;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use Illuminate\Console\Command;

/**
 * Vérifie la cohérence des fichiers JSON `characteristic-definitions/` (nommage, groupe, entités).
 *
 * @example php artisan characteristics:audit-definitions
 */
class AuditCharacteristicDefinitionsCommand extends Command
{
    protected $signature = 'characteristics:audit-definitions';

    protected $description = 'Audit des définitions JSON caractéristiques (nommage, groupe, entités ou lien maître)';

    public function handle(): int
    {
        $paths = CharacteristicDefinitionReader::allDefinitionAbsolutePaths();
        $errors = [];

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
        }

        $count = count($paths);
        if ($errors === []) {
            $this->info("Audit OK — {$count} définition(s) JSON.");

            return ArtisanExitCode::SUCCESS;
        }

        $this->error(count($errors).' problème(s) sur '.$count.' fichier(s) :');
        foreach ($errors as $line) {
            $this->line('  • '.$line);
        }

        return ArtisanExitCode::FAILURE;
    }
}
