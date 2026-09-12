<?php

declare(strict_types=1);

namespace App\Services\Seeder\Panoply;

use App\Models\Entity\Item;
use App\Models\Entity\Panoply;

/**
 * Rejoue les fichiers JSON de panoplies vers la base (upsert sur `dofusdb_id`).
 *
 * @example
 * $result = $importer->import();
 */
final class PanoplySeederImporter
{
    public function __construct(private readonly PanoplySeederFileRepository $files) {}

    /**
     * @return array{created: list<string>, updated: list<string>, skipped: list<string>}
     */
    public function import(): array
    {
        $created = [];
        $updated = [];
        $skipped = [];

        foreach ($this->files->all() as $file) {
            $relative = $this->files->relative($file['path']);
            $payload = $file['payload'];
            $key = is_array($payload['key'] ?? null) ? $payload['key'] : [];
            $dofusdbId = isset($key['dofusdb_id']) ? trim((string) $key['dofusdb_id']) : '';
            if ($dofusdbId === '') {
                $skipped[] = $relative.' : dofusdb_id manquant';

                continue;
            }

            $row = is_array($payload['panoply'] ?? null) ? $payload['panoply'] : [];
            $attributes = [
                'name' => (string) ($row['name'] ?? ''),
                'description' => $row['description'] ?? null,
                'bonus' => $this->encodeBonus($row['bonus'] ?? null),
                'state' => (string) ($row['state'] ?? Panoply::STATE_PLAYABLE),
                'read_level' => (int) ($row['read_level'] ?? 0),
                'write_level' => (int) ($row['write_level'] ?? 3),
            ];

            $existing = Panoply::query()->where('dofusdb_id', $dofusdbId)->first();
            $model = $existing ?? new Panoply;
            $model->fill(array_merge(['dofusdb_id' => $dofusdbId], $attributes));
            $wasNew = ! $model->exists;
            $model->save();

            $itemIds = $this->resolveItemIds($payload['relations']['item_dofusdb_ids'] ?? []);
            if ($itemIds !== [] || $model->items()->exists()) {
                $model->items()->sync($itemIds);
            }

            $wasNew ? $created[] = $relative : $updated[] = $relative;
        }

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped];
    }

    private function encodeBonus(mixed $bonus): ?string
    {
        if ($bonus === null || $bonus === [] || $bonus === '') {
            return null;
        }
        if (is_string($bonus)) {
            return $bonus;
        }

        return json_encode($bonus, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    /**
     * @return list<int>
     */
    private function resolveItemIds(mixed $values): array
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

        return Item::query()->whereIn('dofusdb_id', $normalized)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }
}
