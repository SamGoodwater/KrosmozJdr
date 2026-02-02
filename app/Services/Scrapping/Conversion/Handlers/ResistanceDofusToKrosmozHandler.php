<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Conversion\Handlers;

/**
 * Handler de conversion des résistances Dofus → KrosmozJDR.
 *
 * KrosmozJDR : res_* = un des 4 tiers (50 = résistant, 100 = invulnérable, -50 = faiblesse, -100 = vulnérable) ou 0.
 *              res_fixe_* = valeur fixe.
 * Dofus : % variés. On mappe vers un tier et on applique un plafond par créature pour ne pas donner
 *         trop de résistances/faiblesses sur trop d'éléments.
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/CONVERSION_100_BDD_ET_HANDLERS.md
 */
final class ResistanceDofusToKrosmozHandler
{
    /** Champs résistance % et fixe par élément (ordre neutre, terre, feu, air, eau). */
    private const RES_FIELDS = [
        'res_neutre', 'res_terre', 'res_feu', 'res_air', 'res_eau',
    ];

    private const RES_FIXE_FIELDS = [
        'res_fixe_neutre', 'res_fixe_terre', 'res_fixe_feu', 'res_fixe_air', 'res_fixe_eau',
    ];

    /** Clés Dofus (grades.0.*) pour les résistances. */
    private const DOFUS_KEYS = [
        'neutralResistance',
        'earthResistance',
        'fireResistance',
        'airResistance',
        'waterResistance',
    ];

    /** Tiers Krosmoz autorisés. */
    private const TIERS = [50, 100, -50, -100];

    /**
     * Convertit les résistances Dofus (grades ou champs directs) en res_* et res_fixe_* Krosmoz.
     * Signature standard batch : (entity, dofusData, parameters) → array.
     *
     * @param string $entityType monster, class ou item
     * @param array<string, mixed> $dofusData Données brutes (ex. monster avec grades.0.neutralResistance, …)
     * @param array<string, mixed> $parameters Seuils % → tier, plafonds par créature (max_invulnerable, max_resistant, max_weak, max_vulnerable)
     * @return array<string, string> Map champ Krosmoz => valeur (res_* et res_fixe_*)
     */
    public static function convert(string $entityType, array $dofusData, array $parameters = []): array
    {
        $grade = self::extractGrade($dofusData);
        $thresholds = self::thresholds($parameters);
        $maxInvulnerable = (int) ($parameters['max_invulnerable'] ?? 1);
        $maxResistant = (int) ($parameters['max_resistant'] ?? 3);
        $maxWeak = (int) ($parameters['max_weak'] ?? 3);
        $maxVulnerable = (int) ($parameters['max_vulnerable'] ?? 2);

        $perElement = [];
        foreach (self::RES_FIELDS as $i => $field) {
            $dofusKey = self::DOFUS_KEYS[$i] ?? null;
            if ($dofusKey === null) {
                $perElement[$field] = ['tier' => 0, 'fixe' => 0];
                continue;
            }
            $dofusPercent = self::numeric($grade[$dofusKey] ?? 0);
            $tier = self::percentToTier($dofusPercent, $thresholds);
            $fixe = self::remainderAsFixed($dofusPercent, $tier);
            $perElement[$field] = ['tier' => $tier, 'fixe' => $fixe];
        }

        self::applyCaps($perElement, $maxInvulnerable, $maxResistant, $maxWeak, $maxVulnerable);

        $out = [];
        foreach (self::RES_FIELDS as $field) {
            $out[$field] = (string) ($perElement[$field]['tier'] ?? 0);
        }
        foreach (self::RES_FIXE_FIELDS as $i => $fixeField) {
            $resField = self::RES_FIELDS[$i] ?? '';
            $out[$fixeField] = (string) ($perElement[$resField]['fixe'] ?? 0);
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $dofusData
     * @return array<string, mixed>
     */
    private static function extractGrade(array $dofusData): array
    {
        $grades = $dofusData['grades'] ?? [];
        if (is_array($grades) && isset($grades[0])) {
            return is_array($grades[0]) ? $grades[0] : [];
        }

        return $dofusData;
    }

    /**
     * Seuils % Dofus → tier Krosmoz (par défaut).
     *
     * @param array<string, mixed> $parameters
     * @return array<int, array{min: float, max: float}>
     */
    private static function thresholds(array $parameters): array
    {
        $custom = $parameters['thresholds'] ?? null;
        if (is_array($custom)) {
            $out = [];
            foreach (self::TIERS as $tier) {
                $t = $custom[$tier] ?? null;
                if (is_array($t) && isset($t['min'], $t['max'])) {
                    $out[$tier] = ['min' => (float) $t['min'], 'max' => (float) $t['max']];
                }
            }
            if ($out !== []) {
                return $out;
            }
        }

        return [
            100 => ['min' => 90.0, 'max' => 101.0],
            50 => ['min' => 40.0, 'max' => 90.0],
            -50 => ['min' => -90.0, 'max' => -40.0],
            -100 => ['min' => -101.0, 'max' => -90.0],
        ];
    }

    private static function percentToTier(float $percent, array $thresholds): int
    {
        foreach ($thresholds as $tier => $range) {
            $min = $range['min'];
            $max = $range['max'];
            if ($percent >= $min && $percent <= $max) {
                return $tier;
            }
        }

        return 0;
    }

    /**
     * Partie "fixe" éventuelle (reste après attribution au tier). Ici on met 0 ; une logique plus fine peut répartir.
     */
    private static function remainderAsFixed(float $percent, int $tier): int
    {
        return 0;
    }

    /**
     * Limite le nombre d'éléments par tier (invulnérable, résistant, faiblesse, vulnérable).
     *
     * @param array<string, array{tier: int, fixe: int}> $perElement
     */
    private static function applyCaps(
        array &$perElement,
        int $maxInvulnerable,
        int $maxResistant,
        int $maxWeak,
        int $maxVulnerable
    ): void {
        $counts = [100 => 0, 50 => 0, -50 => 0, -100 => 0];
        $order = [];
        foreach (self::RES_FIELDS as $field) {
            $tier = $perElement[$field]['tier'] ?? 0;
            if ($tier !== 0) {
                $order[] = ['field' => $field, 'tier' => $tier, 'abs' => abs($tier)];
            }
        }
        usort($order, static fn ($a, $b) => $b['abs'] <=> $a['abs']);

        $limits = [100 => $maxInvulnerable, 50 => $maxResistant, -50 => $maxWeak, -100 => $maxVulnerable];
        foreach ($order as $item) {
            $field = $item['field'];
            $tier = $item['tier'];
            if (($counts[$tier] ?? 0) >= ($limits[$tier] ?? 0)) {
                $perElement[$field]['tier'] = 0;
            } else {
                $counts[$tier] = ($counts[$tier] ?? 0) + 1;
            }
        }
    }

    private static function numeric(mixed $v): float
    {
        return is_numeric($v) ? (float) $v : 0.0;
    }
}
