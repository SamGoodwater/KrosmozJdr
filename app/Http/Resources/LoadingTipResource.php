<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour les astuces d’écran de chargement (admin + payload public).
 */
class LoadingTipResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'url' => $this->url,
            'featured' => (bool) $this->featured,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
