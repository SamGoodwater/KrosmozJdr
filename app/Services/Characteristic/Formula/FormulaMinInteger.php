<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Formula;

/**
 * Entier minimal d’une saisie nombre / formule, pour les filtres tableau (slider).
 *
 * Les formules sont évaluées au niveau d’entité le plus bas (1) afin de n’avoir
 * que des entiers comparables.
 */
final class FormulaMinInteger
{
    public function __construct(
        private readonly FormulaExpressionParser $parser
    ) {}

    /**
     * @example
     * (new FormulaMinInteger($parser))->min('10'); // 10
     * (new FormulaMinInteger($parser))->min('{[niveau]}', 1); // 1
     */
    public function min(?string $raw, int $atLevel = 1): ?int
    {
        $value = trim((string) $raw);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^-?\d+$/', $value) === 1) {
            return (int) $value;
        }

        $outcomes = $this->parser->enumerateOutcomes($value, ['level' => $atLevel]);
        if ($outcomes !== []) {
            return (int) round($outcomes[0]);
        }

        $evaluated = $this->parser->evaluate($value, ['level' => $atLevel]);

        return $evaluated === null ? null : (int) round($evaluated);
    }
}
