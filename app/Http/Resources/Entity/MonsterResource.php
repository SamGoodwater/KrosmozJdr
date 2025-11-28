<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Monster.
 */
class MonsterResource extends JsonResource
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
            'official_id' => $this->official_id,
            'dofusdb_id' => $this->dofusdb_id,
            'dofus_version' => $this->dofus_version,
            'auto_update' => $this->auto_update,
            'size' => $this->size,
            'monster_race_id' => $this->monster_race_id,
            'is_boss' => $this->is_boss,
            'boss_pa' => $this->boss_pa,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relations
            'creature' => $this->whenLoaded('creature'),
            'monsterRace' => $this->whenLoaded('monsterRace'),
            'scenarios' => $this->whenLoaded('scenarios'),
            'campaigns' => $this->whenLoaded('campaigns'),
            'spellInvocations' => $this->whenLoaded('spellInvocations'),

            // Droits d'accès
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }
}

