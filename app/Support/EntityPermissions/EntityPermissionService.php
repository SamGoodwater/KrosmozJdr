<?php

namespace App\Support\EntityPermissions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use App\Models\User;

/**
 * EntityPermissionService
 *
 * @description
 * Construit un tableau de permissions "globales" (par entité) à exposer au frontend.
 * Exemple: permissions['entities']['resources']['updateAny'] = true.
 *
 * Cache:
 * - en cache applicatif par user pour éviter les recalculs inutiles
 * - la source de vérité reste les Policies (Gate::can)
 */
class EntityPermissionService
{
    /**
     * @var string
     */
    private const CACHE_PREFIX = 'permissions.entities.user.';

    /**
     * @var int secondes (10 min)
     */
    private const CACHE_TTL_SECONDS = 600;

    /**
     * Retourne les permissions globales par entité pour un utilisateur.
     *
     * @param User|null $user
     * @return array<string, mixed>
     */
    public function forUser(?User $user): array
    {
        // Non connecté : on expose une structure vide (le front s'appuie aussi sur `auth.isLogged`)
        if (!$user) {
            return [
                'entities' => [],
                'access' => [],
            ];
        }

        $cacheKey = self::CACHE_PREFIX . $user->id;

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($user) {
            /** @var array<string, class-string> $registry */
            $registry = (array) Config::get('entity-permissions', []);
            /** @var array<string, array<int, array{entity?: string, ability?: string}>> $accessRegistry */
            $accessRegistry = (array) Config::get('access-permissions', []);

            $entities = [];
            foreach ($registry as $entityType => $modelClass) {
                $entities[$entityType] = [
                    // "read"
                    'viewAny' => $user->can('viewAny', $modelClass),
                    // "add"
                    'create' => $user->can('create', $modelClass),
                    'createAny' => $user->can('createAny', $modelClass),
                    // "update"
                    'updateAny' => $user->can('updateAny', $modelClass),
                    // "delete"
                    'deleteAny' => $user->can('deleteAny', $modelClass),
                    // "admin/maintenance"
                    'manageAny' => $user->can('manageAny', $modelClass),
                ];
            }

            // Permissions d'accès UI (anyOf)
            $access = [];
            foreach ($accessRegistry as $accessKey => $rules) {
                $allowed = false;
                foreach ((array) $rules as $rule) {
                    $entityType = (string) ($rule['entity'] ?? '');
                    $ability = (string) ($rule['ability'] ?? '');
                    if (!$entityType || !$ability) {
                        continue;
                    }
                    $modelClass = $registry[$entityType] ?? null;
                    if (!$modelClass) {
                        continue;
                    }
                    if ($user->can($ability, $modelClass)) {
                        $allowed = true;
                        break;
                    }
                }
                $access[$accessKey] = $allowed;
            }

            return [
                'entities' => $entities,
                'access' => $access,
            ];
        });
    }

    /**
     * Invalide le cache de permissions d'un utilisateur.
     */
    public function forgetForUser(User $user): void
    {
        Cache::forget(self::CACHE_PREFIX . $user->id);
    }
}


