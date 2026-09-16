<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

/**
 * Charge la config IA (base si présente, sinon `resources/ia/generation.json`).
 *
 * @example
 * $loader = GenerationConfigLoader::default();
 * $loader->forEntity('item')->isFieldFrozen('name');
 * $loader->get('generation.max_retries');
 */
final class GenerationConfigLoader
{
    /** @var list<string> */
    public const ENTITY_TYPES = ['item', 'spell', 'monster', 'npc', 'consumable'];

    /** @var list<string> */
    public const ENTITY_KNOWN_KEYS = [
        'has_dofus_source',
        'frozen_fields',
        'writable_fields',
        'frozen_characteristics',
        'writable_characteristics',
        'example_ids',
    ];

    /** @var array<string, mixed>|null */
    private ?array $data = null;

    /**
     * @param  array<string, mixed>|null  $payloadOverride  Payload déjà lu (ex. ligne en base).
     */
    public function __construct(
        private readonly string $configPath,
        private readonly ?array $payloadOverride = null,
    ) {}

    public static function default(): self
    {
        $path = base_path('resources/ia/generation.json');
        $override = null;
        if (function_exists('app') && app()->bound(GenerationConfigStore::class)) {
            $override = app(GenerationConfigStore::class)->storedPayload();
        }

        return new self($path, $override);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function assertValid(array $data): void
    {
        $loader = new self('/dev/null', $data);
        foreach (self::ENTITY_TYPES as $entity) {
            $loader->forEntity($entity);
        }
    }

    /**
     * Lit une valeur par chemin pointé. Les clés hors schéma sont autorisées.
     */
    public function get(string $path, mixed $default = null): mixed
    {
        $segments = explode('.', $path);
        $current = $this->data();

        foreach ($segments as $segment) {
            if (! is_array($current) || ! array_key_exists($segment, $current)) {
                return $default;
            }
            $current = $current[$segment];
        }

        return $current;
    }

    public function forEntity(string $entity): EntityGenerationProfile
    {
        $entities = $this->data()['entities'] ?? [];
        if (! is_array($entities) || ! array_key_exists($entity, $entities) || ! is_array($entities[$entity])) {
            throw new \InvalidArgumentException("Type d'entité IA inconnu: {$entity}");
        }

        $row = $entities[$entity];
        $extra = [];
        foreach ($row as $key => $value) {
            if (is_string($key) && ! in_array($key, self::ENTITY_KNOWN_KEYS, true) && ! str_starts_with($key, '_')) {
                $extra[$key] = $value;
            }
        }

        return new EntityGenerationProfile(
            entity: $entity,
            hasDofusSource: (bool) ($row['has_dofus_source'] ?? false),
            frozenFields: $this->frozenListOrWildcard($row['frozen_fields'] ?? [], "entities.{$entity}.frozen_fields"),
            writableFields: $this->stringList($row['writable_fields'] ?? [], "entities.{$entity}.writable_fields"),
            frozenCharacteristics: $this->frozenListOrWildcard($row['frozen_characteristics'] ?? [], "entities.{$entity}.frozen_characteristics"),
            writableCharacteristics: $this->stringList($row['writable_characteristics'] ?? [], "entities.{$entity}.writable_characteristics"),
            exampleIds: $this->exampleRefList($row['example_ids'] ?? [], "entities.{$entity}.example_ids"),
            extra: $extra,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function raw(): array
    {
        return $this->data();
    }

    /**
     * @return array<string, mixed>
     */
    private function data(): array
    {
        if ($this->data !== null) {
            return $this->data;
        }

        $decoded = $this->payloadOverride ?? $this->readFile();
        $this->validate($decoded);
        $this->data = $decoded;

        return $this->data;
    }

    /**
     * @return array<string, mixed>
     */
    private function readFile(): array
    {
        if (! is_file($this->configPath)) {
            throw new \RuntimeException("Config JSON introuvable: {$this->configPath}");
        }

        $raw = file_get_contents($this->configPath);
        if ($raw === false) {
            throw new \RuntimeException("Impossible de lire le fichier JSON: {$this->configPath}");
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new \RuntimeException("JSON invalide: {$this->configPath}", 0, $exception);
        }

        if (! is_array($decoded)) {
            throw new \RuntimeException("JSON invalide: {$this->configPath}");
        }

        return $decoded;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function validate(array $data): void
    {
        $version = $data['version'] ?? null;
        if (! is_int($version) || $version < 1) {
            throw new \InvalidArgumentException('Config IA : version doit être un entier ≥ 1.');
        }

        $entities = $data['entities'] ?? null;
        if (! is_array($entities)) {
            throw new \InvalidArgumentException('Config IA : entities doit être un objet.');
        }

        foreach (self::ENTITY_TYPES as $entity) {
            if (! array_key_exists($entity, $entities) || ! is_array($entities[$entity])) {
                throw new \InvalidArgumentException("Config IA : entities.{$entity} requis.");
            }
        }

        if (isset($data['generation']) && ! is_array($data['generation'])) {
            throw new \InvalidArgumentException('Config IA : generation doit être un objet.');
        }
    }

    /**
     * @return '*'|list<string>
     */
    private function frozenListOrWildcard(mixed $value, string $path): string|array
    {
        if ($value === '*') {
            return '*';
        }

        return $this->stringList($value, $path);
    }

    /**
     * @return list<string>
     */
    private function stringList(mixed $value, string $path): array
    {
        if (! is_array($value)) {
            throw new \InvalidArgumentException("Config IA : {$path} doit être \"*\" ou une liste de chaînes.");
        }

        $out = [];
        foreach ($value as $item) {
            if (! is_string($item) || $item === '') {
                throw new \InvalidArgumentException("Config IA : {$path} ne contient que des chaînes non vides.");
            }
            $out[] = $item;
        }

        return array_values($out);
    }

    /**
     * @return list<int|string>
     */
    private function exampleRefList(mixed $value, string $path): array
    {
        if (! is_array($value)) {
            throw new \InvalidArgumentException("Config IA : {$path} doit être une liste d'ids, d'official_id ou de noms.");
        }

        $out = [];
        foreach ($value as $item) {
            if (is_int($item) && $item > 0) {
                $out[] = $item;

                continue;
            }
            if (is_string($item) && is_numeric($item) && (string) (int) $item === $item && (int) $item > 0) {
                $out[] = (int) $item;

                continue;
            }
            if (is_string($item) && trim($item) !== '' && strlen($item) <= 255) {
                $out[] = trim($item);

                continue;
            }

            throw new \InvalidArgumentException("Config IA : {$path} ne contient que des ids, official_id ou noms.");
        }

        return array_values($out);
    }
}
