<?php

declare(strict_types=1);

namespace App\Services\Seeder\Monster;

use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

/**
 * Catalogue des invocations de classe JDR (JSON `entities/monsters/class-summons.json`).
 *
 * @example $catalog = ClassSummonCatalog::load();
 */
final class ClassSummonCatalog
{
    public const SCHEMA_VERSION = '1';

    public const OFFICIAL_ID_PREFIX = 'jdr:summon:';

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(private readonly array $payload) {}

    public static function path(): string
    {
        return database_path('seeders/data/entities/monsters/class-summons.json');
    }

    /**
     * @throws JsonException
     */
    public static function load(?string $path = null): self
    {
        $file = $path ?? self::path();
        if (! File::isFile($file)) {
            throw new RuntimeException('Catalogue d’invocations de classe introuvable : '.$file);
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);

        return new self($payload);
    }

    /**
     * @return list<array{
     *     key: string,
     *     official_id: string,
     *     action_official_id: string,
     *     name: string,
     *     breed: string,
     *     character_level: int,
     *     size: int,
     *     monster_race: string|null,
     *     primary: string,
     *     description: string,
     *     other_info: string,
     *     image: string|null,
     *     action: array{
     *         name: string,
     *         kind: string,
     *         element: string|null,
     *         value: string,
     *         attack_characteristic_key: string|null,
     *         resolution_mode: string,
     *         is_magic: bool
     *     }
     * }>
     */
    public function entries(): array
    {
        $raw = $this->payload['entries'] ?? [];
        if (! is_array($raw)) {
            return [];
        }

        $entries = [];
        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $key = trim((string) ($row['key'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            if ($key === '' || $name === '') {
                continue;
            }
            $actionRaw = is_array($row['action'] ?? null) ? $row['action'] : [];
            $actionName = trim((string) ($actionRaw['name'] ?? ''));
            $kind = trim((string) ($actionRaw['kind'] ?? ''));
            $value = trim((string) ($actionRaw['value'] ?? ''));
            if ($actionName === '' || ! in_array($kind, ['frapper', 'soigner'], true) || $value === '') {
                continue;
            }

            $primary = trim((string) ($row['primary'] ?? 'strong'));
            if (! in_array($primary, ['strong', 'intel', 'agi', 'chance', 'sagesse', 'vitality'], true)) {
                $primary = 'strong';
            }

            $entries[] = [
                'key' => $key,
                'official_id' => self::OFFICIAL_ID_PREFIX.$key,
                'action_official_id' => self::OFFICIAL_ID_PREFIX.$key.':action',
                'name' => $name,
                'breed' => trim((string) ($row['breed'] ?? '')),
                'character_level' => max(1, (int) ($row['character_level'] ?? 1)),
                'size' => max(0, min(5, (int) ($row['size'] ?? 1))),
                'monster_race' => $this->nullableString($row['monster_race'] ?? null),
                'primary' => $primary,
                'description' => (string) ($row['description'] ?? ''),
                'other_info' => (string) ($row['other_info'] ?? ''),
                'image' => $this->nullableString($row['image'] ?? null),
                'action' => [
                    'name' => $actionName,
                    'kind' => $kind,
                    'element' => $this->nullableString($actionRaw['element'] ?? null),
                    'value' => $value,
                    'attack_characteristic_key' => $this->nullableString($actionRaw['attack_characteristic_key'] ?? null),
                    'resolution_mode' => (string) ($actionRaw['resolution_mode'] ?? 'attack_roll'),
                    'is_magic' => (bool) ($actionRaw['is_magic'] ?? false),
                ],
            ];
        }

        return $entries;
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value) && ! is_int($value)) {
            return null;
        }
        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
