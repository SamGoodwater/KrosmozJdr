<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion;

/**
 * Transforme une recette DofusDB (ingredientIds + quantities ou recipeIds) en liste
 * recipe_ingredients pour la table pivot resource_recipe.
 *
 * @see docs/50-Fonctionnalités/Scrapping/SIMPLIFICATIONS_SCRAPPING.md
 *
 * @return list<array{ingredient_dofusdb_id: string, quantity: int}>
 */
final class RecipeToResourceRecipeConverter
{
    /**
     * Préfère recipe (depuis /recipes?resultId=) pour les quantités réelles ;
     * sinon fallback sur recipeIds (qty 1 par id).
     *
     * @param array<string, mixed> $raw Données brutes (pour recipeIds si value n'a pas ingredientIds)
     */
    public function convert(mixed $value, array $raw): array
    {
        if (is_array($value) && isset($value['ingredientIds']) && isset($value['quantities'])) {
            return $this->fromIngredientIdsAndQuantities($value['ingredientIds'] ?? [], $value['quantities'] ?? []);
        }
        $recipeIds = $raw['recipeIds'] ?? [];

        return $this->fromRecipeIds(is_array($recipeIds) ? $recipeIds : []);
    }

    /**
     * Transforme une liste recipeIds (ids seuls) en recipe_ingredients (qty 1 par id, doublons agrégés).
     *
     * @return list<array{ingredient_dofusdb_id: string, quantity: int}>
     */
    public function convertFromRecipeIds(mixed $value): array
    {
        $ids = is_array($value) ? $value : [];

        return $this->fromRecipeIds($ids);
    }

    /**
     * @param list<mixed> $ids
     * @param list<mixed> $quantities
     * @return list<array{ingredient_dofusdb_id: string, quantity: int}>
     */
    private function fromIngredientIdsAndQuantities(array $ids, array $quantities): array
    {
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

    /**
     * Fallback : transforme recipeIds (liste d'ids) en recipe_ingredients avec quantité 1 par id.
     *
     * @param list<mixed> $recipeIds
     * @return list<array{ingredient_dofusdb_id: string, quantity: int}>
     */
    private function fromRecipeIds(array $recipeIds): array
    {
        $ids = [];
        foreach ($recipeIds as $id) {
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
