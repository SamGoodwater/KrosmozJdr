<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Spell.
 */
class SpellResource extends JsonResource
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
            'description' => $this->description,
            'effect' => $this->effect,
            'area' => $this->area,
            'level' => $this->level,
            'po' => $this->po,
            'po_editable' => $this->po_editable,
            'pa' => $this->pa,
            'cast_per_turn' => $this->cast_per_turn,
            'cast_per_target' => $this->cast_per_target,
            'sight_line' => $this->sight_line,
            'number_between_two_cast' => $this->number_between_two_cast,
            'number_between_two_cast_editable' => $this->number_between_two_cast_editable,
            'element' => $this->element,
            'category' => $this->category,
            'is_magic' => $this->is_magic,
            'powerful' => $this->powerful,
            'usable' => $this->usable,
            'is_visible' => $this->is_visible,
            'image' => $this->image,
            'auto_update' => $this->auto_update,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Relations
            'createdBy' => $this->whenLoaded('createdBy'),
            'creatures' => $this->whenLoaded('creatures'),
            'classes' => $this->whenLoaded('classes'),
            'scenarios' => $this->whenLoaded('scenarios'),
            'campaigns' => $this->whenLoaded('campaigns'),
            'spellTypes' => $this->whenLoaded('spellTypes'),
            'monsters' => $this->whenLoaded('monsters'),

            // Droits d'accès
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }
}

