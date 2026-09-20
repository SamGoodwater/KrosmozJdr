<?php

declare(strict_types=1);

namespace App\Services\Seeder\Spell;

/**
 * Trois paliers d’intensification (I / II / III) pour un sort de classe.
 *
 * Fiche prioritaire si {@code intensification} est déjà dans l’entrée ; sinon cran
 * par défaut de 3.3.6.3 : +1 dé (plafond 5d6 / 5d4) ou +1 case / tour.
 *
 * @example $tiers = (new SpellIntensificationCran())->tiersFor($entry);
 */
final class SpellIntensificationCran
{
    /** @var list<array{tier: string, required_creature_level: int}> */
    public const TIERS = [
        ['tier' => 'I', 'required_creature_level' => 13],
        ['tier' => 'II', 'required_creature_level' => 16],
        ['tier' => 'III', 'required_creature_level' => 20],
    ];

    /**
     * @param  array<string, mixed>  $entry
     * @return list<array{tier: string, required_creature_level: int, effect: string, sub_effects: list<array<string, mixed>>}>
     */
    public function tiersFor(array $entry): array
    {
        $authored = $entry['intensification'] ?? null;
        if (is_array($authored) && $authored !== []) {
            return $this->normalizeAuthored($authored);
        }

        return $this->defaultTiers($entry);
    }

    /**
     * Concatène la ligne Appris et les trois paliers (y compris « — »).
     *
     * @param  array<string, mixed>  $entry
     */
    public function effectWithTiers(array $entry): string
    {
        $lines = [trim((string) ($entry['effect'] ?? ''))];
        foreach ($this->tiersFor($entry) as $tier) {
            $lines[] = sprintf(
                'Palier %s (perso %d) : %s',
                $tier['tier'],
                $tier['required_creature_level'],
                $tier['effect'] !== '' ? $tier['effect'] : '—',
            );
        }

        return implode("\n", array_filter($lines, static fn (string $line): bool => $line !== ''));
    }

    /**
     * @param  array<string, mixed>  $entry
     * @return list<array{tier: string, required_creature_level: int, effect: string, sub_effects: list<array<string, mixed>>}>
     */
    public function defaultTiers(array $entry): array
    {
        $baseEffect = (string) ($entry['effect'] ?? '');
        $baseSubs = is_array($entry['sub_effects'] ?? null) ? $entry['sub_effects'] : [];
        $kind = $this->kind($baseSubs);
        $previous = $baseEffect;
        $tiers = [];

        foreach (self::TIERS as $index => $meta) {
            $steps = $index + 1;
            $text = $this->intensifyEffectText($baseEffect, $steps, $kind);
            $subs = $this->bumpSubEffects($baseSubs, $steps, $kind);
            if ($text === '—' || $text === $previous) {
                $text = '—';
                $subs = [];
            } else {
                $previous = $text;
            }

            $tiers[] = [
                'tier' => $meta['tier'],
                'required_creature_level' => $meta['required_creature_level'],
                'effect' => $text,
                'sub_effects' => $subs,
            ];
        }

        return $tiers;
    }

    /**
     * @param  list<array<string, mixed>>  $authored
     * @return list<array{tier: string, required_creature_level: int, effect: string, sub_effects: list<array<string, mixed>>}>
     */
    private function normalizeAuthored(array $authored): array
    {
        $tiers = [];
        foreach (self::TIERS as $index => $meta) {
            $row = is_array($authored[$index] ?? null) ? $authored[$index] : [];
            $subs = [];
            foreach (is_array($row['sub_effects'] ?? null) ? $row['sub_effects'] : [] as $sub) {
                if (is_array($sub)) {
                    $subs[] = $sub;
                }
            }
            $effect = trim((string) ($row['effect'] ?? '—'));
            $tiers[] = [
                'tier' => $meta['tier'],
                'required_creature_level' => $meta['required_creature_level'],
                'effect' => $effect !== '' ? $effect : '—',
                'sub_effects' => $effect === '—' ? [] : $subs,
            ];
        }

        return $tiers;
    }

    /**
     * @param  list<array<string, mixed>>  $subs
     */
    private function kind(array $subs): string
    {
        $slugs = [];
        foreach ($subs as $sub) {
            $slugs[] = (string) ($sub['slug'] ?? '');
        }
        if (in_array('soigner', $slugs, true) || in_array('protéger', $slugs, true)) {
            return 'heal';
        }
        $blob = json_encode($subs) ?: '';
        if (preg_match('/\d+d6/i', $blob) === 1) {
            return 'attack';
        }
        if (preg_match('/\d+d4/i', $blob) === 1) {
            return 'heal';
        }

        return 'utility';
    }

    private function intensifyEffectText(string $effect, int $steps, string $kind): string
    {
        $withDice = $this->bumpDiceInString($effect, $steps, $kind);
        if ($withDice !== $effect) {
            return $withDice;
        }

        $withCells = preg_replace_callback(
            '/(\d+)(\s*cases?)/u',
            static fn (array $m): string => ((int) $m[1] + $steps).$m[2],
            $effect,
            1,
        );
        if (is_string($withCells) && $withCells !== $effect) {
            return $withCells;
        }

        return '—';
    }

    /**
     * @param  list<array<string, mixed>>  $subs
     * @return list<array<string, mixed>>
     */
    public function bumpSubEffects(array $subs, int $steps, string $kind): array
    {
        $out = [];
        foreach ($subs as $sub) {
            if (! is_array($sub)) {
                continue;
            }
            $copy = $sub;
            $params = is_array($copy['params'] ?? null) ? $copy['params'] : [];
            $copy['params'] = $this->bumpParams($params, $steps, $kind);
            if (isset($copy['duration_formula']) && is_string($copy['duration_formula'])) {
                $copy['duration_formula'] = $this->bumpScalarFormula($copy['duration_formula'], $steps, $kind);
            }
            $out[] = $copy;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private function bumpParams(array $params, int $steps, string $kind): array
    {
        foreach ($params as $key => $value) {
            if (! is_string($value) && ! is_int($value)) {
                continue;
            }
            $asString = (string) $value;
            if (preg_match('/\d+d\d+/i', $asString) === 1) {
                $params[$key] = $this->bumpDiceInString($asString, $steps, $kind);

                continue;
            }
            if ($kind === 'utility' && in_array($key, ['cells_formula', 'duration_formula'], true) && is_numeric($asString)) {
                $params[$key] = (string) ((int) $asString + $steps);
            }
        }

        return $params;
    }

    private function bumpScalarFormula(string $formula, int $steps, string $kind): string
    {
        if (preg_match('/\d+d\d+/i', $formula) === 1) {
            return $this->bumpDiceInString($formula, $steps, $kind);
        }
        if ($kind === 'utility' && is_numeric($formula)) {
            return (string) ((int) $formula + $steps);
        }

        return $formula;
    }

    private function bumpDiceInString(string $formula, int $steps, string $kind): string
    {
        $cap = 5;
        $replaced = preg_replace_callback(
            '/(\d+)d(\d+)/i',
            static function (array $m) use ($steps, $cap): string {
                $n = (int) $m[1];
                $sides = (int) $m[2];

                return min($n + $steps, $cap).'d'.$sides;
            },
            $formula,
        );

        return is_string($replaced) ? $replaced : $formula;
    }
}
