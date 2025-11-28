<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Consumable.
 */
class ConsumableResource extends JsonResource
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
            'description' => $this->description,
            'effect' => $this->effect,
            'level' => $this->level,
            'recipe' => $this->recipe,
            'price' => $this->price,
            'rarity' => $this->rarity,
            'usable' => $this->usable,
            'is_visible' => $this->is_visible,
            'dofus_version' => $this->dofus_version,
            'image' => $this->image,
            'auto_update' => $this->auto_update,
            'consumable_type_id' => $this->consumable_type_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Relations
            'createdBy' => $this->whenLoaded('createdBy'),
            'consumableType' => $this->whenLoaded('consumableType'),
            'resources' => $this->whenLoaded('resources'),
            'creatures' => $this->whenLoaded('creatures'),
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

