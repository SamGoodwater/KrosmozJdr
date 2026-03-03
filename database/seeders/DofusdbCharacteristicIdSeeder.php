<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Characteristic;
use App\Models\CharacteristicObject;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use Illuminate\Database\Seeder;

/**
 * Remplit dofusdb_characteristic_id sur characteristic_object à partir du mapping DofusDB → Krosmoz.
 *
 * Source : resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json
 * (mapping id GET /characteristics → characteristic_key groupe object).
 * Phase 1.1 — Permet la résolution id → caractéristique côté service (M2).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_SCRAPPING.md
 * @see docs/50-Fonctionnalités/Characteristics-DB/DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md
 */
class DofusdbCharacteristicIdSeeder extends Seeder
{
    private const JSON_PATH = 'resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json';

    public function run(): void
    {
        $path = base_path(self::JSON_PATH);
        if (! is_file($path)) {
            if ($this->command) {
                $this->command->warn('DofusdbCharacteristicIdSeeder : fichier ' . self::JSON_PATH . ' absent, skip.');
            }
            return;
        }

        $content = file_get_contents($path);
        $data = json_decode($content, true);
        if (! is_array($data) || ! isset($data['mapping']) || ! is_array($data['mapping'])) {
            if ($this->command) {
                $this->command->warn('DofusdbCharacteristicIdSeeder : clé "mapping" absente ou invalide, skip.');
            }
            return;
        }

        $updated = 0;
        foreach ($data['mapping'] as $dofusdbIdStr => $characteristicKey) {
            $dofusdbId = (int) $dofusdbIdStr;
            $characteristicKey = is_string($characteristicKey) ? trim($characteristicKey) : '';
            if ($characteristicKey === '') {
                continue;
            }

            $characteristic = Characteristic::where('key', $characteristicKey)->first();
            if ($characteristic === null) {
                continue;
            }

            $count = CharacteristicObject::where('characteristic_id', $characteristic->id)
                ->update(['dofusdb_characteristic_id' => $dofusdbId]);
            $updated += $count;
        }

        if ($this->command) {
            $this->command->info('DofusdbCharacteristicIdSeeder : ' . count($data['mapping']) . ' entrée(s) mapping, ' . $updated . ' ligne(s) characteristic_object mises à jour.');
        }

        app(CharacteristicGetterService::class)->clearCache();
    }
}
