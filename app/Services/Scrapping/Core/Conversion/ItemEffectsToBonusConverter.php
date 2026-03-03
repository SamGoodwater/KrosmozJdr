<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion;

use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;

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

        $objectMap = $this->getter->getDofusdbToCharacteristicKeyMap('object');
        $bonus = [];

        foreach ($value as $effect) {
            if (!is_array($effect)) {
                continue;
            }
            $charId = isset($effect['characteristic']) ? (int) $effect['characteristic'] : null;
            if ($charId === null) {
                continue;
            }
            $charKey = $objectMap[$charId] ?? null;
            if (!is_string($charKey) || $charKey === '') {
                continue;
            }
            $from = isset($effect['from']) && is_numeric($effect['from']) ? (int) $effect['from'] : null;
            $to = isset($effect['to']) && is_numeric($effect['to']) ? (int) $effect['to'] : null;
            $val = $effect['value'] ?? $effect['min'] ?? $effect['max'] ?? null;
            if ($val === null && $from !== null && $to !== null) {
                $val = (int) round(($from + $to) / 2);
            } elseif ($val === null && $to !== null) {
                $val = $to;
            } elseif ($val === null && $from !== null) {
                $val = $from;
            }
            $val = is_numeric($val) ? (int) $val : 0;
            $entityType = (string) ($context['entityType'] ?? 'item');
            if ($this->conversionService !== null) {
                $val = $this->conversionService->convertObjectAttribute($charKey, $val, $entityType, $context);
            }
            $shortKey = str_ends_with($charKey, '_object') ? substr($charKey, 0, -7) : $charKey;
            $bonus[$shortKey] = ($bonus[$shortKey] ?? 0) + $val;
        }

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
}
