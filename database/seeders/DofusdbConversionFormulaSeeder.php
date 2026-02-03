<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DofusdbConversionFormula;
use Illuminate\Database\Seeder;

/**
 * Importe database/seeders/data/dofusdb_conversion_formulas.php vers la table dofusdb_conversion_formulas.
 *
 * Prérequis : EntityCharacteristicSeeder (les characteristic_key utilisés peuvent exister en entity_characteristics).
 *
 * Pour régénérer le fichier depuis la BDD (après modification via l'interface) :
 * php artisan db:export-seeder-data --formulas
 */
class DofusdbConversionFormulaSeeder extends Seeder
{
    private const DATA_FILE = 'database/seeders/data/dofusdb_conversion_formulas.php';

    public function run(): void
    {
        $path = base_path(self::DATA_FILE);
        if (! is_file($path)) {
            if ($this->command) {
                $this->command->warn('Fichier absent : ' . self::DATA_FILE . '. Exécutez : php artisan db:export-seeder-data --formulas');
            }

            return;
        }

        $rows = require $path;
        if (! is_array($rows)) {
            $rows = [];
        }

        if (empty($rows)) {
            if ($this->command) {
                $this->command->warn('Aucune formule dans ' . self::DATA_FILE);
            }

            return;
        }

        foreach ($rows as $row) {
            $key = $row['characteristic_key'] ?? $row['characteristic_id'] ?? null;
            if ($key === null) {
                continue;
            }
            DofusdbConversionFormula::updateOrCreate(
                [
                    'characteristic_key' => $key,
                    'entity' => $row['entity'],
                ],
                [
                    'formula_type' => $row['formula_type'],
                    'parameters' => $row['parameters'] ?? null,
                    'formula_display' => $row['formula_display'] ?? null,
                    'conversion_formula' => $row['conversion_formula'] ?? null,
                    'handler_name' => $row['handler_name'] ?? null,
                ]
            );
        }

        if ($this->command) {
            $this->command->info('DofusdbConversionFormulaSeeder : ' . count($rows) . ' formule(s) importée(s).');
        }
    }
}
