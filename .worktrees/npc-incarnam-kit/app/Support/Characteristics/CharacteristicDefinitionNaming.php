<?php

declare(strict_types=1);

namespace App\Support\Characteristics;

/**
 * Convention : fichier `{stem}-{group}-definition.json` sous
 * `database/seeders/data/characteristic-definitions/{group}/`
 * avec `stem` = clé sans suffixe `_creature` / `_object` / `_spell`.
 */
final class CharacteristicDefinitionNaming
{
    public const RELATIVE_ROOT = 'database/seeders/data/characteristic-definitions';

    public const SUFFIX_CREATURE = '_creature';

    public const SUFFIX_OBJECT = '_object';

    public const SUFFIX_SPELL = '_spell';

    public const GROUP_CREATURE = 'creature';

    public const GROUP_OBJECT = 'object';

    public const GROUP_SPELL = 'spell';

    /**
     * @return array{stem: string, group: string}|null
     */
    public static function parseCharacteristicKey(string $key): ?array
    {
        if (str_ends_with($key, self::SUFFIX_CREATURE)) {
            return [
                'stem' => substr($key, 0, -strlen(self::SUFFIX_CREATURE)),
                'group' => self::GROUP_CREATURE,
            ];
        }
        if (str_ends_with($key, self::SUFFIX_OBJECT)) {
            return [
                'stem' => substr($key, 0, -strlen(self::SUFFIX_OBJECT)),
                'group' => self::GROUP_OBJECT,
            ];
        }
        if (str_ends_with($key, self::SUFFIX_SPELL)) {
            return [
                'stem' => substr($key, 0, -strlen(self::SUFFIX_SPELL)),
                'group' => self::GROUP_SPELL,
            ];
        }

        return null;
    }

    public static function characteristicKeyFromStemAndGroup(string $stem, string $group): string
    {
        return match ($group) {
            self::GROUP_CREATURE => $stem.self::SUFFIX_CREATURE,
            self::GROUP_OBJECT => $stem.self::SUFFIX_OBJECT,
            self::GROUP_SPELL => $stem.self::SUFFIX_SPELL,
            default => throw new \InvalidArgumentException('Groupe inconnu : '.$group),
        };
    }

    public static function definitionFilename(string $stem, string $group): string
    {
        return $stem.'-'.$group.'-definition.json';
    }

    public static function definitionRelativePath(string $stem, string $group): string
    {
        return self::RELATIVE_ROOT.'/'.$group.'/'.self::definitionFilename($stem, $group);
    }

    public static function definitionAbsolutePath(string $stem, string $group): string
    {
        return base_path(self::definitionRelativePath($stem, $group));
    }
}
