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
            'role_name' => $this->role_name, // Nom du rôle (chaîne)
            'avatar' => $this->avatarPath(), // Toujours une URL
            'avatar_is_default' => $this->avatar === null, // Indique si l'avatar est par défaut
            'notifications_enabled' => $this->notifications_enabled,
            'notification_channels' => $this->notification_channels,
            'notification_preferences' => $this->notification_preferences ?? [],
            'last_login_at' => $this->last_login_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Relations (chargées uniquement si incluses)
            'scenarios' => $this->whenLoaded('scenarios'),
            'campaigns' => $this->whenLoaded('campaigns'),
            'pages' => $this->whenLoaded('pages'),
            'sections' => $this->whenLoaded('sections'),
            'oauth_accounts' => $this->whenLoaded('oauthAccounts', fn () => $this->oauthAccounts->map(fn ($a) => [
                'provider' => $a->provider,
                'provider_name' => $a->provider_name,
                'avatar_url' => $a->avatar_url,
            ])->values()->all()),

            // Attributs calculés
            'is_verified' => $this->hasVerifiedEmail(),
            'has_password' => $this->hasPassword(),

            // Droits d'accès pour l'utilisateur courant
            'can' => [
                'update' => $user ? $user->can('update', $this->resource) : false,
                'delete' => $user ? $user->can('delete', $this->resource) : false,
                'forceDelete' => $user ? $user->can('forceDelete', $this->resource) : false,
                'restore' => $user ? $user->can('restore', $this->resource) : false,
                'updateRole' => $user ? $user->can('updateRole', $this->resource) : false,
                'resetPassword' => $user ? $user->can('resetPassword', $this->resource) : false,
            ],
        ];
    }
}
