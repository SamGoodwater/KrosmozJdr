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
            'items' => ($this->relationLoaded('items') || isset($this->items)) ? $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'level' => $item->level,
                    'pivot' => $item->pivot ? [
                        'quantity' => $item->pivot->quantity ?? null,
                    ] : null,
                ];
            })->values()->all() : [],
            'resources' => ($this->relationLoaded('resources') || isset($this->resources)) ? $this->resources->map(function ($resource) {
                return [
                    'id' => $resource->id,
                    'name' => $resource->name,
                    'description' => $resource->description,
                    'level' => $resource->level,
                    'pivot' => $resource->pivot ? [
                        'quantity' => $resource->pivot->quantity ?? null,
                    ] : null,
                ];
            })->values()->all() : [],
            'spells' => $this->whenLoaded('spells'),
            'consumables' => ($this->relationLoaded('consumables') || isset($this->consumables)) ? $this->consumables->map(function ($consumable) {
                return [
                    'id' => $consumable->id,
                    'name' => $consumable->name,
                    'description' => $consumable->description,
                    'level' => $consumable->level,
                    'pivot' => $consumable->pivot ? [
                        'quantity' => $consumable->pivot->quantity ?? null,
                    ] : null,
                ];
            })->values()->all() : [],
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

