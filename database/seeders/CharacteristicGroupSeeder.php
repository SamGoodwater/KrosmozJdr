<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Characteristic;
use Database\Seeders\Concerns\LoadsSeederDataFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Base pour les seeders des tables characteristic_creature, characteristic_object, characteristic_spell.
 * Charge le fichier data, résout characteristic_id par characteristic_key, puis updateOrCreate avec les attributs mappés.
 */
abstract class CharacteristicGroupSeeder extends Seeder
{
    use LoadsSeederDataFile;

    abstract protected function dataPath(): string;

    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    /**
     * Clé entity par défaut si absente du row (ex. '*' ou 'spell').
     */
    protected function defaultEntity(): string
    {
        return '*';
    }

    /**
     * Attributs communs à creature, object et spell (limites, formules, conversion).
     *
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    protected function commonAttributes(array $row): array
    {
        return [
            'db_column' => $row['db_column'] ?? null,
            'min' => $row['min'] ?? null,
            'max' => $row['max'] ?? null,
            'formula' => $row['formula'] ?? null,
            'formula_display' => $row['formula_display'] ?? null,
            'default_value' => $row['default_value'] ?? null,
            'conversion_formula' => $row['conversion_formula'] ?? null,
            'conversion_dofus_sample' => $row['conversion_dofus_sample'] ?? null,
            'conversion_krosmoz_sample' => $row['conversion_krosmoz_sample'] ?? null,
            'conversion_sample_rows' => $row['conversion_sample_rows'] ?? null,
        ];
    }

    /**
     * Mappe une ligne du fichier vers les attributs à passer à updateOrCreate.
     *
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    abstract protected function mapRowToAttributes(array $row): array;

    public function run(): void
    {
        $rows = $this->loadDataFile($this->dataPath());
        $modelClass = $this->modelClass();
        foreach ($rows as $row) {
            $char = Characteristic::where('key', $row['characteristic_key'] ?? '')->first();
            if ($char === null) {
                continue;
            }
            $entity = $row['entity'] ?? $this->defaultEntity();
            $modelClass::updateOrCreate(
                [
                    'characteristic_id' => $char->id,
                    'entity' => $entity,
                ],
                $this->mapRowToAttributes($row)
            );
        }
        if ($this->command) {
            $this->command->info(class_basename(static::class) . ' : ' . count($rows) . ' ligne(s).');
        }
    }
}
