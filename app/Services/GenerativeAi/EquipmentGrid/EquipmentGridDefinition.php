<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\EquipmentGrid;

/**
 * Charge `resources/ia/equipment-grid.json` et résout les super-types DofusDB.
 *
 * @example
 * $grid = EquipmentGridDefinition::loadDefault();
 * $grid->slotKeyForDofusTypeId(9); // 'ring'
 */
final class EquipmentGridDefinition
{
    public const OFFICIAL_ID_PREFIX = 'ia-grid';

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $typeIdToSlot
     * @param  array<string, array{key: string, label: string, category: string, dofusdb_type_ids: list<int>, default_dofusdb_type_id: int}>  $slots
     * @param  array<string, array{label: string, keys: list<string>, primary_key: string, damage_key: string, dofus_characteristic_ids: list<int>, dofus_element_ids: list<int>}>  $voies
     * @param  list<array{from: int, to: int, value: int}>  $bands
     */
    public function __construct(
        private readonly array $data,
        private readonly array $slots,
        private readonly array $voies,
        private readonly array $typeIdToSlot,
        private readonly array $bands,
        public readonly int $minLevel,
        public readonly int $maxLevel,
        public readonly bool $includeNeutral,
        public readonly string $officialIdPrefix,
        public readonly string $namePrefix,
    ) {}

    public static function defaultPath(): string
    {
        return base_path('resources/ia/equipment-grid.json');
    }

    public static function itemTypesPath(): string
    {
        return base_path('resources/scrapping/config/sources/dofusdb/item-types.json');
    }

    public static function loadDefault(): self
    {
        return self::loadFromFile(self::defaultPath(), self::itemTypesPath());
    }

    /**
     * @param  array<string, mixed>|null  $itemTypesPayload
     */
    public static function loadFromFile(string $path, ?string $itemTypesPath = null, ?array $itemTypesPayload = null): self
    {
        if (! is_file($path)) {
            throw new \InvalidArgumentException('Fichier de grille introuvable : '.$path);
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new \RuntimeException('Impossible de lire '.$path);
        }
        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \InvalidArgumentException('JSON de grille invalide : '.$e->getMessage(), 0, $e);
        }
        if (! is_array($data)) {
            throw new \InvalidArgumentException('JSON de grille : objet attendu.');
        }

        $superTypeToTypeIds = [];
        if (is_array($itemTypesPayload)) {
            $superTypeToTypeIds = self::indexSuperTypes($itemTypesPayload);
        } elseif (is_string($itemTypesPath) && is_file($itemTypesPath)) {
            $typesRaw = file_get_contents($itemTypesPath);
            if ($typesRaw !== false) {
                try {
                    $decoded = json_decode($typesRaw, true, 512, JSON_THROW_ON_ERROR);
                    if (is_array($decoded)) {
                        $superTypeToTypeIds = self::indexSuperTypes($decoded);
                    }
                } catch (\JsonException) {
                    $superTypeToTypeIds = [];
                }
            }
        }

        return self::fromArray($data, $superTypeToTypeIds);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, list<int>>  $superTypeToTypeIds
     */
    public static function fromArray(array $data, array $superTypeToTypeIds = []): self
    {
        $levels = is_array($data['levels'] ?? null) ? $data['levels'] : [];
        $minLevel = max(1, (int) ($levels['min'] ?? 1));
        $maxLevel = min(20, (int) ($levels['max'] ?? 20));
        if ($minLevel > $maxLevel) {
            throw new \InvalidArgumentException('equipment-grid : levels.min > levels.max');
        }

        $voies = [];
        $voiesRaw = $data['voies'] ?? null;
        if (! is_array($voiesRaw) || $voiesRaw === []) {
            throw new \InvalidArgumentException('equipment-grid : voies manquantes');
        }
        foreach ($voiesRaw as $key => $row) {
            if (! is_string($key) || $key === '' || ! is_array($row)) {
                continue;
            }
            $voies[$key] = [
                'label' => (string) ($row['label'] ?? $key),
                'keys' => self::stringList($row['keys'] ?? []),
                'primary_key' => (string) ($row['primary_key'] ?? 'strength'),
                'damage_key' => (string) ($row['damage_key'] ?? 'fixed_damage_earth'),
                'dofus_characteristic_ids' => self::intList($row['dofus_characteristic_ids'] ?? []),
                'dofus_element_ids' => self::intList($row['dofus_element_ids'] ?? []),
            ];
        }
        if ($voies === []) {
            throw new \InvalidArgumentException('equipment-grid : aucune voie valide');
        }

        $slots = [];
        $typeIdToSlot = [];
        $slotsRaw = $data['slots'] ?? null;
        if (! is_array($slotsRaw) || $slotsRaw === []) {
            throw new \InvalidArgumentException('equipment-grid : slots manquants');
        }
        foreach ($slotsRaw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $slotKey = (string) ($row['key'] ?? '');
            if ($slotKey === '') {
                continue;
            }
            $typeIds = self::intList($row['dofusdb_type_ids'] ?? []);
            foreach (self::intList($row['dofusdb_super_type_ids'] ?? []) as $superId) {
                foreach ($superTypeToTypeIds[$superId] ?? [] as $typeId) {
                    $typeIds[] = $typeId;
                }
            }
            $typeIds = array_values(array_unique($typeIds));
            $defaultTypeId = isset($row['default_dofusdb_type_id'])
                ? (int) $row['default_dofusdb_type_id']
                : ($typeIds[0] ?? 0);
            if ($defaultTypeId < 1) {
                throw new \InvalidArgumentException("equipment-grid : slot {$slotKey} sans type DofusDB");
            }
            $slots[$slotKey] = [
                'key' => $slotKey,
                'label' => (string) ($row['label'] ?? $slotKey),
                'category' => (string) ($row['category'] ?? 'accessory'),
                'dofusdb_type_ids' => $typeIds,
                'default_dofusdb_type_id' => $defaultTypeId,
            ];
            foreach ($typeIds as $typeId) {
                $typeIdToSlot[$typeId] = $slotKey;
            }
        }
        if ($slots === []) {
            throw new \InvalidArgumentException('equipment-grid : aucun slot valide');
        }

        $bands = [];
        foreach (is_array($data['bands'] ?? null) ? $data['bands'] : [] as $band) {
            if (! is_array($band)) {
                continue;
            }
            $bands[] = [
                'from' => (int) ($band['from'] ?? 0),
                'to' => (int) ($band['to'] ?? 0),
                'value' => (int) ($band['value'] ?? 0),
            ];
        }
        if ($bands === []) {
            $bands = [
                ['from' => 1, 'to' => 5, 'value' => 1],
                ['from' => 6, 'to' => 10, 'value' => 2],
                ['from' => 11, 'to' => 15, 'value' => 3],
                ['from' => 16, 'to' => 20, 'value' => 4],
            ];
        }

        return new self(
            data: $data,
            slots: $slots,
            voies: $voies,
            typeIdToSlot: $typeIdToSlot,
            bands: $bands,
            minLevel: $minLevel,
            maxLevel: $maxLevel,
            includeNeutral: (bool) ($data['include_neutral'] ?? false),
            officialIdPrefix: (string) ($data['official_id_prefix'] ?? self::OFFICIAL_ID_PREFIX),
            namePrefix: (string) ($data['name_prefix'] ?? '[Grille]'),
        );
    }

