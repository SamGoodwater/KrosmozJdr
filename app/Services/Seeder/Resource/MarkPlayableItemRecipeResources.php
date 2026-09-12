<?php

declare(strict_types=1);

namespace App\Services\Seeder\Resource;

use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use Illuminate\Database\Eloquent\Builder;

/**
 * Passe en `playable` les ressources déjà liées aux recettes des équipements jouables.
 *
 * Identité Dofus inchangée (nom, prix, type). Les recettes de ressource vers ressource
 * (arbre de métier Dofus) ne sont pas touchées.
 *
 * @example
 * $count = app(MarkPlayableItemRecipeResources::class)->mark();
 */
final class MarkPlayableItemRecipeResources
{
    /**
     * @return int Nombre de ressources dont l’état vient de passer à jouable
     */
    public function mark(): int
    {
        return Resource::query()
            ->where('state', '!=', Resource::STATE_PLAYABLE)
            ->whereHas('items', static function (Builder $query): void {
                $query->where('items.state', Item::STATE_PLAYABLE);
            })
            ->update(['state' => Resource::STATE_PLAYABLE]);
    }
}
