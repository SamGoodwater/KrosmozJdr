<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Ressource légère pour les données utilisateur partagées avec Inertia
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int $role
 * @property string|null $avatar
 * @property bool $notifications_enabled
 * @property array $notification_channels
 */
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
            'role_name' => $this->role_name,
            'avatar' => $this->avatarPath(),
            'notifications_enabled' => $this->notifications_enabled,
            'notification_channels' => $this->notification_channels,
        ];
    }
}
