<?php

declare(strict_types=1);

namespace App\Services\Effect;

use App\Models\Entity\Spell;
use App\Support\DofusDbElementId;
use App\Support\ElementBitmask;

/**
 * Déduit le masque élémentaire d’un sort à partir des sous-effets (pas du champ Dofus global).
 *
 * Sources, dans l’ordre d’union : {@code params.dofus_element_id} (0–4 Dofus),
 * {@code params.element} (primaire 0–6 ou slug), {@code params.characteristic} (slug / dégâts fixes).
 *
 * @example
 * $mask = app(SpellElementInferenceService::class)->maskFromSpell($spell);
 * // Air → 8
 */
final class SpellElementInferenceService
{
    /**
     * @return array{scanned: int, updated: int, unchanged: int, cleared: int}
     */
    public function syncAll(bool $dryRun = false): array
    {
        $scanned = 0;
        $updated = 0;
        $unchanged = 0;
        $cleared = 0;

        Spell::query()
            ->with(['effects.degrees.effectSubEffects'])
            ->orderBy('id')
            ->each(function (Spell $spell) use ($dryRun, &$scanned, &$updated, &$unchanged, &$cleared): void {
                $scanned++;
                $mask = $this->maskFromSpell($spell);
                $current = $spell->element === null ? null : (int) $spell->element;
                if ($current === $mask) {
                    $unchanged++;

                    return;
                }
                if ($mask === null) {
                    $cleared++;
                } else {
                    $updated++;
                }
                if (! $dryRun) {
                    $spell->element = $mask;
                    $spell->save();
                }
            });

        return [
            'scanned' => $scanned,
            'updated' => $updated,
            'unchanged' => $unchanged,
            'cleared' => $cleared,
        ];
    }

    /**
     * @param  array{
     *   effects?: list<array{sub_effects?: list<array{params?: array<string, mixed>}>}>
     * }  $payload
     */
    public function maskFromConversionPayload(array $payload): ?int
    {
        $primaries = [];
        $effects = $payload['effects'] ?? [];
        if (! is_array($effects)) {
            return null;
        }
        foreach ($effects as $effect) {
            if (! is_array($effect)) {
                continue;
            }
            $subEffects = $effect['sub_effects'] ?? [];
            if (! is_array($subEffects)) {
                continue;
            }
            foreach ($subEffects as $subEffect) {
                if (! is_array($subEffect)) {
                    continue;
                }
                $params = is_array($subEffect['params'] ?? null) ? $subEffect['params'] : [];
                foreach ($this->primariesFromParams($params) as $p) {
                    $primaries[$p] = true;
                }
            }
        }

        return $this->maskFromPrimarySet($primaries);
    }

    public function maskFromSpell(Spell $spell): ?int
    {
        $primaries = [];
        foreach ($spell->effects as $effect) {
            foreach ($effect->degrees as $degree) {
                foreach ($degree->effectSubEffects as $row) {
                    $params = is_array($row->params ?? null) ? $row->params : [];
                    foreach ($this->primariesFromParams($params) as $p) {
                        $primaries[$p] = true;
                    }
                }
            }
        }

        return $this->maskFromPrimarySet($primaries);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<int>
     */
    public function primariesFromParams(array $params): array
    {
        $found = [];

        $dofusEl = $params['dofus_element_id'] ?? null;
        if (is_numeric($dofusEl)) {
            $el = (int) $dofusEl;
            if ($el >= 0 && $el <= 4) {
                $p = DofusDbElementId::toKrosmozElementPrimaryIndex($el);
                if ($p !== null) {
                    $found[$p] = true;
                }
            }
        }

        $rawElement = $params['element'] ?? null;
        if (is_numeric($rawElement)) {
            $p = (int) $rawElement;
            if ($p >= 0 && $p <= 6) {
                $found[$p] = true;
            }
        } elseif (is_string($rawElement)) {
            $p = ElementBitmask::primaryFromSlug($rawElement);
            if ($p !== null) {
                $found[$p] = true;
            }
        }

        $char = $params['characteristic'] ?? null;
        if (is_string($char) || is_numeric($char)) {
            $p = ElementBitmask::primaryFromSlug((string) $char);
            if ($p !== null) {
                $found[$p] = true;
            }
        }

        return array_map('intval', array_keys($found));
    }

    /**
     * @param  array<int, true>  $primaries
     */
    private function maskFromPrimarySet(array $primaries): ?int
    {
        if ($primaries === []) {
            return null;
        }

        return ElementBitmask::fromPrimaries(array_map('intval', array_keys($primaries)));
    }
}
