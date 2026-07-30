<?php

declare(strict_types=1);

namespace App\Services\Entity;

use App\Models\User;
use App\Services\AdminActivityLogger;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * Parcours unique de suppression/restauration des entités JDR.
 *
 * @example app(EntityDeletionService::class)->softDelete($spell, auth()->user())
 */
class EntityDeletionService
{
    public function __construct(
        private readonly AdminActivityLogger $activityLogger
    ) {}

    public function softDelete(Model $entity, User $actor): void
    {
        Gate::forUser($actor)->authorize('delete', $entity);

        DB::transaction(function () use ($entity): void {
            $entity->delete();
        });
        $this->activityLogger->logEntity($entity, 'deleted', $actor);
        NotificationService::notifyEntityDeleted($entity, $actor);
    }

    public function restore(Model $entity, User $actor): void
    {
        Gate::forUser($actor)->authorize('restore', $entity);

        DB::transaction(function () use ($entity): void {
            if (method_exists($entity, 'restore')) {
                $entity->restore();
            }
        });
        $this->activityLogger->logEntity($entity, 'restored', $actor);
        NotificationService::notifyEntityRestored($entity, $actor);
    }

    public function forceDelete(Model $entity, User $actor): void
    {
        Gate::forUser($actor)->authorize('forceDelete', $entity);

        if (method_exists($entity, 'trashed') && ! $entity->trashed()) {
            throw ValidationException::withMessages([
                'entity' => 'Cette entité doit d’abord être placée en corbeille avant suppression définitive.',
            ]);
        }

        DB::transaction(function () use ($entity, $actor): void {
            $impact = $this->impactSummary($entity);
            $this->detachBelongsToManyRelations($entity);
            $this->deleteMedia($entity);

            if (method_exists($entity, 'forceDelete')) {
                $entity->forceDelete();
            } else {
                $entity->delete();
            }

            $this->activityLogger->logEntity($entity, 'force_deleted', $actor, $impact);
        });
        NotificationService::notifyEntityForceDeleted($entity, $actor);
    }

    /**
     * @return array{relations: list<string>, media_count: int}
     */
    public function impactSummary(Model $entity): array
    {
        return [
            'relations' => $this->belongsToManyRelationNames($entity),
            'media_count' => method_exists($entity, 'media') ? (int) $entity->media()->count() : 0,
        ];
    }

    private function detachBelongsToManyRelations(Model $entity): void
    {
        foreach ($this->belongsToManyRelationNames($entity) as $relationName) {
            $relation = $entity->{$relationName}();
            if ($relation instanceof BelongsToMany) {
                $relation->detach();
            }
        }
    }

    private function deleteMedia(Model $entity): void
    {
        if (! method_exists($entity, 'media')) {
            return;
        }

        $entity->media()->get()->each->delete();
    }

    /**
     * @return list<string>
     */
    private function belongsToManyRelationNames(Model $entity): array
    {
        $names = [];
        $ref = new \ReflectionClass($entity);
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getNumberOfParameters() > 0 || $method->isStatic()) {
                continue;
            }

            $name = $method->getName();
            if ($this->shouldSkipMethod($name)) {
                continue;
            }

            try {
                $relation = $entity->{$name}();
            } catch (\Throwable) {
                continue;
            }

            if ($relation instanceof BelongsToMany) {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }

    private function shouldSkipMethod(string $name): bool
    {
        foreach (['__', 'get', 'set', 'scope', 'boot', 'register', 'resolve', 'new', 'to'] as $prefix) {
            if (str_starts_with($name, $prefix)) {
                return true;
            }
        }

        return in_array($name, ['query', 'save', 'delete', 'restore', 'forceDelete', 'media'], true);
    }
}
