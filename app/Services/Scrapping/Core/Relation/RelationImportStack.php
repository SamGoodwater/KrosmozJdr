<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Relation;

use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;

/**
 * Pile d'import pour toutes les relations : on empile ce qui reste à récupérer (sorts, monstres,
 * items, etc.) et on garde en mémoire qui dépend de quoi ; lorsqu'un élément est chargé, on
 * récupère son id et on met à jour les tables de relation (breed_spell, creature_spell,
 * creature_resource, resource_recipe, spell_invocation).
 *
 * Types de dépendances : recipe, breed_spell, creature_spell, creature_resource, spell_invocation.
 *
 * @see RelationResolutionService
 * @see docs/50-Fonctionnalités/Scrapping/Architecture/RELATIONS.md
 */
final class RelationImportStack
{
    private const SOURCE_DOFUSDB = 'dofusdb';

    /** File des (source, entity, dofusdb_id) à importer. */
    private \SplQueue $pending;

    /**
     * Pour chaque clé "entity:dofusdb_id", les dépendants à résoudre quand l'entité est importée.
     *
     * @var array<string, list<array{type: string, payload: array<string, mixed>}>>
     */
    private array $dependents = [];

    /** Clés "entity:dofusdb_id" déjà ajoutées à la pile pour éviter doublons. */
    private array $processed = [];

    public function __construct()
    {
        $this->pending = new \SplQueue();
    }

    private function key(string $entity, string $dofusdbId): string
    {
        return $entity . ':' . $dofusdbId;
    }

    /**
     * Ajoute un élément à la pile s'il n'a pas déjà été traité.
     *
     * @return bool true si ajouté à la pile
     */
    public function pushPending(string $source, string $entity, string $dofusdbId): bool
    {
        if ($dofusdbId === '') {
            return false;
        }
        $k = $this->key($entity, $dofusdbId);
        if (isset($this->processed[$k])) {
            return false;
        }
        $this->pending->enqueue([
            'source' => $source,
            'entity' => $entity,
            'dofusdb_id' => $dofusdbId,
        ]);
        $this->processed[$k] = true;

        return true;
    }

    /**
     * Enregistre un dépendant : quand (entity, dofusdb_id) sera importé, on appliquera
     * la résolution selon type avec payload.
     *
     * @param array<string, mixed> $payload Données selon le type (ex. resource_id, breed_id, creature_id, spell_id, quantity)
     */
    public function registerDependent(string $entity, string $dofusdbId, string $type, array $payload): void
    {
        $k = $this->key($entity, $dofusdbId);
        $this->dependents[$k] = $this->dependents[$k] ?? [];
        $this->dependents[$k][] = ['type' => $type, 'payload' => $payload];
    }

    /**
     * Enregistre les dépendances de recette d'une ressource et ajoute à la pile les ingrédients à importer.
     *
     * @param list<array{ingredient_dofusdb_id?: string, quantity?: int}> $recipeIngredients
     * @return list<string> dofusdb_id ajoutés à la pile
     */
    public function registerRecipeDependents(int $resourceId, array $recipeIngredients, bool $dryRun = false): array
    {
        $resource = Resource::find($resourceId);
        if ($resource === null) {
            return [];
        }

        $sync = [];
        $addedToPending = [];

        foreach ($recipeIngredients as $row) {
            $dofusdbId = (string) ($row['ingredient_dofusdb_id'] ?? '');
            if ($dofusdbId === '') {
                continue;
            }
            $qty = (int) ($row['quantity'] ?? 1);
            $qty = max(1, $qty);

            $existing = Resource::where('dofusdb_id', $dofusdbId)->first();
            if ($existing !== null) {
                $sync[$existing->id] = ['quantity' => (string) $qty];
                continue;
            }

            $this->registerDependent('item', $dofusdbId, 'recipe', ['resource_id' => $resourceId, 'quantity' => $qty]);
            if ($this->pushPending(self::SOURCE_DOFUSDB, 'item', $dofusdbId)) {
                $addedToPending[] = $dofusdbId;
            }
        }

        if (!$dryRun) {
            $resource->recipeIngredients()->sync($sync);
        }

        return $addedToPending;
    }

    /**
     * Enregistre les dépendances sorts d'une classe (breed) : quand chaque sort sera importé, on l'attache au breed.
     *
     * @param list<int> $spellDofusdbIds IDs DofusDB des sorts
     * @return list<string> spell dofusdb_id ajoutés à la pile
     */
    public function registerBreedSpellDependents(int $breedId, array $spellDofusdbIds, bool $dryRun = false): array
    {
        $breed = Breed::find($breedId);
        if ($breed === null) {
            return [];
        }

        $sync = [];
        $addedToPending = [];

        foreach ($spellDofusdbIds as $spellId) {
            $dofusdbId = (string) $spellId;
            if ($dofusdbId === '') {
                continue;
            }
            $existing = Spell::where('dofusdb_id', $dofusdbId)->first();
            if ($existing !== null) {
                $sync[$existing->id] = [];
                continue;
            }

            $this->registerDependent('spell', $dofusdbId, 'breed_spell', ['breed_id' => $breedId]);
            if ($this->pushPending(self::SOURCE_DOFUSDB, 'spell', $dofusdbId)) {
                $addedToPending[] = $dofusdbId;
            }
        }

        if (!$dryRun && $sync !== []) {
            $breed->spells()->sync(array_keys($sync));
        }

        return $addedToPending;
    }

