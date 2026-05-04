<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource légère pour le référentiel des langues (M2M breed / monster / …).
 */
class LanguageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $pivot = $this->resource?->pivot;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'sort_order' => $pivot ? (int) ($pivot->sort_order ?? 0) : 0,
        ];
    }
}
