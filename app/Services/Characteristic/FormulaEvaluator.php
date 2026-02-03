<?php

declare(strict_types=1);

namespace App\Services\Characteristic;

use App\Services\Characteristic\Formula\FormulaResolutionService;

/**
 * Évalue une formule de caractéristique (syntaxe [id], floor, ceil, + - * /)
 * ou un tableau par caractéristique (JSON : characteristic + seuils → valeur fixe ou formule).
 * Délègue au FormulaResolutionService (sécurisé, sans eval()).
 *
 * @see docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md
 * @see FormulaConfigDecoder
 * @see FormulaResolutionService
 */
final class FormulaEvaluator
{
    public function __construct(
        private readonly FormulaResolutionService $resolutionService
    ) {
    }

    /**
     * Évalue le champ formula (formule simple ou table JSON par caractéristique).
     *
     * @param string|null $formula Formule ou JSON table (ex. {"characteristic":"level","1":0,"7":2,"14":4})
     * @param array<string, int|float> $variables Map id => valeur (ex. level, vitality)
     * @return float|null Résultat ou null si invalide
     */
    public function evaluateFormulaOrTable(?string $formula, array $variables): ?float
    {
        return $this->resolutionService->evaluate($formula, $variables);
    }

    /**
     * Évalue une formule en remplaçant les variables [id] par les valeurs fournies.
     *
     * @param string $formula Formule (ex. [vitality]*10+[level]*2)
     * @param array<string, int|float> $variables Map id => valeur
     * @return float|null Résultat ou null si formule invalide
     */
    public function evaluate(string $formula, array $variables): ?float
    {
        return $this->resolutionService->evaluate($formula, $variables);
    }
}
