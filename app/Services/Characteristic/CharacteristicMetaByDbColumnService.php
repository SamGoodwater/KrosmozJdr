<?php

declare(strict_types=1);

namespace App\Services\Characteristic;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Construit les métadonnées "byDbColumn" pour les caractéristiques (creature, object, spell).
 * Utilisé par les API Table (format=entities) pour exposer icônes, couleurs, libellés au frontend.
 *
 * @see docs/00-Project/PLAN-TABLEAUX-ET-DISPLAY-ENTITES.md Phase 3
 */
final class CharacteristicMetaByDbColumnService
{
    /** Clé de cache pour le share Inertia `characteristics` (invalidée à la sauvegarde des pivots / masters). */
    public const FRONTEND_CACHE_KEY = 'characteristics:frontend';

    /**
     * Mapping db_column → définition pour l'entité créature (monster, class, npc ou créature standalone).
     * Utilise entity '*' puis overlay monster (même champs que la créature d'un monstre).
     *
     * @return array<string, array{key: string, db_column: string, name: string, short_name: string|null, helper: string|null, descriptions: array|null, icon: string|null, color: string|null, unit: string|null, type: string|null}>
     */
    public function buildCreatureByDbColumn(): array
    {
        $out = [];
        try {
            $charRows = CharacteristicCreature::query()
                ->whereIn('entity', [
                    CharacteristicCreature::ENTITY_ALL,
                    CharacteristicCreature::ENTITY_MONSTER,
                    CharacteristicCreature::ENTITY_CLASS,
                ])
                ->whereNotNull('db_column')
                ->with(['characteristic.masterCharacteristic'])
                ->get();

            $sorted = $this->sortPivotRowsEntityOverlayLast($charRows);

            foreach ($sorted as $row) {
                $entry = $this->rowToDefinition($row->db_column, $row->characteristic);
                if ($entry !== null) {
                    $out[$entry['db_column']] = $entry;
                    // Alias : les krefs / docs utilisent la clé métier (`strength_creature`) alors que la colonne
                    // modèle est distincte (`strong`, `agi`, `intel`, …). Sans cette entrée, le frontend ne résout
                    // pas l’icône (fallback graphique).
                    $canonicalKey = (string) ($entry['key'] ?? '');
                    if ($canonicalKey !== '' && $canonicalKey !== $entry['db_column']) {
                        $out[$canonicalKey] = $entry;
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->reportMetaBuildFailure('buildCreatureByDbColumn', $e);
        }

        return $out;
    }

    /**
     * Mapping characteristic_key → définition pour les caractéristiques calculées (sans db_column).
     * Inclut les modificateurs (modifier_*_creature) et sauvegardes (save_*_creature).
     * Utilisé pour l'affichage des créatures (monstres, PNJ).
     *
     * @return array<string, array{key: string, db_column: string, name: string, short_name: string|null, helper: string|null, descriptions: array|null, icon: string|null, color: string|null, unit: string|null, type: string|null}>
     */
    public function buildCreatureComputedByKey(): array
    {
        $out = [];
        try {
            $charRows = CharacteristicCreature::query()
                ->whereIn('entity', [CharacteristicCreature::ENTITY_ALL, CharacteristicCreature::ENTITY_MONSTER])
                ->whereNull('db_column')
                ->whereHas('characteristic', fn ($q) => $q->whereIn('key', [
                    'modifier_vitality_creature',
                    'modifier_wisdom_creature',
                    'modifier_strength_creature',
                    'modifier_intelligence_creature',
                    'modifier_chance_creature',
                    'modifier_agility_creature',
                    'save_vitality_creature',
                    'save_wisdom_creature',
                    'save_strength_creature',
                    'save_intelligence_creature',
                    'save_chance_creature',
                    'save_agility_creature',
                    'athletics_creature',
                    'intimidation_creature',
                    'acrobatics_creature',
                    'stealth_creature',
                    'sleight_of_hand_creature',
                    'arcana_creature',
                    'history_creature',
                    'investigation_creature',
                    'nature_creature',
                    'religion_creature',
                    'animal_handling_creature',
                    'medicine_creature',
                    'perception_creature',
                    'insight_creature',
                    'survival_creature',
                    'persuasion_creature',
                    'performance_creature',
                    'deception_creature',
                    'athletics_passive_creature',
                    'intimidation_passive_creature',
                    'acrobatics_passive_creature',
                    'stealth_passive_creature',
                    'sleight_of_hand_passive_creature',
                    'arcana_passive_creature',
                    'history_passive_creature',
                    'investigation_passive_creature',
                    'nature_passive_creature',
                    'religion_passive_creature',
                    'animal_handling_passive_creature',
                    'medicine_passive_creature',
                    'perception_passive_creature',
                    'insight_passive_creature',
                    'survival_passive_creature',
                    'persuasion_passive_creature',
                    'performance_passive_creature',
                    'deception_passive_creature',
                    'hit_dice_creature',
                    'mastery_bonus_creature',
                ]))
                ->with(['characteristic.masterCharacteristic'])
                ->get();

            foreach ($charRows as $row) {
                if ($row->characteristic === null) {
                    continue;
                }
                $entry = $this->rowToDefinitionFromCharacteristic($row->characteristic);
                if ($entry !== null && $entry['key'] !== '') {
                    $out[$entry['key']] = $entry;
                }
            }
        } catch (\Throwable $e) {
            $this->reportMetaBuildFailure('buildCreatureComputedByKey', $e);
        }

        return $out;
    }

    /**
     * Mapping db_column → définition pour une entité objet (item, consumable, resource, panoply).
     *
     * @param  string  $entity  Une des constantes CharacteristicObject::ENTITY_*
     * @return array<string, array{key: string, db_column: string, name: string, short_name: string|null, helper: string|null, descriptions: array|null, icon: string|null, color: string|null, unit: string|null, type: string|null, value_available: array|null}>
     */
    public function buildObjectByDbColumn(string $entity): array
    {
        $out = [];
        try {
            $charRows = CharacteristicObject::query()
                ->whereIn('entity', [CharacteristicObject::ENTITY_ALL, $entity])
                ->whereNotNull('db_column')
                ->with(['characteristic.masterCharacteristic'])
                ->get();

            $sorted = $this->sortPivotRowsEntityOverlayLast($charRows);

            foreach ($sorted as $row) {
                $entry = $this->rowToDefinition($row->db_column, $row->characteristic, ['value_available' => $row->value_available]);
                if ($entry !== null) {
                    $out[$entry['db_column']] = $entry;
                }
            }
        } catch (\Throwable $e) {
            $this->reportMetaBuildFailure('buildObjectByDbColumn', $e);
        }

        return $out;
    }

    /**
     * Mapping characteristic_key (et forme courte) → définition pour une entité objet.
     * Permet de résoudre les effets items dont les clés sont "vitality", "agility", "critical_hit"
     * (sortie de mapDofusdbEffectsToKrosmozBonuses qui utilise les clés courtes = key sans suffixe _object).
     *
     * @param  string  $entity  Une des constantes CharacteristicObject::ENTITY_*
     * @return array<string, array{key: string, db_column: string, name: string, short_name: string|null, helper: string|null, descriptions: array|null, icon: string|null, color: string|null, unit: string|null, type: string|null}>
     */
    public function buildObjectByCharacteristicKey(string $entity): array
    {
        $out = [];
        try {
            $charRows = CharacteristicObject::query()
                ->whereIn('entity', [CharacteristicObject::ENTITY_ALL, $entity])
                ->with(['characteristic.masterCharacteristic'])
                ->get();

            $sorted = $this->sortPivotRowsEntityOverlayLast($charRows);

            foreach ($sorted as $row) {
                if ($row->characteristic === null) {
                    continue;
                }
                $entry = $this->rowToDefinitionFromCharacteristic($row->characteristic, ['value_available' => $row->value_available]);
                if ($entry !== null) {
                    $key = $entry['key'];
                    $out[$key] = $entry;
                    if (str_ends_with($key, '_object')) {
                        $shortKey = substr($key, 0, -7);
                        if ($shortKey !== '') {
                            $out[$shortKey] = $entry;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->reportMetaBuildFailure('buildObjectByCharacteristicKey', $e);
        }

        return $out;
    }

    /**
     * Mapping dofusdb_characteristic_id → définition pour une entité objet.
     * Permet de résoudre les effets items dont les clés sont des IDs DofusDB (ex. 11, 48, 85).
     *
     * @param  string  $entity  Une des constantes CharacteristicObject::ENTITY_*
     * @return array<string, array{key: string, db_column: string, name: string, short_name: string|null, helper: string|null, descriptions: array|null, icon: string|null, color: string|null, unit: string|null, type: string|null}>
     */
    public function buildObjectByDofusdbId(string $entity): array
    {
        $out = [];
        try {
            $charRows = CharacteristicObject::query()
                ->whereIn('entity', [CharacteristicObject::ENTITY_ALL, $entity])
                ->whereNotNull('dofusdb_characteristic_id')
                ->with(['characteristic.masterCharacteristic'])
                ->get();

            $sorted = $this->sortPivotRowsEntityOverlayLast($charRows);

            foreach ($sorted as $row) {
                $idKey = (string) $row->dofusdb_characteristic_id;
                $entry = $this->rowToDefinition($row->db_column ?? $idKey, $row->characteristic);
                if ($entry !== null && $idKey !== '') {
                    $out[$idKey] = $entry;
                }
            }
        } catch (\Throwable $e) {
            $this->reportMetaBuildFailure('buildObjectByDofusdbId', $e);
        }

        return $out;
    }

    /**
     * Mapping db_column → définition pour l'entité spell.
     *
     * @return array<string, array{key: string, db_column: string, name: string, short_name: string|null, helper: string|null, descriptions: array|null, icon: string|null, color: string|null, unit: string|null, type: string|null}>
     */
    public function buildSpellByDbColumn(): array
    {
        $out = [];
        try {
            $charRows = CharacteristicSpell::query()
                ->whereIn('entity', [CharacteristicSpell::ENTITY_ALL, CharacteristicSpell::ENTITY_SPELL])
                ->whereNotNull('db_column')
                ->with(['characteristic.masterCharacteristic'])
                ->get();

            $sorted = $this->sortPivotRowsEntityOverlayLast($charRows);

            foreach ($sorted as $row) {
                $entry = $this->rowToDefinition($row->db_column, $row->characteristic, ['value_available' => $row->value_available]);
                if ($entry !== null) {
                    $out[$entry['db_column']] = $entry;
                }
            }
        } catch (\Throwable $e) {
            $this->reportMetaBuildFailure('buildSpellByDbColumn', $e);
        }

        return $out;
    }

    /**
     * Métadonnées pour champs propres au modèle Monster (taille, race), indexés comme en UI (`size`, `monster_race`).
     *
     * @return array<string, array{key: string, db_column: string, name: string, short_name: string|null, helper: string|null, descriptions: array|null, icon: string|null, color: string|null, unit: string|null, type: string|null}>
     */
    public function buildMonsterFieldMeta(): array
    {
        $out = [];
        try {
            $map = [
                'monster_size' => 'size',
                'monster_race' => 'monster_race',
                'monster_is_boss' => 'is_boss',
            ];
            $chars = Characteristic::query()
                ->whereIn('key', array_keys($map))
                ->with('masterCharacteristic')
                ->get();
            foreach ($chars as $c) {
                $uiKey = $map[$c->key] ?? null;
                if ($uiKey === null) {
                    continue;
                }
                $entry = $this->rowToDefinitionFromCharacteristic($c);
                if ($entry !== null) {
                    $out[$uiKey] = $entry;
                }
            }
        } catch (\Throwable $e) {
            $this->reportMetaBuildFailure('buildMonsterFieldMeta', $e);
        }

        return $out;
    }

    /**
     * Invalide le cache des métadonnées caractéristiques (icônes, couleurs, libellés) exposé au frontend.
     */
    public function forgetFrontendCache(): void
    {
        Cache::forget(self::FRONTEND_CACHE_KEY);
    }

    /**
     * Agrège toutes les caractéristiques pour le frontend (chargement au démarrage).
     *
     * @return array<string, mixed>
     */
    public function buildAllForFrontend(): array
    {
        return Cache::remember(self::FRONTEND_CACHE_KEY, 300, function () {
            $spellByDbColumn = $this->buildSpellByDbColumn();

            return [
                'creature' => [
                    'byDbColumn' => $this->buildCreatureByDbColumn(),
                    'byComputedKey' => $this->buildCreatureComputedByKey(),
                    'byMonsterField' => $this->buildMonsterFieldMeta(),
                ],
                'spell' => [
                    'byDbColumn' => $spellByDbColumn,
                ],
                'capability' => [
                    'byDbColumn' => $spellByDbColumn,
                ],
                'item' => [
                    'byDbColumn' => $this->buildObjectByDbColumn(CharacteristicObject::ENTITY_ITEM),
                    'byCharacteristicKey' => $this->buildObjectByCharacteristicKey(CharacteristicObject::ENTITY_ITEM),
                    'byDofusdbId' => $this->buildObjectByDofusdbId(CharacteristicObject::ENTITY_ITEM),
                ],
                'consumable' => [
                    'byDbColumn' => $this->buildObjectByDbColumn(CharacteristicObject::ENTITY_CONSUMABLE),
                    'byCharacteristicKey' => $this->buildObjectByCharacteristicKey(CharacteristicObject::ENTITY_CONSUMABLE),
                    'byDofusdbId' => $this->buildObjectByDofusdbId(CharacteristicObject::ENTITY_CONSUMABLE),
                ],
                'resource' => [
                    'byDbColumn' => $this->buildObjectByDbColumn(CharacteristicObject::ENTITY_RESOURCE),
                    'byCharacteristicKey' => $this->buildObjectByCharacteristicKey(CharacteristicObject::ENTITY_RESOURCE),
                    'byDofusdbId' => $this->buildObjectByDofusdbId(CharacteristicObject::ENTITY_RESOURCE),
                ],
                'panoply' => [
                    'byDbColumn' => $this->buildObjectByDbColumn(CharacteristicObject::ENTITY_PANOPLY),
                    'byCharacteristicKey' => $this->buildObjectByCharacteristicKey(CharacteristicObject::ENTITY_PANOPLY),
                    'byDofusdbId' => $this->buildObjectByDofusdbId(CharacteristicObject::ENTITY_PANOPLY),
                ],
            ];
        });
    }

    /**
     * Construit une entrée à partir de la caractéristique seule (pour byCharacteristicKey).
     *
     * @param  array<string, mixed>  $extra  Champs additionnels (ex. value_available)
     * @return array{key: string, db_column: string, name: string, short_name: string|null, helper: string|null, descriptions: array|null, icon: string|null, color: string|null, unit: string|null, type: string|null, value_available?: array|null}|null
     */
    private function rowToDefinitionFromCharacteristic(Characteristic $characteristic, array $extra = []): ?array
    {
        $c = $characteristic->effectiveCharacteristic();
        $key = $c->key ?? '';
        if ($key === '') {
            return null;
        }

        $icons = $this->resolveCharacteristicIconPaths($c);

        return array_merge([
            'key' => $key,
            'db_column' => $key,
            'name' => $c->name,
            'short_name' => $c->short_name,
            'helper' => $c->helper,
            'descriptions' => $c->descriptions,
            'icon' => $icons['icon'],
            'icon_false' => $icons['icon_false'],
            'color' => $c->color,
            'value_overrides' => $this->normalizeValueOverridesIcons($c->value_overrides),
            'unit' => $c->unit,
            'type' => $c->type,
            'hide_when_empty' => (bool) ($c->hide_when_empty ?? false),
        ], array_filter($extra, fn ($v) => $v !== null));
    }

    /**
     * @param  array<string, mixed>  $extra  Champs additionnels (ex. value_available)
     * @return array{key: string, db_column: string, name: string, short_name: string|null, helper: string|null, descriptions: array|null, icon: string|null, color: string|null, unit: string|null, type: string|null, hide_when_empty?: bool, value_available?: array|null}|null
     */
    private function rowToDefinition(mixed $dbColumn, ?Characteristic $characteristic, array $extra = []): ?array
    {
        $dbColumn = is_string($dbColumn) ? trim($dbColumn) : '';
        if ($dbColumn === '') {
            return null;
        }
        if ($characteristic === null) {
            return null;
        }
        $c = $characteristic->effectiveCharacteristic();

        $icons = $this->resolveCharacteristicIconPaths($c);

        return array_merge([
            'key' => $c->key,
            'db_column' => $dbColumn,
            'name' => $c->name,
            'short_name' => $c->short_name,
            'helper' => $c->helper,
            'descriptions' => $c->descriptions,
            'icon' => $icons['icon'],
            'icon_false' => $icons['icon_false'],
            'color' => $c->color,
            'value_overrides' => $this->normalizeValueOverridesIcons($c->value_overrides),
            'unit' => $c->unit,
            'type' => $c->type,
            'hide_when_empty' => (bool) ($c->hide_when_empty ?? false),
        ], array_filter($extra, fn ($v) => $v !== null));
    }

    /**
     * Lignes pivot : `entity = *` d'abord, puis surcharge (valeurs finales prioritaires).
     *
     * @param  EloquentCollection<int, CharacteristicCreature|CharacteristicObject|CharacteristicSpell>  $rows
     * @return Collection<int, CharacteristicCreature|CharacteristicObject|CharacteristicSpell>
     */
    private function sortPivotRowsEntityOverlayLast(EloquentCollection $rows)
    {
        return $rows->sortBy(fn ($r) => $r->entity === CharacteristicCreature::ENTITY_ALL ? 0 : 1)->values();
    }

    /**
     * @return array{icon: string|null, icon_false: string|null}
     */
    private function resolveCharacteristicIconPaths(Characteristic $c): array
    {
        $icon = $c->icon;
        if (is_string($icon) && $icon !== '' && ! str_starts_with($icon, 'fa-') && ! str_contains($icon, '/')) {
            $icon = 'icons/caracteristics/'.$icon;
        }
        $iconFalse = $c->icon_false ?? null;
        if (is_string($iconFalse) && $iconFalse !== '' && ! str_starts_with($iconFalse, 'fa-') && ! str_contains($iconFalse, '/')) {
            $iconFalse = 'icons/caracteristics/'.$iconFalse;
        }

        return ['icon' => $icon, 'icon_false' => $iconFalse];
    }

    private function reportMetaBuildFailure(string $context, \Throwable $e): void
    {
        Log::warning('CharacteristicMetaByDbColumnService: échec construction métadonnées', [
            'context' => $context,
            'message' => $e->getMessage(),
        ]);
    }

    /**
     * Préfixe les noms d'icônes courts dans les entrées value_overrides.
     *
     * @param  array<int, array<string, mixed>>|null  $overrides
     * @return array<int, array<string, mixed>>|null
     */
    private function normalizeValueOverridesIcons(?array $overrides): ?array
    {
        if ($overrides === null || $overrides === []) {
            return null;
        }

        foreach ($overrides as &$entry) {
            if (! is_array($entry)) {
                continue;
            }
            $icon = $entry['icon'] ?? null;
            if (is_string($icon) && $icon !== '' && ! str_starts_with($icon, 'fa-') && ! str_contains($icon, '/')) {
                $entry['icon'] = 'icons/caracteristics/'.$icon;
            }
        }

        return $overrides;
    }
}
