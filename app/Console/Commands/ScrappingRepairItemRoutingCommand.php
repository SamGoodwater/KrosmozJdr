<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Models\Type\ResourceType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Corrige les items mal routés (items -> resources/consumables) à partir du type DofusDB.
 *
 * Cette commande sert de rattrapage des données historiques :
 * - détecte les lignes de `items` dont le type cible attendu n'est pas `items`
 * - migre vers `resources` ou `consumables`
 * - transfère les pivots principaux (drops + recettes)
 * - réaffecte media/effect_usages puis supprime la ligne source
 */
final class ScrappingRepairItemRoutingCommand extends Command
{
    protected $signature = 'scrapping:items:repair-routing
                            {--apply : Appliquer les corrections (sinon dry-run)}
                            {--limit=0 : Limiter le nombre de lignes sources traitées (0 = toutes)}';

    protected $description = 'Rattrape les items mal classés vers resources/consumables';

    /** @var array<int,int> */
    private array $itemTypeIdsByDofusType = [];

    /** @var array<int,int> */
    private array $resourceTypeIdsByDofusType = [];

    /** @var array<int,int> */
    private array $consumableTypeIdsByDofusType = [];

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $limit = max(0, (int) $this->option('limit'));

        $this->itemTypeIdsByDofusType = ItemType::query()
            ->whereNotNull('dofusdb_type_id')
            ->pluck('id', 'dofusdb_type_id')
            ->mapWithKeys(static fn ($id, $dofusTypeId): array => [(int) $dofusTypeId => (int) $id])
            ->all();
        $this->resourceTypeIdsByDofusType = ResourceType::query()
            ->whereNotNull('dofusdb_type_id')
            ->pluck('id', 'dofusdb_type_id')
            ->mapWithKeys(static fn ($id, $dofusTypeId): array => [(int) $dofusTypeId => (int) $id])
            ->all();
        $this->consumableTypeIdsByDofusType = ConsumableType::query()
            ->whereNotNull('dofusdb_type_id')
            ->pluck('id', 'dofusdb_type_id')
            ->mapWithKeys(static fn ($id, $dofusTypeId): array => [(int) $dofusTypeId => (int) $id])
            ->all();

        if ($this->itemTypeIdsByDofusType === [] && $this->resourceTypeIdsByDofusType === [] && $this->consumableTypeIdsByDofusType === []) {
            $this->error('Aucun type n\'est présent. Exécute d\'abord TypeSeeder.');
            return self::FAILURE;
        }

        /** @var list<array{item_id:int,dofusdb_id:string,dofus_type_id:int,target:string}> $candidates */
        $candidates = [];
        $query = Item::query()
            ->whereNotNull('item_type_id')
            ->whereNotNull('dofusdb_id')
            ->with('itemType')
            ->orderBy('id');
        if ($limit > 0) {
            $query->limit($limit);
        }

        foreach ($query->get() as $item) {
            $dofusTypeId = (int) ($item->itemType?->dofusdb_type_id ?? 0);
            if ($dofusTypeId <= 0) {
                continue;
            }
            $target = $this->resolveTargetTable($dofusTypeId);
            if ($target === 'items') {
                continue;
            }
            $candidates[] = [
                'item_id' => (int) $item->id,
                'dofusdb_id' => (string) $item->dofusdb_id,
                'dofus_type_id' => $dofusTypeId,
                'target' => $target,
            ];
        }

        $this->info(sprintf(
            '%s : %d item(s) mal classé(s) détecté(s).',
            $apply ? 'Mode APPLY' : 'Mode DRY-RUN',
            count($candidates)
        ));

        if ($candidates === []) {
            return self::SUCCESS;
        }

        $movedToResources = 0;
        $movedToConsumables = 0;
        $transferredCreatureLinks = 0;
        $transferredRecipeLinks = 0;

