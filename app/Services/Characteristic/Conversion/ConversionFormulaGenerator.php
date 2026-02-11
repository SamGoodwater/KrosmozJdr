<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Conversion;

/**
 * Génère une formule de conversion (table ou formules paramétrées) à partir des échantillons
 * Dofus et Krosmoz par niveau. Les courbes Dofus étant souvent en forte croissance (puissance),
 * on propose des ajustements linéaire (k = a*d + b), puissance (k = a*d^b) ou puissance décalée (k = a + b*((d-c)/e)^f).
 *
 * Variable utilisée dans les formules : [d] = valeur Dofus à convertir.
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md
 */
final class ConversionFormulaGenerator
{
    /** Nom de la variable Dofus dans les formules générées. */
    public const DOFUS_VARIABLE = 'd';

    /**
     * Construit les paires (valeur Dofus, valeur Krosmoz) à partir des deux échantillons
     * en faisant correspondre les niveaux. Les niveaux doivent être présents dans les deux.
     *
     * @param array<int|string, int|float> $dofusSample Niveau → valeur Dofus (clés numériques ou "1", "200", etc.)
     * @param array<int|string, int|float> $krosmozSample Niveau → valeur Krosmoz
     * @return list<array{d: float, k: float}>
     */
    public function pairsFromSamples(array $dofusSample, array $krosmozSample): array
    {
        $pairs = [];
        foreach ($dofusSample as $level => $dVal) {
            $levelKey = is_int($level) ? $level : (int) $level;
            if (! array_key_exists($levelKey, $krosmozSample) && ! array_key_exists((string) $levelKey, $krosmozSample)) {
                continue;
            }
            $kVal = $krosmozSample[$levelKey] ?? $krosmozSample[(string) $levelKey] ?? null;
            if ($kVal === null) {
                continue;
            }
            $d = (float) $dVal;
            $k = (float) $kVal;
            $pairs[] = ['d' => $d, 'k' => $k];
        }
        usort($pairs, static fn (array $a, array $b) => $a['d'] <=> $b['d']);
        return $pairs;
    }

    /**
     * Génère une table de conversion (JSON) par valeur Dofus à partir des paires (d, k).
     * Chaque seuil d reçoit la valeur k correspondante. Convient à toute forme de courbe.
     *
     * @param list<array{d: float, k: float}> $pairs Paires (valeur Dofus, valeur Krosmoz)
     * @return string JSON : {"characteristic":"d","d1":k1,"d2":k2,...}
     */
    public function generateTableFromPairs(array $pairs): string
    {
        if ($pairs === []) {
            return '{"characteristic":"d"}';
        }
        $table = ['characteristic' => self::DOFUS_VARIABLE];
        foreach ($pairs as $pair) {
            $d = (int) round($pair['d']);
            $k = (float) $pair['k'];
            $table[(string) $d] = round($k, 4) == (int) round($k, 4) ? (int) round($k) : round($k, 4);
        }
        return json_encode($table, JSON_THROW_ON_ERROR);
    }

    /**
     * Génère la table à partir des deux échantillons (niveau → valeur).
     */
    public function generateTableFromSamples(array $dofusSample, array $krosmozSample): string
    {
        return $this->generateTableFromPairs($this->pairsFromSamples($dofusSample, $krosmozSample));
    }

    /**
     * Ajustement linéaire k = a * d + b (régression linéaire).
     *
     * @param list<array{d: float, k: float}> $pairs
     * @return array{formula: string, a: float, b: float, r2: float}|null
     */
    public function fitLinear(array $pairs): ?array
    {
        if (count($pairs) < 2) {
            return null;
        }
        $n = count($pairs);
        $sumD = 0.0;
        $sumK = 0.0;
        $sumD2 = 0.0;
        $sumDK = 0.0;
        foreach ($pairs as $p) {
            $sumD += $p['d'];
            $sumK += $p['k'];
            $sumD2 += $p['d'] * $p['d'];
            $sumDK += $p['d'] * $p['k'];
        }
        $denom = $n * $sumD2 - $sumD * $sumD;
        if (abs($denom) < 1e-15) {
            return null;
        }
        $a = ($n * $sumDK - $sumD * $sumK) / $denom;
        $b = ($sumK - $a * $sumD) / $n;
        $formula = $this->formatLinearFormula($a, $b);
        $r2 = $this->r2Linear($pairs, $a, $b);
        return ['formula' => $formula, 'a' => $a, 'b' => $b, 'r2' => $r2];
    }

