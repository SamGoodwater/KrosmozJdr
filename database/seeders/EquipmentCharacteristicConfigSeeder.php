<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EquipmentSlot;
use App\Models\EquipmentSlotCharacteristic;
use Illuminate\Database\Seeder;

/**
 * Importe database/seeders/data/equipment_slots.php vers equipment_slots et equipment_slot_characteristics.
 *
 * Prérequis : CharacteristicSeeder + ObjectCharacteristicSeeder (entity=item + characteristic_key dans characteristic_object pour les carac utilisées).
 *
 * Pour régénérer le fichier depuis la BDD (après modification via l'interface) :
 * php artisan db:export-seeder-data --equipment
 */
class EquipmentCharacteristicConfigSeeder extends Seeder
{
    private const DATA_FILE = 'database/seeders/data/equipment_slots.php';

    public function run(): void
    {
        $path = base_path(self::DATA_FILE);
        if (! is_file($path)) {
            if ($this->command) {
                $this->command->warn('Fichier absent : ' . self::DATA_FILE . '. Exécutez : php artisan db:export-seeder-data --equipment');
            }

            return;
        }

        $slots = require $path;
        if (! is_array($slots)) {
            $slots = [];
        }

        if (empty($slots)) {
            if ($this->command) {
                $this->command->warn('Aucun slot dans ' . self::DATA_FILE);
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
            foreach ($characteristics as $charKey => $charData) {
                if (! is_array($charData) || empty($charData['bracket_max'] ?? null)) {
                    continue;
                }

                EquipmentSlotCharacteristic::updateOrCreate(
                    [
                        'equipment_slot_id' => $slotId,
                        'entity' => $charData['entity'] ?? 'item',
                        'characteristic_key' => $charKey,
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
