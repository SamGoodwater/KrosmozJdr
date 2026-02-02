<?php

declare(strict_types=1);

namespace App\Services\Characteristic;

use App\Models\Characteristic;
use Illuminate\Support\Facades\Cache;

/**
 * Service de lecture des caractéristiques depuis la base de données.
 *
 * Expose la même structure que config('characteristics') pour compatibilité
 * avec ValidationService V2 et DofusDbConversionFormulas.
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/PLAN_MIGRATION_CHARACTERISTICS_DB.md
 */
final class CharacteristicService
{
    private const CACHE_KEY = 'characteristics.full';

    private const CACHE_TTL_SECONDS = 3600;

    /**
     * Retourne toutes les caractéristiques (id => définition avec entities).
     *
     * Équivalent de config('characteristics.characteristics').
     *
     * @return array<string, array<string, mixed>>
     */
    public function getCharacteristics(): array
    {
        $full = $this->getFullConfig();

        return $full['characteristics'] ?? [];
    }

    /**
     * Retourne uniquement les compétences (is_competence === true).
     *
     * Équivalent de config('characteristics.competences').
     *
     * @return array<string, array<string, mixed>>
     */
    public function getCompetences(): array
    {
        $full = $this->getFullConfig();

        return $full['competences'] ?? [];
    }

    /**
     * Retourne la config complète (characteristics + competences).
     *
     * Équivalent de config('characteristics').
     *
     * @return array{characteristics: array<string, array<string, mixed>>, competences: array<string, array<string, mixed>>}
     */
    public function getFullConfig(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            return $this->buildFullConfig();
        });
    }

    /**
     * Retourne une caractéristique par id, ou null.
     *
     * @return array<string, mixed>|null
     */
    public function getCharacteristic(string $id): ?array
    {
        $all = $this->getCharacteristics();

        return $all[$id] ?? null;
    }

    /**
     * Retourne les limites min/max pour une caractéristique et une entité.
     *
     * @return array{min: int, max: int}|null
     */
    public function getLimits(string $characteristicId, string $entity): ?array
    {
        $def = $this->getCharacteristic($characteristicId);
        if ($def === null) {
            return null;
        }
        $entityDef = $def['entities'][$entity] ?? null;
        if ($entityDef === null || !isset($entityDef['min'], $entityDef['max'])) {
            return null;
        }

        return [
            'min' => (int) $entityDef['min'],
            'max' => (int) $entityDef['max'],
        ];
    }

    /**
     * Invalide le cache (à appeler après création/update/suppression en base).
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Construit la config complète depuis la base (sans cache).
     *
     * @return array{characteristics: array<string, array<string, mixed>>, competences: array<string, array<string, mixed>>}
     */
    private function buildFullConfig(): array
    {
        $characteristics = Characteristic::query()
            ->with('entityDefinitions')
            ->orderBy('sort_order')
            ->get();

        $byId = [];
        $competences = [];

        foreach ($characteristics as $c) {
            $def = $this->characteristicToConfigArray($c);
            $byId[$c->id] = $def;
            if ($c->is_competence) {
                $competences[$c->id] = $def;
            }
        }

        return [
            'characteristics' => $byId,
            'competences' => $competences,
        ];
    }

    /**
     * Transforme un modèle Characteristic + entityDefinitions en tableau config.
     *
     * @return array<string, mixed>
     */
    private function characteristicToConfigArray(Characteristic $c): array
    {
        $itemEntity = $c->entityDefinitions->firstWhere('entity', 'item');

        $entities = [];
        foreach ($c->entityDefinitions as $entityDef) {
            $entities[$entityDef->entity] = [
                'min' => $entityDef->min,
                'max' => $entityDef->max,
                'formula' => $entityDef->formula,
                'formula_display' => $entityDef->formula_display,
                'default' => $entityDef->default_value !== null ? $this->castDefaultValue($entityDef->default_value, $c->type) : null,
                'required' => $entityDef->required,
                'validation_message' => $entityDef->validation_message,
            ];
        }

        $out = [
            'db_column' => $c->db_column ?? $c->id,
            'name' => $c->name,
            'short_name' => $c->short_name,
            'description' => $c->description,
            'icon' => $c->icon,
            'color' => $c->color,
            'type' => $c->type,
            'unit' => $c->unit,
            'forgemagie' => [
                'allowed' => $itemEntity?->forgemagie_allowed ?? false,
                'max' => (int) ($itemEntity?->forgemagie_max ?? 0),
            ],
            'applies_to' => $c->applies_to ?? [],
            'entities' => $entities,
            'order' => $c->sort_order,
        ];

        if ($c->validation !== null && $c->validation !== []) {
            $out['validation'] = $c->validation;
        }
        if ($c->value_available !== null && $c->value_available !== []) {
            $out['value_available'] = $c->value_available;
        }
        if ($c->labels !== null && $c->labels !== []) {
            $out['labels'] = $c->labels;
        }

        if ($c->is_competence) {
            $out['is_competence'] = true;
            if ($c->characteristic_id !== null) {
                $out['characteristic'] = $c->characteristic_id;
            }
            if ($c->alternative_characteristic_id !== null) {
                $out['alternative_characteristic'] = $c->alternative_characteristic_id;
            }
            if ($c->skill_type !== null) {
                $out['skill_type'] = $c->skill_type;
            }
            if ($c->mastery_value_available !== null && $c->mastery_value_available !== []) {
                $out['mastery_value_available'] = $c->mastery_value_available;
            }
            if ($c->mastery_labels !== null && $c->mastery_labels !== []) {
                $out['mastery_labels'] = $c->mastery_labels;
            }
        }

        if ($itemEntity !== null && $itemEntity->base_price_per_unit !== null) {
            $out['base_price_per_unit'] = (float) $itemEntity->base_price_per_unit;
        }
        if ($itemEntity !== null && $itemEntity->rune_price_per_unit !== null) {
            $out['rune_price_per_unit'] = (float) $itemEntity->rune_price_per_unit;
        }

        return $out;
    }

    /**
     * Interprète default_value (string en BDD) selon le type de la caractéristique.
     */
    private function castDefaultValue(string $defaultValue, string $type): int|string|array|null
    {
        return match ($type) {
            'int' => is_numeric($defaultValue) ? (int) $defaultValue : null,
            'string' => $defaultValue,
            'array' => json_decode($defaultValue, true) ?? [],
            default => $defaultValue,
        };
    }
}
