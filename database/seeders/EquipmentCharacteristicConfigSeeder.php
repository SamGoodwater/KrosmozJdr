<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EquipmentSlot;
use App\Models\EquipmentSlotCharacteristic;
use Illuminate\Database\Seeder;

/**
 * Importe config/equipment_characteristics.php (slots) vers equipment_slots et equipment_slot_characteristics.
 */
class EquipmentCharacteristicConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slots = config('equipment_characteristics.slots', []);

        if (empty($slots)) {
            if ($this->command) {
                $this->command->warn('Aucun slot dans config("equipment_characteristics.slots").');
            }

            return;
        }

        $sortOrder = 0;
        foreach ($slots as $slotId => $slotData) {
            if (! is_array($slotData)) {
                continue;
            }

            EquipmentSlot::updateOrCreate(
                ['id' => $slotId],
                [
                    'name' => $slotData['name'] ?? $slotId,
                    'sort_order' => $sortOrder++,
                ]
            );

            $characteristics = $slotData['characteristics'] ?? [];
            foreach ($characteristics as $charId => $charData) {
                if (! is_array($charData) || empty($charData['bracket_max'])) {
                    continue;
                }

                EquipmentSlotCharacteristic::updateOrCreate(
                    [
                        'equipment_slot_id' => $slotId,
                        'characteristic_id' => $charId,
                    ],
                    [
                        'bracket_max' => $charData['bracket_max'],
                        'forgemagie_max' => $charData['forgemagie_max'] ?? null,
                        'base_price_per_unit' => $charData['base_price_per_unit'] ?? null,
                        'rune_price_per_unit' => $charData['rune_price_per_unit'] ?? null,
                    ]
                );
            }
        }

        if ($this->command) {
            $this->command->info('EquipmentCharacteristicConfigSeeder : ' . count($slots) . ' slots importés.');
        }
    }
}
