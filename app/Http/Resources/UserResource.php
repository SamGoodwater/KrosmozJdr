<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

/**
 * Resource API/Frontend pour l'entité User.
 *
 * - Masque les champs sensibles
 * - Retourne toujours une URL d'avatar (jamais null)
 * - Expose les préférences de notification
 * - Ajoute les droits d'accès (can) pour l'utilisateur courant
 * - Permet d'inclure les relations si chargées (scenarios, campaigns, pages, sections)
 *
 * Champs exposés :
 * - id, name, email, role, avatar, notifications_enabled, notification_channels, created_at, updated_at
 * - is_verified (email vérifié)
 * - can (droits d'accès)
 * - scenarios, campaigns, pages, sections (si chargés)
 */
class UserResource extends JsonResource
{
    /**
     * Transforme la ressource User en tableau pour l'API/frontend.
     *
     * @param Request $request
     * @return array<string, mixed> Données exposées pour le frontend/API
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'avatar' => $this->avatarPath(), // Toujours une URL
            'notifications_enabled' => $this->notifications_enabled,
            'notification_channels' => $this->notification_channels,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relations (chargées uniquement si incluses)
            'scenarios' => $this->whenLoaded('scenarios'),
            'campaigns' => $this->whenLoaded('campaigns'),
            'pages' => $this->whenLoaded('pages'),
            'sections' => $this->whenLoaded('sections'),

            // Attributs calculés
            'is_verified' => $this->hasVerifiedEmail(),

            // Droits d'accès pour l'utilisateur courant
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'forceDelete' => $user ? $user->can('forceDelete', $this->resource) : false,
                'restore' => $user ? $user->can('restore', $this->resource) : false,
            ],
        ];
    }
}
