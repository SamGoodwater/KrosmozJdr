<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Npc.
 */
class NpcResource extends JsonResource
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
            'creature_id' => $this->creature_id,
            'story' => $this->story,
            'historical' => $this->historical,
            'age' => $this->age,
            'size' => $this->size,
            'breed_id' => $this->breed_id,
            'specialization_id' => $this->specialization_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relations
            'creature' => $this->whenLoaded('creature'),
            'breed' => $this->whenLoaded('breed'),
            'specialization' => $this->whenLoaded('specialization'),
            'panoplies' => $this->whenLoaded('panoplies'),
            'scenarios' => $this->whenLoaded('scenarios'),
            'campaigns' => $this->whenLoaded('campaigns'),
            'shop' => $this->whenLoaded('shop'),

            // Droits d'accès
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }
}

