<?php

namespace App\Services\Scrapping\Core\Config;

/**
 * Charge les configs depuis resources/scrapping/config/ (source + entités).
 * Endpoints, filtres, target, meta, relations viennent des JSON.
 * Le mapping (règles DofusDB → Krosmoz) vient uniquement de la BDD via ScrappingMappingService.
 *
 * Validation minimale : version, source, entity, endpoints ; mapping fourni par la BDD (ou []).
 */
final class ConfigLoader
{
    public function __construct(
        private string $baseDir,
        private ?ScrappingMappingService $mappingService = null
    ) {
        if (!is_dir($this->baseDir)) {
            throw new \InvalidArgumentException("Répertoire config scrapping introuvable: {$this->baseDir}");
        }
    }

    public static function default(): self
    {
        return new self(base_path('resources/scrapping/config'));
    }

    /**
     * @return array<string, mixed>
     */
    public function loadSource(string $source): array
    {
        $path = $this->baseDir . "/sources/{$source}/source.json";
        $data = $this->readJson($path);

        if (($data['source'] ?? null) !== $source) {
            throw new \InvalidArgumentException("Source mismatch: attendu '{$source}', trouvé '" . ($data['source'] ?? 'null') . "'");
        }

        return $data;
    }

    /**
     * @return list<string>
     */
    public function listEntities(string $source): array
    {
        $dir = $this->baseDir . "/sources/{$source}/entities";
        if (!is_dir($dir)) {
            return [];
        }

        $files = glob($dir . '/*.json') ?: [];
        $entities = [];
        foreach ($files as $file) {
            $name = basename($file, '.json');
            if ($name !== '') {
                $entities[] = $name;
            }
        }
        sort($entities);

        return $entities;
    }

    /**
     * @return array<string, mixed>
     */
    public function loadEntity(string $source, string $entity): array
    {
        $path = $this->baseDir . "/sources/{$source}/entities/{$entity}.json";
        $data = $this->readJson($path);

        if (($data['source'] ?? null) !== $source) {
            throw new \InvalidArgumentException("Source mismatch pour entité '{$entity}'.");
        }
        if (($data['entity'] ?? null) !== $entity) {
            throw new \InvalidArgumentException("Entity mismatch: attendu '{$entity}'.");
        }

        $endpoints = $data['endpoints'] ?? null;
        if (!is_array($endpoints)) {
            throw new \InvalidArgumentException("Config entité '{$source}/{$entity}': 'endpoints' requis.");
        }

        // Mapping uniquement depuis la BDD (panneau admin). On n'utilise plus le mapping des JSON.
        if ($this->mappingService !== null) {
            $data['mapping'] = $this->mappingService->getMappingForEntity($source, $entity) ?? [];
        } elseif (is_array($data['mapping'] ?? null)) {
            // Fallback si le service n'est pas injecté (tests, CLI sans container).
            $data['mapping'] = $data['mapping'];
        } else {
            $data['mapping'] = [];
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function readJson(string $path): array
    {
        if (!is_file($path)) {
            throw new \RuntimeException("Config JSON introuvable: {$path}");
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new \RuntimeException("Impossible de lire le fichier JSON: {$path}");
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException("JSON invalide: {$path}");
        }

        return $decoded;
    }
}
