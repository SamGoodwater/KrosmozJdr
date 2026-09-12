<?php

declare(strict_types=1);

namespace App\Support\Entity;

/**
 * Lecture numérique d’une clé dans `items.bonus` / `panoplies.bonus` (JSON texte).
 *
 * Objet : `{ "strength": 3 }`. Panoplie : `{ "2": { "strength": 1 }, "3": { "vitality": 2 } }`
 * (somme des paliers pour une même caractéristique).
 */
final class JsonBonusValue
{
    /**
     * @param  list<string>  $metaStems
     */
    public const META_STEMS = ['name', 'description', 'level', 'rarity', 'price', 'weight'];

    /**
     * Clé JSON courte (`strength_object` → `strength`).
     */
    public static function shortKey(string $key): string
    {
        $raw = trim($key);
        if (str_ends_with($raw, '_object')) {
            return substr($raw, 0, -7);
        }

        return $raw;
    }

    /**
     * Identifiant JSON sûr (lettres, chiffres, underscore).
     */
    public static function sanitizeKey(string $key): ?string
    {
        $short = self::shortKey($key);
        if ($short === '' || preg_match('/^[a-z][a-z0-9_]*$/i', $short) !== 1) {
            return null;
        }
        if (strlen($short) > 64) {
            return null;
        }

        return $short;
    }

    public static function isMetaKey(string $key): bool
    {
        $stem = (string) preg_replace('/_(creature|object|spell)$/i', '', self::shortKey($key));

        return in_array(strtolower($stem), self::META_STEMS, true);
    }

    /**
     * Valeur numérique de la caractéristique, ou null si absente.
     *
     * @param  bool  $sumTiers  true = somme des paliers panoplie ; false = objet plat uniquement
     */
    public static function numericForKey(mixed $raw, string $key, bool $sumTiers = false): ?int
    {
        $safe = self::sanitizeKey($key);
        if ($safe === null) {
            return null;
        }
        $payload = self::decode($raw);
        if (! is_array($payload) || $payload === []) {
            return null;
        }

        if ($sumTiers && self::isPieceBonusMap($payload)) {
            $sum = 0;
            $found = false;
            foreach ($payload as $stats) {
                if (! is_array($stats)) {
                    continue;
                }
                $n = self::numericFromFlatMap($stats, $safe);
                if ($n !== null) {
                    $found = true;
                    $sum += $n;
                }
            }

            return $found ? $sum : null;
        }

        return self::numericFromFlatMap($payload, $safe);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function decode(mixed $raw): ?array
    {
        if (is_array($raw)) {
            return $raw;
        }
        if (! is_string($raw)) {
            return null;
        }
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return null;
        }
        try {
            $decoded = json_decode($trimmed, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function isPieceBonusMap(array $payload): bool
    {
        if ($payload === []) {
            return false;
        }
        foreach ($payload as $key => $inner) {
            if (! is_numeric((string) $key)) {
                return false;
            }
            if (! is_array($inner) || array_is_list($inner)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $map
     */
    private static function numericFromFlatMap(array $map, string $short): ?int
    {
        foreach ($map as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            if (self::shortKey((string) $key) !== $short) {
                continue;
            }
            if ($value === null || $value === '' || ! is_numeric($value)) {
                return null;
            }

            return (int) $value;
        }

        return null;
    }
}