        foreach ($candidates as $row) {
            $this->line(sprintf(
                '- item_id=%d dofusdb_id=%s type=%d => %s',
                $row['item_id'],
                $row['dofusdb_id'],
                $row['dofus_type_id'],
                $row['target']
            ));

            if (!$apply) {
                continue;
            }

            DB::transaction(function () use (
                $row,
                &$movedToResources,
                &$movedToConsumables,
                &$transferredCreatureLinks,
                &$transferredRecipeLinks
            ): void {
                $source = Item::query()->find($row['item_id']);
                if (!$source instanceof Item) {
                    return;
                }

                if ($row['target'] === 'resources') {
                    $targetTypeId = $this->resourceTypeIdsByDofusType[$row['dofus_type_id']] ?? null;
                    if ($targetTypeId === null) {
                        return;
                    }
                    $target = Resource::withTrashed()->where('dofusdb_id', $source->dofusdb_id)->first();
                    if (!$target instanceof Resource) {
                        $target = Resource::create($this->buildResourcePayloadFromItem($source, $targetTypeId));
                    } else {
                        if ($target->trashed()) {
                            $target->restore();
                        }
                        $target->update($this->buildResourcePayloadFromItem($source, $targetTypeId, false));
                    }

                    $transferredCreatureLinks += $this->moveCreatureItemToResource((int) $source->id, (int) $target->id);
                    $transferredRecipeLinks += $this->moveItemRecipeToResourceRecipe((int) $source->id, (int) $target->id);
                    $this->moveMedia((int) $source->id, (int) $target->id, Item::class, Resource::class);
                    $this->moveEffectUsages((int) $source->id, (int) $target->id, 'item', 'resource');
                    $source->forceDelete();
                    $movedToResources++;
                    return;
                }

                $targetTypeId = $this->consumableTypeIdsByDofusType[$row['dofus_type_id']] ?? null;
                if ($targetTypeId === null) {
                    return;
                }
                $target = Consumable::withTrashed()->where('dofusdb_id', $source->dofusdb_id)->first();
                if (!$target instanceof Consumable) {
                    $target = Consumable::create($this->buildConsumablePayloadFromItem($source, $targetTypeId));
                } else {
                    if ($target->trashed()) {
                        $target->restore();
                    }
                    $target->update($this->buildConsumablePayloadFromItem($source, $targetTypeId, false));
                }

                $transferredCreatureLinks += $this->moveCreatureItemToConsumable((int) $source->id, (int) $target->id);
                $transferredRecipeLinks += $this->moveItemRecipeToConsumableRecipe((int) $source->id, (int) $target->id);
                $this->moveMedia((int) $source->id, (int) $target->id, Item::class, Consumable::class);
                $this->moveEffectUsages((int) $source->id, (int) $target->id, 'item', 'consumable');
                $source->forceDelete();
                $movedToConsumables++;
            });
        }

        $this->info('---');
        $this->info(sprintf('Déplacés vers resources   : %d', $movedToResources));
        $this->info(sprintf('Déplacés vers consumables : %d', $movedToConsumables));
        $this->info(sprintf('Liens drops transférés     : %d', $transferredCreatureLinks));
        $this->info(sprintf('Liens recette transférés   : %d', $transferredRecipeLinks));