    /**
     * Ajustement puissance k = a * d^b (régression log-log).
     * Retourne null si les données ne permettent pas un bon ajustement (d ou k ≤ 0).
     *
     * @param list<array{d: float, k: float}> $pairs
     * @return array{formula: string, a: float, b: float, r2: float}|null
     */
    public function fitPower(array $pairs): ?array
    {
        $pairs = array_filter($pairs, fn (array $p) => $p['d'] > 0 && $p['k'] > 0);
        if (count($pairs) < 2) {
            return null;
        }
        $n = count($pairs);
        $sumLogD = 0.0;
        $sumLogK = 0.0;
        $sumLogD2 = 0.0;
        $sumLogDLogK = 0.0;
        foreach ($pairs as $p) {
            $logD = log($p['d']);
            $logK = log($p['k']);
            $sumLogD += $logD;
            $sumLogK += $logK;
            $sumLogD2 += $logD * $logD;
            $sumLogDLogK += $logD * $logK;
        }
        $denom = $n * $sumLogD2 - $sumLogD * $sumLogD;
        if (abs($denom) < 1e-15) {
            return null;
        }
        $b = ($n * $sumLogDLogK - $sumLogD * $sumLogK) / $denom;
        $a = exp(($sumLogK - $b * $sumLogD) / $n);
        $formula = $this->formatPowerFormula($a, $b);
        $r2 = $this->r2Power($pairs, $a, $b);
        return ['formula' => $formula, 'a' => $a, 'b' => $b, 'r2' => $r2];
    }

    /**
     * Ajustement puissance décalée k = a + b * ((d - c) / e)^f.
     * On fixe c = min(d), e = max(d) - min(d) (ou 1) puis on cherche a, b, f.
     *
     * @param list<array{d: float, k: float}> $pairs
     * @return array{formula: string, a: float, b: float, c: float, e: float, f: float, r2: float}|null
     */
    public function fitShiftedPower(array $pairs): ?array
    {
        if (count($pairs) < 3) {
            return null;
        }
        $dMin = min(array_column($pairs, 'd'));
        $dMax = max(array_column($pairs, 'd'));
        $c = $dMin;
        $e = $dMax - $dMin;
        if ($e < 1e-9) {
            $e = 1.0;
        }
        $best = null;
        $bestR2 = -1.0;
        foreach (range(30, 150, 10) as $fPercent) {
            $f = $fPercent / 100.0;
            $result = $this->fitShiftedPowerWithExponent($pairs, $c, $e, $f);
            if ($result !== null && $result['r2'] > $bestR2) {
                $bestR2 = $result['r2'];
                $best = $result;
            }
        }
        return $best;
    }

    /**
     * Ajustement exponentiel k = a * exp(b * d). Régression sur (d, ln(k)) ; exige k > 0.
     *
     * @param list<array{d: float, k: float}> $pairs
     * @return array{formula: string, a: float, b: float, r2: float}|null
     */
    public function fitExponential(array $pairs): ?array
    {
        $pairs = array_filter($pairs, fn (array $p) => $p['k'] > 0);
        if (count($pairs) < 2) {
            return null;
        }
        $n = count($pairs);
        $sumD = 0.0;
        $sumLogK = 0.0;
        $sumD2 = 0.0;
        $sumDLogK = 0.0;
        foreach ($pairs as $p) {
            $logK = log($p['k']);
            $sumD += $p['d'];
            $sumLogK += $logK;
            $sumD2 += $p['d'] * $p['d'];
            $sumDLogK += $p['d'] * $logK;
        }
        $denom = $n * $sumD2 - $sumD * $sumD;
        if (abs($denom) < 1e-15) {
            return null;
        }
        $b = ($n * $sumDLogK - $sumD * $sumLogK) / $denom;
        $a = exp(($sumLogK - $b * $sumD) / $n);
        $formula = $this->formatExponentialFormula($a, $b);
        $r2 = $this->r2Exponential($pairs, $a, $b);
        return ['formula' => $formula, 'a' => $a, 'b' => $b, 'r2' => $r2];
    }

