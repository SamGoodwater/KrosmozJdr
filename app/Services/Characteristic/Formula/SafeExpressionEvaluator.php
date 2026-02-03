<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Formula;

/**
 * Évalue une expression mathématique sans eval().
 *
 * Caractères autorisés : chiffres, + - * / ( ) et les noms floor, ceil.
 * Utilisé après remplacement des variables [id] par des valeurs numériques.
 * Sécurisé contre l'injection de code.
 *
 * @internal Utilisé par FormulaResolutionService
 */
final class SafeExpressionEvaluator
{
    /** Caractères autorisés : chiffres, opérateurs, parenthèses, point décimal, notation scientifique (eE), noms floor/ceil */
    private const PATTERN_ALLOWED = '/^[\d\s+\-*\/().eEfloorceil]+$/i';

    /**
     * Vérifie que l'expression ne contient que des tokens autorisés (sécurité).
     * Autorise : nombres, + - * / ( ) , noms floor et ceil (sans appeler de code externe).
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
            $errors[] = 'Caractères non autorisés (seuls chiffres, + - * / ( ) et décimal sont acceptés)';
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

        if (preg_match('/\b(floor|ceil)\s*\(/i', $expr) && !preg_match('/^[\d\s+\-*\/().floorceil]+$/i', $expr)) {
            $errors[] = 'Seules les fonctions floor() et ceil() sont autorisées';
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

        $expr = str_replace(['floor(', 'ceil('], ['__floor(', '__ceil('], $expr);
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
                $right = $this->parseFactor($expr, $pos);
                if ($right === null) {
                    return null;
                }
                $left *= $right;
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

        if (substr($expr, $pos, 8) === '__floor(') {
            $pos += 8;
            $v = $this->parseExpr($expr, $pos);
            $this->skipSpaces($expr, $pos);
            if ($pos >= $len || $expr[$pos] !== ')') {
                return null;
            }
            $pos++;
            return $v !== null ? (float) floor($v) : null;
        }

        if (substr($expr, $pos, 7) === '__ceil(') {
            $pos += 7;
            $v = $this->parseExpr($expr, $pos);
            $this->skipSpaces($expr, $pos);
            if ($pos >= $len || $expr[$pos] !== ')') {
                return null;
            }
            $pos++;
            return $v !== null ? (float) ceil($v) : null;
        }

        return $this->parseNumber($expr, $pos);
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
