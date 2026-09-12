<?php

declare(strict_types=1);

namespace App\Services\Seeder\Item;

use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Services\Seeder\Resource\MarkPlayableItemRecipeResources;
use Illuminate\Database\Eloquent\Builder;

/**
 * Écrit les fichiers JSON versionnés vers la base (seeder → base). Idempotent : upsert sur
 * `dofusdb_id` (ou `official_id` à défaut), puis synchronisation des panoplies et de la recette.
 *
 * @example
 * $result = $importer->import();
 */
final class ItemSeederImporter
{
    public function __construct(private readonly ItemSeederFileRepository $files) {}

    /**
     * @param  bool  $dryRun  N'écrit rien, se contente de compter
     * @return array{created: list<string>, updated: list<string>, skipped: list<string>}
     */
    public function import(bool $dryRun = false): array
    {
        $created = [];
        $updated = [];
        $skipped = [];

        foreach ($this->files->all() as $file) {
            $relative = $this->files->relative($file['path']);
            $payload = $file['payload'];

            $lookup = ItemSeederPayload::lookupKey($payload);
            if ($lookup === null) {
                $skipped[] = $relative.' : ni dofusdb_id ni official_id';

                continue;
            }
            $attributes = ItemSeederPayload::toAttributes($payload);
            if ($attributes === null) {
                $skipped[] = $relative.' : item_type_dofus_id absent ou type inconnu en base';

                continue;
            }

            $existing = Item::query()->where($lookup)->first();
            if ($dryRun) {
                $existing === null ? $created[] = $relative : $updated[] = $relative;

                continue;
            }

            $item = $existing ?? new Item;
            $item->fill(array_merge($lookup, $attributes));
            $wasNew = ! $item->exists;
            $item->save();

            $this->syncRelations($item, $payload);

            $wasNew ? $created[] = $relative : $updated[] = $relative;
        }

        if (! $dryRun) {
            app(MarkPlayableItemRecipeResources::class)->mark();
        }

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped];
    }

    /**
     * Panoplies et recette. Les références introuvables sont ignorées : un environnement sans
     * scrapping n'a pas forcément les ressources Dofus.
     *
     * @param  array<string, mixed>  $payload
     */
    private function syncRelations(Item $item, array $payload): void
    {
        $relations = is_array($payload['relations'] ?? null) ? $payload['relations'] : [];

        $panoplyIds = $this->resolveIds(
            Panoply::query(),
            'dofusdb_id',
            $relations['panoply_dofusdb_ids'] ?? []
        );
        if ($panoplyIds !== [] || $item->panoplies()->exists()) {
            $item->panoplies()->sync($panoplyIds);
        }

        $recipe = [];
        foreach (is_array($relations['resources'] ?? null) ? $relations['resources'] : [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $resourceId = Resource::query()
                ->where('dofusdb_id', (string) ($row['dofusdb_id'] ?? ''))
                ->value('id');
            if (is_numeric($resourceId)) {
                $recipe[(int) $resourceId] = ['quantity' => max(1, (int) ($row['quantity'] ?? 1))];
            }
        }
        if ($recipe !== [] || $item->resources()->exists()) {
            $item->resources()->sync($recipe);
        }
    }

    /**
     * @param  Builder<covariant \Illuminate\Database\Eloquent\Model>  $query
     * @return list<int>
     */
    private function resolveIds($query, string $column, mixed $values): array
    {
        if (! is_array($values) || $values === []) {
            return [];
        }
        $normalized = array_values(array_filter(array_map(
            static fn ($value): string => is_scalar($value) ? trim((string) $value) : '',
            $values
        )));
        if ($normalized === []) {
            return [];
        }

        return $query->whereIn($column, $normalized)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }
}
