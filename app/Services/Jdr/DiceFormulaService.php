<?php

declare(strict_types=1);

namespace App\Services\Jdr;

/**
 * Moteur de formules de dés JDR (saisie utilisateur).
 *
 * Point d’entrée pour parser / borner / simuler une formule. Les stats de monstres
 * et d’autres entités viendront s’ajouter ici plus tard ; ne pas y coller d’`eval`.
 *
 * Grammaire (toute la chaîne, après normalisation) :
 * - termes : nombre (`3`, `1.5`, `2,5`), dé (`d12`, `3d8`), tranche (`[2-6]`)
 * - opérateurs : `+` `-` `*` `/` (alias `x` `×` `:` `÷`)
 * - priorité : `*` `/` avant `+` `-` ; pas de parenthèses
 *
 * Une tranche entière `[min-max]` s’affiche en dés exacts via {@see DiceNotationService::fromInclusiveRange()}
 * (`[2-6]` → `1d5+1`). Min / max / moyenne et le lancer utilisent la fourchette réelle.
 *
 * Sécurité : liste blanche de caractères, plafonds (longueur, dés, termes), jamais `eval`.
 */
final class DiceFormulaService
{
    public const MAX_FORMULA_LENGTH = 64;

    public const MAX_DICE_COUNT = 40;

    public const MAX_DICE_SIDES = 1000;

    public const MAX_TERMS = 12;

    public const MAX_ABS_NUMBER = 100_000;

    public const MAX_RANGE_SPAN = 1000;

    public function __construct(
        private readonly DiceNotationService $notation,
    ) {}

    /**
     * Analyse une formule : min, max, moyenne, équivalents de tranches.
     *
     * `isRecognized` est vrai seulement si la formule est valide et contient au moins
     * un dé ou une tranche (évite d’intercepter une recherche « 12 »).
     */
    public function analyze(string $formula): DiceFormulaAnalysis
    {
        $parsed = $this->parse($formula);
        if ($parsed['error'] !== null) {
            return $parsed['empty']
                ? DiceFormulaAnalysis::empty()
                : DiceFormulaAnalysis::invalid($parsed['error']);
        }

        $stats = $this->evaluateStats($parsed['tokens']);
        if ($stats === null) {
            return DiceFormulaAnalysis::invalid('Impossible d’évaluer la formule');
        }

        return new DiceFormulaAnalysis(
            isValid: true,
            isRecognized: $parsed['recognized'],
            min: $this->roundStat($stats['min']),
            max: $this->roundStat($stats['max']),
            average: $this->roundStat($stats['average']),
            rangeEquivalents: $this->rangeEquivalents($parsed['tokens']),
            error: null,
        );
    }

    /**
     * Simule un lancer (dés tirés, tranche = entier uniforme inclusif).
     */
    public function roll(string $formula): DiceRollResult
    {
        $parsed = $this->parse($formula);
        if ($parsed['error'] !== null) {
            return DiceRollResult::invalid($parsed['empty'] ? 'Formule vide' : $parsed['error']);
        }

        $rolled = $this->rollTokens($parsed['tokens']);
        if ($rolled === null) {
            return DiceRollResult::invalid('Impossible d’évaluer la formule');
        }

        return new DiceRollResult(
            isValid: true,
            result: $this->roundStat($rolled['result']),
            breakdown: $rolled['breakdown'],
            error: null,
        );
    }

    /**
     * @return array{
     *     tokens: list<array<string, mixed>>,
     *     recognized: bool,
     *     empty: bool,
     *     error: string|null
     * }
     */
    private function parse(string $formula): array
    {
        $empty = [
            'tokens' => [],
            'recognized' => false,
            'empty' => true,
            'error' => 'Formule vide',
        ];

        $normalized = $this->normalize($formula);
        if ($normalized === '') {
            return $empty;
        }

        if (strlen($normalized) > self::MAX_FORMULA_LENGTH) {
            return ['tokens' => [], 'recognized' => false, 'empty' => false, 'error' => 'Formule trop longue'];
        }

        if (preg_match('/^[0-9d+*.\/\[\]\-]+$/i', $normalized) !== 1) {
            return ['tokens' => [], 'recognized' => false, 'empty' => false, 'error' => 'Caractères non autorisés'];
        }

        $tokenized = $this->tokenize($normalized);
        if ($tokenized['error'] !== null) {
            return ['tokens' => [], 'recognized' => false, 'empty' => false, 'error' => $tokenized['error']];
        }

        $seqError = $this->validateTokenSequence($tokenized['tokens']);
        if ($seqError !== null) {
            return ['tokens' => [], 'recognized' => false, 'empty' => false, 'error' => $seqError];
        }

        $recognized = false;
        foreach ($tokenized['tokens'] as $token) {
            if ($token['type'] === 'dice' || $token['type'] === 'range') {
                $recognized = true;
                break;
            }
        }

        return [
            'tokens' => $tokenized['tokens'],
            'recognized' => $recognized,
            'empty' => false,
            'error' => null,
        ];
    }

