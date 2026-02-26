<?php

declare(strict_types=1);

namespace App\Http\Resources\Effect;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EffectUsageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'effect_id' => $this->effect_id,
            'level_min' => $this->level_min,
            'level_max' => $this->level_max,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'effect' => new EffectResource($this->whenLoaded('effect')),
        ];
    }
}
