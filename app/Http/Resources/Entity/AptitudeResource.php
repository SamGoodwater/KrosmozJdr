<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Aptitude.
 */
class AptitudeResource extends JsonResource
{
    /**
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
            'reuse_type' => $this->reuse_type,
            'reuse_amount' => $this->reuse_amount !== null ? (int) $this->reuse_amount : null,
            'reuse_unit' => $this->reuse_unit,
            'duration_type' => $this->duration_type,
            'duration_amount' => $this->duration_amount !== null ? (int) $this->duration_amount : null,
            'duration_unit' => $this->duration_unit,
            'duration_note' => $this->duration_note,
            'range_type' => $this->range_type,
            'range_amount' => $this->range_amount !== null ? (int) $this->range_amount : null,
            'range_unit' => $this->range_unit,
            'range_sense' => $this->range_sense,
            'range_note' => $this->range_note,
            'state' => $this->state,
            'read_level' => (int) ($this->read_level ?? 0),
            'write_level' => (int) ($this->write_level ?? 0),
            'image' => $this->image,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
            'pivot' => $this->when($this->pivot !== null, fn () => [
                'level' => isset($this->pivot->level) ? (int) $this->pivot->level : null,
            ]),
            'createdBy' => $this->whenLoaded('createdBy'),
            'specializations' => $this->whenLoaded('specializations'),
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'publish' => $user ? $user->can('publish', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }
}
