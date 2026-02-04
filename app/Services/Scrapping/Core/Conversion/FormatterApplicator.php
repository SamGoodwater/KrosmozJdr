<?php

namespace App\Services\Scrapping\Core\Conversion;

use App\Models\Type\ResourceType;
use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;

/**
 * Applique les formatters purs utilisés par la conversion.
 *
 * Formatters supportés : toString, pickLang, toInt, clampInt, mapSizeToKrosmoz,
 * storeScrappedImage, truncate, clampToCharacteristic (limites BDD par entité).
 * Si DofusConversionService est injecté : dofusdb_level, dofusdb_life, dofusdb_attribute, dofusdb_ini.
 */
final class FormatterApplicator
{
    public function __construct(
        private readonly ?DofusConversionService $conversionService = null,
        private readonly ?CharacteristicGetterService $getter = null
    ) {
    }

    /**
     * @param array<string, mixed> $args
     * @param array<string, mixed> $raw
     * @param array{entityType?: string, lang?: string} $context
     * @return mixed
     */
    public function apply(string $name, mixed $value, array $args, array $raw, array $context = []): mixed
    {
        $entityType = (string) ($context['entityType'] ?? 'monster');

        return match ($name) {
            'toString' => $value === null ? '' : (string) $value,
            'pickLang' => $this->pickLang($value, (string) ($args['lang'] ?? 'fr'), (string) ($args['fallback'] ?? 'fr')),
            'toInt' => is_numeric($value) ? (int) $value : 0,
            'nullableInt' => $value === null ? null : (is_numeric($value) ? (int) $value : null),
            'clampInt' => $this->clampInt($value, (int) ($args['min'] ?? 0), (int) ($args['max'] ?? 0)),
            'clampToCharacteristic' => $this->clampToCharacteristic(
                $value,
                (string) ($args['characteristicId'] ?? ''),
                $entityType
            ),
            'mapSizeToKrosmoz' => $this->mapSize((string) $value, (string) ($args['default'] ?? 'medium')),
            'storeScrappedImage' => $value === null ? null : (string) $value,
            'truncate' => $this->truncate($value, (int) ($args['max'] ?? 255)),
            'dofusdb_level' => $this->conversionService !== null
                ? $this->conversionService->convertLevel($this->numericValue($value), $entityType)
                : $value,
            'dofusdb_life' => $this->conversionService !== null
                ? $this->applyDofusdbLife($value, $raw, $args, $entityType)
                : $value,
            'dofusdb_attribute' => $this->conversionService !== null && isset($args['characteristicId'])
                ? $this->conversionService->convertAttribute((string) $args['characteristicId'], $value, $entityType)
                : $value,
            'dofusdb_ini' => $this->conversionService !== null
                ? $this->conversionService->convertInitiative($this->numericValue($value), $entityType)
                : $value,
            'toJson' => $this->toJson($value),
            'extractItemIds' => $this->extractItemIds($value),
            'resolveResourceTypeId' => $this->resolveResourceTypeId($value, $raw, (string) ($context['lang'] ?? 'fr')),
            'defaultRarityByLevel' => $this->defaultRarityByLevel($value, $raw, (string) ($context['entityType'] ?? 'item')),
            'recipeIdsToResourceRecipe' => $this->recipeIdsToResourceRecipe($value),
            'recipeToResourceRecipe' => $this->recipeToResourceRecipe($value, $raw),
            default => $value,
        };
    }

    /**
     * Encode en JSON (pour bonus panoplie, effects).
     */
    private function toJson(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        try {
            $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

            return $encoded !== false ? $encoded : null;
        } catch (\JsonException) {
            return null;
        }
    }

    /**
     * Extrait les id des éléments d'un tableau (ex. items[].id pour panoplie).
     * Retourne un tableau d'identifiants pour la relation panoply-items.
     */
    private function extractItemIds(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }
        $ids = [];
        foreach ($value as $item) {
            if (is_array($item) && isset($item['id'])) {
                $ids[] = (int) $item['id'];
            }
        }

