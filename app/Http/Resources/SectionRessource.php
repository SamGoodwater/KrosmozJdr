<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class SectionRessource extends JsonResource
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
            'uniqid' => $this->uniqid,
            'component' => $this->component,
            'title' => $this->title,
            'content' => $this->content,
            'order_num' => $this->order_num,
            'is_visible' => $this->is_visible,
            'page_id' => $this->page_id,
            'is_visible' => $this->is_visible,
            // Si authentifié
            $this->mergeWhen($currentUser->verifyRole(User::ROLES['game_master']), [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'uniqid' => $this->uniqid,
            ]),
        ];
    }
}