    /**
     * Enregistre les dépendances sorts d'une créature (monstre) et les drops (items→ressources).
     *
     * @param array{spells?: list<array{id?: int}>, drops?: list<array{id?: int, quantity?: int}>} $rawData
     * @return list<array{entity: string, dofusdb_id: string}> éléments ajoutés à la pile
     */
    public function registerCreatureRelationDependents(int $creatureId, array $rawData, bool $dryRun = false): array
    {
        $creature = Creature::find($creatureId);
        if ($creature === null) {
            return [];
        }

        $spellIdsToSync = [];
        $resourceSync = [];
        $added = [];

        if (isset($rawData['spells']) && is_array($rawData['spells'])) {
            foreach ($rawData['spells'] as $spellData) {
                $spellId = isset($spellData['id']) ? (int) $spellData['id'] : 0;
                if ($spellId <= 0) {
                    continue;
                }
                $dofusdbId = (string) $spellId;
                $existing = Spell::where('dofusdb_id', $dofusdbId)->first();
                if ($existing !== null) {
                    $spellIdsToSync[] = $existing->id;
                    continue;
                }
                $this->registerDependent('spell', $dofusdbId, 'creature_spell', ['creature_id' => $creatureId]);
                if ($this->pushPending(self::SOURCE_DOFUSDB, 'spell', $dofusdbId)) {
                    $added[] = ['entity' => 'spell', 'dofusdb_id' => $dofusdbId];
                }
            }
        }

        if (isset($rawData['drops']) && is_array($rawData['drops'])) {
            foreach ($rawData['drops'] as $dropData) {
                $itemId = isset($dropData['id']) ? (int) $dropData['id'] : 0;
                if ($itemId <= 0) {
                    continue;
                }
                $qty = (int) ($dropData['quantity'] ?? 1);
                $dofusdbId = (string) $itemId;
                $existingResource = Resource::where('dofusdb_id', $dofusdbId)->first();
                if ($existingResource !== null) {
                    $resourceSync[$existingResource->id] = ['quantity' => (string) max(1, $qty)];
                    continue;
                }
                $this->registerDependent('item', $dofusdbId, 'creature_resource', ['creature_id' => $creatureId, 'quantity' => max(1, $qty)]);
                if ($this->pushPending(self::SOURCE_DOFUSDB, 'item', $dofusdbId)) {
                    $added[] = ['entity' => 'item', 'dofusdb_id' => $dofusdbId];
                }
            }
        }

        if (!$dryRun) {
            if ($spellIdsToSync !== []) {
                $creature->spells()->sync(array_values(array_unique($spellIdsToSync)));
            }
            if ($resourceSync !== []) {
                $creature->resources()->sync($resourceSync);
            }
        }

        return $added;
    }

    /**
     * Enregistre le monstre invoqué par un sort : quand le monstre sera importé, on lie spell↔monster.
     *
     * @return bool true si le monstre a été ajouté à la pile
     */
    public function registerSpellInvocationDependent(int $spellId, string $monsterDofusdbId, bool $dryRun = false): bool
    {
        if ($monsterDofusdbId === '') {
            return false;
        }
        $spell = Spell::find($spellId);
        if ($spell === null) {
            return false;
        }
        $existing = $spell->monsters()->where('dofusdb_id', $monsterDofusdbId)->first();
        if ($existing !== null) {
            return false;
        }
        $this->registerDependent('monster', $monsterDofusdbId, 'spell_invocation', ['spell_id' => $spellId]);

        return $this->pushPending(self::SOURCE_DOFUSDB, 'monster', $monsterDofusdbId);
    }

    /**
     * Retourne le prochain élément à importer sans le retirer.
     *
     * @return array{source: string, entity: string, dofusdb_id: string}|null
     */
    public function peekPending(): ?array
    {
        if ($this->pending->isEmpty()) {
            return null;
        }

        return $this->pending->bottom();
    }

    /**
     * Retire et retourne le prochain élément à importer.
     *
     * @return array{source: string, entity: string, dofusdb_id: string}|null
     */
    public function popPending(): ?array
    {
        if ($this->pending->isEmpty()) {
            return null;
        }

        return $this->pending->dequeue();
    }

    public function hasPending(): bool
    {
        return !$this->pending->isEmpty();
    }

