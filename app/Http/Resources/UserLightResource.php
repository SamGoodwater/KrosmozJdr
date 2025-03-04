<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLightResource extends JsonResource
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
            'avatar' => $this->avatarPath() == User::DEFAULT_AVATAR ? null : $this->avatarPath(),
            'email_verified_at' => $this->email_verified_at,
            'is_verified' => $this->hasVerifiedEmail(),
            'light' => 1
        ];
    }
}
