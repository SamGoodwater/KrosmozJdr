<?php

declare(strict_types=1);

namespace App\Http\Resources\Effect;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubEffectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'type_slug' => $this->type_slug,
            'template_text' => $this->template_text,
            'formula' => $this->formula,
            'variables_allowed' => $this->variables_allowed,
            'dofusdb_effect_id' => $this->dofusdb_effect_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
