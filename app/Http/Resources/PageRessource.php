<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Http\Resources\SectionResource;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentUser = $request->user();
        return [
            'name' => $this->name,
            'keyword' => $this->email,
            'slug' => $this->slug,
            'order_num' => $this->order_num,
            'is_dropdown' => $this->is_dropdown,
            'parrent_page' => $this->whenLoaded('page')->get(),
            'sections' => SectionResource::collection($this->whenLoaded('sections')->get()),
            'is_public' => $this->is_public,
            'is_visible' => $this->is_visible,
            // Si authentifié
            $this->mergeWhen($currentUser->verifyRole(User::ROLES['game_master']), [
                'is_editable' => $this->is_editable,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'uniqid' => $this->uniqid,
            ]),
        ];
    }
}
