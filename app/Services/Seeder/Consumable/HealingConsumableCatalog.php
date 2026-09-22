<?php

declare(strict_types=1);

namespace App\Services\Seeder\Consumable;

use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

/**
 * Catalogue versionné des consommables de soin hors combat (11 paliers × 4 types).
 *
 * @example $catalog = HealingConsumableCatalog::load();
 */
final class HealingConsumableCatalog
{
    public const SCHEMA_VERSION = '1';

    public const KINDS = ['pain', 'poisson', 'viande', 'potion'];

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(private readonly array $payload) {}

    public static function path(): string
    {
        return database_path('seeders/data/entities/consumables/healing-out-of-combat.json');
    }

    /**
     * @throws JsonException
     */
    public static function load(?string $path = null): self
    {
        $file = $path ?? self::path();
        if (! File::isFile($file)) {
            throw new RuntimeException('Catalogue de soins hors combat introuvable : '.$file);
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);

        return new self($payload);
    }

    public function recipeQuantity(): int
    {
        $quantity = (int) ($this->payload['recipe_quantity'] ?? 10);

        return max(1, $quantity);
    }

    /**
     * @return list<array{heal: int, price: int}>
     */
    public function tiers(): array
    {
        $tiers = [];
        foreach (is_array($this->payload['tiers'] ?? null) ? $this->payload['tiers'] : [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $tiers[] = [
                'heal' => (int) ($row['heal'] ?? 0),
                'price' => (int) ($row['price'] ?? 0),
            ];
        }

        return $tiers;
    }

    /**
     * @return array<string, array{consumable_type_dofus_id: int, consumable_type_name: string}>
     */
    public function kinds(): array
    {
        $kinds = [];
        foreach (is_array($this->payload['kinds'] ?? null) ? $this->payload['kinds'] : [] as $key => $row) {
            if (! is_string($key) || ! is_array($row)) {
                continue;
            }
            $kinds[$key] = [
                'consumable_type_dofus_id' => (int) ($row['consumable_type_dofus_id'] ?? 0),
                'consumable_type_name' => (string) ($row['consumable_type_name'] ?? ''),
            ];
        }

        return $kinds;
    }

    /**
     * @return list<array{
     *     kind: string,
     *     tier: int,
     *     name: string,
     *     level: string,
     *     dofusdb_id: string|null,
     *     official_id: string|null,
     *     description: string|null,
     *     resource_dofusdb_id: string,
     *     resource_name: string,
     *     heal: int,
     *     price: int,
     *     resource_price: int
     * }>
     */
    public function entries(): array
    {
        $tiers = $this->tiers();
        $quantity = $this->recipeQuantity();
        $entries = [];

        foreach (is_array($this->payload['entries'] ?? null) ? $this->payload['entries'] : [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $tierIndex = (int) ($row['tier'] ?? -1);
            $tier = $tiers[$tierIndex] ?? null;
            if ($tier === null) {
                continue;
            }
            $dofusdbId = $this->nullableString($row['dofusdb_id'] ?? null);
            $officialId = $this->nullableString($row['official_id'] ?? null);
            $resourceId = (string) ($row['resource_dofusdb_id'] ?? '');
            if ($resourceId === '') {
                continue;
            }

            $entries[] = [
                'kind' => (string) ($row['kind'] ?? ''),
                'tier' => $tierIndex,
                'name' => (string) ($row['name'] ?? ''),
                'level' => (string) ($row['level'] ?? ''),
                'dofusdb_id' => $dofusdbId,
                'official_id' => $officialId,
                'description' => $this->nullableString($row['description'] ?? null),
                'resource_dofusdb_id' => $resourceId,
                'resource_name' => (string) ($row['resource_name'] ?? ''),
                'heal' => $tier['heal'],
                'price' => $tier['price'],
                'resource_price' => intdiv($tier['price'], $quantity),
            ];
        }

        return $entries;
    }

    /**
     * Texte d’effet affiché sur la fiche (soin fixe, hors combat).
     *
     * @example HealingConsumableCatalog::effectText(5) === 'Restaure 5 PV. Hors combat uniquement.'
     */
    public static function effectText(int $heal): string
    {
        return sprintf('Restaure %d PV. Hors combat uniquement.', $heal);
    }

    /**
     * Bonus JSON filtrable (restauration de PV).
     *
     * @example HealingConsumableCatalog::bonusJson(5) === '{"life_points_restore":5}'
     */
    public static function bonusJson(int $heal): string
    {
        return json_encode(
            ['life_points_restore' => max(0, $heal)],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
        );
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
