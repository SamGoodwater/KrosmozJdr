<?php

declare(strict_types=1);

namespace App\Services\EntityDisplay;

use App\Enums\EntityState;
use App\Models\ApplicationSetting;
use App\Models\Entity\Creature;
use App\Models\Page;
use App\Models\User;
use App\Policies\Entity\BaseEntityPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

/**
 * Rôle minimal (valeur {@see User::$role}) pour voir une entité dans un état donné,
 * par type d’entité (`entity-permissions`). Les admins court-circuitent toujours dans les policies.
 *
 * @example
 * $svc->minimumRoleForView('monsters', 'playable'); // 0 = invités possibles selon la suite policy
 */
final class EntityDisplayVisibilityService
{
    public const SETTINGS_KEY = 'entity_display_visibility_rules';

    private const CACHE_KEY = 'entity_display_visibility_rules.resolved';

    private const CACHE_TTL_SECONDS = 3600;

    /**
     * @return array<string, array<string, int>>
     */
    public function mergedRules(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            $stored = ApplicationSetting::query()
                ->where('key', self::SETTINGS_KEY)
                ->value('value');

            /** @var array<string, array<string, int>> $storedRules */
            $storedRules = is_array($stored) ? $stored : [];

            return $this->mergeWithBuiltinDefaults($storedRules);
        });
    }

    public function forgetRulesCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Types d’entité éditables dans la matrice admin (hors utilisateurs, pages CMS, créatures).
     *
     * @return list<string>
     */
    public function manageableEntityPermissionKeys(): array
    {
        /** @var array<string, class-string> $registry */
        $registry = (array) Config::get('entity-permissions', []);

        $excludedModels = [
            User::class,
            Page::class,
            Creature::class,
        ];

        $keys = [];
        foreach ($registry as $key => $class) {
            if (in_array($class, $excludedModels, true)) {
                continue;
            }
            $keys[] = $key;
        }

        sort($keys);

        return $keys;
    }

    /**
     * Nettoie le JSON stocké : uniquement entités autorisées et états connus.
     *
     * @param  array<string, mixed>  $rules
     * @return array<string, array<string, int>>
     */
    public function sanitizeStoredPayload(array $rules): array
    {
        $allowedEntities = array_flip($this->manageableEntityPermissionKeys());
        $allowedStates = array_flip(EntityState::values());

        $sanitized = [];
        foreach ($rules as $entityKey => $perState) {
            if (! is_string($entityKey) || ! isset($allowedEntities[$entityKey]) || ! is_array($perState)) {
                continue;
            }
            foreach ($perState as $state => $minRole) {
                $stateStr = is_string($state) ? strtolower(trim($state)) : '';
                if (! isset($allowedStates[$stateStr])) {
                    continue;
                }
                $sanitized[$entityKey][$stateStr] = max(
                    User::ROLE_GUEST,
                    min(User::ROLE_SUPER_ADMIN, (int) $minRole)
                );
            }
        }

        return $sanitized;
    }

    /**
     * Matrice effective (valeurs utilisées par les policies) pour les lignes éditables.
     *
     * @return array<string, array<string, int>>
     */
    public function matrixForManageableEntities(): array
    {
        $matrix = [];
        foreach ($this->manageableEntityPermissionKeys() as $entityKey) {
            foreach (EntityState::values() as $state) {
                $matrix[$entityKey][$state] = $this->minimumRoleForView($entityKey, $state);
            }
        }

        return $matrix;
    }

    /**
     * Clé registry `entity-permissions` pour le modèle, ou null si inconnu.
     */
    public function permissionKeyForModel(Model $model): ?string
    {
        /** @var array<string, class-string> $registry */
        $registry = (array) Config::get('entity-permissions', []);

        $class = $model::class;

        foreach ($registry as $key => $modelClass) {
            if ($modelClass === $class) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Niveau de rôle minimal pour voir une ligne dans cet état (inclusif).
     */
    public function minimumRoleForView(string $entityPermissionKey, string $state): int
    {
        $rules = $this->mergedRules();
        $stateKey = $this->normalizeState($state);

        if (isset($rules[$entityPermissionKey][$stateKey])) {
            return (int) $rules[$entityPermissionKey][$stateKey];
        }

        if (isset($rules['*'][$stateKey])) {
            return (int) $rules['*'][$stateKey];
        }

        return $this->builtinDefaultForState($stateKey);
    }

    public function viewerMeetsMinimumRole(?User $user, string $entityPermissionKey, string $state): bool
    {
        $level = $user !== null ? (int) ($user->role ?? 0) : User::ROLE_GUEST;

        return $level >= $this->minimumRoleForView($entityPermissionKey, $state);
    }

    /**
     * Restreint une requête liste pour qu’elle ne renvoie que les lignes visibles
     * selon {@see BaseEntityPolicy::view} (admin, auteur, matrice, read/write_level).
     *
     * @param  Builder<Model>  $query
     *
     * @example
     * $svc->constrainQueryToViewer(Spell::query(), $request->user(), 'spells');
     */
    public function constrainQueryToViewer(Builder $query, ?User $user, string $entityPermissionKey): void
    {
        if ($user?->isAdmin()) {
            return;
        }

        $role = $user !== null ? (int) ($user->role ?? 0) : User::ROLE_GUEST;
        $minRaw = $this->minimumRoleForView($entityPermissionKey, EntityState::Raw->value);
        $minDraft = $this->minimumRoleForView($entityPermissionKey, EntityState::Draft->value);
        $minAuto = $this->minimumRoleForView($entityPermissionKey, EntityState::Auto->value);
        $minPlayable = $this->minimumRoleForView($entityPermissionKey, EntityState::Playable->value);
        $minArchived = $this->minimumRoleForView($entityPermissionKey, EntityState::Archived->value);

        // Ne pas utiliser isFillable(): Model::unguard() le rend toujours vrai.
        // Monstres / PNJ : pas de created_by sur la table (auteur via créature liée).
        $hasCreatedBy = Schema::hasColumn($query->getModel()->getTable(), 'created_by');

        // Qualifier les colonnes : un JOIN (ex. monstres + créatures, mêmes `state`/`id`)
        // rendrait `where state` ambigu et casserait le catalogue pour les non-admins.
        $stateCol = $query->qualifyColumn('state');
        $readLevelCol = $query->qualifyColumn('read_level');
        $writeLevelCol = $query->qualifyColumn('write_level');
        $createdByCol = $query->qualifyColumn('created_by');

        $query->where(function (Builder $outer) use ($user, $role, $minRaw, $minDraft, $minAuto, $minPlayable, $minArchived, $hasCreatedBy, $stateCol, $readLevelCol, $writeLevelCol, $createdByCol): void {
            // Base fausse : sans branche OR, aucune ligne ne fuit.
            $outer->whereRaw('0 = 1');

            if ($user !== null && $hasCreatedBy) {
                $outer->orWhere($createdByCol, $user->id);
            }

            if ($role >= $minPlayable) {
                $outer->orWhere(function (Builder $q) use ($role, $stateCol, $readLevelCol): void {
                    $q->where($stateCol, EntityState::Playable->value)
                        ->where($readLevelCol, '<=', $role);
                });
            }

            if ($role >= $minArchived) {
                $outer->orWhere(function (Builder $q) use ($role, $stateCol, $readLevelCol): void {
                    $q->where($stateCol, EntityState::Archived->value)
                        ->where($readLevelCol, '<=', $role);
                });
            }

            if ($user !== null && $role >= $minRaw) {
                $outer->orWhere(function (Builder $q) use ($role, $stateCol, $writeLevelCol): void {
                    $q->where($stateCol, EntityState::Raw->value)
                        ->where($writeLevelCol, '<=', $role);
                });
            }

            if ($user !== null && $role >= $minDraft) {
                $outer->orWhere(function (Builder $q) use ($role, $stateCol, $writeLevelCol): void {
                    $q->where($stateCol, EntityState::Draft->value)
                        ->where($writeLevelCol, '<=', $role);
                });
            }

            if ($user !== null && $role >= $minAuto) {
                $outer->orWhere(function (Builder $q) use ($role, $stateCol, $writeLevelCol): void {
                    $q->where($stateCol, EntityState::Auto->value)
                        ->where($writeLevelCol, '<=', $role);
                });
            }
        });
    }

    /**
     * @param  array<string, mixed>  $stored
     * @return array<string, array<string, int>>
     */
    private function mergeWithBuiltinDefaults(array $stored): array
    {
        $merged = [
            '*' => [
                EntityState::Raw->value => User::ROLE_GAME_MASTER,
                EntityState::Draft->value => User::ROLE_GAME_MASTER,
                EntityState::Auto->value => User::ROLE_GAME_MASTER,
                EntityState::Playable->value => User::ROLE_GUEST,
                EntityState::Archived->value => User::ROLE_ADMIN,
            ],
        ];

        foreach ($stored as $entityKey => $perState) {
            if (! is_string($entityKey) || ! is_array($perState)) {
                continue;
            }
            foreach ($perState as $state => $minRole) {
                $normalizedState = $this->normalizeState((string) $state);
                $merged[$entityKey][$normalizedState] = (int) $minRole;
            }
        }

        return $merged;
    }

    private function normalizeState(string $state): string
    {
        $s = strtolower(trim($state));

        return $s === '' ? 'draft' : $s;
    }

    private function builtinDefaultForState(string $stateKey): int
    {
        return match ($stateKey) {
            'playable' => User::ROLE_GUEST,
            'raw', 'draft', 'auto' => User::ROLE_GAME_MASTER,
            'archived' => User::ROLE_ADMIN,
            default => User::ROLE_GAME_MASTER,
        };
    }
}
