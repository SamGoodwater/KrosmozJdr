<?php

namespace Tests;

use App\Models\Scrapping\ScrappingEntityMapping;
use App\Models\Scrapping\ScrappingEntityMappingTarget;

/**
 * Seeders minimaux pour exécuter le pipeline scrapping en tests.
 */
trait SeedsScrappingPipeline
{
    protected function seedScrappingPipeline(): void
    {
        $this->seed([
            \Database\Seeders\Type\TypeSeeder::class,
            \Database\Seeders\CharacteristicSeeder::class,
            \Database\Seeders\CreatureCharacteristicSeeder::class,
            \Database\Seeders\ObjectCharacteristicSeeder::class,
            \Database\Seeders\DofusdbCharacteristicIdSeeder::class,
            \Database\Seeders\SpellCharacteristicSeeder::class,
            \Database\Seeders\SpellEffectTypeSeeder::class,
            \Database\Seeders\DofusdbEffectMappingSeeder::class,
            \Database\Seeders\ScrappingEntityMappingSeeder::class,
            \Database\Seeders\ScrappingEntityMappingCharacteristicSeeder::class,
        ]);

        $this->ensurePanoplyMapping();
    }

    /**
     * Garantit la présence du mapping panoply en BDD pour les tests d'orchestrateur.
     * Le fichier data seeder peut ne pas contenir cette entité selon l'état du snapshot.
     */
    protected function ensurePanoplyMapping(): void
    {
        $exists = ScrappingEntityMapping::query()
            ->where('source', 'dofusdb')
            ->where('entity', 'panoply')
            ->exists();

        if ($exists) {
            return;
        }

        $path = resource_path('scrapping/config/sources/dofusdb/entities/panoply.json');
        if (! is_file($path)) {
            return;
        }

        $json = json_decode((string) file_get_contents($path), true);
        if (! is_array($json)) {
            return;
        }

        $rows = $json['mapping'] ?? [];
        if (! is_array($rows) || $rows === []) {
            return;
        }

        foreach (array_values($rows) as $index => $row) {
            if (! is_array($row)) {
                continue;
            }
            $key = (string) ($row['key'] ?? '');
            $fromPath = (string) (($row['from']['path'] ?? null) ?: '');
            $targets = $row['to'] ?? [];
            if ($key === '' || $fromPath === '' || ! is_array($targets) || $targets === []) {
                continue;
            }

            $mapping = ScrappingEntityMapping::query()->create([
                'source' => 'dofusdb',
                'entity' => 'panoply',
                'mapping_key' => $key,
                'from_path' => $fromPath,
                'from_lang_aware' => (bool) ($row['from']['langAware'] ?? false),
                'characteristic_id' => null,
                'formatters' => is_array($row['formatters'] ?? null) ? $row['formatters'] : null,
                'sort_order' => (int) (($index + 1) * 10),
            ]);

            foreach (array_values($targets) as $targetIndex => $target) {
                if (! is_array($target)) {
                    continue;
                }
                $model = (string) ($target['model'] ?? '');
                $field = (string) ($target['field'] ?? '');
                if ($model === '' || $field === '') {
                    continue;
                }
                ScrappingEntityMappingTarget::query()->create([
                    'scrapping_entity_mapping_id' => $mapping->id,
                    'target_model' => $model,
                    'target_field' => $field,
                    'sort_order' => (int) (($targetIndex + 1) * 10),
                ]);
            }
        }
    }
}

