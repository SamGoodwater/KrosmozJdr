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
     * @return array{written: list<string>, removed: list<string>, skipped: list<string>}
     */
    public function export(array $states = [], array $ids = [], bool $prune = false): array
    {
        $written = [];
        $skipped = [];
        $takenByIdentity = [];

        foreach ($this->query($states, $ids)->cursor() as $item) {
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
    private function query(array $states, array $ids): Builder
    {
        $query = Item::query()
            ->with(['itemType', 'panoplies', 'resources'])
            ->orderBy('item_type_id')
            ->orderBy('level')
            ->orderBy('id');

        if ($states !== []) {
            $query->whereIn('state', $states);
        }
        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }

        return $query;
    }
}
