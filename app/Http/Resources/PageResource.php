<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'is_public' => $this->is_public,
            'is_visible' => $this->is_visible,
        ];

        if ($this->is_public || $request->user()) {
            $data['sections'] = SectionResource::collection($this->whenLoaded('sections'));
        }

        if ($request->user()) {
            $data['can'] = [
                'update' => $request->user()->can('update', $this->resource),
                'delete' => $request->user()->can('delete', $this->resource)
            ];
        }

        return $data;
    }
}
