<?php

declare(strict_types=1);

namespace App\Services\Seeder\Consumable;

use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

/**
 * Catalogue des consommables utilitaires JDR (sans recette, prix custom).
 *
 * @example $catalog = UtilityConsumableCatalog::load();
 */
final class UtilityConsumableCatalog
{
    public const SCHEMA_VERSION = '1';

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(private readonly array $payload) {}

    public static function path(): string
    {
        return database_path('seeders/data/entities/consumables/utility-playable.json');
    }

    /**
     * @throws JsonException
     */
    public static function load(?string $path = null): self
    {
        $file = $path ?? self::path();
        if (! File::isFile($file)) {
            throw new RuntimeException('Catalogue de consommables utilitaires introuvable : '.$file);
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);

        return new self($payload);
    }

    /**
     * @return list<array{dofusdb_type_id: int, name: string}>
     */
    public function types(): array
    {
        $types = [];
        foreach (is_array($this->payload['types'] ?? null) ? $this->payload['types'] : [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $types[] = [
                'dofusdb_type_id' => (int) ($row['dofusdb_type_id'] ?? 0),
                'name' => (string) ($row['name'] ?? ''),
            ];
        }

        return $types;
    }

    /**
     * @return list<array{
     *     name: string,
     *     dofusdb_id: string|null,
     *     official_id: string|null,
     *     type_dofus_id: int,
     *     level: string,
     *     rarity: int,
     *     price: int,
     *     effect: string,
     *     description: string|null
     * }>
     */
    public function entries(): array
    {
        $entries = [];
        foreach (is_array($this->payload['entries'] ?? null) ? $this->payload['entries'] : [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $name = trim((string) ($row['name'] ?? ''));
            $typeId = (int) ($row['type_dofus_id'] ?? 0);
            if ($name === '' || $typeId < 1) {
                continue;
            }
            $dofusdbId = $this->nullableString($row['dofusdb_id'] ?? null);
            $officialId = $this->nullableString($row['official_id'] ?? null);
            if ($dofusdbId === null && $officialId === null) {
                continue;
            }

            $entries[] = [
                'name' => $name,
                'dofusdb_id' => $dofusdbId,
                'official_id' => $officialId,
                'type_dofus_id' => $typeId,
                'level' => (string) ($row['level'] ?? '1'),
                'rarity' => max(0, min(5, (int) ($row['rarity'] ?? 0))),
                'price' => max(0, (int) ($row['price'] ?? 0)),
                'effect' => (string) ($row['effect'] ?? ''),
                'description' => $this->nullableString($row['description'] ?? null),
            ];
        }

        return $entries;
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }
        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
