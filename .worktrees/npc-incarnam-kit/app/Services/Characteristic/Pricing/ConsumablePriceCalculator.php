<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Pricing;

use App\Support\KamasAmount;
use Illuminate\Support\Collection;

/**
 * Prix automatique d’un consommable : somme des prix des ressources de la recette × quantité.
 *
 * @example
 * $price = $calculator->calculateFromResources($consumable->resources);
 */
final class ConsumablePriceCalculator
{
    /**
     * @param  Collection<int, object>|iterable<int, object>  $resources  Ressources avec `price` et pivot.quantity
     */
    public function calculateFromResources(iterable $resources): int
    {
        $sum = 0.0;
        foreach ($resources as $resource) {
            $quantity = (int) ($resource->pivot->quantity ?? 1);
            if ($quantity < 1) {
                $quantity = 1;
            }
            $sum += KamasAmount::parse($resource->price ?? null) * $quantity;
        }

        return (int) max(0, round($sum));
    }
}
