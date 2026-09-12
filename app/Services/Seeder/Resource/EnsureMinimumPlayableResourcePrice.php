<?php

declare(strict_types=1);

namespace App\Services\Seeder\Resource;

use App\Models\Entity\Resource;
use App\Support\KamasAmount;

/**
 * Pose un plancher de 1 kama sur les ressources déjà jouables dont le prix Dofus est 0 ou vide.
 *
 * @example $count = app(EnsureMinimumPlayableResourcePrice::class)->apply();
 */
final class EnsureMinimumPlayableResourcePrice
{
    public const MINIMUM_KAMAS = 1;

    /**
     * @return int Nombre de ressources dont le prix vient d’être porté au plancher
     */
    public function apply(int $minimum = self::MINIMUM_KAMAS): int
    {
        $minimum = max(1, $minimum);
        $ids = Resource::query()
            ->where('state', Resource::STATE_PLAYABLE)
            ->get(['id', 'price'])
            ->filter(static fn (Resource $resource): bool => KamasAmount::parse($resource->price) < $minimum)
            ->pluck('id');

        if ($ids->isEmpty()) {
            return 0;
        }

        return (int) Resource::query()
            ->whereIn('id', $ids)
            ->update(['price' => (string) $minimum]);
    }

    /**
     * Plancher à l’enregistrement d’une fiche jouable (évite de reposer 0 après un scrap).
     */
    public static function clampPriceOnResource(Resource $resource): void
    {
        if ($resource->state !== Resource::STATE_PLAYABLE) {
            return;
        }
        if (KamasAmount::parse($resource->price) < self::MINIMUM_KAMAS) {
            $resource->price = (string) self::MINIMUM_KAMAS;
        }
    }
}
