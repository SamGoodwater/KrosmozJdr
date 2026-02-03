<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Resource.
 */
class ResourceResource extends JsonResource
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
            'dofusdb_id' => $this->dofusdb_id,
            'official_id' => $this->official_id,
            'name' => $this->name,
            'description' => $this->description,
            'level' => $this->level,
            'price' => $this->price,
            'weight' => $this->weight,
            'rarity' => $this->rarity,
            'dofus_version' => $this->dofus_version,
            'state' => $this->state,
            'read_level' => (int) ($this->read_level ?? 0),
            'write_level' => (int) ($this->write_level ?? 0),
            'image' => $this->image,
            'auto_update' => $this->auto_update,
            'resource_type_id' => $this->resource_type_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Relations
            'createdBy' => $this->whenLoaded('createdBy'),
            'resourceType' => $this->whenLoaded('resourceType'),
            'consumables' => $this->whenLoaded('consumables'),
            'creatures' => $this->whenLoaded('creatures'),
            'items' => $this->whenLoaded('items'),
            'recipeIngredients' => $this->whenLoaded('recipeIngredients'),
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
}

