<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Domain;

use App\Services\Characteristic\Formula\FormulaExpressionParser;

/**
 * Résout le niveau d'une entité en liste de niveaux possibles.
 *
 * Le niveau est la seule caractéristique qui accepte un domaine variable : un nombre (`7`),
 * une fourchette (`{[5-8]}`) ou un dé (`{8 + [1d4]}`). Toutes les autres caractéristiques se
 * calculent ensuite niveau par niveau.
 *
 * @example
 *   $resolver->resolve('7');            // [7]
 *   $resolver->resolve('{[5-8]}');      // [5, 6, 7, 8]
 *   $resolver->resolve('{8 + [1d4]}');  // [9, 10, 11, 12]
 *   $resolver->resolve('1d4');          // [1, 2, 3, 4] (forme héritée, sans accolades)
 *
 * @see docs/features/characteristics/COMPUTED_VALUES.md
 */
final class LevelDomainResolver
{
    /** Nombre maximal de niveaux retournés (évite les tableaux ingérables côté UI). */
    public const MAX_LEVELS = 20;

    /** Niveau utilisé quand la saisie est vide ou illisible. */
    private const FALLBACK_LEVEL = 1;

    public function __construct(
        private readonly FormulaExpressionParser $parser
    ) {}

    /**
     * @return list<int> Niveaux possibles, triés, bornés à MAX_LEVELS
     */
    public function resolve(int|string|null $level, int $maxLevels = self::MAX_LEVELS): array
    {
        $raw = trim((string) $level);
        if ($raw === '') {
            return [self::FALLBACK_LEVEL];
        }

        $outcomes = $this->parser->enumerateOutcomes($this->normalizeLegacySyntax($raw), [], $maxLevels);

        $levels = [];
        foreach ($outcomes as $outcome) {
            $value = (int) round($outcome);
            if ($value >= 0 && ! in_array($value, $levels, true)) {
                $levels[] = $value;
            }
        }

        if ($levels === []) {
            return [self::FALLBACK_LEVEL];
        }

        sort($levels);

        return $levels;
    }

    /**
     * Premier niveau possible : celui affiché par défaut sur les fiches.
     */
    public function defaultLevel(int|string|null $level): int
    {
        return $this->resolve($level)[0];
    }

    public function isVariable(int|string|null $level): bool
    {
        return count($this->resolve($level)) > 1;
    }

    /**
     * Accepte les écritures historiques sans accolades (`1d4`, `[5-8]`, `5-8`) en les ramenant
     * à la forme canonique `{...}`.
     */
    private function normalizeLegacySyntax(string $raw): string
    {
        if (str_starts_with($raw, '{')) {
            return $raw;
        }
        if (is_numeric($raw)) {
            return $raw;
        }

        if (preg_match('/^\[?\s*\d+\s*(?:-|–|\.\.)\s*\d+\s*\]?$/', $raw) === 1) {
            $inner = trim($raw, '[]');

            return '{['.trim($inner).']}';
        }

        if (preg_match('/^\d*[dD]\d+$/', $raw) === 1) {
            return '{'.$raw.'}';
        }

        return $raw;
    }
}
