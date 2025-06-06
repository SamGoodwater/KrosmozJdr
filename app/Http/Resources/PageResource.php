<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Page.
 *
 * Structure et expose les champs principaux, relations et droits d'accès pour le frontend/API.
 * Permet d'inclure dynamiquement les relations si chargées.
 */
class PageResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'is_visible' => $this->is_visible,
            'in_menu' => $this->in_menu,
            'state' => $this->state,
            'parent_id' => $this->parent_id,
            'menu_order' => $this->menu_order,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relations (chargées uniquement si incluses)
            'parent' => $this->whenLoaded('parent'),
            'children' => $this->whenLoaded('children'),
            'users' => $this->whenLoaded('users'),
            'sections' => $this->whenLoaded('sections'),
            'campaigns' => $this->whenLoaded('campaigns'),
            'scenarios' => $this->whenLoaded('scenarios'),
            'createdBy' => $this->whenLoaded('createdBy'),

            // Droits d'accès pour l'utilisateur courant
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'forceDelete' => $user ? $user->can('forceDelete', $this->resource) : false,
                'restore' => $user ? $user->can('restore', $this->resource) : false,
            ],
        ];
    }
}
