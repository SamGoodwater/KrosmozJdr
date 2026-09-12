<?php

declare(strict_types=1);

namespace App\Services\Seeder\Breed;

use App\Models\Entity\Breed;
use App\Services\Entity\SyncBreedElementOrientations;

/**
 * Importe les fiches des 12 classes originales (voix §2.3.1) pour recoller les kits de sorts.
 *
 * Idempotent. Upsert sur `dofusdb_id`, `official_id` ou `name`. `auto_update = false`.
 *
 * @example $result = app(ClassBreedSeederImporter::class)->import();
 */
final class ClassBreedSeederImporter
{
    public function __construct(
        private readonly SyncBreedElementOrientations $syncOrientations,
    ) {}

    /**
     * @return array{
     *     created: list<string>,
     *     updated: list<string>,
     *     skipped: list<string>
     * }
     */
    public function import(?ClassBreedCatalog $catalog = null): array
    {
        $catalogs = $catalog instanceof ClassBreedCatalog
            ? [$catalog]
            : ClassBreedCatalog::loadAllInDirectory();

        $created = [];
        $updated = [];
        $skipped = [];

        foreach ($catalogs as $one) {
            $part = $this->importCatalog($one);
            $created = array_merge($created, $part['created']);
            $updated = array_merge($updated, $part['updated']);
            $skipped = array_merge($skipped, $part['skipped']);
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * @return array{created: list<string>, updated: list<string>, skipped: list<string>}
     */
    private function importCatalog(ClassBreedCatalog $catalog): array
    {
        $entry = $catalog->entry();
        if ($entry === null) {
            return [
                'created' => [],
                'updated' => [],
                'skipped' => ['Catalogue de classe sans nom.'],
            ];
        }

        $breed = $this->findBreed($entry);
        $wasNew = $breed === null;
        $breed ??= new Breed;

        $attributes = [
            'name' => $entry['name'],
            'description_fast' => $entry['description_fast'],
            'description' => $entry['description'],
            'specificity' => $entry['specificity'],
            'dofus_version' => $entry['dofus_version'],
            'auto_update' => false,
        ];
        if ($entry['dofusdb_id'] !== null) {
            $attributes['dofusdb_id'] = $entry['dofusdb_id'];
        }
        if ($entry['official_id'] !== null) {
            $attributes['official_id'] = $entry['official_id'];
        }
        if ($wasNew) {
            $attributes['state'] = $entry['state'];
            $attributes['read_level'] = $entry['read_level'];
            $attributes['write_level'] = $entry['write_level'];
        }

        $breed->fill($attributes);
        $breed->save();

        $this->syncOrientations->sync($breed, $entry['element_orientations']);

        $label = $entry['name'];

        return $wasNew
            ? ['created' => [$label], 'updated' => [], 'skipped' => []]
            : ['created' => [], 'updated' => [$label], 'skipped' => []];
    }

    /**
     * @param  array{dofusdb_id: string|null, official_id: string|null, name: string}  $entry
     */
    private function findBreed(array $entry): ?Breed
    {
        if ($entry['dofusdb_id'] !== null) {
            $byDofus = Breed::query()->where('dofusdb_id', $entry['dofusdb_id'])->first();
            if ($byDofus !== null) {
                return $byDofus;
            }
        }
        if ($entry['official_id'] !== null) {
            $byOfficial = Breed::query()->where('official_id', $entry['official_id'])->first();
            if ($byOfficial !== null) {
                return $byOfficial;
            }
        }

        return Breed::query()->where('name', $entry['name'])->first();
    }
}
