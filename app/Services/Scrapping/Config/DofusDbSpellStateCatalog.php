<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Config;

use App\Services\Scrapping\Http\DofusDbClient;

/**
 * Catalogue des états DofusDB (/spell-states/{id}).
 */
class DofusDbSpellStateCatalog
{
    /** @var array<string, array<int, array<string,mixed>>> */
    private array $byLang = [];

    public function __construct(private DofusDbClient $client)
    {
    }

    /**
     * @return array<string,mixed>
     */
    public function get(int $stateId, string $lang = 'fr', array $options = []): array
    {
        if ($stateId <= 0) {
            return [];
        }
        if (isset($this->byLang[$lang][$stateId])) {
            return $this->byLang[$lang][$stateId];
        }

        $url = "https://api.dofusdb.fr/spell-states/{$stateId}?lang={$lang}";
        $data = $this->client->getJson($url, $options);
        if (!is_array($data)) {
            $data = [];
        }

        $this->byLang[$lang][$stateId] = $data;

        return $data;
    }
}

