<?php

declare(strict_types=1);

namespace App\Support\Npc;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

/**
 * Archétypes narratifs de PNJ (règles §5.1.2.3).
 *
 * @example NpcRole::label('merchant') // 'Marchand'
 */
final class NpcRole
{
    public const SOCIAL = 'social';

    public const MERCHANT = 'merchant';

    public const GUARD = 'guard';

    public const ALLY = 'ally';

    public const ENEMY = 'enemy';

    public const OTHER = 'other';

    /** @var array<string, string> */
    public const LABELS = [
        self::SOCIAL => 'Social',
        self::MERCHANT => 'Marchand',
        self::GUARD => 'Garde',
        self::ALLY => 'Allié',
        self::ENEMY => 'Ennemi',
        self::OTHER => 'Autre',
    ];

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_keys(self::LABELS);
    }

    public static function label(?string $role): string
    {
        if ($role === null || $role === '') {
            return '';
        }

        return self::LABELS[$role] ?? $role;
    }

    public static function rule(): In
    {
        return Rule::in(self::values());
    }
}
