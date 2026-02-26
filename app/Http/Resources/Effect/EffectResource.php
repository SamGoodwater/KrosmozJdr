<?php

declare(strict_types=1);

namespace App\Http\Resources\Effect;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EffectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $subEffects = $this->whenLoaded('subEffects');
        $withPivot = $subEffects && $this->relationLoaded('subEffects')
            ? $this->subEffects->map(fn ($sub) => [
                'id' => $sub->id,
                'slug' => $sub->slug,
                'type_slug' => $sub->type_slug,
                'template_text' => $sub->template_text,
                'formula' => $sub->formula,
                'order' => $sub->pivot->order ?? 0,
                'scope' => $sub->pivot->scope ?? 'general',
                'value_min' => $sub->pivot->value_min,
                'value_max' => $sub->pivot->value_max,
                'dice_num' => $sub->pivot->dice_num,
                'dice_side' => $sub->pivot->dice_side,
                'params' => $sub->pivot->params,
            ])->values()->all()
            : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'effect_group_id' => $this->effect_group_id,
            'degree' => $this->degree,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'sub_effects' => $withPivot ?? SubEffectResource::collection($this->whenLoaded('subEffects')),
        ];
    }
}
