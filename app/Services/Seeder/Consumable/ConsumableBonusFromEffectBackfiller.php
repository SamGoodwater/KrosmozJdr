<?php

declare(strict_types=1);

namespace App\Services\Seeder\Consumable;

use App\Models\Entity\Consumable;
use App\Support\Entity\JsonBonusValue;

/**
 * Remplit `consumables.bonus` depuis le texte `effect` quand le bonus est encore vide.
 *
 * Couvre les patterns JDR (soin hors combat, bouclier, PV temporaires) et un JSON déjà
 * présent dans `effect` (héritage scrap).
 *
 * @example $n = app(ConsumableBonusFromEffectBackfiller::class)->backfill();
 */
final class ConsumableBonusFromEffectBackfiller
{
    /**
     * @return array{updated: int, skipped: int}
     */
    public function backfill(bool $onlyEmptyBonus = true): array
    {
        $updated = 0;
        $skipped = 0;

        Consumable::query()
            ->whereNotNull('effect')
            ->where('effect', '!=', '')
            ->when($onlyEmptyBonus, function ($q): void {
                $q->where(function ($inner): void {
                    $inner->whereNull('bonus')->orWhere('bonus', '');
                });
            })
            ->orderBy('id')
            ->chunkById(200, function ($rows) use (&$updated, &$skipped): void {
                foreach ($rows as $consumable) {
                    /** @var Consumable $consumable */
                    $bonus = $this->inferBonus((string) $consumable->effect);
                    if ($bonus === null) {
                        $skipped++;

                        continue;
                    }
                    $consumable->bonus = $bonus;
                    $consumable->save();
                    $updated++;
                }
            });

        return ['updated' => $updated, 'skipped' => $skipped];
    }

    /**
     * Déduit un JSON bonus plat depuis un texte d’effet.
     */
    public function inferBonus(string $effect): ?string
    {
        $trimmed = trim($effect);
        if ($trimmed === '') {
            return null;
        }

        $decoded = JsonBonusValue::decode($trimmed);
        if (is_array($decoded) && $decoded !== [] && ! JsonBonusValue::isPieceBonusMap($decoded)) {
            $flat = [];
            foreach ($decoded as $key => $value) {
                if (! is_string($key) || ! is_numeric($value)) {
                    continue;
                }
                $safe = JsonBonusValue::sanitizeKey($key);
                if ($safe === null || JsonBonusValue::isMetaKey($safe)) {
                    continue;
                }
                $flat[$safe] = (int) $value;
            }
            if ($flat !== []) {
                return json_encode($flat, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            }
        }

        if (preg_match('/Restaure\s+(\d+)\s+PV/iu', $trimmed, $m) === 1) {
            return HealingConsumableCatalog::bonusJson((int) $m[1]);
        }

        if (preg_match('/\+(\d+)\s+points?\s+de\s+bouclier/iu', $trimmed, $m) === 1) {
            return json_encode(
                ['shield_points' => (int) $m[1]],
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
            );
        }

        if (preg_match('/\+(\d+)\s+PV\s+temporaires/iu', $trimmed, $m) === 1) {
            return json_encode(
                ['temporary_life_points' => (int) $m[1]],
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
            );
        }

        if (preg_match('/\+(\d+)\s+Supercherie/iu', $trimmed, $m) === 1) {
            return json_encode(
                ['deception' => (int) $m[1]],
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
            );
        }

        if (preg_match('/\+(\d+)\s+Investigation/iu', $trimmed, $m) === 1) {
            return json_encode(
                ['investigation' => (int) $m[1]],
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
            );
        }

        if (preg_match('/Restaure\s+(\d+)\s+points?\s+de\s+réserve\s+de\s+Wakfu/iu', $trimmed, $m) === 1) {
            return json_encode(
                ['wakfu_recharge' => (int) $m[1]],
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
            );
        }

        return null;
    }
}
