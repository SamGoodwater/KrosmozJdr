<?php

namespace App\Support\EntityPermissions;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

/**
 * EntityPermissionService
 *
 * @description
 * Construit un tableau de permissions "globales" (par entité) à exposer au frontend.
 * Exemple: permissions['entities']['resources']['updateAny'] = true.
 *
 * Cache:
 * - en cache applicatif par utilisateur pour éviter les recalculs inutiles
 * - suffixe **`r{révision}`** : incrémenté après un changement global (ex. matrice « Gérer l’affichage »)
 * pour que les anciennes entrées TTL-out sans avoir à tout vider ni lister tous les utilisateurs.
 * - la source de vérité reste les Policies (Gate::can)
 */
class EntityPermissionService
{
    /**
     * Clé de révision globale : quand elle change, une nouvelle clé de cache par utilisateur est utilisée.
     */
    public const PERMISSIONS_CACHE_REVISION_KEY = 'permissions.entities.cache_revision';

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
     * @return array<string, mixed>
     */
    public function forUser(?User $user): array
    {
        // Non connecté :
        // - on expose au minimum `viewAny` par entité (utile pour masquer/afficher des colonnes côté front)
        // - les autres permissions restent à false
        if (! $user) {
            /** @var array<string, class-string> $registry */
            $registry = (array) Config::get('entity-permissions', []);
            /** @var array<string, array<int, array{entity?: string, ability?: string}>> $accessRegistry */
            $accessRegistry = (array) Config::get('access-permissions', []);

            $entities = [];
            foreach ($registry as $entityType => $modelClass) {
                $entities[$entityType] = [
                    'viewAny' => Gate::allows('viewAny', $modelClass),
                    'create' => false,
                    'createAny' => false,
                    'updateAny' => false,
                    'deleteAny' => false,
                    'manageAny' => false,
                ];
            }

            $access = [];
            foreach ($accessRegistry as $accessKey => $rules) {
                $allowed = false;
                foreach ((array) $rules as $rule) {
                    $entityType = (string) ($rule['entity'] ?? '');
                    $ability = (string) ($rule['ability'] ?? '');
                    if (! $entityType || ! $ability) {
                        continue;
                    }
                    $modelClass = $registry[$entityType] ?? null;
                    if (! $modelClass) {
                        continue;
                    }
                    if (Gate::allows($ability, $modelClass)) {
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
        }

        $revision = self::currentCacheRevision();

        return Cache::remember(self::cacheKeyForUserId($user->id, $revision), self::CACHE_TTL_SECONDS, function () use ($user) {
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
                    if (! $entityType || ! $ability) {
                        continue;
                    }
                    $modelClass = $registry[$entityType] ?? null;
                    if (! $modelClass) {
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
     *
     * @description
     * Efface les entrées suffixées révision pour les valeurs \[révision − 50 ; révision courante\] (boucle courte).
     */
    public function forgetForUser(User $user): void
    {
        $rev = self::currentCacheRevision();
        $min = max(0, $rev - 50);
        for ($r = $min; $r <= $rev; $r++) {
            Cache::forget(self::cacheKeyForUserId($user->id, $r));
        }
    }

    /**
     * Après modification de la révision globale, les anciennes clés TTL-out (10 min max).
     */
    public static function bumpPermissionsCacheRevision(): void
    {
        $v = (int) Cache::get(self::PERMISSIONS_CACHE_REVISION_KEY, 0);
        Cache::forever(self::PERMISSIONS_CACHE_REVISION_KEY, $v + 1);
    }

    private static function currentCacheRevision(): int
    {
        return max(0, (int) Cache::get(self::PERMISSIONS_CACHE_REVISION_KEY, 0));
    }

    private static function cacheKeyForUserId(int $userId, int $revision): string
    {
        return self::CACHE_PREFIX.$userId.'.r'.$revision;
    }
}
