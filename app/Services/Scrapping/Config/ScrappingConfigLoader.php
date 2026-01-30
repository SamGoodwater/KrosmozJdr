<?php

namespace App\Services\Scrapping\Config;

/**
 * Loader/validator des configs JSON de scrapping.
 *
 * @description
 * Charge les fichiers JSON versionnés dans `resources/scrapping/` et applique
 * une validation minimale (structure + formatters en liste blanche).
 *
 * Ce loader est conçu pour être branché progressivement sur l'existant (refonte in-place),
 * sans créer un second système de scrapping en doublon.
 */
class ScrappingConfigLoader
{
    private string $baseDir;
    private FormatterRegistry $formatters;

    public function __construct(?string $baseDir = null, ?FormatterRegistry $formatters = null)
    {
        $this->baseDir = $baseDir ?: base_path('resources/scrapping');
        $this->formatters = $formatters ?: FormatterRegistry::fromDefaultPath($this->baseDir);
    }

    /**
     * @return array<string, mixed>
     */
    public function loadSource(string $source): array
    {
        $path = $this->baseDir . "/sources/{$source}/source.json";
        $data = $this->readJson($path);

        $this->assertString($data, 'source');
        $this->assertInt($data, 'version');
        if (($data['source'] ?? null) !== $source) {
            throw new \InvalidArgumentException("Source mismatch: attendu '{$source}', trouvé '" . ($data['source'] ?? 'null') . "'");
        }

        return $data;
    }

    /**
     * @return array<int, string>
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

        $this->assertInt($data, 'version');
        $this->assertString($data, 'source');
        $this->assertString($data, 'entity');

        if (($data['source'] ?? null) !== $source) {
            throw new \InvalidArgumentException("Source mismatch pour entité '{$entity}': attendu '{$source}', trouvé '" . ($data['source'] ?? 'null') . "'");
        }
        if (($data['entity'] ?? null) !== $entity) {
            throw new \InvalidArgumentException("Entity mismatch: attendu '{$entity}', trouvé '" . ($data['entity'] ?? 'null') . "'");
        }

        $mapping = $data['mapping'] ?? null;
        if (!is_array($mapping)) {
            throw new \InvalidArgumentException("Config entité '{$source}/{$entity}': 'mapping' doit être un tableau.");
        }

        foreach ($mapping as $i => $map) {
            if (!is_array($map)) {
                throw new \InvalidArgumentException("Mapping[{$i}] invalide: attendu un objet.");
            }
            if (!isset($map['key']) || !is_string($map['key']) || $map['key'] === '') {
                throw new \InvalidArgumentException("Mapping[{$i}] invalide: 'key' requis.");
            }

            $from = $map['from'] ?? null;
            if (!is_array($from) || !isset($from['path']) || !is_string($from['path']) || $from['path'] === '') {
                throw new \InvalidArgumentException("Mapping '{$map['key']}': 'from.path' requis.");
            }

            $to = $map['to'] ?? null;
            if (!is_array($to) || empty($to)) {
                throw new \InvalidArgumentException("Mapping '{$map['key']}': 'to' doit être un tableau non vide.");
            }
            foreach ($to as $j => $t) {
                if (!is_array($t) || !isset($t['model'], $t['field']) || !is_string($t['model']) || !is_string($t['field'])) {
                    throw new \InvalidArgumentException("Mapping '{$map['key']}': to[{$j}] invalide (model/field requis).");
                }
            }

            $formatters = $map['formatters'] ?? [];
            if (!is_array($formatters)) {
                throw new \InvalidArgumentException("Mapping '{$map['key']}': 'formatters' doit être un tableau.");
            }
            foreach ($formatters as $k => $fmt) {
                if (!is_array($fmt) || !isset($fmt['name']) || !is_string($fmt['name']) || $fmt['name'] === '') {
                    throw new \InvalidArgumentException("Mapping '{$map['key']}': formatter[{$k}] invalide (name requis).");
                }
                if (!$this->formatters->has($fmt['name'])) {
                    throw new \InvalidArgumentException("Mapping '{$map['key']}': formatter non autorisé '{$fmt['name']}'.");
                }
                if (isset($fmt['args']) && !is_array($fmt['args'])) {
                    throw new \InvalidArgumentException("Mapping '{$map['key']}': formatter '{$fmt['name']}' args doit être un objet.");
                }
            }
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

    /**
     * @param array<string, mixed> $data
     */
    private function assertString(array $data, string $key): void
    {
        if (!isset($data[$key]) || !is_string($data[$key]) || $data[$key] === '') {
            throw new \InvalidArgumentException("Champ requis '{$key}' manquant ou invalide.");
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function assertInt(array $data, string $key): void
    {
        if (!isset($data[$key]) || !is_int($data[$key])) {
            throw new \InvalidArgumentException("Champ requis '{$key}' manquant ou invalide (int attendu).");
        }
    }
}