    private function normalize(string $formula): string
    {
        $value = str_replace(["\0", "\u{00A0}"], '', $formula);
        $value = preg_replace('/\s+/u', '', $value) ?? '';
        $value = str_replace(['×', 'x', 'X'], '*', $value);
        $value = str_replace(['÷', ':'], '/', $value);
        $value = str_replace(['–', '—'], '-', $value);
        $value = preg_replace('/(\d),(\d)/', '$1.$2', $value) ?? $value;

        return $value;
    }

    /**
     * @return array{tokens: list<array<string, mixed>>, error: string|null}
     */
    private function tokenize(string $str): array
    {
        $tokens = [];
        $length = strlen($str);
        $i = 0;
        $termCount = 0;

        while ($i < $length) {
            $rest = substr($str, $i);

            if (preg_match('/^\[(-?\d+(?:\.\d+)?)-(-?\d+(?:\.\d+)?)\]/', $rest, $match) === 1) {
                $range = $this->makeRangeToken($match[1], $match[2]);
                if (is_string($range)) {
                    return ['tokens' => [], 'error' => $range];
                }
                $tokens[] = $range;
                $termCount++;
                $i += strlen($match[0]);

                continue;
            }

            if (preg_match('/^(\d{0,2})d(\d{1,4})/i', $rest, $match) === 1) {
                $dice = $this->makeDiceToken($match[1], $match[2]);
                if (is_string($dice)) {
                    return ['tokens' => [], 'error' => $dice];
                }
                $tokens[] = $dice;
                $termCount++;
                $i += strlen($match[0]);

                continue;
            }

            if (preg_match('/^\d+(?:\.\d+)?/', $rest, $match) === 1) {
                $number = $this->makeNumberToken($match[0]);
                if (is_string($number)) {
                    return ['tokens' => [], 'error' => $number];
                }
                $tokens[] = $number;
                $termCount++;
                $i += strlen($match[0]);

                continue;
            }

            if (preg_match('/^[-+*\/]/', $rest, $match) === 1) {
                $tokens[] = ['type' => 'op', 'value' => $match[0]];
                $i += 1;

                continue;
            }

            return ['tokens' => [], 'error' => 'Caractère inattendu'];
        }

        if ($termCount > self::MAX_TERMS) {
            return ['tokens' => [], 'error' => 'Trop de termes'];
        }

        return ['tokens' => $tokens, 'error' => null];
    }

    /**
     * @return array<string, mixed>|string
     */
    private function makeRangeToken(string $rawMin, string $rawMax): array|string
    {
        $min = (float) $rawMin;
        $max = (float) $rawMax;
        if (! is_finite($min) || ! is_finite($max)) {
            return 'Tranche invalide';
        }
        if ($max < $min) {
            [$min, $max] = [$max, $min];
        }
        if (abs($min) > self::MAX_ABS_NUMBER || abs($max) > self::MAX_ABS_NUMBER) {
            return 'Nombre trop grand';
        }
        if (($max - $min) > self::MAX_RANGE_SPAN) {
            return 'Tranche trop large';
        }

        $equivalent = null;
        if ($this->isWholeNumber($min) && $this->isWholeNumber($max)) {
            $equivalent = $this->notation->fromInclusiveRange((int) $min, (int) $max);
        }

        return [
            'type' => 'range',
            'min' => $min,
            'max' => $max,
            'equivalent' => $equivalent,
        ];
    }

    /**
     * @return array<string, mixed>|string
     */
    private function makeDiceToken(string $rawN, string $rawX): array|string
    {
        $n = $rawN === '' ? 1 : (int) $rawN;
        $x = (int) $rawX;
        if ($n < 1 || $n > self::MAX_DICE_COUNT) {
            return 'Nombre de dés hors limites';
        }
        if ($x < 1 || $x > self::MAX_DICE_SIDES) {
            return 'Nombre de faces hors limites';
        }

        return ['type' => 'dice', 'n' => $n, 'x' => $x];
    }