    /**
     * Appelé lorsqu'une entité a été importée : résout tous les dépendants (recettes, breed_spell,
     * creature_spell, creature_resource, spell_invocation). Ne fait rien si $dryRun.
     *
     * @param string $table Table cible si entité item (resources, consumables, items) pour filtrer creature_resource / recipe
     */
    public function onImported(string $entity, string $dofusdbId, ?int $krosmozPrimaryId, ?string $table = null, bool $dryRun = false): void
    {
        $k = $this->key($entity, $dofusdbId);
        $list = $this->dependents[$k] ?? [];
        unset($this->dependents[$k]);

        if ($dryRun) {
            return;
        }

        foreach ($list as $dep) {
            $type = $dep['type'] ?? '';
            $payload = $dep['payload'] ?? [];
            if ($type === 'recipe' && $entity === 'item' && $table === 'resources' && $krosmozPrimaryId !== null && $krosmozPrimaryId > 0) {
                $this->resolveRecipe($payload, $krosmozPrimaryId);
            } elseif ($type === 'breed_spell' && $entity === 'spell' && $krosmozPrimaryId !== null && $krosmozPrimaryId > 0) {
                $this->resolveBreedSpell($payload, $krosmozPrimaryId);
            } elseif ($type === 'creature_spell' && $entity === 'spell' && $krosmozPrimaryId !== null && $krosmozPrimaryId > 0) {
                $this->resolveCreatureSpell($payload, $krosmozPrimaryId);
            } elseif ($type === 'creature_resource' && $entity === 'item' && $table === 'resources' && $krosmozPrimaryId !== null && $krosmozPrimaryId > 0) {
                $this->resolveCreatureResource($payload, $krosmozPrimaryId);
            } elseif ($type === 'spell_invocation' && $entity === 'monster' && $krosmozPrimaryId !== null && $krosmozPrimaryId > 0) {
                $this->resolveSpellInvocation($payload, $krosmozPrimaryId);
            }
        }
    }

    private function resolveRecipe(array $payload, int $ingredientResourceId): void
    {
        $resourceId = (int) ($payload['resource_id'] ?? 0);
        $quantity = (int) ($payload['quantity'] ?? 1);
        if ($resourceId <= 0) {
            return;
        }
        $resource = Resource::find($resourceId);
        if ($resource === null) {
            return;
        }
        $sync = [];
        foreach ($resource->recipeIngredients as $ing) {
            $sync[$ing->id] = ['quantity' => (string) ($ing->pivot->quantity ?? 1)];
        }
        $sync[$ingredientResourceId] = ['quantity' => (string) max(1, $quantity)];
        $resource->recipeIngredients()->sync($sync);
    }

    private function resolveBreedSpell(array $payload, int $spellId): void
    {
        $breedId = (int) ($payload['breed_id'] ?? 0);
        if ($breedId <= 0) {
            return;
        }
        $breed = Breed::find($breedId);
        if ($breed === null) {
            return;
        }
        $current = $breed->spells()->pluck('id')->all();
        $breed->spells()->sync(array_values(array_unique(array_merge($current, [$spellId]))));
    }

    private function resolveCreatureSpell(array $payload, int $spellId): void
    {
        $creatureId = (int) ($payload['creature_id'] ?? 0);
        if ($creatureId <= 0) {
            return;
        }
        $creature = Creature::find($creatureId);
        if ($creature === null) {
            return;
        }
        $current = $creature->spells()->pluck('id')->all();
        $creature->spells()->sync(array_values(array_unique(array_merge($current, [$spellId]))));
    }

    private function resolveCreatureResource(array $payload, int $resourceId): void
    {
        $creatureId = (int) ($payload['creature_id'] ?? 0);
        $quantity = (int) ($payload['quantity'] ?? 1);
        if ($creatureId <= 0) {
            return;
        }
        $creature = Creature::find($creatureId);
        if ($creature === null) {
            return;
        }
        $sync = [];
        foreach ($creature->resources as $r) {
            $sync[$r->id] = ['quantity' => (string) ($r->pivot->quantity ?? 1)];
        }
        $sync[$resourceId] = ['quantity' => (string) max(1, $quantity)];
        $creature->resources()->sync($sync);
    }

    private function resolveSpellInvocation(array $payload, int $monsterId): void
    {
        $spellId = (int) ($payload['spell_id'] ?? 0);
        if ($spellId <= 0) {
            return;
        }
        $spell = Spell::find($spellId);
        if ($spell === null) {
            return;
        }
        $current = $spell->monsters()->pluck('id')->all();
        $spell->monsters()->sync(array_values(array_unique(array_merge($current, [$monsterId]))));
    }

    /**
     * Nombre d'éléments en attente (pour logs).
     */
    public function pendingCount(): int
    {
        return $this->pending->count();
    }
}
