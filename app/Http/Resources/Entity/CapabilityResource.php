<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Capability.
 */
class CapabilityResource extends JsonResource
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
            'effect' => $this->effect,
            'level' => $this->level,
            'pa' => $this->pa,
            'po' => $this->po,
            'po_editable' => $this->po_editable,
            'time_before_use_again' => $this->time_before_use_again,
            'casting_time' => $this->casting_time,
            'duration' => $this->duration,
            'element' => $this->element,
            'is_magic' => $this->is_magic,
            'ritual_available' => $this->ritual_available,
            'powerful' => $this->powerful,
            'usable' => $this->usable,
            'is_visible' => $this->is_visible,
            'image' => $this->image,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Relations
            'createdBy' => $this->whenLoaded('createdBy'),
            'specializations' => $this->whenLoaded('specializations'),
            'creatures' => $this->whenLoaded('creatures'),

            // Droits d'accès
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }
}

