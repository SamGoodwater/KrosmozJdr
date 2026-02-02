<?php

namespace App\Services\Scrapping\V2\Config;

/**
 * Résout les alias de collecte (spell, monster, classe, ressource, item, consumable)
 * vers la config (source, entity, defaultFilter).
 *
 * Config : resources/scrapping/v2/collect_aliases.json
 */
final class CollectAliasResolver
{
    private const FILENAME = 'collect_aliases.json';

    public function __construct(
        private string $baseDir
    ) {
    }

    public static function default(): self
    {
        return new self(base_path('resources/scrapping/v2'));
    }

    /**
     * Retourne la config pour un alias (source, entity, defaultFilter?, filterByRace?, filterByType?).
     *
     * @return array{source: string, entity: string, label: string, defaultFilter?: array{superTypeGroup: string}, filterByRace?: string, filterByType?: string}|null
     */
    public function resolve(string $alias): ?array
    {
        $data = $this->loadConfig();
        $aliases = $data['aliases'] ?? [];
        if (!is_array($aliases)) {
            return null;
        }
        $key = strtolower(trim($alias));
        $cfg = $aliases[$key] ?? null;

        return is_array($cfg) && isset($cfg['source'], $cfg['entity']) ? $cfg : null;
    }

    /**
     * Liste les alias disponibles (spell, monster, classe, ressource, item, consumable).
     *
     * @return list<string>
     */
    public function listAliases(): array
    {
        $data = $this->loadConfig();
        $aliases = $data['aliases'] ?? [];
        if (!is_array($aliases)) {
            return [];
        }
        $list = array_keys($aliases);
        sort($list);

        return $list;
    }

    /**
     * @return array<string, mixed>
     */
    private function loadConfig(): array
    {
        $path = rtrim($this->baseDir, '/') . '/' . self::FILENAME;
        if (!is_file($path)) {
            return [];
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return [];
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }
}
