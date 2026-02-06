<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Characteristic;
use App\Models\CharacteristicObject;

/**
 * Seed characteristic_object (groupe object : item, consumable, resource, panoply).
 * Enrichit les lignes avec les samples depuis storage/app/characteristics_object_samples.json si présent.
 */
class ObjectCharacteristicSeeder extends CharacteristicGroupSeeder
{
    /** Niveaux Dofus de référence (alignés sur l'admin). */
    private const DOFUS_REFERENCE_LEVELS = [1, 40, 80, 120, 160, 200];

    /** Niveaux Krosmoz de référence (alignés sur l'admin). */
    private const KROSMOZ_REFERENCE_LEVELS = [1, 4, 8, 12, 16, 20];

    protected function dataPath(): string
    {
        return 'database/seeders/data/characteristic_object.php';
    }

    /**
     * @return class-string<\App\Models\CharacteristicObject>
     */
    protected function modelClass(): string
    {
        return CharacteristicObject::class;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    protected function mapRowToAttributes(array $row): array
    {
        return array_merge($this->commonAttributes($row), [
            'forgemagie_allowed' => (bool) ($row['forgemagie_allowed'] ?? false),
            'forgemagie_max' => (int) ($row['forgemagie_max'] ?? 0),
            'base_price_per_unit' => isset($row['base_price_per_unit']) ? (float) $row['base_price_per_unit'] : null,
            'rune_price_per_unit' => isset($row['rune_price_per_unit']) ? (float) $row['rune_price_per_unit'] : null,
            'value_available' => isset($row['value_available']) ? $row['value_available'] : null,
        ]);
    }

    /**
     * Charge les samples depuis le JSON d'extraction (optionnel).
     *
     * @return array<string, array<string, mixed>>
     */
    protected function loadObjectSamples(): array
    {
        $path = storage_path('app/characteristics_object_samples.json');
        if (! is_file($path)) {
            return [];
        }
        $json = file_get_contents($path);
        $data = json_decode($json, true);
        if (! is_array($data) || ! isset($data['by_characteristic_key'])) {
            return [];
        }

        return $data['by_characteristic_key'];
    }

    /**
     * Construit conversion_sample_rows à partir des deux échantillons (paires dofus_level / krosmoz_level).
     *
     * @param array<string, int|float> $dofusSample
     * @param array<string, int|float> $krosmozSample
     * @return list<array{dofus_level: int, dofus_value: int|float|null, krosmoz_level: int, krosmoz_value: int|float|null}>
     */
    protected function buildConversionSampleRows(array $dofusSample, array $krosmozSample): array
    {
        $rows = [];
        foreach (self::DOFUS_REFERENCE_LEVELS as $i => $dofusLevel) {
            $krosmozLevel = self::KROSMOZ_REFERENCE_LEVELS[$i] ?? $dofusLevel;
            $dofusKey = (string) $dofusLevel;
            $krosmozKey = (string) $krosmozLevel;
            $rows[] = [
                'dofus_level' => $dofusLevel,
                'dofus_value' => array_key_exists($dofusKey, $dofusSample) ? $dofusSample[$dofusKey] : null,
                'krosmoz_level' => $krosmozLevel,
                'krosmoz_value' => array_key_exists($krosmozKey, $krosmozSample) ? $krosmozSample[$krosmozKey] : null,
            ];
        }

        return $rows;
    }

    public function run(): void
    {
        $rows = $this->loadDataFile($this->dataPath());
        $samplesByKey = $this->loadObjectSamples();
        $modelClass = $this->modelClass();
        $enrichedCount = 0;

        foreach ($rows as $row) {
            $key = $row['characteristic_key'] ?? '';
            if ($key !== '' && isset($samplesByKey[$key])) {
                $samples = $samplesByKey[$key];
                $dofusRef = $samples['conversion_dofus_sample_reference'] ?? $samples['conversion_dofus_sample'] ?? [];
                $krosmozRef = $samples['conversion_krosmoz_sample_reference'] ?? $samples['conversion_krosmoz_sample'] ?? [];
                $dofusRef = is_array($dofusRef) ? $dofusRef : [];
                $krosmozRef = is_array($krosmozRef) ? $krosmozRef : [];
                if ($dofusRef !== [] || $krosmozRef !== []) {
                    $row['conversion_dofus_sample'] = $dofusRef !== [] ? $dofusRef : null;
                    $row['conversion_krosmoz_sample'] = $krosmozRef !== [] ? $krosmozRef : null;
                    $row['conversion_sample_rows'] = $this->buildConversionSampleRows($dofusRef, $krosmozRef);
                    $enrichedCount++;
                }
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
            $this->command->info(class_basename(static::class) . ' : ' . count($rows) . ' ligne(s)' . ($enrichedCount > 0 ? ", {$enrichedCount} avec samples" : '') . '.');
        }
    }
}
