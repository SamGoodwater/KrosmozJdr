<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DofusdbConversionConfig;
use Illuminate\Database\Seeder;

/**
 * Importe database/seeders/data/dofusdb_conversion_config.php vers dofusdb_conversion_config.
 *
 * À exécuter avant ou après DofusdbConversionFormulaSeeder (ordre indifférent).
 */
class DofusdbConversionConfigSeeder extends Seeder
{
    private const DATA_FILE = 'database/seeders/data/dofusdb_conversion_config.php';

    public function run(): void
    {
        $path = base_path(self::DATA_FILE);
        if (! is_file($path)) {
            if ($this->command) {
                $this->command->warn('Fichier absent : ' . self::DATA_FILE);
            }

            return;
        }

        $data = require $path;
        if (! is_array($data)) {
            return;
        }

        $keys = [
            DofusdbConversionConfig::KEY_PASS_THROUGH,
            DofusdbConversionConfig::KEY_TRANSFORMATIONS,
            DofusdbConversionConfig::KEY_LIMITS_SOURCE,
            DofusdbConversionConfig::KEY_EFFECT_TO_CHAR,
            DofusdbConversionConfig::KEY_ELEMENT_TO_RES,
            DofusdbConversionConfig::KEY_LIMITS,
        ];

        foreach ($keys as $key) {
            if (! array_key_exists($key, $data)) {
                continue;
            }
            $value = $data[$key];
            if ($key === DofusdbConversionConfig::KEY_LIMITS_SOURCE && is_string($value)) {
                $value = [$value];
            }
            DofusdbConversionConfig::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        if ($this->command) {
            $this->command->info('DofusdbConversionConfigSeeder : ' . count($keys) . ' clés importées.');
        }
    }
}
