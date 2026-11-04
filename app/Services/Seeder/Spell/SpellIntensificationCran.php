<?php

declare(strict_types=1);

namespace App\Services\Seeder\Spell;

/**
 * Lignes d’intensification d’un sort de classe (§3.3.6).
 *
 * Fiche prioritaire si {@code intensification} est déjà dans l’entrée (tableau vide =
 * aucune ligne). Sinon cran par défaut : une ligne par cran du repère, +1 dé
 * (plafond 5d6 / 5d4) ou +1 case / tour.
 *
 * Le choix élémentaire du niveau 1 (slot 1) suit 5, 10, 15, 20. Les autres sorts
 * suivent le repère de leur niveau d’apprentissage.
 *
 * @example $tiers = (new SpellIntensificationCran())->tiersFor($entry);
 */
final class SpellIntensificationCran
{
    /** @var list<string> */
    private const TIER_LABELS = ['I', 'II', 'III', 'IV', 'V', 'VI'];

    /**
     * @param  array<string, mixed>  $entry
     * @return list<array{tier: string, required_creature_level: int, effect: string, sub_effects: list<array<string, mixed>>}>
     */
    public function tiersFor(array $entry): array
    {
        if (! array_key_exists('intensification', $entry) || $entry['intensification'] === null) {
            return $this->defaultTiers($entry);
        }

        $authored = $entry['intensification'];
        if (! is_array($authored) || $authored === []) {
            return [];
        }

        return $this->normalizeAuthored($authored);
    }

    /**
     * Niveaux d’intensification du repère §3.3.6.2, hors ligne d’apprentissage.
     *
     * @return list<int>
     */
    public function scheduleLevels(int $characterLevel, bool $elementalLevel1): array
    {
        if ($characterLevel === 1 && $elementalLevel1) {
            return [5, 10, 15, 20];
        }

        return match ($characterLevel) {
            1 => [7, 14, 20],
            2 => [6, 13, 19],
            4 => [9, 14, 19],
            5 => [12, 20],
            6 => [11, 18],
            7 => [13, 18],
            8 => [13, 20],
            10 => [17],
            11 => [20],
            12 => [17],
            default => [7, 14, 20],
        };
    }

    /**
     * Concatène la ligne Appris et les paliers (y compris « — »).
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
        $characterLevel = max(1, (int) ($entry['character_level'] ?? 1));
        $elemental = $characterLevel === 1 && (int) ($entry['slot_index'] ?? 0) === 1;

        foreach ($this->scheduleLevels($characterLevel, $elemental) as $index => $requiredLevel) {
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
                'tier' => self::TIER_LABELS[$index] ?? (string) ($index + 1),
                'required_creature_level' => $requiredLevel,
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
        foreach (array_values($authored) as $index => $row) {
            if (! is_array($row)) {
                continue;
            }
            $requiredLevel = (int) ($row['required_creature_level'] ?? 0);
            if ($requiredLevel <= 0) {
                continue;
            }
            $subs = [];
            foreach (is_array($row['sub_effects'] ?? null) ? $row['sub_effects'] : [] as $sub) {
                if (is_array($sub)) {
                    $subs[] = $sub;
                }
            }
            $effect = trim((string) ($row['effect'] ?? ''));
            $label = trim((string) ($row['tier'] ?? ''));
            // Un palier sans commentaire libre mais avec sous-effets reste valide.
            $tier = [
                'tier' => $label !== '' ? $label : (self::TIER_LABELS[$index] ?? (string) ($index + 1)),
                'required_creature_level' => $requiredLevel,
                'effect' => $effect,
                'sub_effects' => $subs,
            ];
            foreach ([
                'pa',
                'po_min',
                'po_max',
                'po_editable',
                'sight_line',
                'cast_in_line',
                'cast_in_diagonal',
                'area',
                'cast_per_turn',
                'cast_per_target',
                'number_between_two_cast',
                'global_cooldown',
                'max_stack',
                'duration',
                'casting_time',
            ] as $usageKey) {
                if (array_key_exists($usageKey, $row)) {
                    $tier[$usageKey] = $row[$usageKey];
                }
            }
            $tiers[] = $tier;
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
