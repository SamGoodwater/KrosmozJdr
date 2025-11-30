<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Item.
 */
class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        return [
            'id' => $this->id,
            'official_id' => $this->official_id,
            'dofusdb_id' => $this->dofusdb_id,
            'name' => $this->name,
            'level' => $this->level,
            'description' => $this->description,
            'effect' => $this->effect,
            'bonus' => $this->bonus,
            'recipe' => $this->recipe,
            'price' => $this->price,
            'rarity' => $this->rarity,
            'dofus_version' => $this->dofus_version,
            'usable' => $this->usable,
            'is_visible' => $this->is_visible,
            'image' => $this->image,
            'auto_update' => $this->auto_update,
            'item_type_id' => $this->item_type_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Relations
            'createdBy' => $this->whenLoaded('createdBy'),
            'itemType' => $this->whenLoaded('itemType'),
            'resources' => $this->getResourcesArray(),
            'panoplies' => $this->whenLoaded('panoplies'),
            'scenarios' => $this->whenLoaded('scenarios'),
            'campaigns' => $this->whenLoaded('campaigns'),
            'shops' => $this->whenLoaded('shops'),

            // Droits d'accès
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }

    /**
     * Retourne toujours un tableau pour les ressources, même si la relation n'est pas chargée.
     *
     * @return array
     */
    protected function getResourcesArray(): array
    {
        try {
            if ($this->relationLoaded('resources')) {
                return $this->resources->map(function ($resource) {
                    return [
                        'id' => $resource->id,
                        'name' => $resource->name,
                        'description' => $resource->description,
                        'level' => $resource->level,
                        'pivot' => $resource->pivot ? [
                            'quantity' => $resource->pivot->quantity ?? null,
                        ] : null,
                    ];
                })->values()->all();
            }
        } catch (\Exception $e) {
            // Si la relation n'existe pas ou n'est pas chargée, retourner un tableau vide
        }
        return [];
    }
}

