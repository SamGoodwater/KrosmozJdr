<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\EquipmentGrid;

/**
 * Extrait un dictionnaire de bonus JDR (clés courtes) depuis `effect` / `bonus`.
 *
 * Ordre : JSON objet converti (`effect` puis `bonus`), sinon tableau d’effets DofusDB brut.
 *
 * @example
 * $decoder->decode('{"strength":2}', null); // ['strength' => 2.0]
 */
final class EquipmentBonusDecoder
{
    /**
     * @return array<string, float>
     */
    public function decode(mixed $effect, mixed $bonus = null): array
    {
        $fromEffect = $this->decodePayload($effect);
        if ($fromEffect !== []) {
            return $fromEffect;
        }

        return $this->decodePayload($bonus);
    }

    /**
     * @return array<string, float>
     */
    public function decodePayload(mixed $value): array
    {
        $decoded = $this->asArray($value);
        if ($decoded === null || $decoded === []) {
            return [];
        }

        if ($this->isListOfObjects($decoded)) {
            return $this->fromDofusEffects($decoded);
        }

        if ($this->isNumericKeyedMap($decoded)) {
            $merged = [];
            foreach ($decoded as $inner) {
                if (! is_array($inner)) {
                    continue;
                }
                foreach ($this->numericMap($inner) as $key => $amount) {
                    $merged[$key] = ($merged[$key] ?? 0.0) + $amount;
                }
            }

            return $merged;
        }

        return $this->numericMap($decoded);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function asArray(mixed $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_array($value)) {
            return $value;
        }
        if (! is_string($value)) {
            return null;
        }
        $trimmed = trim($value);
        if ($trimmed === '' || $trimmed[0] === '<') {
            return null;
        }
        try {
            $decoded = json_decode($trimmed, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @param  array<mixed>  $decoded
     */
    private function isListOfObjects(array $decoded): bool
    {
        if ($decoded === [] || ! array_is_list($decoded)) {
            return false;
        }
        $first = $decoded[0];

        return is_array($first) && (array_key_exists('characteristic', $first) || array_key_exists('effectId', $first));
    }

    /**
     * @param  array<mixed>  $decoded
     */
    private function isNumericKeyedMap(array $decoded): bool
    {
        if ($decoded === [] || array_is_list($decoded)) {
            return false;
        }
        foreach ($decoded as $key => $inner) {
            if (! is_numeric((string) $key) || ! is_array($inner)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<mixed>  $map
     * @return array<string, float>
     */
    private function numericMap(array $map): array
    {
        $out = [];
        foreach ($map as $key => $rawValue) {
            if (! is_string($key) || $key === '' || is_numeric($key)) {
                continue;
            }
            if (! is_numeric($rawValue)) {
                continue;
            }
            $short = str_ends_with($key, '_object') ? substr($key, 0, -7) : $key;
            $out[$short] = (float) $rawValue;
        }

        return $out;
    }

    /**
     * @param  list<array<string, mixed>>  $effects
     * @return array<string, float>
     */
    private function fromDofusEffects(array $effects): array
    {
        $out = [];
        foreach ($effects as $effect) {
            if (! is_array($effect)) {
                continue;
            }
            $charId = isset($effect['characteristic']) && is_numeric($effect['characteristic'])
                ? (int) $effect['characteristic']
                : null;
            $elementId = isset($effect['elementId']) && is_numeric($effect['elementId'])
                ? (int) $effect['elementId']
                : null;
            $shortKey = $this->shortKeyFromDofus($charId, $elementId);
            if ($shortKey === null) {
                continue;
            }
            $value = $this->dofusEffectValue($effect);
            if ($value === 0.0) {
                continue;
            }
            $out[$shortKey] = ($out[$shortKey] ?? 0.0) + $value;
        }

        return $out;
    }

    private function shortKeyFromDofus(?int $characteristicId, ?int $elementId): ?string
    {
        return match ($characteristicId) {
            10 => 'strength',
            11 => 'vitality',
            12 => 'wisdom',
            13 => 'chance',
            14 => 'agility',
            15 => 'intelligence',
            16 => 'fixed_damage_multiple',
            88 => 'fixed_damage_earth',
            89 => 'fixed_damage_fire',
            90 => 'fixed_damage_water',
            91 => 'fixed_damage_air',
            92 => 'fixed_damage_neutral',
            -1 => match ($elementId) {
                1 => 'fixed_damage_earth',
                2 => 'fixed_damage_fire',
                3 => 'fixed_damage_water',
                4 => 'fixed_damage_air',
                0, 5 => 'fixed_damage_neutral',
                default => null,
            },
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $effect
     */
    private function dofusEffectValue(array $effect): float
    {
        $from = isset($effect['from']) && is_numeric($effect['from']) ? (float) $effect['from'] : null;
        $to = isset($effect['to']) && is_numeric($effect['to']) ? (float) $effect['to'] : null;
        $val = $effect['value'] ?? $effect['min'] ?? $effect['max'] ?? null;
        if ($val === null && $from !== null && $to !== null) {
            if ($to === 0.0) {
                return $from;
            }

            return ($from + $to) / 2.0;
        }
        if ($val === null && $to !== null) {
            return $to;
        }
        if ($val === null && $from !== null) {
            return $from;
        }

        return is_numeric($val) ? (float) $val : 0.0;
    }
}
