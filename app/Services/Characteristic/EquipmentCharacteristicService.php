<?php

declare(strict_types=1);

namespace App\Services\Characteristic;

use App\Models\EquipmentSlot;
use Illuminate\Support\Facades\Cache;

/**
 * Service de lecture des slots d'équipement et caractéristiques par slot depuis la base.
 *
 * Expose la même structure que config('equipment_characteristics.slots').
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/PLAN_MIGRATION_CHARACTERISTICS_DB.md
 */
final class EquipmentCharacteristicService
{
    private const CACHE_KEY = 'equipment_characteristics.slots';

    private const CACHE_TTL_SECONDS = 3600;

    /**
     * Retourne tous les slots avec leurs caractéristiques (bracket_max, forgemagie_max, prix).
     *
     * Équivalent de config('equipment_characteristics.slots').
     *
     * @return array<string, array{name: string, characteristics: array<string, array{bracket_max: array<int>, forgemagie_max: int|null, base_price_per_unit?: float, rune_price_per_unit?: float}>}>
     */
    public function getSlots(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            return $this->buildSlots();
        });
    }

    /**
     * Retourne un slot par id, ou null.
     *
     * @return array{name: string, characteristics: array<string, array<string, mixed>>}|null
     */
    public function getSlot(string $slotId): ?array
    {
        $slots = $this->getSlots();

        return $slots[$slotId] ?? null;
    }

    /**
     * Invalide le cache (à appeler après création/update/suppression en base).
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Construit la structure slots depuis la base (sans cache).
     *
     * @return array<string, array{name: string, characteristics: array<string, array<string, mixed>>}>
     */
    private function buildSlots(): array
    {
        $slots = EquipmentSlot::query()
            ->with('slotCharacteristics')
            ->orderBy('sort_order')
            ->get();

        $out = [];
        foreach ($slots as $slot) {
            $characteristics = [];
            foreach ($slot->slotCharacteristics as $sc) {
                $charDef = [
                    'bracket_max' => $sc->bracket_max,
                    'forgemagie_max' => $sc->forgemagie_max,
                ];
                if ($sc->base_price_per_unit !== null) {
                    $charDef['base_price_per_unit'] = (float) $sc->base_price_per_unit;
                }
                if ($sc->rune_price_per_unit !== null) {
                    $charDef['rune_price_per_unit'] = (float) $sc->rune_price_per_unit;
                }
                $characteristics[$sc->characteristic_key] = $charDef;
            }
            $out[$slot->id] = [
                'name' => $slot->name,
                'characteristics' => $characteristics,
            ];
        }

        return $out;
    }
}
