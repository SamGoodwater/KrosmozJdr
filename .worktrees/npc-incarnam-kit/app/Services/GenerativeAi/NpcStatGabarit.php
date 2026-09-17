<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Support\Npc\NpcRole;

/**
 * Gabarit 5.1.2 (PV, CA, dés) selon le niveau et le rôle du PNJ.
 *
 * @example
 * $stats = (new NpcStatGabarit)->forLevelAndRole(4, NpcRole::SOCIAL);
 */
final class NpcStatGabarit
{
    /**
     * @return array{
     *     life: string,
     *     pa: string,
     *     pm: string,
     *     po: string,
     *     ca: string,
     *     ini: string,
     *     touch: string,
     *     vitality: string,
     *     sagesse: string,
     *     strong: string,
     *     intel: string,
     *     agi: string,
     *     chance: string,
     *     damage_dice: string,
     *     band: string
     * }
     */
    public function forLevelAndRole(int $level, string $role): array
    {
        $level = max(1, min(20, $level));
        $band = $this->band($level);
        $weight = $this->roleWeight($role);
        $span = max(1, $band['to'] - $band['from']);
        $progress = ($level - $band['from']) / $span;
        $lifeSpan = $band['max_life'] - $band['min_life'];
        $life = (int) round($band['min_life'] + $lifeSpan * (0.35 * $progress + 0.65 * $weight));

        $social = in_array($role, [NpcRole::SOCIAL, NpcRole::MERCHANT], true);
        $vitality = $social ? 8 : ($role === NpcRole::GUARD ? 12 : 10);
        $ca = $social ? 11 : ($role === NpcRole::GUARD ? 13 : 12);
        $dice = $weight < 0.35 ? $band['dice'][0] : $band['dice'][1];

        $base = 8;
        $primary = $social ? 12 : 14;
        $stats = [
            'vitality' => (string) $vitality,
            'sagesse' => (string) $base,
            'strong' => (string) $base,
            'intel' => (string) $base,
            'agi' => (string) $base,
            'chance' => (string) $base,
        ];
        match ($role) {
            NpcRole::SOCIAL => $stats['chance'] = (string) $primary,
            NpcRole::MERCHANT => $stats['intel'] = (string) $primary,
            NpcRole::GUARD, NpcRole::ENEMY => $stats['strong'] = (string) $primary,
            NpcRole::ALLY => $stats['intel'] = (string) 12,
            default => $stats['strong'] = (string) 10,
        };

        return array_merge($stats, [
            'life' => (string) $life,
            'pa' => $social ? '4' : '6',
            'pm' => '3',
            'po' => '1',
            'ca' => (string) $ca,
            'ini' => (string) $ca,
            'touch' => (string) $ca,
            'damage_dice' => $dice,
            'band' => $band['key'],
        ]);
    }

    /**
     * @return array{key: string, from: int, to: int, min_life: int, max_life: int, dice: list<string>}
     */
    private function band(int $level): array
    {
        return match (true) {
            $level <= 5 => [
                'key' => '1-5',
                'from' => 1,
                'to' => 5,
                'min_life' => 20,
                'max_life' => 50,
                'dice' => ['1d6', '2d6'],
            ],
            $level <= 10 => [
                'key' => '6-10',
                'from' => 6,
                'to' => 10,
                'min_life' => 50,
                'max_life' => 100,
                'dice' => ['2d6', '3d6'],
            ],
            $level <= 15 => [
                'key' => '11-15',
                'from' => 11,
                'to' => 15,
                'min_life' => 100,
                'max_life' => 200,
                'dice' => ['3d6', '4d6'],
            ],
            default => [
                'key' => '16-20',
                'from' => 16,
                'to' => 20,
                'min_life' => 200,
                'max_life' => 400,
                'dice' => ['4d6', '5d6'],
            ],
        };
    }

    private function roleWeight(string $role): float
    {
        return match ($role) {
            NpcRole::SOCIAL, NpcRole::MERCHANT => 0.15,
            NpcRole::ALLY => 0.4,
            NpcRole::GUARD => 0.55,
            NpcRole::ENEMY => 0.7,
            default => 0.4,
        };
    }
}
