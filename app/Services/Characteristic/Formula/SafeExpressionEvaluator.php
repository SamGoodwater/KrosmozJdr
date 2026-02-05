<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Formula;

/**
 * Évalue une expression mathématique sans eval().
 *
 * Caractères autorisés : chiffres, + - * / ( ) , d (notation dés) et noms de fonctions listées ci-dessous.
 * Utilisé après remplacement des variables [id] par des valeurs numériques.
 * Sécurisé contre l'injection de code.
 *
 * Notation dés (JDR) : NdX = somme de N dés à X faces (ex. 2d6 = entre 2 et 12, 1d8 = entre 1 et 8).
 * N et X peuvent être des expressions numériques (souvent des variables substituées : [level]d[life_dice]).
 *
 * Opérateur puissance : ** (ex. 2**3 = 8, [level]**2). Associatif à droite (2**3**2 = 2**(3**2)).
 * Fonctions autorisées (1 argument) : floor, ceil, round, sqrt, abs, cos, sin, tan, asin, acos, atan.
 * Fonctions autorisées (2 arguments) : pow, min, max.
 *
 * @internal Utilisé par FormulaResolutionService
 */
final class SafeExpressionEvaluator
{
    /** Caractères autorisés : chiffres, opérateurs, parenthèses, virgule, lettres pour noms de fonctions */
    private const PATTERN_ALLOWED = '/^[\d\s+\-*\/().eE,a-zA-Z]+$/';

    /** Noms de fonctions autorisées (sans espaces, en minuscules pour comparaison) */
    private const ALLOWED_FUNCTIONS = [
        'floor', 'ceil', 'round', 'sqrt', 'abs',
        'cos', 'sin', 'tan', 'asin', 'acos', 'atan',
        'pow', 'min', 'max',
    ];

