<?php

declare(strict_types=1);

namespace App\Services\Seeder\Capability;

use App\Models\Entity\Breed;
use App\Models\Entity\Capability;
use App\Models\User;

/**
 * Importe les 19 passifs de classe et les lie via `breed_capability`.
 *
 * Idempotent. Upsert sur `name` parmi les passifs de ce catalogue. `playable`,
 * `is_passive = true`. L’état n’est posé qu’à la création.
 *
 * @example $result = app(ClassPassiveSeederImporter::class)->import();
 */
final class ClassPassiveSeederImporter
{
    /**
     * @return array{
     *     created: list<string>,
     *     updated: list<string>,
     *     skipped: list<string>
     * }
     */
    public function import(?ClassPassiveCatalog $catalog = null): array
    {
        $catalog ??= ClassPassiveCatalog::load();
        $entries = $catalog->entries();

        $created = [];
        $updated = [];
        $skipped = [];
        $ownedIds = [];
        $breedToCapability = [];

        foreach ($entries as $entry) {
            $capability = $this->upsertCapability($entry);
            $wasNew = $capability['created'];
            $model = $capability['model'];
            $ownedIds[] = $model->id;
            $breedToCapability[$entry['breed']] = $model->id;
            $label = $entry['breed'].' — '.$entry['name'];
            if ($wasNew) {
                $created[] = $label;
            } else {
                $updated[] = $label;
            }
        }

        foreach ($breedToCapability as $breedName => $capabilityId) {
            $breed = Breed::query()->where('name', $breedName)->first();
            if ($breed === null) {
                $skipped[] = 'Classe introuvable pour le passif : '.$breedName;

                continue;
            }

            $stale = $breed->capabilities()
                ->whereIn('capabilities.id', $ownedIds)
                ->where('capabilities.id', '!=', $capabilityId)
                ->pluck('capabilities.id')
                ->all();
            if ($stale !== []) {
                $breed->capabilities()->detach($stale);
            }
            $breed->capabilities()->syncWithoutDetaching([$capabilityId]);
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * @param  array{name: string, engine: string, is_magic: bool, description: string, effect: string}  $entry
     * @return array{created: bool, model: Capability}
     */
    private function upsertCapability(array $entry): array
    {
        $existing = Capability::query()
            ->where('is_passive', true)
            ->where('name', $entry['name'])
            ->first();

        $wasNew = $existing === null;
        $capability = $existing ?? new Capability;

        $attributes = [
            'name' => $entry['name'],
            'description' => $entry['description'],
            'effect' => $entry['effect'],
            'level' => '1',
            'pa' => '0',
            'po' => '0',
            'po_editable' => false,
            'time_before_use_again' => '0',
            'casting_time' => '0',
            'duration' => 'permanent',
            'element' => 0,
            'is_magic' => $entry['is_magic'],
            'ritual_available' => false,
            'is_passive' => true,
            'powerful' => $entry['engine'] !== '' ? $entry['engine'] : null,
        ];
        if ($wasNew) {
            $attributes['state'] = Capability::STATE_PLAYABLE;
            $attributes['read_level'] = User::ROLE_GUEST;
            $attributes['write_level'] = User::ROLE_GAME_MASTER;
        }

        $capability->fill($attributes);
        $capability->save();

        return [
            'created' => $wasNew,
            'model' => $capability,
        ];
    }
}
