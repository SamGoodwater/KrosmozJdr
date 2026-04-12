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
     * Fichier de normes séparé (optionnel). Retourne null si aucun fichier de normes.
     */
    protected function normsDataPath(): ?string
    {
        return null;
    }

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
     * Charge le fichier de normes et retourne un tableau characteristic_key → données.
     * N'utilise pas loadDataFile car les normes sont indexées par clé (string), pas par index.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function loadNormsData(): array
    {
        $path = $this->normsDataPath();
        if ($path === null) {
            return [];
        }

        $fullPath = base_path($path);
        if (! is_file($fullPath)) {
            return [];
        }

        $data = require $fullPath;

        return is_array($data) ? $data : [];
    }

    /**
     * Attributs communs à creature, object et spell (limites, formules, conversion).
     *
     * @param  array<string, mixed>  $row
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
            'conversion_function' => isset($row['conversion_function']) && $row['conversion_function'] !== '' ? $row['conversion_function'] : null,
            'conversion_dofus_sample' => $row['conversion_dofus_sample'] ?? null,
            'conversion_krosmoz_sample' => $row['conversion_krosmoz_sample'] ?? null,
            'conversion_sample_rows' => $row['conversion_sample_rows'] ?? null,
            'norms_grid' => $row['norms_grid'] ?? null,
            'norms_conditions' => $row['norms_conditions'] ?? null,
            'norms_description' => $row['norms_description'] ?? null,
        ];
    }

    /**
     * Mappe une ligne du fichier vers les attributs à passer à updateOrCreate.
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    abstract protected function mapRowToAttributes(array $row): array;

    public function run(): void
    {
        $rows = $this->loadDataFile($this->dataPath());
        $normsData = $this->loadNormsData();

        // Dédoublonnage par (characteristic_key, entity) pour éviter les violations de contrainte unique.
        $byKeyEntity = [];
        foreach ($rows as $row) {
            $key = $row['characteristic_key'] ?? '';
            $entity = $row['entity'] ?? $this->defaultEntity();
            if ($key !== '') {
                $byKeyEntity[$key."\0".$entity] = $row;
            }
        }
        $rows = array_values($byKeyEntity);

        $normsApplied = 0;
        $modelClass = $this->modelClass();
        foreach ($rows as $row) {
            $key = $row['characteristic_key'] ?? '';

            // Fusionner les normes depuis le fichier dédié si disponible.
            if (isset($normsData[$key])) {
                $row['norms_grid'] = $normsData[$key]['norms_grid'] ?? null;
                $row['norms_conditions'] = $normsData[$key]['norms_conditions'] ?? null;
                $row['norms_description'] = $normsData[$key]['norms_description'] ?? null;
                $normsApplied++;
            }

            $char = Characteristic::where('key', $key)->first();
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
            $info = class_basename(static::class).' : '.count($rows).' ligne(s)';
            if ($normsApplied > 0) {
                $info .= ", {$normsApplied} avec normes";
            }
            $this->command->info($info.'.');
        }
    }
}
