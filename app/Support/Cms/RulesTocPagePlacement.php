<?php

declare(strict_types=1);

namespace App\Support\Cms;

use App\Models\User;

/**
 * Placement menu / visibilité des pages issues de la table des matières.
 *
 * Le chapitre 5 (équilibrage des entités) est réservé aux MJ.
 *
 * @example
 * RulesTocPagePlacement::forNumber('5.2.3');
 * // ['menu_group' => 'Pour les MJ', 'read_level' => 3]
 */
final class RulesTocPagePlacement
{
    /**
     * @return array{menu_group: string, read_level: int}
     */
    public static function forNumber(string $number): array
    {
        $major = explode('.', trim($number))[0];

        if ($major === '5') {
            return [
                'menu_group' => 'Pour les MJ',
                'read_level' => User::ROLE_GAME_MASTER,
            ];
        }

        return [
            'menu_group' => 'Règles',
            'read_level' => User::ROLE_GUEST,
        ];
    }
}