    public function slotKeyForDofusTypeId(?int $dofusTypeId): ?string
    {
        if ($dofusTypeId === null) {
            return null;
        }

        return $this->typeIdToSlot[$dofusTypeId] ?? null;
    }

    /**
     * @return array{key: string, label: string, category: string, dofusdb_type_ids: list<int>, default_dofusdb_type_id: int}|null
     */
    public function slot(string $key): ?array
    {
        return $this->slots[$key] ?? null;
    }

    /**
     * @return array<string, array{key: string, label: string, category: string, dofusdb_type_ids: list<int>, default_dofusdb_type_id: int}>
     */
    public function slots(): array
    {
        return $this->slots;
    }

    /**
     * @return list<int>
     */
    public function allDofusTypeIds(): array
    {
        return array_keys($this->typeIdToSlot);
    }

    /**
     * Voies actives (sans Neutre si `include_neutral` est faux).
     *
     * @return list<string>
     */
    public function activeVoieKeys(): array
    {
        $keys = array_keys($this->voies);
        if (! $this->includeNeutral) {
            $keys = array_values(array_filter($keys, static fn (string $key): bool => $key !== 'neutre'));
        }

        return $keys;
    }

    /**
     * @return array{label: string, keys: list<string>, primary_key: string, damage_key: string, dofus_characteristic_ids: list<int>, dofus_element_ids: list<int>}|null
     */
    public function voie(string $key): ?array
    {
        return $this->voies[$key] ?? null;
    }

    /**
     * @return array<string, array{label: string, keys: list<string>, primary_key: string, damage_key: string, dofus_characteristic_ids: list<int>, dofus_element_ids: list<int>}>
     */
    public function voies(): array
    {
        return $this->voies;
    }

    public function clampLevel(int $level): int
    {
        return max($this->minLevel, min($this->maxLevel, $level));
    }

    public function bandValue(int $level): int
    {
        $clamped = $this->clampLevel($level);
        $best = 1;
        foreach ($this->bands as $band) {
            if ($clamped >= $band['from'] && $clamped <= $band['to']) {
                return max(1, $band['value']);
            }
            if ($clamped >= $band['from']) {
                $best = max(1, $band['value']);
            }
        }

        return $best;
    }

    public function officialId(string $slotKey, string $voieKey, int $level): string
    {
        return $this->officialIdPrefix.':'.$slotKey.':'.$voieKey.':'.$this->clampLevel($level);
    }

    public function cellCount(): int
    {
        return count($this->slots) * count($this->activeVoieKeys()) * (($this->maxLevel - $this->minLevel) + 1);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, list<int>>
     */
    private static function indexSuperTypes(array $payload): array
    {
        $rows = $payload['itemTypes'] ?? $payload;
        if (! is_array($rows)) {
            return [];
        }
        $out = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $typeId = isset($row['id']) ? (int) $row['id'] : 0;
            $superId = isset($row['superTypeId']) ? (int) $row['superTypeId'] : 0;
            if ($typeId < 1 || $superId < 1) {
                continue;
            }
            $out[$superId][] = $typeId;
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    private static function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }
        $out = [];
        foreach ($value as $item) {
            if (is_string($item) && $item !== '') {
                $out[] = $item;
            }
        }

        return $out;
    }

    /**
     * @return list<int>
     */
    private static function intList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }
        $out = [];
        foreach ($value as $item) {
            if (is_numeric($item)) {
                $out[] = (int) $item;
            }
        }

        return $out;
    }
}