    /**
     * Ajustement logarithmique k = a * log(d) + b. Régression sur (ln(d), k) ; exige d > 0.
     *
     * @param list<array{d: float, k: float}> $pairs
     * @return array{formula: string, a: float, b: float, r2: float}|null
     */
    public function fitLogarithmic(array $pairs): ?array
    {
        $pairs = array_filter($pairs, fn (array $p) => $p['d'] > 0);
        if (count($pairs) < 2) {
            return null;
        }
        $n = count($pairs);
        $sumLogD = 0.0;
        $sumK = 0.0;
        $sumLogD2 = 0.0;
        $sumLogDK = 0.0;
        foreach ($pairs as $p) {
            $logD = log($p['d']);
            $sumLogD += $logD;
            $sumK += $p['k'];
            $sumLogD2 += $logD * $logD;
            $sumLogDK += $logD * $p['k'];
        }
        $denom = $n * $sumLogD2 - $sumLogD * $sumLogD;
        if (abs($denom) < 1e-15) {
            return null;
        }
        $a = ($n * $sumLogDK - $sumLogD * $sumK) / $denom;
        $b = ($sumK - $a * $sumLogD) / $n;
        $formula = $this->formatLogarithmicFormula($a, $b);
        $r2 = $this->r2Logarithmic($pairs, $a, $b);
        return ['formula' => $formula, 'a' => $a, 'b' => $b, 'r2' => $r2];
    }

    /**
     * Ajustement polynôme degré 2 : k = a * d² + b * d + c.
     *
     * @param list<array{d: float, k: float}> $pairs
     * @return array{formula: string, a: float, b: float, c: float, r2: float}|null
     */
    public function fitPolynomial2(array $pairs): ?array
    {
        if (count($pairs) < 3) {
            return null;
        }
        $n = count($pairs);
        $sumD = 0.0;
        $sumD2 = 0.0;
        $sumD3 = 0.0;
        $sumD4 = 0.0;
        $sumK = 0.0;
        $sumDK = 0.0;
        $sumD2K = 0.0;
        foreach ($pairs as $p) {
            $d = $p['d'];
            $d2 = $d * $d;
            $sumD += $d;
            $sumD2 += $d2;
            $sumD3 += $d2 * $d;
            $sumD4 += $d2 * $d2;
            $sumK += $p['k'];
            $sumDK += $d * $p['k'];
            $sumD2K += $d2 * $p['k'];
        }
        $m = [
            [$n, $sumD, $sumD2],
            [$sumD, $sumD2, $sumD3],
            [$sumD2, $sumD3, $sumD4],
        ];
        $rhs = [$sumK, $sumDK, $sumD2K];
        $sol = $this->solve3x3($m, $rhs);
        if ($sol === null) {
            return null;
        }
        [$c, $b, $a] = $sol;
        $formula = $this->formatPolynomial2Formula($a, $b, $c);
        $r2 = $this->r2Polynomial2($pairs, $a, $b, $c);
        return ['formula' => $formula, 'a' => $a, 'b' => $b, 'c' => $c, 'r2' => $r2];
    }

    /**
     * Résout un système 3×3 (Cramer ou substitution). Retourne [x0, x1, x2] ou null.
     *
     * @param array<int, array<int, float>> $m
     * @param array<int, float> $rhs
     * @return array{0: float, 1: float, 2: float}|null
     */
    private function solve3x3(array $m, array $rhs): ?array
    {
        $det = $m[0][0] * ($m[1][1] * $m[2][2] - $m[1][2] * $m[2][1])
            - $m[0][1] * ($m[1][0] * $m[2][2] - $m[1][2] * $m[2][0])
            + $m[0][2] * ($m[1][0] * $m[2][1] - $m[1][1] * $m[2][0]);
        if (abs($det) < 1e-15) {
            return null;
        }
        $det0 = $rhs[0] * ($m[1][1] * $m[2][2] - $m[1][2] * $m[2][1])
            - $m[0][1] * ($rhs[1] * $m[2][2] - $m[1][2] * $rhs[2])
            + $m[0][2] * ($rhs[1] * $m[2][1] - $m[1][1] * $rhs[2]);
        $det1 = $m[0][0] * ($rhs[1] * $m[2][2] - $m[1][2] * $rhs[2])
            - $rhs[0] * ($m[1][0] * $m[2][2] - $m[1][2] * $m[2][0])
            + $m[0][2] * ($m[1][0] * $rhs[2] - $rhs[1] * $m[2][0]);
        $det2 = $m[0][0] * ($m[1][1] * $rhs[2] - $rhs[1] * $m[2][1])
            - $m[0][1] * ($m[1][0] * $rhs[2] - $rhs[1] * $m[2][0])
            + $rhs[0] * ($m[1][0] * $m[2][1] - $m[1][1] * $m[2][0]);
        return [$det0 / $det, $det1 / $det, $det2 / $det];
    }

