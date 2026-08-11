<?php

namespace App\Http\Resources\Entity;

use App\Services\Effect\SpellEffectDefinitionsSerializer;
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
            'po' => $this->po_display,
            'po_min' => $this->po_min,
            'po_max' => $this->po_max,
            'po_editable' => $this->po_editable,
            'pa' => $this->pa,
            'casting_time' => $this->casting_time,
            'ritual_available' => $this->ritual_available,
            'cast_per_turn' => $this->cast_per_turn,
            'cast_per_target' => $this->cast_per_target,
            'sight_line' => $this->sight_line,
            'cast_in_line' => (bool) ($this->cast_in_line ?? false),
            'cast_in_diagonal' => (bool) ($this->cast_in_diagonal ?? false),
            'target_type' => $this->target_type,
            'max_stack' => (int) ($this->max_stack ?? 0),
            'global_cooldown' => (int) ($this->global_cooldown ?? 0),
            'number_between_two_cast' => $this->number_between_two_cast,
            'duration' => $this->duration,
            'element' => $this->element,
            'category' => $this->category,
            'is_magic' => $this->is_magic,
            'powerful' => $this->powerful,
            'resolution_mode' => $this->resolution_mode,
            'attack_characteristic_key' => $this->attack_characteristic_key,
            'save_characteristic_key' => $this->save_characteristic_key,
            'save_dc_formula' => $this->save_dc_formula,
            'save_success_note' => $this->save_success_note,
            'auto_success_if_willing_target' => (bool) ($this->auto_success_if_willing_target ?? false),
            'allows_reaction' => (bool) ($this->allows_reaction ?? false),
            'is_ritual' => $this->when(
                array_key_exists('is_ritual', $this->resource->getAttributes()),
                (bool) $this->resource->getAttribute('is_ritual')
            ),
            'state' => $this->state,
            'read_level' => (int) ($this->read_level ?? 0),
            'write_level' => (int) ($this->write_level ?? 0),
            'image' => $this->image,
            'auto_update' => $this->auto_update,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Relations
            'createdBy' => $this->whenLoaded('createdBy'),
            'creatures' => $this->whenLoaded('creatures'),
            'breeds' => ($this->relationLoaded('breeds') || isset($this->breeds)) ? $this->breeds->map(function ($breed) {
                return [
                    'id' => $breed->id,
                    'name' => $breed->name,
                    'description' => $breed->description,
                ];
            })->values()->all() : [],
            'scenarios' => $this->whenLoaded('scenarios'),
            'campaigns' => $this->whenLoaded('campaigns'),
            'spellTypes' => ($this->relationLoaded('spellTypes') || isset($this->spellTypes)) ? $this->spellTypes->map(function ($spellType) {
                return [
                    'id' => $spellType->id,
                    'name' => $spellType->name,
                    'description' => $spellType->description,
                    'color' => $spellType->color ?? null,
                    'icon' => $spellType->icon ?? null,
                ];
            })->values()->all() : [],
            // Legacy `spell_effects` retiré de la resource : canal canon = effects_definitions (Effect/Degree).
            'monsters' => $this->whenLoaded('monsters'),

            /** Définitions d’effets liées (pivot effect_spell) + degrés + pivots sous-effets — pour affichage fiche. */
            'effects_definitions' => $this->when(
                $this->relationLoaded('effects'),
                fn () => app(SpellEffectDefinitionsSerializer::class)->serialize($this->effects)
            ),

            // Droits d'accès
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'view' => $user ? $user->can('view', $this->resource) : false,
            ],
        ];
    }
}