    /**
     * @return array<string, mixed>|string
     */
    private function makeNumberToken(string $raw): array|string
    {
        if (str_contains($raw, 'e') || str_contains($raw, 'E')) {
            return 'Caractères non autorisés';
        }
        $value = (float) $raw;
        if (! is_finite($value) || abs($value) > self::MAX_ABS_NUMBER) {
            return 'Nombre trop grand';
        }

        return ['type' => 'number', 'value' => $value];
    }

    /**
     * @param  list<array<string, mixed>>  $tokens
     */
    private function validateTokenSequence(array $tokens): ?string
    {
        if ($tokens === []) {
            return 'Formule vide';
        }

        $expectTerm = true;
        foreach ($tokens as $token) {
            $isTerm = in_array($token['type'], ['dice', 'number', 'range'], true);
            if ($expectTerm) {
                if (! $isTerm) {
                    return 'Terme attendu (nombre, dé ou tranche)';
                }
                $expectTerm = false;
            } else {
                if ($token['type'] !== 'op') {
                    return 'Opérateur attendu';
                }
                $expectTerm = true;
            }
        }

        return $expectTerm ? 'Terme attendu après le dernier opérateur' : null;
    }

    /**
     * @param  list<array<string, mixed>>  $tokens
     * @return array{min: float, max: float, average: float}|null
     */
    private function evaluateStats(array $tokens): ?array
    {
        $terms = [];
        $ops = [];
        foreach ($tokens as $token) {
            if ($token['type'] === 'op') {
                $ops[] = $token['value'];
            } else {
                $terms[] = $this->tokenStats($token);
            }
        }

        if ($terms === [] || count($terms) !== count($ops) + 1) {
            return null;
        }

        return $this->reduce($terms, $ops, fn (array $a, array $b, string $op): array => $this->applyStatsOp($a, $b, $op));
    }

    /**
     * @param  array<string, mixed>  $token
     * @return array{min: float, max: float, average: float}
     */
    private function tokenStats(array $token): array
    {
        if ($token['type'] === 'dice') {
            $n = (int) $token['n'];
            $x = (int) $token['x'];

            return [
                'min' => (float) $n,
                'max' => (float) ($n * $x),
                'average' => $n * (1 + $x) / 2,
            ];
        }
        if ($token['type'] === 'range') {
            $min = (float) $token['min'];
            $max = (float) $token['max'];

            return [
                'min' => $min,
                'max' => $max,
                'average' => ($min + $max) / 2,
            ];
        }
        $value = (float) $token['value'];

        return ['min' => $value, 'max' => $value, 'average' => $value];
    }

    /**
     * @param  array{min: float, max: float, average: float}  $a
     * @param  array{min: float, max: float, average: float}  $b
     * @return array{min: float, max: float, average: float}
     */
    private function applyStatsOp(array $a, array $b, string $op): array
    {
        return match ($op) {
            '+' => [
                'min' => $a['min'] + $b['min'],
                'max' => $a['max'] + $b['max'],
                'average' => $a['average'] + $b['average'],
            ],
            '-' => [
                'min' => $a['min'] - $b['max'],
                'max' => $a['max'] - $b['min'],
                'average' => $a['average'] - $b['average'],
            ],
            '*' => $this->endpointStats($a, $b, static fn (float $x, float $y): float => $x * $y),
            '/' => $this->divisionStats($a, $b),
            default => $a,
        };
    }

    /**
     * @param  array{min: float, max: float, average: float}  $a
     * @param  array{min: float, max: float, average: float}  $b
     * @param  callable(float, float): float  $combine
     * @return array{min: float, max: float, average: float}
     */
    private function endpointStats(array $a, array $b, callable $combine): array
    {
        $values = [
            $combine($a['min'], $b['min']),
            $combine($a['min'], $b['max']),
            $combine($a['max'], $b['min']),
            $combine($a['max'], $b['max']),
        ];

        return [
            'min' => min($values),
            'max' => max($values),
            'average' => $combine($a['average'], $b['average']),
        ];
    }

    /**
     * @param  array{min: float, max: float, average: float}  $a
     * @param  array{min: float, max: float, average: float}  $b
     * @return array{min: float, max: float, average: float}
     */
    private function divisionStats(array $a, array $b): array
    {
        if ($b['min'] <= 0 && $b['max'] >= 0) {
            return ['min' => 0.0, 'max' => 0.0, 'average' => 0.0];
        }

        return $this->endpointStats($a, $b, static fn (float $x, float $y): float => $x / $y);
    }

