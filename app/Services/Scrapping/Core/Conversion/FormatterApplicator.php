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
 * Si la règle de mapping (context.mappingRule) a characteristic_key, dofusdb_attribute utilise
 * convertByCharacteristicKey pour formules/limites BDD.
 */
final class FormatterApplicator
{
    /** @var array<string, callable(mixed, array, array, array): mixed> */
    private array $registry = [];

    public function __construct(
        private readonly ?DofusConversionService $conversionService = null,
        private readonly ?CharacteristicGetterService $getter = null
    ) {
        $this->registry = $this->buildRegistry();
    }

    /**
     * Dispatch unique : délègue au formatter enregistré ou retourne la valeur inchangée.
     *
     * @param array<string, mixed> $args
     * @param array<string, mixed> $raw
     * @param array{entityType?: string, lang?: string, mappingRule?: array{characteristic_key?: string|null}} $context
     * @return mixed
     */
    public function apply(string $name, mixed $value, array $args, array $raw, array $context = []): mixed
    {
        $context['_resolvedCharacteristicKey'] = $this->resolveCharacteristicKeyFromContext($context);
        if (isset($this->registry[$name])) {
            return ($this->registry[$name])($value, $args, $raw, $context);
        }

        return $value;
    }

    /**
     * Construit le registry nom → callable pour un dispatch unique et des formatters testables.
     *
     * @return array<string, callable(mixed, array, array, array): mixed>
     */
    private function buildRegistry(): array
    {
        $registry = [
            'toString' => fn (mixed $v): string => $v === null ? '' : (string) $v,
            'pickLang' => fn (mixed $v, array $a): string => $this->pickLang($v, (string) ($a['lang'] ?? 'fr'), (string) ($a['fallback'] ?? 'fr')),
            'toInt' => fn (mixed $v): int => is_numeric($v) ? (int) $v : 0,
            'nullableInt' => fn (mixed $v): ?int => $v === null ? null : (is_numeric($v) ? (int) $v : null),
            'clampInt' => fn (mixed $v, array $a): int => $this->clampInt($v, (int) ($a['min'] ?? 0), (int) ($a['max'] ?? 0)),
            'clampToCharacteristic' => function (mixed $v, array $a, array $r, array $c): int {
                return $this->clampToCharacteristic($v, (string) ($a['characteristicId'] ?? ''), (string) ($c['entityType'] ?? 'monster'));
            },
            'mapSizeToKrosmoz' => fn (mixed $v, array $a): string => $this->mapSize((string) $v, (string) ($a['default'] ?? 'medium')),
            'storeScrappedImage' => fn (mixed $v): ?string => $v === null ? null : (string) $v,
            'truncate' => fn (mixed $v, array $a): string => $this->truncate($v, (int) ($a['max'] ?? 255)),
            'toJson' => fn (mixed $v): ?string => $this->toJson($v),
            'extractItemIds' => fn (mixed $v): array => $this->extractItemIds($v),
            'resolveResourceTypeId' => function (mixed $v, array $a, array $r, array $c): ?int {
                return $this->resolveResourceTypeId($v, $r, (string) ($c['lang'] ?? 'fr'));
            },
            'defaultRarityByLevel' => function (mixed $v, array $a, array $r, array $c): int {
                return $this->defaultRarityByLevel($v, $r, (string) ($c['entityType'] ?? 'item'));
            },
            'recipeIdsToResourceRecipe' => fn (mixed $v): array => $this->recipeIdsToResourceRecipe($v),
            'recipeToResourceRecipe' => function (mixed $v, array $a, array $r): array {
                return $this->recipeToResourceRecipe($v, $r);
            },
            'itemEffectsToKrosmozBonus' => function (mixed $v, array $a, array $r, array $c): ?string {
                return $this->itemEffectsToKrosmozBonus($v, $r, $c);
            },
        ];

        if ($this->conversionService !== null) {
            $registry['dofusdb_level'] = function (mixed $v, array $a, array $r, array $c): mixed {
                $entityType = (string) ($c['entityType'] ?? 'monster');
                $d = $this->numericValue($v);
                $key = $this->conversionService->getLevelCharacteristicKey($entityType);
                return $this->conversionService->convert($key, ['d' => $d], $entityType, (float) round($d / 10), $c);
            };
            $registry['dofusdb_life'] = function (mixed $v, array $a, array $r, array $c): mixed {
                return $this->applyDofusdbLife($v, $r, $a, (string) ($c['entityType'] ?? 'monster'), $c);
            };
            $registry['dofusdb_attribute'] = function (mixed $v, array $a, array $r, array $c): mixed {
                return $this->applyDofusdbAttribute($v, $a, (string) ($c['entityType'] ?? 'monster'), $c['_resolvedCharacteristicKey'] ?? null, $c);
            };
            $registry['dofusdb_ini'] = function (mixed $v, array $a, array $r, array $c): mixed {
                $entityType = (string) ($c['entityType'] ?? 'monster');
                $d = $this->numericValue($v);
                return $this->conversionService->convert('ini_creature', ['d' => $d], $entityType, $d, $c);
            };
        }

        return $registry;
    }