    /**
     * Retourne la table générée + les formules suggérées (linéaire, puissance, puissance décalée, exponentielle, log, polynôme 2) avec leur R².
     *
     * @param array<int|string, int|float> $dofusSample
     * @param array<int|string, int|float> $krosmozSample
     * @return array{table: string, linear: array{formula: string, a: float, b: float, r2: float}|null, power: array{formula: string, a: float, b: float, r2: float}|null, shifted_power: array{formula: string, a: float, b: float, c: float, e: float, f: float, r2: float}|null}
     */
    public function suggestFormulas(array $dofusSample, array $krosmozSample): array
    {
        $pairs = $this->pairsFromSamples($dofusSample, $krosmozSample);
        return $this->suggestFormulasFromPairs($pairs);
    }

    /**
     * Même retour que suggestFormulas mais à partir de paires (d, k) explicites (une paire par ligne du tableau).
     *
     * @param list<array{d: float, k: float}> $pairs
     * @return array{table: string, linear: array{formula: string, a: float, b: float, r2: float}|null, power: array{formula: string, a: float, b: float, r2: float}|null, shifted_power: array{formula: string, a: float, b: float, c: float, e: float, f: float, r2: float}|null}
     */
    public function suggestFormulasFromPairs(array $pairs): array
    {
        $table = $this->generateTableFromPairs($pairs);
        return [
            'table' => $table,
            'linear' => $this->fitLinear($pairs),
            'power' => $this->fitPower($pairs),
            'shifted_power' => $this->fitShiftedPower($pairs),
            'exponential' => $this->fitExponential($pairs),
            'log' => $this->fitLogarithmic($pairs),
            'polynomial2' => $this->fitPolynomial2($pairs),
        ];
    }

    /**
     * Enveloppe l'expression dans floor() pour que le résultat soit un entier (manipulation JDR).
     */
    private function wrapInteger(string $expr): string
    {
        return 'floor(' . $expr . ')';
    }

    private function formatLinearFormula(float $a, float $b): string
    {
        $aStr = $this->formatCoeff($a);
        $bStr = $this->formatCoeff($b);
        if (abs($b) < 1e-9) {
            return $this->wrapInteger($aStr . ' * [d]');
        }
        if ($b >= 0) {
            return $this->wrapInteger($aStr . ' * [d] + ' . $bStr);
        }
        return $this->wrapInteger($aStr . ' * [d] - ' . $this->formatCoeff(-$b));
    }

    private function formatPowerFormula(float $a, float $b): string
    {
        $aStr = $this->formatCoeff($a);
        $bStr = $this->formatCoeff($b);
        return $this->wrapInteger($aStr . ' * pow([d], ' . $bStr . ')');
    }

    private function formatShiftedPowerFormula(float $a, float $b, float $c, float $e, float $f): string
    {
        $aStr = $this->formatCoeff($a);
        $bStr = $this->formatCoeff($b);
        $cStr = $this->formatCoeff($c);
        $eStr = $this->formatCoeff($e);
        $fStr = $this->formatCoeff($f);
        return $this->wrapInteger($aStr . ' + ' . $bStr . ' * pow(([d]-' . $cStr . ')/' . $eStr . ', ' . $fStr . ')');
    }

    private function formatExponentialFormula(float $a, float $b): string
    {
        $aStr = $this->formatCoeff($a);
        $bStr = $this->formatCoeff($b);
        return $this->wrapInteger($aStr . ' * exp(' . $bStr . ' * [d])');
    }

    private function formatLogarithmicFormula(float $a, float $b): string
    {
        $aStr = $this->formatCoeff($a);
        $bStr = $this->formatCoeff($b);
        return $this->wrapInteger($aStr . ' * log([d]) + ' . $bStr);
    }

    private function formatPolynomial2Formula(float $a, float $b, float $c): string
    {
        $aStr = $this->formatCoeff($a);
        $bStr = $this->formatCoeff($b);
        $cStr = $this->formatCoeff($c);
        return $this->wrapInteger($aStr . ' * pow([d], 2) + ' . $bStr . ' * [d] + ' . $cStr);
    }

    private function formatCoeff(float $x): string
    {
        if (round($x, 4) == (int) $x) {
            return (string) (int) $x;
        }
        return (string) round($x, 4);
    }

    private function r2Linear(array $pairs, float $a, float $b): float
    {
        $meanK = array_sum(array_column($pairs, 'k')) / count($pairs);
        $ssTot = 0.0;
        $ssRes = 0.0;
        foreach ($pairs as $p) {
            $kPred = $a * $p['d'] + $b;
            $ssTot += ($p['k'] - $meanK) ** 2;
            $ssRes += ($p['k'] - $kPred) ** 2;
        }
        if ($ssTot < 1e-15) {
            return 1.0;
        }
        return (float) max(0, 1 - $ssRes / $ssTot);
    }

