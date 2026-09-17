<?php

declare(strict_types=1);

namespace App\Services\Effect;

/**
 * Répartit les budgets d'action de sorts issus de « Creation sort ».
 *
 * Les tables sources expriment un total par tour proportionnel aux PV moyens.
 * Ce service transforme ce total en budget par lancement selon le coût en PA.
 *
 * @example
 * $budget = (new SpellActionBudgetService)->budgetForCast('damage', 10, 3);
 */
final class SpellActionBudgetService
{
    public const ACTION_DAMAGE = 'damage';

    public const ACTION_HEAL = 'heal';

    public const ACTION_SHIELD = 'shield';

    /** Alias budget soins / boucliers pour les PV temporaires (même enveloppe survie). */
    public const ACTION_TEMP_HP = 'temp_hp';

    public const ACTION_LIFE_STEAL = 'life_steal';

    public const POWER_LEVELS = [
        'very_weak',
        'weak',
        'neutral',
        'strong',
        'very_strong',
    ];

    /** PA disponibles par tour selon le niveau. */
    private const MAX_ACTION_POINTS = [
        6, 6, 6, 7, 7, 7, 8, 8, 8, 9,
        9, 9, 10, 10, 10, 11, 11, 11, 12, 12,
    ];

    /**
     * Budgets de dégâts totaux par tour.
     *
     * Paliers : sans rôle minimum, second rôle minimum/moyen, premier rôle moyen/maximum.
     */
    private const DAMAGE_TURN_BUDGETS = [
        'very_weak' => [1, 2, 3, 4, 5, 7, 10, 11, 13, 17, 19, 21, 27, 30, 33, 40, 44, 48, 58, 65],
        'weak' => [1, 2, 3, 5, 7, 8, 12, 14, 16, 20, 22, 25, 31, 35, 38, 46, 50, 54, 65, 73],
        'neutral' => [2, 3, 4, 7, 8, 10, 14, 16, 19, 24, 27, 29, 37, 41, 45, 53, 58, 63, 75, 83],
        'strong' => [2, 4, 5, 8, 10, 12, 17, 19, 22, 28, 31, 34, 42, 46, 51, 60, 65, 70, 83, 92],
        'very_strong' => [3, 5, 7, 10, 12, 15, 20, 23, 26, 32, 36, 39, 48, 53, 58, 68, 74, 80, 94, 104],
    ];

    /**
     * Budgets de soins totaux par tour.
     *
     * Ils correspondent au référentiel « dégâts max × 0,35 + 2 + mod. Vitalité ».
     */
    private const HEAL_TURN_BUDGETS = [
        'very_weak' => [1, 2, 3, 3, 4, 4, 5, 6, 6, 8, 9, 9, 12, 13, 14, 16, 17, 19, 22, 25],
        'weak' => [1, 2, 3, 4, 4, 5, 6, 7, 7, 9, 10, 11, 13, 14, 15, 18, 19, 21, 25, 27],
        'neutral' => [4, 4, 5, 6, 7, 8, 10, 11, 12, 14, 15, 16, 20, 21, 23, 27, 28, 30, 35, 38],
        'strong' => [4, 4, 5, 7, 8, 8, 11, 12, 13, 16, 17, 18, 22, 23, 25, 29, 31, 33, 38, 41],
        'very_strong' => [6, 7, 8, 9, 10, 12, 14, 16, 17, 20, 21, 24, 27, 30, 31, 36, 38, 41, 46, 50],
    ];

    /**
     * Retourne le budget total par tour pour une action, un niveau et un palier.
     */
    public function turnBudget(string $action, int $level, int $powerIndex = 2): int
    {
        $levelIndex = $this->levelIndex($level);
        $powerKey = self::POWER_LEVELS[$this->powerIndex($powerIndex)];
        $budget = match ($action) {
            self::ACTION_HEAL, self::ACTION_SHIELD, self::ACTION_TEMP_HP => self::HEAL_TURN_BUDGETS[$powerKey][$levelIndex],
            self::ACTION_LIFE_STEAL => (int) round(self::DAMAGE_TURN_BUDGETS[$powerKey][$levelIndex] / 2),
            default => self::DAMAGE_TURN_BUDGETS[$powerKey][$levelIndex],
        };

        return max(1, $budget);
    }

    /**
     * Répartit le budget total selon la part de PA consommée par un lancement.
     */
    public function budgetForCast(
        string $action,
        int $level,
        int $actionPointCost,
        int $powerIndex = 2
    ): int {
        $maxActionPoints = $this->maxActionPoints($level);
        $cost = max(1, min($maxActionPoints, $actionPointCost));

        return max(1, (int) round(
            $this->turnBudget($action, $level, $powerIndex) * $cost / $maxActionPoints
        ));
    }

    /**
     * Distribue un budget entier entre plusieurs lignes en conservant leurs poids relatifs.
     *
     * @param  list<int|float>  $weights
     * @return list<int>
     */
    public function distribute(int $budget, array $weights): array
    {
        if ($weights === []) {
            return [];
        }
        $budget = max(count($weights), $budget);
        $positiveWeights = array_map(static fn (int|float $value): float => max(0.0, (float) $value), $weights);
        $weightTotal = array_sum($positiveWeights);
        if ($weightTotal <= 0) {
            $positiveWeights = array_fill(0, count($weights), 1.0);
            $weightTotal = (float) count($weights);
        }

        $raw = array_map(
            static fn (float $weight): float => $budget * $weight / $weightTotal,
            $positiveWeights
        );
        $values = array_map(static fn (float $value): int => max(1, (int) floor($value)), $raw);

        while (array_sum($values) < $budget) {
            $bestIndex = 0;
            $bestRemainder = -INF;
            foreach ($raw as $index => $value) {
                $remainder = $value - $values[$index];
                if ($remainder > $bestRemainder) {
                    $bestIndex = $index;
                    $bestRemainder = $remainder;
                }
            }
            $values[$bestIndex]++;
        }
        while (array_sum($values) > $budget) {
            $largestIndex = array_keys($values, max($values), true)[0];
            if ($values[$largestIndex] <= 1) {
                break;
            }
            $values[$largestIndex]--;
        }

        return array_values($values);
    }

    public function maxActionPoints(int $level): int
    {
        return self::MAX_ACTION_POINTS[$this->levelIndex($level)];
    }

    private function levelIndex(int $level): int
    {
        return max(0, min(19, $level - 1));
    }

    private function powerIndex(int $powerIndex): int
    {
        return max(0, min(4, $powerIndex));
    }
}
