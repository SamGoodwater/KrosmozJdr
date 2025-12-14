<?php

namespace App\Http\Resources\Type;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour le type ResourceType.
 */
class ResourceTypeResource extends JsonResource
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
            'usable' => $this->usable,
            'is_visible' => $this->is_visible,

            // Registry DofusDB
            'dofusdb_type_id' => $this->dofusdb_type_id,
            'decision' => $this->decision,
            'seen_count' => $this->seen_count,
            'last_seen_at' => $this->last_seen_at?->toISOString(),

            // Aggregats
            'resources_count' => $this->when(isset($this->resources_count), $this->resources_count),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Permissions (pour EntityTable)
            'can' => [
                'view' => $user ? $user->can('view', $this->resource) : false,
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
            ],
        ];
    }
}


