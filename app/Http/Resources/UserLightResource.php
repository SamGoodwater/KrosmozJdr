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
 * @property array<int, string> $notification_channels
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
        /** @var \App\Models\User $user */
        $user = $this->resource;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'role_name' => $user->role_name,
            'is_admin' => $user->isAdmin(),
            'is_super_admin' => $user->isSuperAdmin(),
            'is_game_master' => $user->isGameMaster(),
            'avatar' => $user->avatarPath(),
            'notifications_enabled' => $user->notifications_enabled,
            'notification_channels' => $user->notification_channels,
        ];
    }
}
