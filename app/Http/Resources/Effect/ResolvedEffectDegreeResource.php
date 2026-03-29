<?php

declare(strict_types=1);

namespace App\Http\Resources\Effect;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Représente un {@see \App\Models\EffectDegree} actif (aperçu / API for-entity).
 */
class ResolvedEffectDegreeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\EffectDegree $deg */
        $deg = $this->resource;
        $deg->loadMissing('effect');

        return [
            'id' => $deg->effect?->id,
            'effect_degree_id' => $deg->id,
            'name' => $deg->effect?->name,
            'slug' => $deg->effect?->slug,
            'description' => $deg->effect?->description,
            'target_type' => $deg->effect?->target_type ?? \App\Models\Effect::TARGET_DIRECT,
            'degree' => $deg->degree,
            'area' => $deg->area,
            'required_creature_level' => $deg->required_creature_level,
        ];
    }
}
