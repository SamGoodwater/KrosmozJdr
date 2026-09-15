<?php

declare(strict_types=1);

namespace App\Services\Seeder\Npc;

use App\Support\Creature\CreatureSize;
use App\Support\Npc\NpcRole;
use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

/**
 * Catalogue des PNJ JDR (JSON `entities/npcs/*.json`).
 *
 * @example $catalog = IncarnamNpcCatalog::load();
 */
final class IncarnamNpcCatalog
{
    public const SCHEMA_VERSION = '1';

    public const OFFICIAL_ID_PREFIX = 'jdr:npc:incarnam:';

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(private readonly array $payload) {}

    public static function directory(): string
    {
        return database_path('seeders/data/entities/npcs');
    }

    public static function filePath(string $filename = 'incarnam.json'): string
    {
        return self::directory().DIRECTORY_SEPARATOR.$filename;
    }

    /**
     * @throws JsonException
     */
    public static function load(string $path = ''): self
    {
        $path = $path !== '' ? $path : self::filePath();
        if (! File::isFile($path)) {
            throw new RuntimeException('Catalogue PNJ introuvable : '.$path);
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        return new self($payload);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function entries(): array
    {
        $raw = $this->payload['entries'] ?? [];
        if (! is_array($raw)) {
            return [];
        }

        $defaultLocation = $this->nullableString($this->payload['location'] ?? null) ?? '';

        $entries = [];
        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $key = trim((string) ($row['key'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            $role = trim((string) ($row['npc_role'] ?? ''));
            if ($key === '' || $name === '' || ! in_array($role, NpcRole::values(), true)) {
                continue;
            }

            $hostility = (int) ($row['hostility'] ?? 2);
            $hostility = max(0, min(4, $hostility));

            $entries[] = [
                'key' => $key,
                'official_id' => self::OFFICIAL_ID_PREFIX.$key,
                'name' => $name,
                'level' => max(1, (int) ($row['level'] ?? 1)),
                'size' => max(CreatureSize::MINUSCULE, min(CreatureSize::GIGANTESQUE, (int) ($row['size'] ?? CreatureSize::MOYEN))),
                'npc_role' => $role,
                'hostility' => $hostility,
                'breed' => $this->nullableString($row['breed'] ?? null),
                'specialization' => $this->nullableString($row['specialization'] ?? null),
                'age' => $this->nullableString($row['age'] ?? null),
                'location' => (string) ($row['location'] ?? $defaultLocation),
                'description' => (string) ($row['description'] ?? ''),
                'story' => $this->nullableString($row['story'] ?? null),
                'historical' => $this->nullableString($row['historical'] ?? null),
                'other_info' => (string) ($row['other_info'] ?? ''),
                'languages' => $this->stringList($row['languages'] ?? null),
                'items' => $this->stringList($row['items'] ?? null),
                'spells' => $this->stringList($row['spells'] ?? null),
                'panoplies' => $this->stringList($row['panoplies'] ?? null),
            ];
        }

        return $entries;
    }

    /**
     * @return list<string>
     */
    private function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }
        $out = [];
        foreach ($value as $item) {
            $trimmed = trim((string) $item);
            if ($trimmed !== '') {
                $out[] = $trimmed;
            }
        }

        return $out;
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
