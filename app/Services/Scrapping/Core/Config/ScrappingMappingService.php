<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Config;

use App\Models\Scrapping\ScrappingEntityMapping;

/**
 * Charge le mapping DofusDB → Krosmoz depuis la BDD (scrapping_entity_mappings).
 * Retourne un tableau au format attendu par ConversionService (même forme que le JSON).
 * Expose characteristic_id et characteristic_key pour que le pipeline utilise les formules/limites BDD.
 *
 * @see docs/50-Fonctionnalités/VISION_UI_ADMIN_MAPPING_ET_CARACTERISTIQUES.md
 * @see docs/50-Fonctionnalités/ARCHITECTURE_SCRAPPING_MAPPING_CARACTERISTIQUES.md
 */
final class ScrappingMappingService
{
    /**
     * Retourne le mapping pour (source, entity) au format config JSON.
     * Chaque entrée : key, from.path, from.langAware, to[{model, field}], formatters[{name, args}],
     * characteristic_id (nullable), characteristic_key (nullable, clé BDD de la caractéristique).
     *
     * @return list<array{key: string, from: array{path: string, langAware?: bool}, to: list<array{model: string, field: string}>, formatters?: list<array{name: string, args: array}>, characteristic_id?: int|null, characteristic_key?: string|null}>|null
     *         null si aucun mapping en BDD (le pipeline utilisera le JSON).
     */
    public function getMappingForEntity(string $source, string $entity): ?array
    {
        $rows = ScrappingEntityMapping::where('source', $source)
            ->where('entity', $entity)
            ->with(['targets', 'characteristic'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            return null;
        }

        $out = [];
        foreach ($rows as $row) {
            $to = $row->getTargetsForConversion();
            if ($to === []) {
                continue;
            }
            $from = ['path' => $row->from_path];
            if ($row->from_lang_aware) {
                $from['langAware'] = true;
            }
            $entry = [
                'key' => $row->mapping_key,
                'from' => $from,
                'to' => $to,
            ];
            if ($row->formatters !== null && $row->formatters !== []) {
                $entry['formatters'] = $row->formatters;
            }
            if ($row->characteristic_id !== null) {
                $entry['characteristic_id'] = $row->characteristic_id;
                $entry['characteristic_key'] = $row->characteristic?->key;
            }
            $out[] = $entry;
        }

        return $out;
    }

    /**
     * Indique si la BDD contient au moins une règle de mapping pour cette entité.
     */
    public function hasMappingForEntity(string $source, string $entity): bool
    {
        return ScrappingEntityMapping::where('source', $source)
            ->where('entity', $entity)
            ->exists();
    }

    /**
     * Liste des entités ayant au moins un mapping en BDD pour la source donnée.
     *
     * @return list<string>
     */
    public function listEntitiesWithMapping(string $source): array
    {
        return ScrappingEntityMapping::where('source', $source)
            ->distinct()
            ->pluck('entity')
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Règles de mapping qui utilisent une caractéristique donnée (pour le panneau 3 de la fiche caractéristique).
     *
     * @return list<array{id: int, source: string, entity: string, mapping_key: string, from_path: string, targets: list<array{model: string, field: string}>}>
     */
    public function listMappingsForCharacteristic(int $characteristicId): array
    {
        return ScrappingEntityMapping::where('characteristic_id', $characteristicId)
            ->with('targets')
            ->orderBy('source')
            ->orderBy('entity')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ScrappingEntityMapping $m): array => $m->toSummaryArray())
            ->values()
            ->all();
    }

    /**
     * Règles de mapping disponibles pour une entité (pour le modal « Lier » depuis la fiche caractéristique).
     * Retourne toutes les règles (source, entity) avec characteristic_id et characteristic pour afficher « déjà liée à X ».
     *
     * @return list<array{id: int, mapping_key: string, from_path: string, characteristic_id: int|null, characteristic: array{id: int, key: string, name: string|null}|null}>
     */
    public function listAvailableMappingsForEntity(string $source, string $entity): array
    {
        return ScrappingEntityMapping::where('source', $source)
            ->where('entity', $entity)
            ->with('characteristic:id,key,name')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (ScrappingEntityMapping $m): array => [
                'id' => $m->id,
                'mapping_key' => $m->mapping_key,
                'from_path' => $m->from_path,
                'characteristic_id' => $m->characteristic_id,
                'characteristic' => $m->relationLoaded('characteristic') && $m->characteristic
                    ? ['id' => $m->characteristic->id, 'key' => $m->characteristic->key, 'name' => $m->characteristic->name]
                    : null,
            ])
            ->values()
            ->all();
    }
}
