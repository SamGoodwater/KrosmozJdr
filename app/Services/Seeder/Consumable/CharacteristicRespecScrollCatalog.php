<?php

declare(strict_types=1);

namespace App\Services\Seeder\Consumable;

use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

/**
 * Catalogue des parchemins de caractéristique (respec JDR, sans recette).
 *
 * @example $catalog = CharacteristicRespecScrollCatalog::load();
 */
final class CharacteristicRespecScrollCatalog
{
    public const SCHEMA_VERSION = '1';

    public const TYPE_DOFUS_ID = 76;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(private readonly array $payload) {}

    public static function path(): string
    {
        return database_path('seeders/data/entities/consumables/characteristic-respec-scrolls.json');
    }

    /**
     * @throws JsonException
     */
    public static function load(?string $path = null): self
    {
        $file = $path ?? self::path();
        if (! File::isFile($file)) {
            throw new RuntimeException('Catalogue de parchemins de caractéristique introuvable : '.$file);
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);

        return new self($payload);
    }

    public function consumableTypeDofusId(): int
    {
        return (int) ($this->payload['consumable_type_dofus_id'] ?? self::TYPE_DOFUS_ID);
    }

    public function consumableTypeName(): string
    {
        return (string) ($this->payload['consumable_type_name'] ?? 'Parchemin de caractéristique');
    }

    public function description(): ?string
    {
        return $this->nullableString($this->payload['description'] ?? null);
    }

    /**
     * @return list<array{prefix: string, points: int, price: int, level: string}>
     */
    public function tiers(): array
    {
        $tiers = [];
        foreach (is_array($this->payload['tiers'] ?? null) ? $this->payload['tiers'] : [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $tiers[] = [
                'prefix' => (string) ($row['prefix'] ?? ''),
                'points' => (int) ($row['points'] ?? 0),
                'price' => (int) ($row['price'] ?? 0),
                'level' => (string) ($row['level'] ?? ''),
            ];
        }

        return $tiers;
    }

    /**
     * @return array<string, array{label: string, of: string}>
     */
    public function characteristics(): array
    {
        $out = [];
        foreach (is_array($this->payload['characteristics'] ?? null) ? $this->payload['characteristics'] : [] as $key => $row) {
            if (! is_string($key) || ! is_array($row)) {
                continue;
            }
            $out[$key] = [
                'label' => (string) ($row['label'] ?? ''),
                'of' => (string) ($row['of'] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * @return list<array{
     *     characteristic: string,
     *     label: string,
     *     of: string,
     *     tier: int,
     *     points: int,
     *     price: int,
     *     level: string,
     *     name: string,
     *     dofusdb_id: string,
     *     description: string|null
     * }>
     */
    public function entries(): array
    {
        $tiers = $this->tiers();
        $characteristics = $this->characteristics();
        $description = $this->description();
        $entries = [];

        foreach (is_array($this->payload['entries'] ?? null) ? $this->payload['entries'] : [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $tierIndex = (int) ($row['tier'] ?? -1);
            $tier = $tiers[$tierIndex] ?? null;
            $charKey = (string) ($row['characteristic'] ?? '');
            $char = $characteristics[$charKey] ?? null;
            $dofusdbId = $this->nullableString($row['dofusdb_id'] ?? null);
            if ($tier === null || $char === null || $dofusdbId === null) {
                continue;
            }

            $entries[] = [
                'characteristic' => $charKey,
                'label' => $char['label'],
                'of' => $char['of'],
                'tier' => $tierIndex,
                'points' => $tier['points'],
                'price' => $tier['price'],
                'level' => $tier['level'],
                'name' => (string) ($row['name'] ?? ''),
                'dofusdb_id' => $dofusdbId,
                'description' => $description,
            ];
        }

        return $entries;
    }

    /**
     * Texte d’effet affiché sur la fiche (respec, hors combat).
     *
     * @example CharacteristicRespecScrollCatalog::effectText(1, 'de Chance')
     */
    public static function effectText(int $points, string $of): string
    {
        if ($points === 1) {
            return sprintf(
                'Retire 1 point %s déjà réparti et place-le sur une autre caractéristique (plafonds habituels). Hors combat. Usage unique.',
                $of
            );
        }

        return sprintf(
            'Retire %d points %s déjà répartis et place-les sur une ou plusieurs autres caractéristiques (plafonds habituels). Hors combat. Usage unique.',
            $points,
            $of
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