        return $ids;
    }

    public function supports(string $name): bool
    {
        $base = [
            'toString',
            'pickLang',
            'toInt',
            'nullableInt',
            'clampInt',
            'clampToCharacteristic',
            'mapSizeToKrosmoz',
            'storeScrappedImage',
            'truncate',
            'toJson',
            'extractItemIds',
            'resolveResourceTypeId',
            'defaultRarityByLevel',
            'recipeIdsToResourceRecipe',
            'recipeToResourceRecipe',
        ];
        if ($this->conversionService !== null) {
            $base = array_merge($base, ['dofusdb_level', 'dofusdb_life', 'dofusdb_attribute', 'dofusdb_ini']);
        }

        return in_array($name, $base, true);
    }

    private function numericValue(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function applyDofusdbLife(mixed $value, array $raw, array $args, string $entityType): int
    {
        $levelPath = (string) ($args['levelPath'] ?? 'grades.0.level');
        $levelRaw = $this->getByPath($raw, $levelPath);
        $levelKrosmoz = $this->conversionService->convertLevel($this->numericValue($levelRaw), $entityType);

        return $this->conversionService->convertLife($this->numericValue($value), $levelKrosmoz, $entityType);
    }

    /**
     * @param array<string, mixed> $data
     * @return mixed
     */
    private function getByPath(array $data, string $path): mixed
    {
        $parts = explode('.', $path);
        $cur = $data;
        foreach ($parts as $part) {
            if (!is_array($cur)) {
                return null;
            }
            $key = ctype_digit($part) ? (int) $part : $part;
            $cur = $cur[$key] ?? null;
        }

        return $cur;
    }

    private function truncate(mixed $value, int $max): string
    {
        $s = $value === null ? '' : (string) $value;
        if ($max <= 0) {
            return $s;
        }
        if (mb_strlen($s) <= $max) {
            return $s;
        }

        return mb_substr($s, 0, $max);
    }

    private function pickLang(mixed $value, string $lang, string $fallback): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (!is_array($value)) {
            return '';
        }
        if (isset($value[$lang]) && is_string($value[$lang])) {
            return $value[$lang];
        }
        if (isset($value[$fallback]) && is_string($value[$fallback])) {
            return $value[$fallback];
        }
        $first = reset($value);

        return is_string($first) ? $first : '';
    }

    private function clampInt(mixed $value, int $min, int $max): int
    {
        $v = is_numeric($value) ? (int) $value : 0;
        if ($max !== 0 && $max < $min) {
            return $v;
        }

        return max($min, min($max, $v));
    }

    /**
     * Clamp une valeur selon les limites min/max de la caractéristique pour l'entité (ex. spell).
     * Si le service ou les limites sont absents, retourne la valeur convertie en int.
     */
    private function clampToCharacteristic(mixed $value, string $characteristicId, string $entityType): int
    {
        $v = is_numeric($value) ? (int) $value : 0;
        if ($this->getter === null || $characteristicId === '') {
            return $v;
        }
        $limits = $this->getter->getLimits($characteristicId, $entityType);
        if ($limits === null) {
            return $v;
        }

        return max($limits['min'], min($limits['max'], $v));
    }

    private function mapSize(string $value, string $default): string
    {
        $valid = ['tiny', 'small', 'medium', 'large', 'huge'];

        return in_array($value, $valid, true) ? $value : $default;
    }

    /**
     * Résout un typeId DofusDB vers l'id Krosmoz resource_types (firstOrCreate).
     * Utilise raw.type.name pour le libellé si créé.
     */
    private function resolveResourceTypeId(mixed $value, array $raw, string $lang): ?int
    {
        $typeId = is_numeric($value) ? (int) $value : 0;
        if ($typeId <= 0) {
            return null;
        }
        $typeNode = $this->getByPath($raw, 'type');
        $name = null;
        if (is_array($typeNode) && isset($typeNode['name'])) {
            $name = $this->pickLang($typeNode['name'], $lang, 'fr');
        }
        $name = is_string($name) && $name !== '' ? $name : 'DofusDB type #' . $typeId;

        $rt = ResourceType::firstOrCreate(
            ['dofusdb_type_id' => $typeId],
            ['name' => $name, 'state' => ResourceType::STATE_PLAYABLE]
        );

        return $rt->id;
    }

    /**
     * Rareté par défaut selon le niveau (resource, consumable, item, panoply).
     * Niveau = niveau Krosmoz (après conversion level DofusDB). Si conversionService présent, utilise getRarityByLevel.
     */
    private function defaultRarityByLevel(mixed $value, array $raw, string $entityType): int
    {
        if ($value !== null && $value !== '' && is_numeric($value)) {
            return (int) $value;
        }
        $rawLevel = (int) ($this->getByPath($raw, 'level') ?? 0);
        $level = $this->conversionService !== null
            ? $this->conversionService->convertLevel($rawLevel, $entityType)
            : (int) round($rawLevel / 10);

        if ($this->conversionService !== null) {
            return $this->conversionService->getRarityByLevel($level, $entityType);
        }

        $bands = config('characteristics_rarity.rarity_default_by_level', [
            0 => 0, 3 => 1, 7 => 2, 10 => 3, 17 => 4,
        ]);
        krsort($bands, SORT_NUMERIC);
        foreach ($bands as $minLevel => $rarity) {
            if ($level >= $minLevel) {
                return (int) $rarity;
            }
        }

        return 0;
    }

    /**
     * Transforme l'objet recette DofusDB (ingredientIds + quantities) ou recipeIds en liste
     * recipe_ingredients pour la table pivot resource_recipe.
     * Préfère recipe (depuis /recipes?resultId=) pour avoir les quantités réelles ; sinon fallback sur recipeIds (qty 1).
     *
     * @return list<array{ingredient_dofusdb_id: string, quantity: int}>
     */
    private function recipeToResourceRecipe(mixed $value, array $raw): array
    {
        if (is_array($value) && isset($value['ingredientIds']) && isset($value['quantities'])) {
            $ids = $value['ingredientIds'] ?? [];
            $quantities = $value['quantities'] ?? [];
            if (!is_array($ids) || !is_array($quantities)) {
                return [];
            }
            $out = [];
            foreach ($ids as $idx => $id) {
                if (!is_numeric($id)) {
                    continue;
                }
                $qty = isset($quantities[$idx]) && is_numeric($quantities[$idx])
                    ? (int) $quantities[$idx]
                    : 1;
                $out[] = [
                    'ingredient_dofusdb_id' => (string) $id,
                    'quantity' => max(1, $qty),
                ];
            }

            return $out;
        }
        $recipeIds = $raw['recipeIds'] ?? [];
        return $this->recipeIdsToResourceRecipe(is_array($recipeIds) ? $recipeIds : []);
    }

    /**
     * Fallback : transforme recipeIds (liste d'ids) en recipe_ingredients avec quantité 1 par id.
     *
     * @return list<array{ingredient_dofusdb_id: string, quantity: int}>
     */
    private function recipeIdsToResourceRecipe(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }
        $ids = [];
        foreach ($value as $id) {
            if (is_numeric($id)) {
                $ids[] = (int) $id;
            }
        }
        if ($ids === []) {
            return [];
        }
        $byId = array_count_values($ids);
        $out = [];
        foreach ($byId as $dofusdbId => $quantity) {
            $out[] = [
                'ingredient_dofusdb_id' => (string) $dofusdbId,
                'quantity' => (int) $quantity,
            ];
        }

        return $out;
    }
}
