<?php

declare(strict_types=1);

namespace App\Console\Concerns;

/**
 * Normalise les noms d’entités passés en CLI (alias utilisateur → clés internes / scrapping).
 */
trait NormalizesProjectSyncEntities
{
    /**
     * Alias utilisateur → entité interne (scrapping / project:data:sync).
     */
    protected function normalizeEntityToken(string $raw): string
    {
        $t = strtolower(trim($raw));

        return match ($t) {
            'breed', 'nbreed', 'classe' => 'class',
            default => $t,
        };
    }

    /**
     * @return list<string>
     */
    protected function normalizeEntityCsvToList(string $csv): array
    {
        $out = [];
        foreach (explode(',', $csv) as $part) {
            $n = $this->normalizeEntityToken($part);
            if ($n !== '' && ! in_array($n, $out, true)) {
                $out[] = $n;
            }
        }

        return $out;
    }

    protected function normalizeEntityCsvToOptionString(string $csv): string
    {
        return implode(',', $this->normalizeEntityCsvToList($csv));
    }
}
