<?php

declare(strict_types=1);

namespace Database\Seeders\Concerns;

/**
 * Charge un fichier PHP de données seeder (return array) depuis database/seeders/data/.
 */
trait LoadsSeederDataFile
{
    /**
     * Charge et retourne le tableau exporté par le fichier.
     *
     * @return list<array<string, mixed>>
     */
    protected function loadDataFile(string $relativePath): array
    {
        $path = base_path($relativePath);
        if (! is_file($path)) {
            return [];
        }
        $rows = require $path;

        return is_array($rows) ? array_values($rows) : [];
    }
}
