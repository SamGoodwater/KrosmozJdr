<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Classe.
 */
class ClasseResource extends JsonResource
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
            'official_id' => $this->official_id,
            'dofusdb_id' => $this->dofusdb_id,
            'name' => $this->name,
            'description_fast' => $this->description_fast,
            'description' => $this->description,
            'life' => $this->life,
            'life_dice' => $this->life_dice,
            'specificity' => $this->specificity,
            'dofus_version' => $this->dofus_version,
            'state' => $this->state,
            'read_level' => (int) ($this->read_level ?? 0),
            'write_level' => (int) ($this->write_level ?? 0),
            'image' => $this->image,
            'icon' => $this->icon,
            'auto_update' => $this->auto_update,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Relations
            'createdBy' => $this->whenLoaded('createdBy'),
            'npcs' => $this->whenLoaded('npcs'),
            'spells' => $this->whenLoaded('spells'),

            // Droits d'accès
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }
}

