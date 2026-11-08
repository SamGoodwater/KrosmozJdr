<?php

declare(strict_types=1);

namespace App\Services\Jdr;

/**
 * Calcule la part des dés de vie (classes et formules monstre).
 *
 * Ne stocke pas l’historique : retourne un total du niveau 1 au niveau demandé,
 * ou un gain pour une montée d’un niveau. Le mod. de Vitalité reste hors de ce total.
 */
final class LifeDiceCalculator
{
    public const METHOD_RANDOM = 'random';

    public const METHOD_STABLE = 'stable';

    /** @var array<int, string> faces du dé de classe → gain stable (hors niveau 1) */
    private const CLASS_STABLE_FORMULA = [
        6 => '6',
        8 => '6+1d2',
        10 => '6+1d4',
        12 => '6+1d6',
    ];

    public function __construct(
        private readonly DiceFormulaService $dice,
    ) {}

    /**
     * Parse `1d8` / `d10` / `8` → nombre de faces, ou null.
     */
    public function parseClassDieSides(?string $lifeDice): ?int
    {
        if ($lifeDice === null) {
            return null;
        }
        $trimmed = strtolower(trim($lifeDice));
        if ($trimmed === '') {
            return null;
        }
        if (preg_match('/^(?:(\d*)d)?(\d+)$/', $trimmed, $m) !== 1) {
            return null;
        }
        $sides = (int) $m[2];

        return in_array($sides, [4, 6, 8, 10, 12, 20], true) ? $sides : null;
    }

    /**
     * @param  array{
     *     random_int?: callable(int, int): int,
     *     fixed_by_sides?: array<int, int>
     * }  $options
     * @return array{
     *     ok: bool,
     *     error: string|null,
     *     life_dice_total: int,
     *     levels: list<array{level: int, gain: int, breakdown: list<string>}>,
     *     method: string
     * }
     */
    public function totalForClass(int $sides, int $level, string $method, array $options = []): array
    {
        $level = max(1, min(20, $level));
        if (! isset(self::CLASS_STABLE_FORMULA[$sides])) {
            return $this->failure('Dé de classe inconnu (attendu d6, d8, d10 ou d12).');
        }
        if (! in_array($method, [self::METHOD_RANDOM, self::METHOD_STABLE], true)) {
            return $this->failure('Méthode inconnue.');
        }

        $levels = [];
        $total = $sides;
        $levels[] = [
            'level' => 1,
            'gain' => $sides,
            'breakdown' => ['niveau 1 : max du dé = '.$sides],
        ];

        for ($n = 2; $n <= $level; $n++) {
            $gain = $method === self::METHOD_STABLE
                ? $this->rollClassStableGain($sides, $options)
                : $this->rollClassRandomGain($sides, $options);
            if ($gain === null) {
                return $this->failure('Impossible de lancer le dé de classe.');
            }
            $total += $gain['value'];
            $levels[] = [
                'level' => $n,
                'gain' => $gain['value'],
                'breakdown' => $gain['breakdown'],
            ];
        }

        return [
            'ok' => true,
            'error' => null,
            'life_dice_total' => $total,
            'levels' => $levels,
            'method' => $method,
        ];
    }

    /**
     * @param  array{
     *     random_int?: callable(int, int): int,
     *     fixed_by_sides?: array<int, int>
     * }  $options
     * @return array{
     *     ok: bool,
     *     error: string|null,
     *     life_dice_total: int,
     *     levels: list<array{level: int, gain: int, breakdown: list<string>}>,
     *     method: string
     * }
     */
    public function totalForFormula(string $formula, int $level, string $method, array $options = []): array
    {
        $level = max(1, min(20, $level));
        if (! in_array($method, [self::METHOD_RANDOM, self::METHOD_STABLE], true)) {
            return $this->failure('Méthode inconnue.');
        }

        $analysis = $this->dice->analyze($formula);
        if (! $analysis->isValid || $analysis->max === null) {
            return $this->failure($analysis->error ?? 'Formule invalide.');
        }

        $levelOne = (int) round((float) $analysis->max);
        $levels = [[
            'level' => 1,
            'gain' => $levelOne,
            'breakdown' => ['niveau 1 : max de la formule = '.$levelOne],
        ]];
        $total = $levelOne;

        $fixed = $options['fixed_by_sides'] ?? $this->dice->defaultFixedBySides($formula);

        for ($n = 2; $n <= $level; $n++) {
            $gain = $method === self::METHOD_STABLE
                ? $this->rollFormulaStableGain($formula, is_array($fixed) ? $fixed : [], $options)
                : $this->rollFormulaRandomGain($formula, $options);
            if ($gain === null) {
                return $this->failure('Impossible d’évaluer la formule.');
            }
            $total += $gain['value'];
            $levels[] = [
                'level' => $n,
                'gain' => $gain['value'],
                'breakdown' => $gain['breakdown'],
            ];
        }

        return [
            'ok' => true,
            'error' => null,
            'life_dice_total' => $total,
            'levels' => $levels,
            'method' => $method,
        ];
    }

