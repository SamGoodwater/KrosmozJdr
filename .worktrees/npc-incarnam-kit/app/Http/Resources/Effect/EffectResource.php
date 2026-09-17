<?php

declare(strict_types=1);

namespace App\Http\Resources\Effect;

use App\Models\Effect;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EffectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $degrees = $this->whenLoaded('degrees', function () {
            return $this->degrees->map(fn ($d) => [
                'id' => $d->id,
                'degree' => $d->degree,
                'slug' => $d->slug,
                'area' => $d->area,
                'required_creature_level' => $d->required_creature_level,
                'config_signature' => $d->config_signature,
            ])->values()->all();
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'target_type' => $this->target_type ?? Effect::TARGET_DIRECT,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'degrees' => $degrees,
        ];
    }
}
