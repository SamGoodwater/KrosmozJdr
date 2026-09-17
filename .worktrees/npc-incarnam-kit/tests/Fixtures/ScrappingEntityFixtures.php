<?php

declare(strict_types=1);

namespace Tests\Fixtures;

/**
 * Jeux de données DofusDB minimaux pour caractériser le pipeline sans appel réseau.
 *
 * @example
 * $monster = ScrappingEntityFixtures::monster();
 */
final class ScrappingEntityFixtures
{
    /** @return array<string, mixed> */
    public static function monster(): array
    {
        return [
            'id' => 31,
            'name' => ['fr' => 'Bouftou'],
            'description' => ['fr' => 'Un bouftou de référence.'],
            'grades' => [[
                'level' => 50,
                'lifePoints' => 800,
                'strength' => 120,
                'intelligence' => 40,
                'agility' => 60,
                'wisdom' => 20,
                'chance' => 30,
                'actionPoints' => 8,
                'movementPoints' => 4,
                'bonusRange' => 7,
                'paDodge' => 9,
                'pmDodge' => 11,
                'vitality' => 120,
                'neutralResistance' => 10,
                'earthResistance' => 25,
                'fireResistance' => -25,
                'airResistance' => 0,
                'waterResistance' => 75,
                'bonusCharacteristics' => [
                    'tackleBlock' => 12,
                    'tackleEvade' => 8,
                    'criticalHit' => 55,
                    'healBonus' => 30,
                ],
            ]],
            'size' => 'medium',
        ];
    }

    /** @return array<string, mixed> */
    public static function spell(): array
    {
        return [
            'id' => 201,
            'name' => ['fr' => 'Pression'],
            'description' => ['fr' => 'Inflige des dommages Terre.'],
            'levels' => [[
                'grade' => 1,
                'apCost' => 3,
                'minRange' => 1,
                'range' => 3,
                'effects' => [['effectId' => 100, 'diceNum' => 8, 'diceSide' => 12, 'order' => 0]],
            ]],
        ];
    }

    /** @return array<string, mixed> */
    public static function equipment(): array
    {
        return self::item(1001, 1, 'Coiffe de test');
    }

    /** @return array<string, mixed> */
    public static function resource(): array
    {
        return self::item(1002, 15, 'Bois de test');
    }

    /** @return array<string, mixed> */
    public static function consumable(): array
    {
        return self::item(1003, 12, 'Potion de test');
    }

    /** @return array<string, mixed> */
    public static function breed(): array
    {
        return ['id' => 1, 'name' => ['fr' => 'Iop'], 'description' => ['fr' => 'Classe de test.']];
    }

    /** @return array<string, mixed> */
    public static function panoply(): array
    {
        return ['id' => 501, 'name' => ['fr' => 'Panoplie de test'], 'effects' => [[], []], 'items' => []];
    }

    /** @return array<string, mixed> */
    private static function item(int $id, int $typeId, string $name): array
    {
        return [
            'id' => $id,
            'name' => ['fr' => $name],
            'description' => ['fr' => 'Objet de caractérisation.'],
            'typeId' => $typeId,
            'level' => 50,
            'effects' => [],
        ];
    }
}
