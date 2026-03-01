<?php

declare(strict_types=1);

namespace App\Services\Characteristic;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;

/**
 * Construit les métadonnées "byDbColumn" pour les caractéristiques (creature, object, spell).
 * Utilisé par les API Table (format=entities) pour exposer icônes, couleurs, libellés au frontend.
 *
 * @see docs/00-Project/PLAN-TABLEAUX-ET-DISPLAY-ENTITES.md Phase 3
 */
final class CharacteristicMetaByDbColumnService
{
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
                ->whereIn('entity', [CharacteristicCreature::ENTITY_ALL, CharacteristicCreature::ENTITY_MONSTER])
                ->whereNotNull('db_column')
                ->with(['characteristic.masterCharacteristic'])
                ->get();

            $sorted = $charRows->sortBy(fn (CharacteristicCreature $r) => $r->entity === CharacteristicCreature::ENTITY_ALL ? 0 : 1)->values();

            foreach ($sorted as $row) {
                $entry = $this->rowToDefinition($row->db_column, $row->characteristic);
                if ($entry !== null) {
                    $out[$entry['db_column']] = $entry;
                }
            }
        } catch (\Throwable $e) {
            // Ne pas bloquer le tableau en cas d'erreur (table manquante, etc.)
        }

        return $out;
    }

    /**
     * Mapping db_column → définition pour une entité objet (item, consumable, resource, panoply).
     *
     * @param string $entity Une des constantes CharacteristicObject::ENTITY_*
     * @return array<string, array{key: string, db_column: string, name: string, short_name: string|null, helper: string|null, descriptions: array|null, icon: string|null, color: string|null, unit: string|null, type: string|null}>
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

            $sorted = $charRows->sortBy(fn (CharacteristicObject $r) => $r->entity === CharacteristicObject::ENTITY_ALL ? 0 : 1)->values();

            foreach ($sorted as $row) {
                $entry = $this->rowToDefinition($row->db_column, $row->characteristic);
                if ($entry !== null) {
                    $out[$entry['db_column']] = $entry;
                }
            }
        } catch (\Throwable $e) {
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

            $sorted = $charRows->sortBy(fn (CharacteristicSpell $r) => $r->entity === CharacteristicSpell::ENTITY_ALL ? 0 : 1)->values();

            foreach ($sorted as $row) {
                $entry = $this->rowToDefinition($row->db_column, $row->characteristic);
                if ($entry !== null) {
                    $out[$entry['db_column']] = $entry;
                }
            }
        } catch (\Throwable $e) {
        }

        return $out;
    }

    /**
     * @return array{key: string, db_column: string, name: string, short_name: string|null, helper: string|null, descriptions: array|null, icon: string|null, color: string|null, unit: string|null, type: string|null}|null
     */
    private function rowToDefinition(mixed $dbColumn, ?Characteristic $characteristic): ?array
    {
        $dbColumn = is_string($dbColumn) ? trim($dbColumn) : '';
        if ($dbColumn === '') {
            return null;
        }
        if ($characteristic === null) {
            return null;
        }
        $c = $characteristic->effectiveCharacteristic();

        $icon = $c->icon;
        if (is_string($icon) && $icon !== '' && !str_starts_with($icon, 'fa-') && !str_contains($icon, '/')) {
            $icon = 'icons/caracteristics/' . $icon;
        }

        return [
            'key' => $c->key,
            'db_column' => $dbColumn,
            'name' => $c->name,
            'short_name' => $c->short_name,
            'helper' => $c->helper,
            'descriptions' => $c->descriptions,
            'icon' => $icon,
            'color' => $c->color,
            'unit' => $c->unit,
            'type' => $c->type,
        ];
    }
}
