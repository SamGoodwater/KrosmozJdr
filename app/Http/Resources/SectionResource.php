<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Section.
 *
 * Structure et expose les champs principaux, relations et droits d'accès pour le frontend/API.
 * Permet d'inclure dynamiquement les relations si chargées.
 */
class SectionResource extends JsonResource
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
            'page_id' => $this->page_id,
            'order' => $this->order,
            'type' => $this->type,
            'params' => $this->params,
            'is_visible' => $this->is_visible,
            'state' => $this->state,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relations (chargées uniquement si incluses)
            'page' => $this->whenLoaded('page'),
            'users' => $this->whenLoaded('users'),
            'files' => $this->whenLoaded('files'),
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
