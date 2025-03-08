<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'avatar' => $this->avatarPath() === asset(User::DEFAULT_AVATAR) ? null : $this->avatarPath(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relations (chargées uniquement si elles sont incluses dans la requête)
            'scenarios' => $this->whenLoaded('scenarios'),
            'campaigns' => $this->whenLoaded('campaigns'),

            // Attributs calculés
            'is_verified' => $this->hasVerifiedEmail(),
        ];
    }
}
