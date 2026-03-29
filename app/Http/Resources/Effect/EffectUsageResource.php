<?php

declare(strict_types=1);

namespace App\Http\Resources\Effect;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EffectUsageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $row = [
            'id' => $this->id,
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'effect_degree_id' => $this->effect_degree_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
        if ($this->relationLoaded('effectDegree') && $this->effectDegree) {
            $row['effect_degree'] = (new ResolvedEffectDegreeResource($this->effectDegree))->toArray($request);
        }

        return $row;
    }
}
