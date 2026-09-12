<?php

declare(strict_types=1);

namespace App\Services\Seeder\Spell;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\Entity\Breed;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Models\Type\SpellType;
use Illuminate\Support\Facades\DB;

/**
 * Importe les sorts de classe niveau 1 (kit 3×2) en `playable`.
 *
 * Idempotent. Upsert sur `dofusdb_id` ou `official_id`. Les autres sorts de la classe
 * passent hors grille (`character_level` 0).
 *
 * @example $result = app(ClassLevel1SpellSeederImporter::class)->import();
 */
final class ClassLevel1SpellSeederImporter
{
    /** Hors emplacement de progression (aligné `breedSpellExtra.js`). */
    public const EXTRA_CHARACTER_LEVEL = 0;

    public const EXTRA_SLOT_INDEX = 1;

    /**
     * Encodage 0–4 (Neutre, Terre, Feu, Air, Eau) lu correctement par l’UI actuelle.
     *
     * @var array<string, int>
     */
    private const ELEMENT_STORAGE = [
        'neutral' => 0,
        'earth' => 1,
        'fire' => 2,
        'air' => 3,
        'water' => 4,
    ];

    /**
     * @return array{
     *     created: list<string>,
     *     updated: list<string>,
     *     skipped: list<string>
     * }
     */
    public function import(?ClassLevel1SpellCatalog $catalog = null): array
    {
        $catalogs = $catalog instanceof ClassLevel1SpellCatalog
            ? [$catalog]
            : ClassLevel1SpellCatalog::loadAllInDirectory();

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
    private function importCatalog(ClassLevel1SpellCatalog $catalog): array
    {
        $created = [];
        $updated = [];
        $skipped = [];
        $breedName = $catalog->breedName();
        if ($breedName === '') {
            return [
                'created' => [],
                'updated' => [],
                'skipped' => ['Catalogue sans nom de classe.'],
            ];
        }

        $kitSpellIds = [];
        $slotMap = [];

        foreach ($catalog->entries() as $entry) {
            $spell = $this->findSpell($entry);
            $wasNew = $spell === null;
            $spell ??= new Spell;

            $spell->fill($this->spellAttributes($entry));
            $spell->save();

            $this->syncTypes($spell, $entry['types'], $skipped);
            $this->syncJdrEffect($spell, $entry, $skipped);

            $kitSpellIds[] = $spell->id;
            $slotMap[$spell->id] = [
                'character_level' => 1,
                'slot_index' => $entry['slot_index'],
                'choice_order' => $entry['choice_order'],
            ];

            $label = $breedName.' / '.$entry['name'];
            $wasNew ? $created[] = $label : $updated[] = $label;
        }

        $breed = Breed::query()->where('name', $breedName)->first();
        if ($breed === null) {
            $skipped[] = $breedName.' : classe introuvable, sorts créés sans emplacement';

            return compact('created', 'updated', 'skipped');
        }

        $this->syncBreedSlots($breed, $kitSpellIds, $slotMap);

        return compact('created', 'updated', 'skipped');
    }

    /**
     * @param  array{dofusdb_id: string|null, official_id: string|null}  $entry
     */
    private function findSpell(array $entry): ?Spell
    {
        if ($entry['dofusdb_id'] !== null) {
            $byDofus = Spell::query()->where('dofusdb_id', $entry['dofusdb_id'])->first();
            if ($byDofus !== null) {
                return $byDofus;
            }
        }
        if ($entry['official_id'] !== null) {
            return Spell::query()->where('official_id', $entry['official_id'])->first();
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $entry
     * @return array<string, mixed>
     */
    private function spellAttributes(array $entry): array
    {
        $elementSlug = $entry['element'];
        $element = is_string($elementSlug) && isset(self::ELEMENT_STORAGE[$elementSlug])
            ? self::ELEMENT_STORAGE[$elementSlug]
            : null;

        $attributes = [
            'name' => $entry['name'],
            'description' => $entry['description'],
            'effect' => $entry['effect'],
            'level' => '1',
            'pa' => $entry['pa'],
            'po_min' => $entry['po_min'],
            'po_max' => $entry['po_max'],
            'po_editable' => $entry['po_editable'],
            'sight_line' => $entry['sight_line'],
            'cast_per_turn' => $entry['cast_per_turn'],
            'cast_per_target' => '0',
            'number_between_two_cast' => '0',
            'cast_in_line' => false,
            'cast_in_diagonal' => false,
            'target_type' => 'direct',
            'max_stack' => $entry['max_stack'],
            'global_cooldown' => 0,
            'element' => $element,
            'category' => Spell::CATEGORY_CLASS,
            'is_magic' => $entry['is_magic'],
            'powerful' => $entry['powerful'],
            'resolution_mode' => $entry['resolution_mode'],
            'attack_characteristic_key' => $entry['attack_characteristic_key'],
            'save_characteristic_key' => $entry['save_characteristic_key'],
            'save_dc_formula' => $entry['save_dc_formula'],
            'save_success_note' => $entry['save_success_note'],
            'auto_success_if_willing_target' => $entry['auto_success_if_willing_target'],
            'allows_reaction' => false,
            'duration' => $entry['duration'],
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => 0,
            'write_level' => 3,
            'auto_update' => false,
        ];

        if ($entry['dofusdb_id'] !== null) {
            $attributes['dofusdb_id'] = $entry['dofusdb_id'];
        }
        if ($entry['official_id'] !== null) {
            $attributes['official_id'] = $entry['official_id'];
        }

        return $attributes;
    }

    /**
     * @param  list<string>  $typeNames
     * @param  list<string>  $skipped
     */
    private function syncTypes(Spell $spell, array $typeNames, array &$skipped): void
    {
        $ids = [];
        foreach ($typeNames as $name) {
            $type = SpellType::query()->where('name', $name)->first();
            if ($type === null) {
                $skipped[] = $spell->name.' : type « '.$name.' » introuvable';

                continue;
            }
            $ids[] = $type->id;
        }
        $spell->spellTypes()->sync($ids);
    }

    /**
     * @param  array<string, mixed>  $entry
     * @param  list<string>  $skipped
     */
    private function syncJdrEffect(Spell $spell, array $entry, array &$skipped): void
    {
        $subEffects = $entry['sub_effects'];
        if ($subEffects === []) {
            $spell->effects()->sync([]);

            return;
        }

        $slug = 'jdr-'.$entry['key'];
        if (strlen($slug) > 64) {
            $slug = substr($slug, 0, 64);
        }

        $effect = Effect::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $entry['name'],
                'description' => $entry['effect'],
                'target_type' => Effect::TARGET_DIRECT,
            ]
        );
        $effect->fill([
            'name' => $entry['name'],
            'description' => $entry['effect'],
            'target_type' => Effect::TARGET_DIRECT,
        ]);
        $effect->save();

        $area = (string) ($subEffects[0]['area'] ?? 'point');
        $degree = EffectDegree::query()->firstOrCreate(
            [
                'effect_id' => $effect->id,
                'degree' => 1,
            ],
            [
                'required_creature_level' => 1,
                'area' => $area,
                'slug' => $slug.'-d1',
            ]
        );
        $degree->update([
            'required_creature_level' => 1,
            'area' => $area,
        ]);
        $degree->effectSubEffects()->delete();

        foreach ($subEffects as $sub) {
            $subId = SubEffect::query()->where('slug', $sub['slug'])->value('id');
            if ($subId === null) {
                $skipped[] = $entry['name'].' : sous-effet « '.$sub['slug'].' » introuvable';

                continue;
            }
            $params = is_array($sub['params'] ?? null) ? $sub['params'] : [];
            $durationFormula = $sub['duration_formula']
                ?? (isset($params['duration_formula']) ? trim((string) $params['duration_formula']) : null);
            $durationFormula = is_string($durationFormula) && $durationFormula !== '' ? $durationFormula : null;

            $degree->effectSubEffects()->create([
                'sub_effect_id' => (int) $subId,
                'order' => (int) ($sub['order'] ?? 0),
                'scope' => Effect::SCOPE_GENERAL,
                'params' => $params,
                'crit_only' => false,
                'duration_formula' => $durationFormula,
            ]);
        }

        $spell->effects()->sync([$effect->id]);
    }

    /**
     * @param  list<int>  $kitSpellIds
     * @param  array<int, array{character_level: int, slot_index: int, choice_order: int}>  $slotMap
     */
    private function syncBreedSlots(Breed $breed, array $kitSpellIds, array $slotMap): void
    {
        $sync = $slotMap;
        $extraOrder = 0;
        $existingIds = $breed->spells()->pluck('spells.id');
        foreach ($existingIds as $spellId) {
            $id = (int) $spellId;
            if (in_array($id, $kitSpellIds, true)) {
                continue;
            }
            $sync[$id] = [
                'character_level' => self::EXTRA_CHARACTER_LEVEL,
                'slot_index' => self::EXTRA_SLOT_INDEX,
                'choice_order' => $extraOrder,
            ];
            $extraOrder++;
        }

        DB::transaction(function () use ($breed, $sync): void {
            $breed->spells()->sync($sync);
        });
    }
}
