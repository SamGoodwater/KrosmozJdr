<?php

namespace App\Services\Scrapping\Config;

/**
 * Registry des formatters autorisés pour le scrapping.
 *
 * @description
 * Charge `resources/scrapping/formatters/registry.json` et expose les formatters
 * autorisés (liste blanche). Sert à valider les configs JSON pour éviter toute
 * exécution arbitraire.
 */
class FormatterRegistry
{
    /** @var array<string, array{name:string,type?:string,argsSchema?:array<string,string>,description?:string}> */
    private array $formattersByName = [];

    public function __construct(private string $registryPath)
    {
        $this->load();
    }

    public static function fromDefaultPath(?string $baseDir = null): self
    {
        $baseDir = $baseDir ?: base_path('resources/scrapping');
        return new self($baseDir . '/formatters/registry.json');
    }

    public function has(string $name): bool
    {
        return isset($this->formattersByName[$name]);
    }

    /**
     * @return array{name:string,type?:string,argsSchema?:array<string,string>,description?:string}|null
     */
    public function get(string $name): ?array
    {
        return $this->formattersByName[$name] ?? null;
    }

    private function load(): void
    {
        if (!is_file($this->registryPath)) {
            throw new \RuntimeException("Formatter registry introuvable: {$this->registryPath}");
        }

        $raw = file_get_contents($this->registryPath);
        if ($raw === false) {
            throw new \RuntimeException("Impossible de lire le formatter registry: {$this->registryPath}");
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException("Formatter registry invalide (JSON): {$this->registryPath}");
        }

        $formatters = $decoded['formatters'] ?? null;
        if (!is_array($formatters)) {
            throw new \RuntimeException("Formatter registry invalide: clé 'formatters' manquante.");
        }

        $byName = [];
        foreach ($formatters as $fmt) {
            if (!is_array($fmt) || !isset($fmt['name']) || !is_string($fmt['name'])) {
                continue;
            }
            $byName[$fmt['name']] = $fmt;
        }

        $this->formattersByName = $byName;
    }
}

