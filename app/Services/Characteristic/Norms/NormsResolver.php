<?php

namespace App\Services\Characteristic\Norms;

/**
 * Résolution PHP d'une grille de normes 5 puissances × 20 niveaux.
 *
 * @example
 * $value = (new NormsResolver())->resolve($grid, 8, 2);
 */
final class NormsResolver
{
    public const POWER_LEVELS = ['very_weak', 'weak', 'neutral', 'strong', 'very_strong'];

    public const NEUTRAL_INDEX = 2;

    public const MAX_LEVEL = 20;

    /**
     * @param  array<string, array<int, int|float|null>>|null  $grid
     * @param  list<array{target?: string, modifier?: int|float}>  $conditions
     */
    public function resolve(?array $grid, int $level, int $powerIndex = self::NEUTRAL_INDEX, array $conditions = []): int|float|null
    {
        if ($grid === null || $grid === []) {
            return null;
        }

        $effectivePower = $this->effectivePowerLevel($powerIndex, $conditions);
        $row = $grid[$effectivePower] ?? null;
        if (! is_array($row)) {
            return null;
        }

        $index = $this->effectiveLevelIndex($level, $conditions);

        return $row[$index] ?? null;
    }

    /**
     * @param  array<string, array<int, int|float|null>>|null  $grid
     * @param  list<array{target?: string, modifier?: int|float}>  $conditions
     * @return array{value: int|float|null, delta: int|float|null, in_band: bool}
     */
    public function compare(int|float $value, ?array $grid, int $level, int $powerIndex = self::NEUTRAL_INDEX, array $conditions = []): array
    {
        $norm = $this->resolve($grid, $level, $powerIndex, $conditions);
        if ($norm === null) {
            return ['value' => null, 'delta' => null, 'in_band' => false];
        }

        $delta = $value - $norm;

        return [
            'value' => $norm,
            'delta' => $delta,
            'in_band' => abs($delta) <= 0,
        ];
    }

    /**
     * @param  list<array{target?: string, modifier?: int|float}>  $conditions
     */
    private function effectivePowerLevel(int $powerIndex, array $conditions): string
    {
        $offset = 0;
        foreach ($conditions as $condition) {
            if (($condition['target'] ?? null) === 'power') {
                $offset += (int) ($condition['modifier'] ?? 0);
            }
        }

        $index = max(0, min(count(self::POWER_LEVELS) - 1, $powerIndex + $offset));

        return self::POWER_LEVELS[$index];
    }

    /**
     * @param  list<array{target?: string, modifier?: int|float}>  $conditions
     */
    private function effectiveLevelIndex(int $level, array $conditions): int
    {
        $offset = 0;
        foreach ($conditions as $condition) {
            if (($condition['target'] ?? null) === 'level') {
                $offset += (int) ($condition['modifier'] ?? 0);
            }
        }

        return max(0, min(self::MAX_LEVEL - 1, $level - 1 + $offset));
    }
}
