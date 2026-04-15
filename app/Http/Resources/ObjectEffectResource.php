<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ObjectEffect;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ObjectEffect
 */
class ObjectEffectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ObjectEffect $effect */
        $effect = $this->resource;

        return [
            'id' => $effect->id,
            'action' => $effect->action->value,
            'action_label' => $effect->action->label(),
            'characteristic_id' => $effect->characteristic_id,
            'monster_id' => $effect->monster_id,
            'value' => $effect->value,
            'characteristic' => $this->whenLoaded('characteristic', fn () => $effect->characteristic ? [
                'id' => $effect->characteristic->id,
                'key' => $effect->characteristic->key,
                'name' => $effect->characteristic->name,
                'short_name' => $effect->characteristic->short_name,
            ] : null),
            'monster' => $this->whenLoaded('monster', fn () => $effect->monster ? [
                'id' => $effect->monster->id,
                'name' => $effect->monster->creature?->name ?? ('Monstre #'.$effect->monster->id),
            ] : null),
        ];
    }
}
