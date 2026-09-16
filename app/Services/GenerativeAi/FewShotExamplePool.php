<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Enums\EntityState;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Models\Entity\Spell;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Résout les étalons few-shot vers des fiches `playable` (official_id, nom, puis id local).
 *
 * @example
 * $ids = app(FewShotExamplePool::class)->resolvePlayableIds('npc', ['jdr:npc:incarnam:ganymede']);
 */
final class FewShotExamplePool
{
    /** @var array<string, class-string<Model>> */
    public const ENTITY_MODELS = [
        'item' => Item::class,
        'spell' => Spell::class,
        'monster' => Monster::class,
        'npc' => Npc::class,
        'consumable' => Consumable::class,
    ];

    /**
     * @param  list<int|string>  $refs
     * @return list<int|string> refs normalisées (official_id, nom, ou id)
     */
    public function normalizePlayableRefs(string $entity, array $refs): array
    {
        $out = [];
        foreach ($refs as $ref) {
            $model = $this->findPlayable($entity, $ref);
            if ($model === null) {
                throw new RuntimeException(
                    "Étalon few-shot « {$this->stringifyRef($ref)} » introuvable en état jouable ({$entity})."
                );
            }
            $out[] = $this->portableRef($model);
        }

        return array_values(array_unique($out, SORT_REGULAR));
    }

    /**
     * @param  list<int|string>  $refs
     * @return list<int>
     */
    public function resolvePlayableIds(string $entity, array $refs): array
    {
        $ids = [];
        foreach ($refs as $ref) {
            $model = $this->findPlayable($entity, $ref);
            if ($model === null) {
                continue;
            }
            $ids[] = (int) $model->getKey();
        }

        return array_values(array_unique($ids));
    }

    /**
     * Chaque entrée doit exister et être `playable`. Liste vide = aucun étalon configuré (autorisé).
     *
     * @param  list<int|string>  $refs
     */
    public function assertAllPlayable(string $entity, array $refs): void
    {
        foreach ($refs as $ref) {
            if ($this->findPlayable($entity, $ref) === null) {
                throw new RuntimeException(
                    "Étalon few-shot « {$this->stringifyRef($ref)} » introuvable en état jouable ({$entity})."
                );
            }
        }
    }

    /**
     * Pool utilisé par un assembleur : config non vide → doit résoudre ; sinon repli fourni.
     * Refuse un pool vide.
     *
     * @param  list<int|string>  $configured
     * @param  list<int>  $fallbackIds
     * @return list<int>
     */
    public function requireIds(string $entity, array $configured, array $fallbackIds = []): array
    {
        if ($configured !== []) {
            $ids = $this->resolvePlayableIds($entity, $configured);
            if ($ids === []) {
                throw new RuntimeException(
                    "Pool few-shot vide pour {$entity} : aucun example_id n’est jouable."
                );
            }

            return $ids;
        }

        $fallback = array_values(array_unique(array_map(static fn ($id): int => (int) $id, $fallbackIds)));
        if ($fallback === []) {
            throw new RuntimeException(
                "Pool few-shot vide pour {$entity} : aucun étalon playable."
            );
        }

        return $fallback;
    }

    public function findPlayable(string $entity, mixed $ref): ?Model
    {
        $modelClass = self::ENTITY_MODELS[$entity] ?? null;
        if ($modelClass === null) {
            return null;
        }

        $playable = EntityState::Playable->value;
        $query = $modelClass::query()->where('state', $playable);

        if (is_int($ref) || (is_string($ref) && $ref !== '' && ctype_digit($ref))) {
            $byId = (clone $query)->whereKey((int) $ref)->first();
            if ($byId !== null) {
                return $byId;
            }
        }

        $token = is_string($ref) ? trim($ref) : '';
        if ($token === '') {
            return null;
        }

        $table = (new $modelClass)->getTable();
        $schema = $modelClass::query()->getConnection()->getSchemaBuilder();

        if ($schema->hasColumn($table, 'official_id')) {
            $byOfficial = (clone $query)->where('official_id', $token)->first();
            if ($byOfficial !== null) {
                return $byOfficial;
            }
        }

        if ($schema->hasColumn($table, 'name')) {
            return (clone $query)->where('name', $token)->first();
        }

        return null;
    }

    private function portableRef(Model $model): int|string
    {
        $official = $model->getAttribute('official_id');
        if (is_string($official) && $official !== '') {
            return $official;
        }

        $name = $model->getAttribute('name');
        if (is_string($name) && $name !== '') {
            return $name;
        }

        return (int) $model->getKey();
    }

    private function stringifyRef(mixed $ref): string
    {
        if (is_int($ref) || is_string($ref)) {
            return (string) $ref;
        }

        return json_encode($ref, JSON_THROW_ON_ERROR);
    }
}