    /**
     * Vérifie que l'expression ne contient que des tokens autorisés (sécurité).
     *
     * @return list<string> Liste d'erreurs (vide si valide)
     */
    public function validate(string $expression): array
    {
        $expr = preg_replace('/\s+/', ' ', trim($expression));
        if ($expr === '') {
            return [];
        }

        $errors = [];

        if (!preg_match(self::PATTERN_ALLOWED, $expr)) {
            $errors[] = 'Caractères non autorisés (chiffres, + - * / ( ) , et noms de fonctions uniquement)';
        }

        $parens = 0;
        $len = strlen($expr);
        for ($i = 0; $i < $len; $i++) {
            $c = $expr[$i];
            if ($c === '(') {
                $parens++;
            } elseif ($c === ')') {
                $parens--;
                if ($parens < 0) {
                    $errors[] = 'Parenthèses non équilibrées';
                    break;
                }
            }
        }
        if ($parens > 0) {
            $errors[] = 'Parenthèses non fermées';
        }

        if (preg_match_all('/\b([a-zA-Z]+)\s*\(/', $expr, $matches)) {
            foreach ($matches[1] as $name) {
                if (!in_array(strtolower($name), self::ALLOWED_FUNCTIONS, true)) {
                    $errors[] = sprintf('Fonction "%s" non autorisée. Autorisées : %s.', $name, implode(', ', self::ALLOWED_FUNCTIONS));
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * Évalue l'expression (sans eval).
     *
     * @param string $expression Expression après remplacement des variables (ex. "floor(3.7)+2*10")
     * @return float|null Résultat ou null si invalide
     */
    public function evaluate(string $expression): ?float
    {
        $expr = preg_replace('/\s+/', ' ', trim($expression));
        if ($expr === '' || $this->validate($expr) !== []) {
            return null;
        }

        // Ordre important : asin, acos, atan avant sin, cos, tan pour ne pas couper les noms
        $oneArgReplace = [
            'floor(', 'ceil(', 'round(', 'sqrt(', 'abs(',
            'asin(', 'acos(', 'atan(', 'cos(', 'sin(', 'tan(',
        ];
        $oneArgWithPrefix = [
            '__floor(', '__ceil(', '__round(', '__sqrt(', '__abs(',
            '__asin(', '__acos(', '__atan(', '__cos(', '__sin(', '__tan(',
        ];
        $expr = str_replace($oneArgReplace, $oneArgWithPrefix, $expr);
        $pos = 0;
        $result = $this->parseExpr($expr, $pos);
        if ($result === null || $pos < strlen($expr)) {
            return null;
        }
        return $result;
    }

    private function parseExpr(string $expr, int &$pos): ?float
    {
        $left = $this->parseTerm($expr, $pos);
        if ($left === null) {
            return null;
        }
        $len = strlen($expr);
        while ($pos < $len) {
            $this->skipSpaces($expr, $pos);
            if ($pos >= $len) {
                break;
            }
            $op = $expr[$pos];
            if ($op === '+') {
                $pos++;
                $right = $this->parseTerm($expr, $pos);
                if ($right === null) {
                    return null;
                }
                $left += $right;
            } elseif ($op === '-') {
                $pos++;
                $right = $this->parseTerm($expr, $pos);
                if ($right === null) {
                    return null;
                }
                $left -= $right;
            } else {
                break;
            }
        }
        return $left;
    }

    private function parseTerm(string $expr, int &$pos): ?float
    {
        $left = $this->parseFactor($expr, $pos);
        if ($left === null) {
            return null;
        }
        $len = strlen($expr);
        while ($pos < $len) {
            $this->skipSpaces($expr, $pos);
            if ($pos >= $len) {
                break;
            }
            $op = $expr[$pos];
            if ($op === '*') {
                $pos++;
                if ($pos < $len && $expr[$pos] === '*') {
                    $pos++;
                    $right = $this->parseTerm($expr, $pos);
                    if ($right === null) {
                        return null;
                    }
                    $left = (float) pow($left, $right);
                } else {
                    $right = $this->parseFactor($expr, $pos);
                    if ($right === null) {
                        return null;
                    }
                    $left *= $right;
                }
            } elseif ($op === '/') {
                $pos++;
                $right = $this->parseFactor($expr, $pos);
                if ($right === null) {
                    return null;
                }
                $left = $right === 0.0 ? 0.0 : ($left / $right);
            } else {
                break;
            }
        }
        return $left;
    }

    private function parseFactor(string $expr, int &$pos): ?float
    {
        $this->skipSpaces($expr, $pos);
        $len = strlen($expr);
        if ($pos >= $len) {
            return null;
        }

        if ($expr[$pos] === '-') {
            $pos++;
            $f = $this->parseFactor($expr, $pos);
            return $f !== null ? -$f : null;
        }

        if ($expr[$pos] === '(') {
            $pos++;
            $v = $this->parseExpr($expr, $pos);
            $this->skipSpaces($expr, $pos);
            if ($pos >= $len || $expr[$pos] !== ')') {
                return null;
            }
            $pos++;
            return $v;
        }

        // Fonctions à 1 argument (préfixées __ après str_replace)
        $oneArgFuncs = [
            '__floor(' => 8,
            '__ceil(' => 7,
            '__round(' => 8,
            '__sqrt(' => 7,
            '__abs(' => 6,
            '__cos(' => 6,
            '__sin(' => 6,
            '__tan(' => 6,
            '__asin(' => 7,
            '__acos(' => 7,
            '__atan(' => 7,
        ];
        foreach ($oneArgFuncs as $prefix => $prefixLen) {
            if (substr($expr, $pos, $prefixLen) === $prefix) {
                $pos += $prefixLen;
                $v = $this->parseExpr($expr, $pos);
                $this->skipSpaces($expr, $pos);
                if ($pos >= $len || $expr[$pos] !== ')') {
                    return null;
                }
                $pos++;
                if ($v === null) {
                    return null;
                }
                return match ($prefix) {
                    '__floor(' => (float) floor($v),
                    '__ceil(' => (float) ceil($v),
                    '__round(' => (float) round($v),
                    '__sqrt(' => $v < 0 ? 0.0 : (float) sqrt($v),
                    '__abs(' => (float) abs($v),
                    '__cos(' => (float) cos($v),
                    '__sin(' => (float) sin($v),
                    '__tan(' => (float) tan($v),
                    '__asin(' => (float) asin(max(-1, min(1, $v))),
                    '__acos(' => (float) acos(max(-1.0, min(1.0, $v))),
                    '__atan(' => (float) atan($v),
                    default => $v,
                };
            }
        }

        // Fonctions à 2 arguments : pow, min, max
        $twoArgFuncs = ['pow', 'min', 'max'];
        foreach ($twoArgFuncs as $fn) {
            $fnLen = strlen($fn);
            if ($pos + $fnLen + 1 <= $len && substr($expr, $pos, $fnLen + 1) === $fn . '(') {
                $pos += $fnLen + 1;
                $a = $this->parseExpr($expr, $pos);
                if ($a === null) {
                    return null;
                }
                $this->skipSpaces($expr, $pos);
                if ($pos >= $len || $expr[$pos] !== ',') {
                    return null;
                }
                $pos++;
                $b = $this->parseExpr($expr, $pos);
                if ($b === null) {
                    return null;
                }
                $this->skipSpaces($expr, $pos);
                if ($pos >= $len || $expr[$pos] !== ')') {
                    return null;
                }
                $pos++;
                return match ($fn) {
                    'pow' => (float) pow($a, $b),
                    'min' => (float) min($a, $b),
                    'max' => (float) max($a, $b),
                    default => null,
                };
            }
        }

        // Notation dés JDR : NdX = somme de N dés à X faces (N et X numériques après substitution)
        $n = $this->parseNumber($expr, $pos);
        if ($n !== null) {
            $this->skipSpaces($expr, $pos);
            if ($pos < $len && $expr[$pos] === 'd') {
                $pos++;
                $this->skipSpaces($expr, $pos);
                $x = $this->parseNumber($expr, $pos);
                if ($x === null) {
                    return null; // "Nd" sans nombre après d = invalide
                }
                return (float) $this->rollDice((int) max(1, $n), (int) max(1, $x));
            }
            return $n;
        }

        return null;
    }

    /**
     * Lance N dés à X faces et retourne la somme (chaque dé : entier entre 1 et X inclus).
     */
    private function rollDice(int $n, int $x): int
    {
        $sum = 0;
        for ($i = 0; $i < $n; $i++) {
            $sum += mt_rand(1, max(1, $x));
        }

        return $sum;
    }

    private function parseNumber(string $expr, int &$pos): ?float
    {
        $this->skipSpaces($expr, $pos);
        $len = strlen($expr);
        if ($pos >= $len) {
            return null;
        }
        $start = $pos;
        if ($expr[$pos] === '.' || ($expr[$pos] >= '0' && $expr[$pos] <= '9')) {
            if ($expr[$pos] >= '0' && $expr[$pos] <= '9') {
                while ($pos < $len && $expr[$pos] >= '0' && $expr[$pos] <= '9') {
                    $pos++;
                }
            }
            if ($pos < $len && $expr[$pos] === '.') {
                $pos++;
                while ($pos < $len && $expr[$pos] >= '0' && $expr[$pos] <= '9') {
                    $pos++;
                }
            }
            if ($pos < $len && ($expr[$pos] === 'e' || $expr[$pos] === 'E')) {
                $pos++;
                if ($pos < $len && ($expr[$pos] === '+' || $expr[$pos] === '-')) {
                    $pos++;
                }
                while ($pos < $len && $expr[$pos] >= '0' && $expr[$pos] <= '9') {
                    $pos++;
                }
            }
            $s = substr($expr, $start, $pos - $start);
            $n = is_numeric($s) ? (float) $s : null;
            return $n;
        }
        return null;
    }

    private function skipSpaces(string $expr, int &$pos): void
    {
        $len = strlen($expr);
        while ($pos < $len && $expr[$pos] === ' ') {
            $pos++;
        }
    }
}
