<?php

namespace App\Http\Resources\Entity;

use App\Models\Entity\Spell;
use App\Services\Effect\SpellNestedPreviewSerializer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource API/Frontend pour l'entité Npc.
 */
class NpcResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id' => $this->id,
            'creature_id' => $this->creature_id,
            'story' => $this->story,
            'historical' => $this->historical,
            'age' => $this->age,
            'size' => $this->size,
            'npc_role' => $this->npc_role,
            'breed_id' => $this->breed_id,
            'specialization_id' => $this->specialization_id,
            'state' => $this->state,
            'read_level' => $this->read_level,
            'write_level' => $this->write_level,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'creature' => $this->whenLoaded('creature', function () {
                $creature = $this->creature;
                if ($creature?->relationLoaded('spells')) {
                    $serializer = app(SpellNestedPreviewSerializer::class);
                    foreach ($creature->spells as $spell) {
                        if ($spell instanceof Spell) {
                            $serializer->decorate($spell);
                        }
                    }
                }

                return $creature;
            }),
            'breed' => $this->whenLoaded('breed'),
            'specialization' => $this->whenLoaded('specialization'),
            'panoplies' => $this->whenLoaded('panoplies'),
            'scenarios' => $this->whenLoaded('scenarios'),
            'campaigns' => $this->whenLoaded('campaigns'),
            'shop' => $this->whenLoaded('shop'),
            'languages' => $this->whenLoaded('languages', fn () => LanguageResource::collection($this->languages)->resolve($request)),

            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }
}
