<?php

declare(strict_types=1);

namespace App\Services\Seeder\Item;

use App\Models\Entity\Item;
use Illuminate\Database\Eloquent\Builder;

/**
 * Écrit les équipements de la base vers les fichiers JSON versionnés (base → seeder).
 *
 * @example
 * $result = $exporter->export(states: ['auto'], prune: true);
 */
final class ItemSeederExporter
{
    public function __construct(private readonly ItemSeederFileRepository $files) {}

    /**
     * @param  list<string>  $states  États retenus (vide = tous)
     * @param  list<int>  $ids  Restriction à des identifiants d'items
     * @param  bool  $prune  Supprime les fichiers qui ne correspondent plus à la sélection
     * @param  bool  $versioned  Inclut aussi les items déjà présents dans les JSON versionnés
     * @return array{written: list<string>, removed: list<string>, skipped: list<string>}
     */
    public function export(array $states = [], array $ids = [], bool $prune = false, bool $versioned = false): array
    {
        $written = [];
        $skipped = [];
        $takenByIdentity = [];

        foreach ($this->query($states, $ids, $versioned)->cursor() as $item) {
            $payload = ItemSeederPayload::fromModel($item);
            if (ItemSeederPayload::identity($payload) === '') {
                $skipped[] = sprintf('#%d %s (ni dofusdb_id ni official_id)', $item->id, $item->name);

                continue;
            }
            if ($payload['item']['item_type_dofus_id'] === null) {
                $skipped[] = sprintf('#%d %s (type d’item sans dofusdb_type_id)', $item->id, $item->name);

                continue;
            }

            $fileName = $this->files->fileNameFor($payload, $takenByIdentity);
            $takenByIdentity[str_replace('-item.json', '', $fileName)] = ItemSeederPayload::identity($payload);
            $written[] = $this->files->write($payload, $fileName);
        }

        $removed = $prune ? $this->files->prune($written) : [];

        return ['written' => $written, 'removed' => $removed, 'skipped' => $skipped];
    }

    /**
     * @param  list<string>  $states
     * @param  list<int>  $ids
     * @return Builder<Item>
     */
    private function query(array $states, array $ids, bool $versioned = false): Builder
    {
        $query = Item::query()
            ->with(['itemType', 'panoplies', 'resources'])
            ->orderBy('item_type_id')
            ->orderBy('level')
            ->orderBy('id');

        if ($versioned) {
            $lookups = $this->versionedLookups();
            $query->where(function (Builder $outer) use ($states, $lookups): void {
                $stateFilter = $states !== [] ? $states : ['auto'];
                $outer->whereIn('state', $stateFilter);
                if ($lookups['dofusdb_ids'] !== []) {
                    $outer->orWhereIn('dofusdb_id', $lookups['dofusdb_ids']);
                }
                if ($lookups['official_ids'] !== []) {
                    $outer->orWhereIn('official_id', $lookups['official_ids']);
                }
            });
        } elseif ($states !== []) {
            $query->whereIn('state', $states);
        }

        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }

        return $query;
    }

    /**
     * Clés des items déjà versionnés dans les fichiers JSON.
     *
     * @return array{dofusdb_ids: list<string>, official_ids: list<string>}
     */
    private function versionedLookups(): array
    {
        $dofusdbIds = [];
        $officialIds = [];
        foreach ($this->files->all() as $file) {
            $lookup = ItemSeederPayload::lookupKey($file['payload']);
            if ($lookup === null) {
                continue;
            }
            if (isset($lookup['dofusdb_id'])) {
                $dofusdbIds[] = $lookup['dofusdb_id'];
            }
            if (isset($lookup['official_id'])) {
                $officialIds[] = $lookup['official_id'];
            }
        }

        return [
            'dofusdb_ids' => array_values(array_unique($dofusdbIds)),
            'official_ids' => array_values(array_unique($officialIds)),
        ];
    }
}