    /**
     * Convertit item.effects[] DofusDB (characteristic id + value) en JSON bonus Krosmoz
     * (stats : intel, strong, etc.) pour la colonne effect.
     * Utilise dofusdb_characteristic_to_krosmoz.json pour mapper id → characteristic_key.
     *
     * @param mixed $value item.effects (array)
     * @param array<string, mixed> $raw item brut
     * @param array<string, mixed> $context Contexte (convertedOutput, raw) pour les fonctions de conversion
     * @return string|null JSON encodé ou null
     */
    private function itemEffectsToKrosmozBonus(mixed $value, array $raw, array $context = []): ?string
    {
        if (!is_array($value) || $value === []) {
            return null;
        }

        $path = base_path('resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json');
        if (!is_file($path)) {
            return $this->toJson($value);
        }

        try {
            $config = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $this->toJson($value);
        }

        $mapping = $config['mapping'] ?? [];
        if (!is_array($mapping)) {
            return $this->toJson($value);
        }

        $bonus = [];
        foreach ($value as $effect) {
            if (!is_array($effect)) {
                continue;
            }
            $charId = isset($effect['characteristic']) ? (int) $effect['characteristic'] : null;
            if ($charId === null) {
                continue;
            }
            $charKey = $mapping[(string) $charId] ?? null;
            if (!is_string($charKey) || $charKey === '') {
                continue;
            }
            $from = isset($effect['from']) && is_numeric($effect['from']) ? (int) $effect['from'] : null;
            $to = isset($effect['to']) && is_numeric($effect['to']) ? (int) $effect['to'] : null;
            $val = $effect['value'] ?? $effect['min'] ?? $effect['max'] ?? null;
            if ($val === null && $from !== null && $to !== null) {
                $val = (int) round(($from + $to) / 2);
            } elseif ($val === null && $to !== null) {
                $val = $to;
            } elseif ($val === null && $from !== null) {
                $val = $from;
            }
            $val = is_numeric($val) ? (int) $val : 0;
            if ($this->conversionService !== null) {
                $val = $this->conversionService->convertObjectAttribute($charKey, $val, 'item', $context);
            }
            $shortKey = str_ends_with($charKey, '_object') ? substr($charKey, 0, -7) : $charKey;
            $bonus[$shortKey] = ($bonus[$shortKey] ?? 0) + $val;
        }

        if ($bonus === []) {
            return null;
        }

        return $this->toJson($bonus);
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
        return isset($this->registry[$name]);
    }

    private function numericValue(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return is_numeric($value) ? (float) $value : 0.0;
    }

    /**
     * @param array<string, mixed> $context Contexte (convertedOutput, raw) pour les fonctions de conversion
     */
    private function applyDofusdbLife(mixed $value, array $raw, array $args, string $entityType, array $context = []): int
    {
        $levelPath = (string) ($args['levelPath'] ?? 'grades.0.level');
        $levelRaw = $this->getByPath($raw, $levelPath);
        $dLevel = $this->numericValue($levelRaw);
        $keyLevel = $this->conversionService->getLevelCharacteristicKey($entityType);
        $levelKrosmoz = $this->conversionService->convert($keyLevel, ['d' => $dLevel], $entityType, (float) round($dLevel / 10), $context);
        $dLife = $this->numericValue($value);

        return $this->conversionService->convert('life_creature', ['d' => $dLife, 'level' => $levelKrosmoz], $entityType, (float) round($dLife / 200 + $levelKrosmoz * 5), $context);
    }

    /**
     * Extrait la clé de caractéristique depuis context.mappingRule (règle de mapping BDD).
     * Une seule responsabilité : résoudre la clé pour les formatters dofusdb_*.
     *
     * @param array{mappingRule?: array{characteristic_key?: string|null}} $context
     */
    private function resolveCharacteristicKeyFromContext(array $context): ?string
    {
        $mappingRule = $context['mappingRule'] ?? null;
        if (! is_array($mappingRule) || ! isset($mappingRule['characteristic_key']) || ! is_string($mappingRule['characteristic_key'])) {
            return null;
        }
        $key = $mappingRule['characteristic_key'];

        return $key !== '' ? $key : null;
    }

    /**
     * Applique dofusdb_attribute : utilise characteristic_key de la règle de mapping si présent,
     * sinon args.characteristicId (convention xxx_creature).
     *
     * @param array<string, mixed> $context Contexte (convertedOutput, raw) pour les fonctions de conversion
     */
    private function applyDofusdbAttribute(mixed $value, array $args, string $entityType, ?string $characteristicKey, array $context = []): mixed
    {
        if ($this->conversionService === null) {
            return $value;
        }
        if ($characteristicKey !== null) {
            return $this->conversionService->convertByCharacteristicKey($characteristicKey, $value, $entityType, $context);
        }
        if (isset($args['characteristicId']) && is_string($args['characteristicId'])) {
            return $this->conversionService->convertAttribute($args['characteristicId'], $value, $entityType, $context);
        }

        return $value;
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
     * Niveau = niveau Krosmoz (convert level). Si conversionService présent, appelle convert('rarity_object', ['level' => level], …).
     */
    private function defaultRarityByLevel(mixed $value, array $raw, string $entityType): int
    {
        if ($value !== null && $value !== '' && is_numeric($value)) {
            return (int) $value;
        }
        $rawLevel = (int) ($this->getByPath($raw, 'level') ?? 0);
        if ($this->conversionService === null) {
            $level = (int) round($rawLevel / 10);
            if ($this->getter !== null) {
                $def = $this->getter->getDefinition('rarity_object', $entityType);
                if ($def !== null && isset($def['value_available']) && is_array($def['value_available']) && $def['value_available'] !== []) {
                    $first = reset($def['value_available']);
                    return is_numeric($first) ? (int) (float) $first : 0;
                }
            }
            return 0;
        }
        $keyLevel = $this->conversionService->getLevelCharacteristicKey($entityType);
        $level = $this->conversionService->convert($keyLevel, ['d' => (float) $rawLevel], $entityType, (float) round($rawLevel / 10));
        if (! in_array($entityType, DofusConversionService::RARITY_ENTITIES, true)) {
            return 0;
        }
        $fallback = $this->conversionService->getRarityFallbackForLevel($level);

        return $this->conversionService->convert('rarity_object', ['level' => $level], $entityType, (float) $fallback);
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
