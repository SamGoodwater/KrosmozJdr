<?php

declare(strict_types=1);

namespace App\Services\Seeder\Monster;

use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

/**
 * Catalogue du bestiaire JDR Incarnam (JSON `entities/monsters/incarnam.json`).
 *
 * @example $catalog = IncarnamBestiaryCatalog::load();
 */
final class IncarnamBestiaryCatalog
{
    public const SCHEMA_VERSION = '1';

    public const OFFICIAL_ID_PREFIX = 'jdr:bestiary:';

    /** @var list<string> */
    private const SPELL_KINDS = [
        'frapper',
        'soigner',
        'appliquer-etat',
        'protéger',
        'donner-pv-temporaires',
        'booster',
        'retirer',
        'autre',
    ];

    /** @var list<string> */
    private const STAT_KEYS = [
        'life', 'pa', 'pm', 'po', 'ini', 'invocation', 'touch', 'ca',
        'dodge_pa', 'dodge_pm', 'fuite', 'tacle',
        'vitality', 'sagesse', 'strong', 'intel', 'agi', 'chance',
    ];

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(private readonly array $payload) {}

    public static function path(): string
    {
        return database_path('seeders/data/entities/monsters/incarnam.json');
    }

    /**
     * @throws JsonException
     */
    public static function load(?string $path = null): self
    {
        $file = $path ?? self::path();
        if (! File::isFile($file)) {
            throw new RuntimeException('Catalogue bestiaire Incarnam introuvable : '.$file);
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);

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

            $spells = $this->parseSpells($row['spells'] ?? null, $key);
            if ($spells === []) {
                continue;
            }

            $statsRaw = is_array($row['stats'] ?? null) ? $row['stats'] : [];
            $stats = [];
            foreach (self::STAT_KEYS as $statKey) {
                if (! array_key_exists($statKey, $statsRaw)) {
                    continue;
                }
                $stats[$statKey] = trim((string) $statsRaw[$statKey]);
            }

            $hostility = (int) ($row['hostility'] ?? 3);
            $hostility = max(0, min(4, $hostility));

            $entries[] = [
                'key' => $key,
                'official_id' => self::OFFICIAL_ID_PREFIX.$key,
                'name' => $name,
                'level' => max(1, (int) ($row['level'] ?? 1)),
                'size' => max(0, min(5, (int) ($row['size'] ?? 1))),
                'monster_race' => $this->nullableString($row['monster_race'] ?? null),
                'hostility' => $hostility,
                'is_boss' => (bool) ($row['is_boss'] ?? false),
                'boss_pa' => trim((string) ($row['boss_pa'] ?? '')),
                'location' => (string) ($row['location'] ?? 'Incarnam'),
                'description' => (string) ($row['description'] ?? ''),
                'other_info' => (string) ($row['other_info'] ?? ''),
                'traits' => $this->stringList($row['traits'] ?? null),
                'capabilities' => $this->stringList($row['capabilities'] ?? null),
                'stats' => $stats,
                'spells' => $spells,
            ];
        }

        return $entries;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function parseSpells(mixed $raw, string $monsterKey): array
    {
        if (! is_array($raw)) {
            return [];
        }

        $spells = [];
        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $key = trim((string) ($row['key'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            $kind = trim((string) ($row['kind'] ?? ''));
            if ($key === '' || $name === '' || ! in_array($kind, self::SPELL_KINDS, true)) {
                continue;
            }
            if (in_array($kind, ['frapper', 'soigner', 'protéger', 'donner-pv-temporaires', 'booster', 'retirer', 'autre'], true)
                && trim((string) ($row['value'] ?? '')) === '') {
                continue;
            }
            if ($kind === 'appliquer-etat' && trim((string) ($row['condition'] ?? '')) === '') {
                continue;
            }

            $spells[] = [
                'key' => $key,
                'official_id' => self::OFFICIAL_ID_PREFIX.$monsterKey.':'.$key,
                'name' => $name,
                'kind' => $kind,
                'element' => $this->nullableString($row['element'] ?? null),
                'value' => $this->nullableString($row['value'] ?? null),
                'pa' => trim((string) ($row['pa'] ?? '3')),
                'po_min' => trim((string) ($row['po_min'] ?? '1')),
                'po_max' => trim((string) ($row['po_max'] ?? '1')),
                'attack_characteristic_key' => $this->nullableString($row['attack_characteristic_key'] ?? null),
                'resolution_mode' => (string) ($row['resolution_mode'] ?? 'attack_roll'),
                'is_magic' => (bool) ($row['is_magic'] ?? false),
                'type' => trim((string) ($row['type'] ?? $this->defaultSpellType($kind))),
                'life_steal' => $this->nullableString($row['life_steal'] ?? null),
                'condition' => $this->nullableString($row['condition'] ?? null),
                'characteristic' => $this->nullableString($row['characteristic'] ?? null),
            ];
        }

        return $spells;
    }

    private function defaultSpellType(string $kind): string
    {
        return match ($kind) {
            'soigner' => 'Soin',
            'protéger', 'donner-pv-temporaires' => 'Défensif',
            'booster' => 'Buff',
            'appliquer-etat', 'retirer' => 'Debuff',
            default => 'Offensif',
        };
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