    private function r2Power(array $pairs, float $a, float $b): float
    {
        $meanK = array_sum(array_column($pairs, 'k')) / count($pairs);
        $ssTot = 0.0;
        $ssRes = 0.0;
        foreach ($pairs as $p) {
            $kPred = $a * ($p['d'] ** $b);
            $ssTot += ($p['k'] - $meanK) ** 2;
            $ssRes += ($p['k'] - $kPred) ** 2;
        }
        if ($ssTot < 1e-15) {
            return 1.0;
        }
        return (float) max(0, 1 - $ssRes / $ssTot);
    }

    private function fitShiftedPowerWithExponent(array $pairs, float $c, float $e, float $f): ?array
    {
        $n = count($pairs);
        $sumX = 0.0;
        $sumK = 0.0;
        $sumX2 = 0.0;
        $sumXK = 0.0;
        foreach ($pairs as $p) {
            $x = (($p['d'] - $c) / $e) ** $f;
            if ($x < 0) {
                continue;
            }
            $sumX += $x;
            $sumK += $p['k'];
            $sumX2 += $x * $x;
            $sumXK += $x * $p['k'];
        }
        $denom = $n * $sumX2 - $sumX * $sumX;
        if (abs($denom) < 1e-15) {
            return null;
        }
        $b = ($n * $sumXK - $sumX * $sumK) / $denom;
        $a = ($sumK - $b * $sumX) / $n;
        $formula = $this->formatShiftedPowerFormula($a, $b, $c, $e, $f);
        $r2 = $this->r2ShiftedPower($pairs, $a, $b, $c, $e, $f);
        return [
            'formula' => $formula,
            'a' => $a,
            'b' => $b,
            'c' => $c,
            'e' => $e,
            'f' => $f,
            'r2' => $r2,
        ];
    }

    private function r2ShiftedPower(array $pairs, float $a, float $b, float $c, float $e, float $f): float
    {
        $meanK = array_sum(array_column($pairs, 'k')) / count($pairs);
        $ssTot = 0.0;
        $ssRes = 0.0;
        foreach ($pairs as $p) {
            $x = (($p['d'] - $c) / $e) ** $f;
            $kPred = $x >= 0 ? $a + $b * $x : $a;
            $ssTot += ($p['k'] - $meanK) ** 2;
            $ssRes += ($p['k'] - $kPred) ** 2;
        }
        if ($ssTot < 1e-15) {
            return 1.0;
        }
        return (float) max(0, 1 - $ssRes / $ssTot);
    }

    private function r2Exponential(array $pairs, float $a, float $b): float
    {
        $meanK = array_sum(array_column($pairs, 'k')) / count($pairs);
        $ssTot = 0.0;
        $ssRes = 0.0;
        foreach ($pairs as $p) {
            $kPred = $a * exp($b * $p['d']);
            $ssTot += ($p['k'] - $meanK) ** 2;
            $ssRes += ($p['k'] - $kPred) ** 2;
        }
        if ($ssTot < 1e-15) {
            return 1.0;
        }
        return (float) max(0, 1 - $ssRes / $ssTot);
    }

    private function r2Logarithmic(array $pairs, float $a, float $b): float
    {
        $meanK = array_sum(array_column($pairs, 'k')) / count($pairs);
        $ssTot = 0.0;
        $ssRes = 0.0;
        foreach ($pairs as $p) {
            $kPred = $p['d'] > 0 ? $a * log($p['d']) + $b : $b;
            $ssTot += ($p['k'] - $meanK) ** 2;
            $ssRes += ($p['k'] - $kPred) ** 2;
        }
        if ($ssTot < 1e-15) {
            return 1.0;
        }
        return (float) max(0, 1 - $ssRes / $ssTot);
    }

    private function r2Polynomial2(array $pairs, float $a, float $b, float $c): float
    {
        $meanK = array_sum(array_column($pairs, 'k')) / count($pairs);
        $ssTot = 0.0;
        $ssRes = 0.0;
        foreach ($pairs as $p) {
            $d = $p['d'];
            $kPred = $a * $d * $d + $b * $d + $c;
            $ssTot += ($p['k'] - $meanK) ** 2;
            $ssRes += ($p['k'] - $kPred) ** 2;
        }
        if ($ssTot < 1e-15) {
            return 1.0;
        }
        return (float) max(0, 1 - $ssRes / $ssTot);
    }
}
