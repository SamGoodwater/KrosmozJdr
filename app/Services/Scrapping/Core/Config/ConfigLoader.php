<?php

namespace App\Services\Scrapping\Core\Config;

/**
 * Charge les configs depuis resources/scrapping/config/ (source + entités).
 * Endpoints, filtres, target, meta, relations viennent des JSON.
 * Mapping : BDD via ScrappingMappingService si présent, sinon fallback sur le mapping des JSON d'entité.
 *
 * Validation minimale : version, source, entity, endpoints ; mapping = BDD ou JSON (jamais vide si le JSON en contient).
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

        // Mapping : BDD (panneau admin) si présent, sinon fallback sur le JSON (tests, première install).
        $jsonMapping = is_array($data['mapping'] ?? null) ? $data['mapping'] : [];
        if ($this->mappingService !== null) {
            $fromDb = $this->mappingService->getMappingForEntity($source, $entity);
            $data['mapping'] = ($fromDb !== null && $fromDb !== []) ? $fromDb : $jsonMapping;
        } else {
            $data['mapping'] = $jsonMapping;
        }

        return $data;
    }

    /**
     * Lit le mapping d'une entité depuis le fichier JSON uniquement (sans fusion BDD).
     * Utilisé pour lister les chemins possibles (modal « Lier » depuis la caractéristique).
     * Ne retourne que les entrées ayant "from.path" (pas "extract").
     *
     * @return list<array{path: string, key: string, langAware: bool, targets: list<array{model: string, field: string}>, formatters: list<array{name: string, args: array}>}>
     */
    public function getEntityMappingEntriesFromFile(string $source, string $entity): array
    {
        $path = $this->baseDir . "/sources/{$source}/entities/{$entity}.json";
        $data = $this->readJson($path);
        $mapping = $data['mapping'] ?? [];
        if (! is_array($mapping)) {
            return [];
        }
        $out = [];
        foreach ($mapping as $entry) {
            $from = $entry['from'] ?? null;
            if (! is_array($from) || ! isset($from['path']) || is_string($from['path']) === false) {
                continue;
            }
            $to = $entry['to'] ?? [];
            $targets = [];
            foreach (is_array($to) ? $to : [] as $t) {
                if (isset($t['model'], $t['field'])) {
                    $targets[] = ['model' => (string) $t['model'], 'field' => (string) $t['field']];
                }
            }
            $formatters = $entry['formatters'] ?? [];
            if (! is_array($formatters)) {
                $formatters = [];
            }
            $out[] = [
                'path' => $from['path'],
                'key' => $entry['key'] ?? $from['path'],
                'langAware' => (bool) ($from['langAware'] ?? false),
                'targets' => $targets,
                'formatters' => $formatters,
            ];
        }
        return $out;
    }

    /**
     * Retourne le libellé d'une entité depuis son fichier JSON (clé "label" ou id de l'entité).
     */
    public function getEntityLabel(string $source, string $entity): string
    {
        $path = $this->baseDir . "/sources/{$source}/entities/{$entity}.json";
        if (! is_file($path)) {
            return $entity;
        }
        $data = $this->readJson($path);
        return (string) ($data['label'] ?? $entity);
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