        return self::SUCCESS;
    }

    private function resolveTargetTable(int $dofusTypeId): string
    {
        if (isset($this->itemTypeIdsByDofusType[$dofusTypeId])) {
            return 'items';
        }
        if (isset($this->consumableTypeIdsByDofusType[$dofusTypeId])) {
            return 'consumables';
        }
        if (isset($this->resourceTypeIdsByDofusType[$dofusTypeId])) {
            return 'resources';
        }

        return 'items';
    }

    /**
     * @return array<string,mixed>
     */
    private function buildResourcePayloadFromItem(Item $source, int $resourceTypeId, bool $forCreate = true): array
    {
        $payload = [
            'resource_type_id' => $resourceTypeId,
            'dofusdb_id' => $source->dofusdb_id,
            'official_id' => is_numeric((string) $source->official_id) ? (int) $source->official_id : null,
            'name' => $source->name,
            'description' => $source->description,
            'effect' => $source->effect,
            'level' => $source->level,
            'price' => $source->price,
            'rarity' => (int) ($source->rarity ?? 0),
            'dofus_version' => $source->dofus_version ?: '2.0',
            'state' => $source->state ?: Resource::STATE_RAW,
            'read_level' => (int) ($source->read_level ?? 0),
            'write_level' => (int) ($source->write_level ?? 0),
            'image' => $source->image,
            'auto_update' => (bool) ($source->auto_update ?? false),
            'created_by' => $source->created_by,
        ];
        if ($forCreate) {
            return $payload;
        }

        return array_filter($payload, static fn ($v): bool => $v !== null && $v !== '');
    }

    /**
     * @return array<string,mixed>
     */
    private function buildConsumablePayloadFromItem(Item $source, int $consumableTypeId, bool $forCreate = true): array
    {
        $payload = [
            'consumable_type_id' => $consumableTypeId,
            'dofusdb_id' => $source->dofusdb_id,
            'official_id' => $source->official_id,
            'name' => $source->name,
            'description' => $source->description,
            'effect' => $source->effect,
            'level' => $source->level,
            'price' => $source->price,
            'rarity' => (int) ($source->rarity ?? 0),
            'dofus_version' => $source->dofus_version ?: '2.0',
            'state' => $source->state ?: Consumable::STATE_RAW,
            'read_level' => (int) ($source->read_level ?? 0),
            'write_level' => (int) ($source->write_level ?? 0),
            'image' => $source->image,
            'auto_update' => (bool) ($source->auto_update ?? false),
            'created_by' => $source->created_by,
            'recipe' => $source->recipe,
        ];
        if ($forCreate) {
            return $payload;
        }

        return array_filter($payload, static fn ($v): bool => $v !== null && $v !== '');
    }

    private function moveCreatureItemToResource(int $sourceItemId, int $targetResourceId): int
    {
        $rows = DB::table('creature_item')->where('item_id', $sourceItemId)->get();
        $moved = 0;
        foreach ($rows as $row) {
            $creatureId = (int) $row->creature_id;
            $quantity = max(1, (int) $row->quantity);
            $existing = DB::table('creature_resource')
                ->where('creature_id', $creatureId)
                ->where('resource_id', $targetResourceId)
                ->first();
            if ($existing !== null) {
                $newQuantity = max(1, (int) $existing->quantity) + $quantity;
                DB::table('creature_resource')
                    ->where('creature_id', $creatureId)
                    ->where('resource_id', $targetResourceId)
                    ->update(['quantity' => $newQuantity]);
            } else {
                DB::table('creature_resource')->insert([
                    'creature_id' => $creatureId,
                    'resource_id' => $targetResourceId,
                    'quantity' => $quantity,
                ]);
            }
            $moved++;
        }
        DB::table('creature_item')->where('item_id', $sourceItemId)->delete();

        return $moved;
    }

    private function moveCreatureItemToConsumable(int $sourceItemId, int $targetConsumableId): int
    {
        $rows = DB::table('creature_item')->where('item_id', $sourceItemId)->get();
        $moved = 0;
        foreach ($rows as $row) {
            $creatureId = (int) $row->creature_id;
            $quantity = max(1, (int) $row->quantity);
            $existing = DB::table('consumable_creature')
                ->where('consumable_id', $targetConsumableId)
                ->where('creature_id', $creatureId)
                ->first();
            if ($existing !== null) {
                $newQuantity = max(1, (int) $existing->quantity) + $quantity;
                DB::table('consumable_creature')
                    ->where('consumable_id', $targetConsumableId)
                    ->where('creature_id', $creatureId)
                    ->update(['quantity' => (string) $newQuantity]);
            } else {
                DB::table('consumable_creature')->insert([
                    'consumable_id' => $targetConsumableId,
                    'creature_id' => $creatureId,
                    'quantity' => (string) $quantity,
                ]);
            }
            $moved++;
        }
        DB::table('creature_item')->where('item_id', $sourceItemId)->delete();

        return $moved;
    }

    private function moveItemRecipeToResourceRecipe(int $sourceItemId, int $targetResourceId): int
    {
        $rows = DB::table('item_resource')->where('item_id', $sourceItemId)->get();
        $moved = 0;
        foreach ($rows as $row) {
            $ingredientResourceId = (int) $row->resource_id;
            $quantity = max(1, (int) $row->quantity);
            $existing = DB::table('resource_recipe')
                ->where('resource_id', $targetResourceId)
                ->where('ingredient_resource_id', $ingredientResourceId)
                ->first();
            if ($existing !== null) {
                $newQuantity = max(1, (int) $existing->quantity) + $quantity;
                DB::table('resource_recipe')
                    ->where('resource_id', $targetResourceId)
                    ->where('ingredient_resource_id', $ingredientResourceId)
                    ->update(['quantity' => (string) $newQuantity]);
            } else {
                DB::table('resource_recipe')->insert([
                    'resource_id' => $targetResourceId,
                    'ingredient_resource_id' => $ingredientResourceId,
                    'quantity' => (string) $quantity,
                ]);
            }
            $moved++;
        }
        DB::table('item_resource')->where('item_id', $sourceItemId)->delete();

        return $moved;
    }

    private function moveItemRecipeToConsumableRecipe(int $sourceItemId, int $targetConsumableId): int
    {
        $rows = DB::table('item_resource')->where('item_id', $sourceItemId)->get();
        $moved = 0;
        foreach ($rows as $row) {
            $resourceId = (int) $row->resource_id;
            $quantity = max(1, (int) $row->quantity);
            $existing = DB::table('consumable_resource')
                ->where('consumable_id', $targetConsumableId)
                ->where('resource_id', $resourceId)
                ->first();
            if ($existing !== null) {
                $newQuantity = max(1, (int) $existing->quantity) + $quantity;
                DB::table('consumable_resource')
                    ->where('consumable_id', $targetConsumableId)
                    ->where('resource_id', $resourceId)
                    ->update(['quantity' => (string) $newQuantity]);
            } else {
                DB::table('consumable_resource')->insert([
                    'consumable_id' => $targetConsumableId,
                    'resource_id' => $resourceId,
                    'quantity' => (string) $quantity,
                ]);
            }
            $moved++;
        }
        DB::table('item_resource')->where('item_id', $sourceItemId)->delete();

        return $moved;
    }

    private function moveMedia(int $sourceId, int $targetId, string $sourceClass, string $targetClass): void
    {
        DB::table('media')
            ->where('model_type', $sourceClass)
            ->where('model_id', $sourceId)
            ->update([
                'model_type' => $targetClass,
                'model_id' => $targetId,
            ]);
    }

    private function moveEffectUsages(int $sourceId, int $targetId, string $sourceType, string $targetType): void
    {
        DB::table('effect_usages')
            ->where('entity_type', $sourceType)
            ->where('entity_id', $sourceId)
            ->update([
                'entity_type' => $targetType,
                'entity_id' => $targetId,
            ]);
    }
}