    /**
     * @param  list<array<string, mixed>>  $tokens
     * @return array{result: float, breakdown: list<string>}|null
     */
    private function rollTokens(array $tokens): ?array
    {
        $values = [];
        $ops = [];
        $breakdown = [];

        foreach ($tokens as $token) {
            if ($token['type'] === 'op') {
                $ops[] = $token['value'];

                continue;
            }
            $rolled = $this->rollToken($token);
            $values[] = $rolled['value'];
            $breakdown[] = $rolled['label'];
        }

        if ($values === [] || count($values) !== count($ops) + 1) {
            return null;
        }

        $result = $this->reduce(
            $values,
            $ops,
            function (float $a, float $b, string $op): float {
                return match ($op) {
                    '+' => $a + $b,
                    '-' => $a - $b,
                    '*' => $a * $b,
                    '/' => $b == 0.0 ? 0.0 : $a / $b,
                    default => $a,
                };
            }
        );

        return ['result' => $result, 'breakdown' => $breakdown];
    }

    /**
     * @param  array<string, mixed>  $token
     * @return array{value: float, label: string}
     */
    private function rollToken(array $token): array
    {
        if ($token['type'] === 'dice') {
            $n = (int) $token['n'];
            $x = (int) $token['x'];
            $rolls = [];
            $sum = 0;
            for ($i = 0; $i < $n; $i++) {
                $roll = random_int(1, $x);
                $rolls[] = $roll;
                $sum += $roll;
            }

            return [
                'value' => (float) $sum,
                'label' => $n.'d'.$x.': '.implode('+', $rolls).'='.$sum,
            ];
        }

        if ($token['type'] === 'range') {
            $min = (float) $token['min'];
            $max = (float) $token['max'];
            if ($this->isWholeNumber($min) && $this->isWholeNumber($max)) {
                $value = (float) random_int((int) $min, (int) $max);
            } else {
                $value = $min + ($max - $min) * (random_int(0, 10_000) / 10_000);
            }
            $labelMin = $this->formatNumber($min);
            $labelMax = $this->formatNumber($max);

            return [
                'value' => $value,
                'label' => '['.$labelMin.'-'.$labelMax.']: '.$this->formatNumber($value),
            ];
        }

        $value = (float) $token['value'];

        return ['value' => $value, 'label' => $this->formatNumber($value)];
    }

    /**
     * @template T
     *
     * @param  list<T>  $terms
     * @param  list<string>  $ops
     * @param  callable(T, T, string): T  $apply
     * @return T|null
     */
    private function reduce(array $terms, array $ops, callable $apply): mixed
    {
        $acc = [$terms[0]];
        $accOps = [];
        $j = 0;

        foreach ($ops as $i => $op) {
            if ($op === '*' || $op === '/') {
                $acc[array_key_last($acc)] = $apply($acc[array_key_last($acc)], $terms[$j + 1], $op);
                $j++;
            } else {
                $acc[] = $terms[$j + 1];
                $accOps[] = $op;
                $j++;
            }
        }

        $result = $acc[0];
        foreach ($accOps as $i => $op) {
            $result = $apply($result, $acc[$i + 1], $op);
        }

        return $result;
    }

    /**
     * @param  list<array<string, mixed>>  $tokens
     * @return list<string>
     */
    private function rangeEquivalents(array $tokens): array
    {
        $lines = [];
        foreach ($tokens as $token) {
            if ($token['type'] !== 'range' || ! is_string($token['equivalent'] ?? null) || $token['equivalent'] === '') {
                continue;
            }
            $min = $this->formatNumber((float) $token['min']);
            $max = $this->formatNumber((float) $token['max']);
            $lines[] = '['.$min.'-'.$max.'] = '.$token['equivalent'];
        }

        return $lines;
    }

    private function isWholeNumber(float $value): bool
    {
        return abs($value - round($value)) < 0.0000001;
    }

    private function roundStat(int|float $value): int|float
    {
        if (! is_finite((float) $value)) {
            return 0;
        }
        $rounded = round((float) $value, 3);

        return $this->isWholeNumber($rounded) ? (int) round($rounded) : $rounded;
    }

    private function formatNumber(int|float $value): string
    {
        $rounded = $this->roundStat($value);

        return (string) $rounded;
    }
}