    /**
     * Gain d’un niveau (après le 1er) pour une classe.
     *
     * @param  array{random_int?: callable(int, int): int}  $options
     * @return array{value: int, breakdown: list<string>}|null
     */
    public function gainForClass(int $sides, string $method, array $options = []): ?array
    {
        if (! isset(self::CLASS_STABLE_FORMULA[$sides])) {
            return null;
        }

        return $method === self::METHOD_STABLE
            ? $this->rollClassStableGain($sides, $options)
            : $this->rollClassRandomGain($sides, $options);
    }

    /**
     * Gain d’un niveau (après le 1er) pour une formule monstre.
     *
     * @param  array{
     *     random_int?: callable(int, int): int,
     *     fixed_by_sides?: array<int, int>
     * }  $options
     * @return array{value: int, breakdown: list<string>}|null
     */
    public function gainForFormula(string $formula, string $method, array $options = []): ?array
    {
        $fixed = $options['fixed_by_sides'] ?? $this->dice->defaultFixedBySides($formula);

        return $method === self::METHOD_STABLE
            ? $this->rollFormulaStableGain($formula, is_array($fixed) ? $fixed : [], $options)
            : $this->rollFormulaRandomGain($formula, $options);
    }

    /**
     * PV max prévisionnels : part des dés + max(0, mod) × niveau.
     */
    public function previewLifePoints(int $lifeDiceTotal, int $level, int $vitalityModifier): int
    {
        $level = max(1, $level);
        $mod = max(0, $vitalityModifier);

        return $lifeDiceTotal + ($mod * $level);
    }

    /**
     * @param  array{random_int?: callable(int, int): int}  $options
     * @return array{value: int, breakdown: list<string>}|null
     */
    private function rollClassRandomGain(int $sides, array $options): ?array
    {
        $result = $this->dice->roll('1d'.$sides, [
            'reroll_ones' => true,
            'random_int' => $options['random_int'] ?? null,
        ]);
        if (! $result->isValid) {
            return null;
        }

        return [
            'value' => (int) round($result->result),
            'breakdown' => $result->breakdown,
        ];
    }

    /**
     * @param  array{random_int?: callable(int, int): int}  $options
     * @return array{value: int, breakdown: list<string>}|null
     */
    private function rollClassStableGain(int $sides, array $options): ?array
    {
        $formula = self::CLASS_STABLE_FORMULA[$sides] ?? null;
        if ($formula === null) {
            return null;
        }
        $result = $this->dice->roll($formula, [
            'reroll_ones' => false,
            'random_int' => $options['random_int'] ?? null,
        ]);
        if (! $result->isValid) {
            return null;
        }

        return [
            'value' => (int) round($result->result),
            'breakdown' => $result->breakdown,
        ];
    }

    /**
     * @param  array{random_int?: callable(int, int): int}  $options
     * @return array{value: int, breakdown: list<string>}|null
     */
    private function rollFormulaRandomGain(string $formula, array $options): ?array
    {
        $result = $this->dice->roll($formula, [
            'reroll_ones' => true,
            'random_int' => $options['random_int'] ?? null,
        ]);
        if (! $result->isValid) {
            return null;
        }

        return [
            'value' => (int) round($result->result),
            'breakdown' => $result->breakdown,
        ];
    }

    /**
     * @param  array<int, int>  $fixedBySides
     * @param  array{random_int?: callable(int, int): int}  $options
     * @return array{value: int, breakdown: list<string>}|null
     */
    private function rollFormulaStableGain(string $formula, array $fixedBySides, array $options): ?array
    {
        $result = $this->dice->roll($formula, [
            'reroll_ones' => false,
            'fixed_by_sides' => $fixedBySides,
            'random_int' => $options['random_int'] ?? null,
        ]);
        if (! $result->isValid) {
            return null;
        }

        return [
            'value' => (int) round($result->result),
            'breakdown' => $result->breakdown,
        ];
    }

    /**
     * @return array{
     *     ok: bool,
     *     error: string,
     *     life_dice_total: int,
     *     levels: list<array{level: int, gain: int, breakdown: list<string>}>,
     *     method: string
     * }
     */
    private function failure(string $error): array
    {
        return [
            'ok' => false,
            'error' => $error,
            'life_dice_total' => 0,
            'levels' => [],
            'method' => '',
        ];
    }
}
