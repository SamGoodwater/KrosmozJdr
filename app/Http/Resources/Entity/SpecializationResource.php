<?php

namespace App\Http\Resources\Entity;

use App\Http\Resources\SectionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Specialization.
 */
class SpecializationResource extends JsonResource
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
            'short_description' => $this->short_description,
            'description' => $this->description,
            'state' => $this->state,
            'read_level' => (int) ($this->read_level ?? 0),
            'write_level' => (int) ($this->write_level ?? 0),
            'image' => $this->image,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Relations
            'createdBy' => $this->whenLoaded('createdBy'),
            'capabilities' => $this->whenLoaded('capabilities', fn () => CapabilityResource::collection($this->capabilities)->resolve($request)),
            'spells' => $this->whenLoaded('spells', fn () => SpellResource::collection($this->spells)->resolve($request)),
            'creatureTraits' => $this->whenLoaded('creatureTraits', fn () => CreatureTraitResource::collection($this->creatureTraits)->resolve($request)),
            'consumables' => $this->whenLoaded('consumables', fn () => ConsumableResource::collection($this->consumables)->resolve($request)),
            'resources' => $this->whenLoaded('resources', fn () => ResourceResource::collection($this->resources)->resolve($request)),
            'items' => $this->whenLoaded('items', fn () => ItemResource::collection($this->items)->resolve($request)),
            'sections' => $this->whenLoaded('sections', fn () => SectionResource::collection($this->sections)->resolve($request)),
            'npcs' => $this->whenLoaded('npcs'),

            // Droits d'accès
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }
}
