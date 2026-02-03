<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Formula;

use App\Services\Characteristic\FormulaConfigDecoder;

/**
 * Service central de résolution des formules de caractéristiques.
 *
 * Responsabilités :
 * - Valider le format des formules (syntaxe, pas de constructions dangereuses)
 * - Sécurité : évaluation sans eval(), uniquement nombres, opérateurs, floor/ceil, variables [id]
 * - Évaluer une formule (ou table JSON) avec des variables données
 * - Produire toutes les valeurs possibles pour une plage d'une variable (ex. level 1 à 20)
 *
 * @see docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md
 * @see FormulaConfigDecoder
 * @see SafeExpressionEvaluator
 */
final class FormulaResolutionService
{
    /** Pattern pour les variables [id] : alphanumériques et underscore uniquement (sécurité) */
    private const VARIABLE_PATTERN = '/\[([a-zA-Z_][a-zA-Z0-9_]*)\]/';

    public function __construct(
        private readonly SafeExpressionEvaluator $expressionEvaluator
    ) {
    }

    /**
     * Valide le format d'une formule (ou table) et retourne la liste d'erreurs.
     *
     * Pour une formule : vérifie que seuls [id], nombres, + - * / ( ), floor, ceil sont présents,
     * puis valide l'expression après substitution des variables par 0.
     * Pour une table JSON : valide la structure et chaque entrée (valeur fixe ou sous-formule).
     *
     * @return list<string> Liste d'erreurs (vide si valide)
     */
    public function validateFormula(?string $formula): array
    {
        if ($formula === null || trim($formula ?? '') === '') {
            return [];
        }

        $decoded = FormulaConfigDecoder::decode($formula);

        if ($decoded['type'] === 'table') {
            return $this->validateTable($decoded);
        }

        return $this->validateExpression($decoded['expression']);
    }

    /**
     * Évalue une formule (simple ou table JSON) avec les variables fournies.
     *
     * @param string|null $formula Formule (ex. [vitality]*10+[level]*2) ou JSON table
     * @param array<string, int|float> $variables Map id => valeur (ex. level => 5, vitality => 12)
     * @return float|null Résultat ou null si formule invalide / vide
     */
    public function evaluate(?string $formula, array $variables): ?float
    {
        if ($formula === null || trim($formula) === '') {
            return null;
        }

        $decoded = FormulaConfigDecoder::decode($formula);

        if ($decoded['type'] === 'table') {
            return $this->evaluateTable($decoded, $variables);
        }

        return $this->evaluateExpression($decoded['expression'], $variables);
    }

    /**
     * Pour une formule, retourne un tableau de toutes les valeurs possibles
     * quand une variable parcourt une plage [min, max] (inclus).
     *
     * Les autres variables restent fixées via $baseVariables. Utile par ex. pour
     * afficher les valeurs level 1→20 d'une formule qui contient [level].
     *
     * @param string|null $formula Formule ou table JSON
     * @param string $variableName Nom de la variable à faire varier (ex. "level")
     * @param int $min Valeur minimale (incluse)
     * @param int $max Valeur maximale (incluse)
     * @param array<string, int|float> $baseVariables Variables fixes (ex. vitality => 10)
     * @return array<int, float> Map valeur_variable => résultat (ex. [1 => 12.0, 2 => 14.0, ...])
     */
    public function evaluateForVariableRange(
        ?string $formula,
        string $variableName,
        int $min,
        int $max,
        array $baseVariables = []
    ): array {
        $result = [];
        $minVal = min($min, $max);
        $maxVal = max($min, $max);

        for ($value = $minVal; $value <= $maxVal; $value++) {
            $vars = $baseVariables;
            $vars[$variableName] = $value;
            $evaluated = $this->evaluate($formula, $vars);
            $result[$value] = $evaluated ?? 0.0;
        }

        return $result;
    }

    /**
     * Valide une expression (formule simple) : caractères autorisés et syntaxe.
     *
     * @return list<string>
     */
    private function validateExpression(string $expression): array
    {
        $expr = trim($expression);
        if ($expr === '') {
            return [];
        }

        $errors = [];

        // Vérifier que les crochets contiennent uniquement des identifiants sûrs
        if (preg_match_all('/\[([^\]]*)\]/', $expr, $matches)) {
            foreach ($matches[1] as $id) {
                if ($id === '' || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $id)) {
                    $errors[] = 'Variable invalide dans les crochets (attendu : [id] avec id alphanumérique ou underscore)';
                    break;
                }
            }
        }

        $substituted = $this->substituteVariables($expr, []);
        $exprErrors = $this->expressionEvaluator->validate($substituted);
        foreach ($exprErrors as $e) {
            $errors[] = $e;
        }

        return $errors;
    }

    /**
     * @param array{type: 'table', characteristic: string, entries: list<array{from: int, value: int|float|string}>} $decoded
     * @return list<string>
     */
    private function validateTable(array $decoded): array
    {
        $errors = [];
        $char = $decoded['characteristic'] ?? '';
        if ($char === '') {
            $errors[] = 'Table : characteristic manquant';
        }

        $entries = $decoded['entries'] ?? [];
        if ($entries === []) {
            $errors[] = 'Table : au moins une entrée requise';
        }

        foreach ($entries as $entry) {
            $value = $entry['value'] ?? null;
            if (is_string($value) && trim($value) !== '') {
                foreach ($this->validateExpression($value) as $e) {
                    $errors[] = 'Table (entrée ' . ($entry['from'] ?? '?') . ') : ' . $e;
                }
            }
        }

        return $errors;
    }

    /**
     * Évalue une table : prend la valeur de la caractéristique de référence,
     * choisit la tranche (plus grand seuil ≤ valeur), retourne la valeur fixe ou évalue la sous-formule.
     *
     * @param array{type: 'table', characteristic: string, entries: list<array{from: int, value: int|float|string}>} $decoded
     * @param array<string, int|float> $variables
     */
    private function evaluateTable(array $decoded, array $variables): ?float
    {
        $char = $decoded['characteristic'];
        $entries = $decoded['entries'];
        if ($entries === []) {
            return null;
        }

        $refValue = isset($variables[$char]) ? (float) $variables[$char] : 0.0;
        $chosen = null;
        $bestFrom = -1;
        foreach ($entries as $entry) {
            $from = (int) ($entry['from'] ?? 0);
            if ($from <= $refValue && $from >= $bestFrom) {
                $bestFrom = $from;
                $chosen = $entry;
            }
        }
        if ($chosen === null) {
            $chosen = $entries[0];
        }

        $value = $chosen['value'];
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        return $this->evaluateExpression((string) $value, $variables);
    }

    /**
     * Remplace les variables [id] par les valeurs fournies, les [id] restants par 0 (éviter erreurs).
     *
     * @param array<string, int|float> $variables
     */
    private function substituteVariables(string $expression, array $variables): string
    {
        $expr = $expression;
        foreach ($variables as $id => $val) {
            $expr = str_replace('[' . $id . ']', (string) (float) $val, $expr);
        }
        // [id] restants (variables non fournies) → 0
        $expr = (string) preg_replace(self::VARIABLE_PATTERN, '0', $expr);

        return $expr;
    }

    /**
     * Évalue une expression (formule simple) après substitution des variables.
     * Utilise SafeExpressionEvaluator (sans eval()) pour la sécurité.
     *
     * @param array<string, int|float> $variables
     */
    private function evaluateExpression(string $expression, array $variables): ?float
    {
        if (trim($expression) === '') {
            return null;
        }

        $substituted = $this->substituteVariables($expression, $variables);

        return $this->expressionEvaluator->evaluate($substituted);
    }
}
