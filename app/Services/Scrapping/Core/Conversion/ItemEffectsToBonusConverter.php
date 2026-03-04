<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion;

use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Core\Conversion\UnknownCharacteristicRunTracker;
use Illuminate\Support\Facades\Log;

/**
 * Convertit item.effects[] / panoply.effects[] DofusDB en JSON bonus Krosmoz (clés courtes : intel, strong, …).
 *
 * Source de vérité : BDD (dofusdb_characteristic_id sur characteristic_object).
 * Pas de fallback JSON : les id non mappés en BDD sont ignorés.
 *
 * @see docs/50-Fonctionnalités/Scrapping/SIMPLIFICATIONS_SCRAPPING.md
 */
final class ItemEffectsToBonusConverter
{
    public function __construct(
        private readonly CharacteristicGetterService $getter,
        private readonly ?DofusConversionService $conversionService = null
    ) {
    }

    /**
     * @param mixed $value item.effects ou panoply.effects (liste d'objets avec characteristic, from, to, value/min/max)
     * @param array<string, mixed> $raw Données brutes de l'item/panoply (non utilisées ici, réservé au contexte)
     * @param array<string, mixed> $context entityType (item|panoply), lang, etc. pour DofusConversionService
     */
    public function convert(mixed $value, array $raw, array $context = []): ?string
    {
        if (!is_array($value) || $value === []) {
            return null;
        }

        $unknownCharacteristicCounts = [];

        if ($this->looksLikeTieredSetEffects($value)) {
            $tieredBonus = $this->convertTieredSetEffects($value, $context, $unknownCharacteristicCounts);
            $this->logUnknownCharacteristics($unknownCharacteristicCounts, $context, $raw);
            if ($tieredBonus === []) {
                return null;
            }
            try {
                $encoded = json_encode($tieredBonus, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                return $encoded !== false ? $encoded : null;
            } catch (\JsonException) {
                return null;
            }
        }

        $entityType = (string) ($context['entityType'] ?? 'item');
        $bonus = $this->convertEffectsListToBonus($value, $entityType, $context, $unknownCharacteristicCounts);
        $this->logUnknownCharacteristics($unknownCharacteristicCounts, $context, $raw);
        if ($bonus === []) {
            return null;
        }

        try {
            $encoded = json_encode($bonus, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            return $encoded !== false ? $encoded : null;
        } catch (\JsonException) {
            return null;
        }
    }

    /**
     * Détecte la structure DofusDB de panoplie: effects = [ [], [effect...], [effect...] ... ].
     *
     * @param array<int, mixed> $value
     */
    private function looksLikeTieredSetEffects(array $value): bool
    {
        foreach ($value as $row) {
            if (!is_array($row)) {
                return false;
            }
            if (array_key_exists('characteristic', $row)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Convertit les bonus de panoplie par palier (nombre d'objets équipés).
     *
     * @param array<int, mixed> $tiers
     * @param array<string, mixed> $context
     * @return array<string, array<string, int>>
     */
    private function convertTieredSetEffects(array $tiers, array $context, array &$unknownCharacteristicCounts): array
    {
        $entityType = (string) ($context['entityType'] ?? 'panoply');
        $out = [];

        foreach (array_values($tiers) as $index => $tierEffects) {
            if (!is_array($tierEffects) || $tierEffects === []) {
                continue;
            }
            $tierBonus = $this->convertEffectsListToBonus($tierEffects, $entityType, $context, $unknownCharacteristicCounts);
            if ($tierBonus !== []) {
                // L'index 0 correspond à 1 pièce équipée, index 1 à 2 pièces, etc.
                $out[(string) ($index + 1)] = $tierBonus;
            }
        }

        return $out;
    }

    /**
     * Convertit une liste d'effets DofusDB en map bonus Krosmoz (clé courte => valeur).
     *
     * @param array<int, mixed> $effects
     * @return array<string, int>
     */
    private function convertEffectsListToBonus(
        array $effects,
        string $entityType,
        array $context,
        array &$unknownCharacteristicCounts
    ): array {
        $objectMap = $this->getter->getDofusdbToCharacteristicKeyMap('object');
        $bonus = [];

        foreach ($effects as $effect) {
            if (!is_array($effect)) {
                continue;
            }
            $charId = isset($effect['characteristic']) ? (int) $effect['characteristic'] : null;
            if ($charId === null) {
                continue;
            }
            $charKey = $objectMap[$charId] ?? null;
            if (!is_string($charKey) || $charKey === '') {
                $unknownCharacteristicCounts[$charId] = ($unknownCharacteristicCounts[$charId] ?? 0) + 1;
                continue;
            }
            $from = isset($effect['from']) && is_numeric($effect['from']) ? (int) $effect['from'] : null;
            $to = isset($effect['to']) && is_numeric($effect['to']) ? (int) $effect['to'] : null;
            $val = $effect['value'] ?? $effect['min'] ?? $effect['max'] ?? null;
            if ($val === null && $from !== null && $to !== null) {
                // DofusDB encode fréquemment les valeurs fixes en from=X, to=0.
                if ($to === 0) {
                    $val = $from;
                } else {
                    $val = (int) round(($from + $to) / 2);
                }
            } elseif ($val === null && $to !== null) {
                $val = $to;
            } elseif ($val === null && $from !== null) {
                $val = $from;
            }
            $val = is_numeric($val) ? (int) $val : 0;
            if ($this->conversionService !== null) {
                $val = $this->conversionService->convertObjectAttribute($charKey, $val, $entityType, $context);
            }
            $shortKey = str_ends_with($charKey, '_object') ? substr($charKey, 0, -7) : $charKey;
            $bonus[$shortKey] = ($bonus[$shortKey] ?? 0) + $val;
        }

        return $bonus;
    }

    /**
     * @param array<int, int> $unknownCharacteristicCounts
     * @param array<string, mixed> $context
     * @param array<string, mixed> $raw
     */
    private function logUnknownCharacteristics(array $unknownCharacteristicCounts, array $context, array $raw): void
    {
        if ($unknownCharacteristicCounts === []) {
            return;
        }

        ksort($unknownCharacteristicCounts);
        $entityType = (string) ($context['entityType'] ?? 'item');
        $sourceId = isset($raw['id']) && is_numeric($raw['id']) ? (int) $raw['id'] : null;
        $runId = isset($context['run_id']) && is_string($context['run_id']) ? $context['run_id'] : null;
        UnknownCharacteristicRunTracker::addCounts($runId, $unknownCharacteristicCounts);

        Log::warning('Scrapping bonus: IDs characteristic DofusDB non mappés ignorés.', [
            'run_id' => $runId,
            'entity_type' => $entityType,
            'source_id' => $sourceId,
            'unknown_characteristic_ids' => array_keys($unknownCharacteristicCounts),
            'unknown_counts' => $unknownCharacteristicCounts,
            'contains_id_38' => isset($unknownCharacteristicCounts[38]),
        ]);
    }
}
