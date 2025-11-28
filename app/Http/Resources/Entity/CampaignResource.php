<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Campaign.
 */
class CampaignResource extends JsonResource
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
            'slug' => $this->slug,
            'keyword' => $this->keyword,
            'is_public' => $this->is_public,
            'state' => $this->state,
            'usable' => $this->usable,
            'is_visible' => $this->is_visible,
            'image' => $this->image,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Relations
            'createdBy' => $this->whenLoaded('createdBy'),
            'users' => $this->whenLoaded('users'),
            'scenarios' => $this->whenLoaded('scenarios'),
            'pages' => $this->whenLoaded('pages'),
            'items' => $this->whenLoaded('items'),
            'consumables' => $this->whenLoaded('consumables'),
            'resources' => $this->whenLoaded('resources'),
            'shops' => $this->whenLoaded('shops'),
            'npcs' => $this->whenLoaded('npcs'),
            'monsters' => $this->whenLoaded('monsters'),
            'spells' => $this->whenLoaded('spells'),
            'panoplies' => $this->whenLoaded('panoplies'),
            'files' => $this->whenLoaded('files'),

            // Droits d'accès
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }
}

