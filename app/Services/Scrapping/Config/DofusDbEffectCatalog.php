<?php

namespace App\Services\Scrapping\Config;

use App\Services\Scrapping\Http\DofusDbClient;

/**
 * Catalogue des effets DofusDB (dictionnaire /effects).
 *
 * @description
 * Fournit un accès simple et cache-friendly aux définitions d'effets DofusDB
 * (`/effects/{id}?lang=...`) afin de pouvoir mapper proprement des instances
 * d'effets (items/spell-levels) vers des bonus KrosmozJDR.
 *
 * On s'appuie sur `DofusDbClient` pour le cache/retry.
 */
class DofusDbEffectCatalog
{
    /** @var array<string, array<int, array<string,mixed>>> */
    private array $byLang = [];

    public function __construct(private DofusDbClient $client)
    {
    }

    /**
     * @return array<string,mixed>
     */
    public function get(int $effectId, string $lang = 'fr', array $options = []): array
    {
        if (isset($this->byLang[$lang][$effectId])) {
            return $this->byLang[$lang][$effectId];
        }

        $url = "https://api.dofusdb.fr/effects/{$effectId}?lang={$lang}";
        $data = $this->client->getJson($url, $options);

        if (!is_array($data)) {
            $data = [];
        }

        $this->byLang[$lang][$effectId] = $data;
        return $data;
    }
}

