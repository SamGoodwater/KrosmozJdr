<?php

declare(strict_types=1);

namespace App\Services\Seeder\Capability;

use App\Models\Entity\Breed;
use App\Models\Entity\Capability;
use App\Models\User;

/**
 * Importe les 19 passifs de classe et les capacités de classe (niveaux 3, 9, 14),
 * puis les lie via `breed_capability`.
 *
 * Idempotent. Passif : upsert sur `name` parmi les passifs. Capacité de classe :
 * upsert sur `name` (puis `previous_name`) parmi les capacités déjà liées à la
 * classe, ce qui permet de convertir un ancien passif en capacité. L’état n’est
 * posé qu’à la création. Détache les capacités actives liées à la classe qui ne
 * sont pas au catalogue.
 *
 * @example $result = app(ClassPassiveSeederImporter::class)->import();
 */
final class ClassPassiveSeederImporter
{
    /**
     * @param  bool  $overwrite  False = ignorer les fiches déjà présentes
     * @return array{
     *     created: list<string>,
     *     updated: list<string>,
     *     skipped: list<string>
     * }
     */
    public function import(?ClassPassiveCatalog $catalog = null, bool $overwrite = true): array
    {
        $catalog ??= ClassPassiveCatalog::load();
        $entries = $catalog->entries();

        $created = [];
        $updated = [];
        $skipped = [];
        $ownedIds = [];
        /** @var array<string, list<int>> $wantedByBreed */
        $wantedByBreed = [];

        foreach ($entries as $entry) {
            $breed = Breed::query()->where('name', $entry['breed'])->first();
            if ($breed === null) {
                $skipped[] = 'Classe introuvable pour le passif : '.$entry['breed'];

                continue;
            }

            $passive = $this->upsertPassive($entry, $overwrite);
            $label = $entry['breed'].' — '.$entry['name'];
            if ($passive === null) {
                $skipped[] = $label.' : déjà présent, conservation';
            } else {
                $ownedIds[] = $passive['model']->id;
                $wantedByBreed[$entry['breed']][] = $passive['model']->id;
                $passive['created'] ? $created[] = $label : $updated[] = $label;
            }

            foreach ($entry['capabilities'] as $capabilityEntry) {
                $capability = $this->upsertClassCapability($breed, $capabilityEntry, $overwrite);
                $capabilityLabel = $entry['breed'].' — '.$capabilityEntry['name'].' (niv. '.$capabilityEntry['level'].')';
                if ($capability === null) {
                    $skipped[] = $capabilityLabel.' : déjà présent, conservation';

                    continue;
                }
                $wantedByBreed[$entry['breed']][] = $capability['model']->id;
                $capability['created'] ? $created[] = $capabilityLabel : $updated[] = $capabilityLabel;
            }
        }

        foreach ($wantedByBreed as $breedName => $wantedIds) {
            $breed = Breed::query()->where('name', $breedName)->first();
            if ($breed === null) {
                continue;
            }

            if ($overwrite) {
                $stalePassives = $breed->capabilities()
                    ->whereIn('capabilities.id', $ownedIds)
                    ->pluck('capabilities.id')
                    ->all();
                $activeExtras = $breed->capabilities()
                    ->where('capabilities.is_passive', false)
                    ->pluck('capabilities.id')
                    ->all();
                $toDetach = array_values(array_diff(
                    array_unique([...$stalePassives, ...$activeExtras]),
                    $wantedIds,
                ));
                if ($toDetach !== []) {
                    $breed->capabilities()->detach($toDetach);
                }
            }
            $breed->capabilities()->syncWithoutDetaching($wantedIds);
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * @param  array{name: string, engine: string, is_magic: bool, description: string, effect: string, previous_name: string}  $entry
     * @return array{created: bool, model: Capability}|null Null = existant conservé (pas d’overwrite)
     */
    private function upsertPassive(array $entry, bool $overwrite): ?array
    {
        $existing = Capability::query()
            ->where('is_passive', true)
            ->where('name', $entry['name'])
            ->first();

        if ($existing === null && $entry['previous_name'] !== '') {
            $existing = Capability::query()
                ->where('is_passive', true)
                ->where('name', $entry['previous_name'])
                ->first();
        }

        return $this->save($existing, $overwrite, [
            'name' => $entry['name'],
            'description' => $entry['description'],
            'effect' => $entry['effect'],
            'level' => '1',
            'pa' => '0',
            'time_before_use_again' => '0',
            'duration' => 'permanent',
            'is_magic' => $entry['is_magic'],
            'is_passive' => true,
            'powerful' => $entry['engine'] !== '' ? $entry['engine'] : null,
        ]);
    }

    /**
     * @param  array{name: string, previous_name: string, level: int, pa: string, is_magic: bool, duration: string, time_before_use_again: string, description: string, effect: string}  $entry
     * @return array{created: bool, model: Capability}|null Null = existant conservé (pas d’overwrite)
     */
    private function upsertClassCapability(Breed $breed, array $entry, bool $overwrite): ?array
    {
        $names = array_values(array_filter([$entry['name'], $entry['previous_name']]));
        $existing = null;
        foreach ($names as $name) {
            $existing = $breed->capabilities()->where('capabilities.name', $name)->first();
            if ($existing !== null) {
                break;
            }
        }

        return $this->save($existing, $overwrite, [
            'name' => $entry['name'],
            'description' => $entry['description'],
            'effect' => $entry['effect'],
            'level' => (string) $entry['level'],
            'pa' => $entry['pa'],
            'time_before_use_again' => $entry['time_before_use_again'],
            'duration' => $entry['duration'],
            'is_magic' => $entry['is_magic'],
            'is_passive' => false,
            'powerful' => null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array{created: bool, model: Capability}|null
     */
    private function save(?Capability $existing, bool $overwrite, array $attributes): ?array
    {
        if ($existing !== null && ! $overwrite) {
            return null;
        }

        $wasNew = $existing === null;
        $capability = $existing ?? new Capability;

        $attributes += [
            'po' => '0',
            'po_editable' => false,
            'casting_time' => '0',
            'element' => 0,
            'ritual_available' => false,
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
