<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Equipment;

use App\Models\EquipmentSlot;
use Illuminate\Support\Facades\Cache;

/**
 * Lecture des slots d’équipement et caractéristiques par slot (equipment_slots, equipment_slot_characteristics).
 */
final class EquipmentCharacteristicService
{
    private const CACHE_KEY = 'equipment_characteristics.slots';
    private const CACHE_TTL_SECONDS = 3600;

    public function getSlots(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            return $this->buildSlots();
        });
    }

    public function getSlot(string $slotId): ?array
    {
        $slots = $this->getSlots();
        return $slots[$slotId] ?? null;
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** @return array<string, array{name: string, characteristics: array<string, array<string, mixed>>}> */
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
