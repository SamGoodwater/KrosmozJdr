<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Creature.
 */
class CreatureResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'hostility' => $this->hostility,
            'location' => $this->location,
            'level' => $this->level,
            'life' => $this->life,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Relations
            'createdBy' => $this->whenLoaded('createdBy'),
            'attributes' => $this->whenLoaded('attributes'),
            'capabilities' => $this->whenLoaded('capabilities'),
            'items' => $this->whenLoaded('items'),
            'resources' => $this->whenLoaded('resources'),
            'spells' => $this->whenLoaded('spells'),
            'consumables' => $this->whenLoaded('consumables'),
            'npc' => $this->whenLoaded('npc'),
            'monster' => $this->whenLoaded('monster'),

            // Droits d'accès
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }
}

