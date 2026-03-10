<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Relation;

use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Consumable;
use App\Models\Entity\Spell;
use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use App\Services\Scrapping\Core\Orchestrator\OrchestratorResult;
use Illuminate\Support\Facades\Log;

/**
 * Résolution des relations pour tous les types d'entités (classes→sorts, sorts→invocations,
 * monstres→sorts/drops, ressources→recettes). Utilise une pile partagée (RelationImportStack)
 * pour empiler les objets à importer et mettre à jour les tables de relation au fur et à mesure.
 *
 * @see RelationImportStack
 * @see docs/50-Fonctionnalités/Scrapping/Architecture/RELATIONS.md
 */
final class RelationResolutionService
{
    public function __construct(
        private Orchestrator $orchestrator,
        private ?CollectService $collectService = null
    ) {
    }

    private function getCollectService(): CollectService
    {
        if ($this->collectService === null) {
            $this->collectService = app(CollectService::class);
        }

        return $this->collectService;
    }

    /**
     * Résout les relations monster (spells, drops) : enregistre sur la pile ou importe en inline.
     *
     * @param array<string, mixed> $rawData Données brutes du monstre (avec spells, drops)
     * @param array{convert?: bool, validate?: bool, integrate?: bool, dry_run?: bool, force_update?: bool} $options
     * @return array{spell_ids?: list<int>, resource_ids?: list<int>, related_results?: list<array{type: string, id: int, success: bool, krosmoz_id: int|null}>, pending_added?: list<array{entity: string, dofusdb_id: string}>}
     */
    public function resolveAndSyncMonsterRelations(
        array $rawData,
        int $creatureId,
        array $options = [],
        ?RelationImportStack $stack = null
    ): array {
        $dryRun = (bool) ($options['dry_run'] ?? false);

        if ($stack !== null) {
            $pendingAdded = $stack->registerCreatureRelationDependents($creatureId, $rawData, $dryRun);

            return ['synced' => true, 'pending_added' => $pendingAdded];
        }

        return $this->resolveAndSyncMonsterRelationsInline($rawData, $creatureId, $options);
    }

    /**
     * Comportement historique : import inline des sorts et drops puis sync.
     *
     * @param array<string, mixed> $rawData
     * @param array{convert?: bool, validate?: bool, integrate?: bool, dry_run?: bool, force_update?: bool} $options
     * @return array{spell_ids: list<int>, resource_ids: list<int>, related_results: list<array{type: string, id: int, success: bool, krosmoz_id: int|null}>}
     */
    private function resolveAndSyncMonsterRelationsInline(array $rawData, int $creatureId, array $options): array
    {
        $integrate = (bool) ($options['integrate'] ?? true);
        $dryRun = (bool) ($options['dry_run'] ?? false);

        $v2Options = [
            'convert' => true,
            'validate' => true,
            'integrate' => $integrate && !$dryRun,
            'dry_run' => $dryRun,
            'force_update' => (bool) ($options['force_update'] ?? false),
            'spell_category_hint' => Spell::CATEGORY_CREATURE,
            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
        ];

        $importedSpellIds = [];
        $relatedResults = [];

        if (isset($rawData['spells']) && is_array($rawData['spells'])) {
            foreach ($rawData['spells'] as $spellData) {
                $spellId = isset($spellData['id']) ? (int) $spellData['id'] : 0;
                if ($spellId <= 0) {
                    continue;
                }
                $result = $this->orchestrator->runOne('dofusdb', 'spell', $spellId, $v2Options);
                $krosmozId = null;
                if ($result->isSuccess()) {
                    $intResult = $result->getIntegrationResult();
                    if ($intResult !== null && $intResult->isSuccess()) {
                        $krosmozId = $intResult->getPrimaryId();
                        if ($krosmozId !== null) {
                            $importedSpellIds[] = $krosmozId;
                        }
                    }
                } else {
                    Log::warning('RelationResolutionService: import sort échoué', [
                        'spell_id' => $spellId,
                        'message' => $result->getMessage(),
                    ]);
                }
                $relatedResults[] = [
                    'type' => 'spell',
                    'id' => $spellId,
                    'success' => $result->isSuccess(),
                    'krosmoz_id' => $krosmozId,
                ];
            }
        }

        $importedResourceIds = [];
        $importedItemIds = [];
        $importedConsumableIds = [];
        $dropSyncByTable = ['resources' => [], 'items' => [], 'consumables' => []];

        if (isset($rawData['drops']) && is_array($rawData['drops'])) {
            foreach ($rawData['drops'] as $dropData) {
                $itemId = isset($dropData['itemId']) ? (int) $dropData['itemId'] : (isset($dropData['id']) ? (int) $dropData['id'] : 0);
                if ($itemId <= 0) {
                    continue;
                }
                $qty = max(1, (int) ($dropData['quantity'] ?? 1));
                $result = $this->orchestrator->runOne('dofusdb', 'item', $itemId, $v2Options);
                $krosmozId = null;
                $table = 'items';
                if ($result->isSuccess()) {
                    $intResult = $result->getIntegrationResult();
                    if ($intResult !== null && $intResult->isSuccess()) {
                        $data = $intResult->getData();
                        $table = (string) ($data['table'] ?? 'items');
                        $krosmozId = $intResult->getPrimaryId();
                        if ($krosmozId !== null && $table === 'resources') {
                            $importedResourceIds[] = $krosmozId;
                            $prev = (int) ($dropSyncByTable['resources'][$krosmozId]['quantity'] ?? 0);
                            $dropSyncByTable['resources'][$krosmozId] = ['quantity' => (string) ($prev + $qty)];
                        } elseif ($krosmozId !== null && $table === 'items') {
                            $importedItemIds[] = $krosmozId;
                            $prev = (int) ($dropSyncByTable['items'][$krosmozId]['quantity'] ?? 0);
                            $dropSyncByTable['items'][$krosmozId] = ['quantity' => $prev + $qty];
                        } elseif ($krosmozId !== null && $table === 'consumables') {
                            $importedConsumableIds[] = $krosmozId;
                            $prev = (int) ($dropSyncByTable['consumables'][$krosmozId]['quantity'] ?? 0);
                            $dropSyncByTable['consumables'][$krosmozId] = ['quantity' => (string) ($prev + $qty)];
                        }
                    }
                } else {
                    Log::warning('RelationResolutionService: import item (drop) échoué', [
                        'item_id' => $itemId,
                        'message' => $result->getMessage(),
                    ]);
                }
                $relatedResults[] = [
                    'type' => $table === 'resources' ? 'resource' : ($table === 'consumables' ? 'consumable' : 'item'),
                    'id' => $itemId,
                    'success' => $result->isSuccess(),
                    'krosmoz_id' => $krosmozId,
                ];
            }
        }

        $creature = Creature::find($creatureId);
        if ($creature !== null && !$dryRun) {
            $validSpellIds = array_values(array_unique(array_filter($importedSpellIds)));
            if ($validSpellIds !== []) {
                $creature->spells()->sync($validSpellIds);
                Spell::query()
                    ->whereIn('id', $validSpellIds)
                    ->where('category', '!=', Spell::CATEGORY_CLASS)
                    ->update(['category' => Spell::CATEGORY_CREATURE]);
            }
            if ($dropSyncByTable['resources'] !== []) {
                $creature->resources()->sync($dropSyncByTable['resources']);
            }
            if ($dropSyncByTable['items'] !== []) {
                $creature->items()->sync($dropSyncByTable['items']);
            }
            if ($dropSyncByTable['consumables'] !== []) {
                $creature->consumables()->sync($dropSyncByTable['consumables']);
            }
        }

        return [
            'spell_ids' => array_values(array_unique(array_filter($importedSpellIds))),
            'resource_ids' => array_values(array_unique(array_filter($importedResourceIds))),
            'related_results' => $relatedResults,
        ];
    }

    /**
     * Résout les sorts d'une classe (breed) : utilise breedSpellsId de l'API si disponible
     * (inclut les 2 variantes par niveau), sinon fallback sur spell-levels.
     *
     * @param array{integrate?: bool, dry_run?: bool, force_update?: bool, validate?: bool} $options
     * @param array<string, mixed>|null $rawBreed Données brutes du breed (breedSpellsId prioritaire)
     * @return array{synced: bool, pending_added?: list<string>}
     */
    public function resolveAndSyncBreedSpells(
        int $breedId,
        string $source,
        int $breedDofusdbId,
        array $options = [],
        ?RelationImportStack $stack = null,
        ?array $rawBreed = null
    ): array {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        // Utiliser spell-variants (44 sorts : base + variantes) en priorité — source fiable et alignée avec Dofus
        $spellIds = $this->getCollectService()->fetchSpellIdsFromSpellVariants($source, $breedDofusdbId, [
            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
        ]);
        if ($spellIds === []) {
            // Fallback : breedSpellsId (22 base) + spell-levels
            $spellIds = $this->extractBreedSpellIds($rawBreed);
            if ($spellIds === []) {
                $spellIds = $this->getCollectService()->fetchSpellIdsByBreedId($source, $breedDofusdbId, [
                    'skip_cache' => (bool) ($options['skip_cache'] ?? false),
                ]);
            }
        }

        if ($stack !== null) {
            $pendingAdded = $stack->registerBreedSpellDependents($breedId, $spellIds, $dryRun);

            return ['synced' => true, 'pending_added' => $pendingAdded];
        }

        $v2Options = [
            'convert' => true,
            'validate' => ($options['validate'] ?? true) !== false,
            'integrate' => ($options['integrate'] ?? true) && !$dryRun,
            'dry_run' => $dryRun,
            'force_update' => (bool) ($options['force_update'] ?? false),
            'spell_category_hint' => Spell::CATEGORY_CLASS,
            'include_relations' => true,
            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
        ];
        $importedSpellIds = [];
        foreach ($spellIds as $spellId) {
            $result = $this->orchestrator->runOne($source, 'spell', $spellId, $v2Options);
            if ($result->isSuccess()) {
                $id = $result->getIntegrationResult()?->getPrimaryId();
                if ($id !== null) {
                    $importedSpellIds[] = $id;
                }
            }
        }
        $breed = \App\Models\Entity\Breed::find($breedId);
        if ($breed !== null && !$dryRun && $importedSpellIds !== []) {
            $breed->spells()->sync(array_values(array_unique($importedSpellIds)));
            Spell::query()
                ->whereIn('id', array_values(array_unique($importedSpellIds)))
                ->update(['category' => Spell::CATEGORY_CLASS]);
        }

        return ['synced' => true];
    }

    /**
     * Extrait les IDs de sorts depuis les données brutes d'un breed (breedSpellsId).
     *
     * @param array<string, mixed>|null $raw
     * @return list<int>
     */
    private function extractBreedSpellIds(?array $raw): array
    {
        if ($raw === null || !isset($raw['breedSpellsId']) || !is_array($raw['breedSpellsId'])) {
            return [];
        }
        $ids = [];
        foreach ($raw['breedSpellsId'] as $value) {
            $id = is_array($value) ? ($value['id'] ?? null) : $value;
            if (is_numeric($id)) {
                $n = (int) $id;
                if ($n > 0) {
                    $ids[] = $n;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * Résout le monstre invoqué par un sort : enregistre sur la pile ou importe en inline.
     *
     * @param array{integrate?: bool, dry_run?: bool, force_update?: bool, validate?: bool} $options
     * @return array{synced: bool, pending_added?: bool}
     */
    public function resolveAndSyncSpellInvocation(
        int $spellId,
        string $monsterDofusdbId,
        array $options = [],
        ?RelationImportStack $stack = null
    ): array {
        if ($monsterDofusdbId === '') {
            return ['synced' => true];
        }
        $dryRun = (bool) ($options['dry_run'] ?? false);

        if ($stack !== null) {
            $added = $stack->registerSpellInvocationDependent($spellId, $monsterDofusdbId, $dryRun);

            return ['synced' => true, 'pending_added' => $added];
        }

        $v2Options = [
            'convert' => true,
            'validate' => ($options['validate'] ?? true) !== false,
            'integrate' => ($options['integrate'] ?? true) && !$dryRun,
            'dry_run' => $dryRun,
            'force_update' => (bool) ($options['force_update'] ?? false),
            'include_relations' => true,
            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
        ];
        $result = $this->orchestrator->runOne('dofusdb', 'monster', (int) $monsterDofusdbId, $v2Options);
        $monsterId = null;
        if ($result->isSuccess()) {
            $monsterId = $result->getIntegrationResult()?->getMonsterId();
        }
        if ($monsterId !== null && !$dryRun) {
            $spell = \App\Models\Entity\Spell::find($spellId);
            if ($spell !== null) {
                $current = $spell->monsters()->pluck('id')->all();
                $spell->monsters()->sync(array_values(array_unique(array_merge($current, [$monsterId]))));
            }
        }

        return ['synced' => true];
    }

    /**
     * Résout les items d'une panoplie : enregistre les dépendances sur la pile ou importe en inline.
     *
     * @param list<int> $itemDofusdbIds
     * @param array{integrate?: bool, dry_run?: bool, force_update?: bool, validate?: bool, skip_cache?: bool} $options
     * @return array{synced: bool, pending_added?: list<string>}
     */
    public function resolveAndSyncPanoplyItems(
        int $panoplyId,
        array $itemDofusdbIds,
        array $options = [],
        ?RelationImportStack $stack = null
    ): array {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $itemDofusdbIds = array_values(array_unique(array_map('intval', $itemDofusdbIds)));
        $itemDofusdbIds = array_values(array_filter($itemDofusdbIds, static fn (int $id): bool => $id > 0));

        if ($stack !== null) {
            $pendingAdded = $stack->registerPanoplyItemDependents($panoplyId, $itemDofusdbIds, $dryRun);

            return ['synced' => true, 'pending_added' => $pendingAdded];
        }

        $v2Options = [
            'convert' => true,
            'validate' => ($options['validate'] ?? true) !== false,
            'integrate' => ($options['integrate'] ?? true) && !$dryRun,
            'dry_run' => $dryRun,
            'force_update' => (bool) ($options['force_update'] ?? false),
            'include_relations' => true,
            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
        ];
        $importedItemIds = [];
        foreach ($itemDofusdbIds as $itemDofusdbId) {
            $result = $this->orchestrator->runOne('dofusdb', 'item', $itemDofusdbId, $v2Options);
            if ($result->isSuccess()) {
                $intResult = $result->getIntegrationResult();
                if ($intResult !== null && $intResult->isSuccess()) {
                    $data = $intResult->getData();
                    if (($data['table'] ?? '') === 'items') {
                        $id = $intResult->getPrimaryId();
                        if ($id !== null) {
                            $importedItemIds[] = $id;
                        }
                    }
                }
            }
        }
        $panoply = Panoply::find($panoplyId);
        if ($panoply !== null && !$dryRun && $importedItemIds !== []) {
            $panoply->items()->sync(array_values(array_unique($importedItemIds)));
        }

        return ['synced' => true];
    }

    /**
     * Résout les ingrédients de recette d'un équipement (item).
     *
     * @param list<array{ingredient_dofusdb_id?: string, quantity?: int}> $recipeIngredients
     * @param array{integrate?: bool, dry_run?: bool, force_update?: bool, validate?: bool, skip_cache?: bool} $options
     * @return array{imported: list<int>, synced: bool}
     */
    public function resolveAndSyncItemRecipe(array $recipeIngredients, int $itemId, array $options = []): array
    {
        $item = Item::find($itemId);
        if ($item === null) {
            return ['imported' => [], 'synced' => false];
        }
        if ($recipeIngredients === []) {
            if (!(bool) ($options['dry_run'] ?? false)) {
                $item->resources()->sync([]);
            }

            return ['imported' => [], 'synced' => true];
        }

        $dryRun = (bool) ($options['dry_run'] ?? false);
        $runOptions = [
            'convert' => true,
            'validate' => ($options['validate'] ?? true) !== false,
            'integrate' => ($options['integrate'] ?? true) && !$dryRun,
            'dry_run' => $dryRun,
            'force_update' => (bool) ($options['force_update'] ?? false),
            'include_relations' => false,
            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
        ];

        $importedIds = [];
        $dofusdbIdsToResolve = array_unique(array_filter(array_map(
            static fn (array $row): string => (string) ($row['ingredient_dofusdb_id'] ?? ''),
            $recipeIngredients
        )));

        foreach ($dofusdbIdsToResolve as $dofusdbId) {
            if ($dofusdbId === '') {
                continue;
            }
            $existing = Resource::where('dofusdb_id', $dofusdbId)->first();
            if ($existing !== null) {
                continue;
            }
            $result = $this->orchestrator->runOne('dofusdb', 'item', (int) $dofusdbId, $runOptions);
            if ($result->isSuccess()) {
                $intResult = $result->getIntegrationResult();
                if ($intResult !== null && $intResult->isSuccess()) {
                    $data = $intResult->getData();
                    if (($data['table'] ?? '') === 'resources') {
                        $id = $intResult->getPrimaryId();
                        if ($id !== null) {
                            $importedIds[] = $id;
                        }
                    }
                }
            } else {
                Log::warning('RelationResolutionService: import ingrédient recette (item) échoué', [
                    'dofusdb_id' => $dofusdbId,
                    'message' => $result->getMessage(),
                ]);
            }
        }

        $resourceIdsByDofusdbId = Resource::whereIn(
            'dofusdb_id',
            array_values($dofusdbIdsToResolve)
        )->pluck('id', 'dofusdb_id')->all();

        $sync = [];
        foreach ($recipeIngredients as $row) {
            $dofusdbId = (string) ($row['ingredient_dofusdb_id'] ?? '');
            $ingredientResourceId = $resourceIdsByDofusdbId[$dofusdbId] ?? null;
            if ($ingredientResourceId !== null) {
                $qty = (int) ($row['quantity'] ?? 1);
                $sync[$ingredientResourceId] = ['quantity' => (string) max(1, $qty)];
            }
        }

        if (!$dryRun) {
            $item->resources()->sync($sync);
        }

        return ['imported' => $importedIds, 'synced' => true];
    }

    /**
     * Résout les ingrédients de recette d'un consommable.
     *
     * @param list<array{ingredient_dofusdb_id?: string, quantity?: int}> $recipeIngredients
     * @param array{integrate?: bool, dry_run?: bool, force_update?: bool, validate?: bool, skip_cache?: bool} $options
     * @return array{imported: list<int>, synced: bool}
     */
    public function resolveAndSyncConsumableRecipe(array $recipeIngredients, int $consumableId, array $options = []): array
    {
        $consumable = Consumable::find($consumableId);
        if ($consumable === null) {
            return ['imported' => [], 'synced' => false];
        }
        if ($recipeIngredients === []) {
            if (!(bool) ($options['dry_run'] ?? false)) {
                $consumable->resources()->sync([]);
            }

            return ['imported' => [], 'synced' => true];
        }

        $dryRun = (bool) ($options['dry_run'] ?? false);
        $runOptions = [
            'convert' => true,
            'validate' => ($options['validate'] ?? true) !== false,
            'integrate' => ($options['integrate'] ?? true) && !$dryRun,
            'dry_run' => $dryRun,
            'force_update' => (bool) ($options['force_update'] ?? false),
            'include_relations' => false,
            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
        ];

        $importedIds = [];
        $dofusdbIdsToResolve = array_unique(array_filter(array_map(
            static fn (array $row): string => (string) ($row['ingredient_dofusdb_id'] ?? ''),
            $recipeIngredients
        )));

        foreach ($dofusdbIdsToResolve as $dofusdbId) {
            if ($dofusdbId === '') {
                continue;
            }
            $existing = Resource::where('dofusdb_id', $dofusdbId)->first();
            if ($existing !== null) {
                continue;
            }
            $result = $this->orchestrator->runOne('dofusdb', 'item', (int) $dofusdbId, $runOptions);
            if ($result->isSuccess()) {
                $intResult = $result->getIntegrationResult();
                if ($intResult !== null && $intResult->isSuccess()) {
                    $data = $intResult->getData();
                    if (($data['table'] ?? '') === 'resources') {
                        $id = $intResult->getPrimaryId();
                        if ($id !== null) {
                            $importedIds[] = $id;
                        }
                    }
                }
            } else {
                Log::warning('RelationResolutionService: import ingrédient recette (consumable) échoué', [
                    'dofusdb_id' => $dofusdbId,
                    'message' => $result->getMessage(),
                ]);
            }
        }

        $resourceIdsByDofusdbId = Resource::whereIn(
            'dofusdb_id',
            array_values($dofusdbIdsToResolve)
        )->pluck('id', 'dofusdb_id')->all();

        $sync = [];
        foreach ($recipeIngredients as $row) {
            $dofusdbId = (string) ($row['ingredient_dofusdb_id'] ?? '');
            $ingredientResourceId = $resourceIdsByDofusdbId[$dofusdbId] ?? null;
            if ($ingredientResourceId !== null) {
                $qty = (int) ($row['quantity'] ?? 1);
                $sync[$ingredientResourceId] = ['quantity' => (string) max(1, $qty)];
            }
        }

        if (!$dryRun) {
            $consumable->resources()->sync($sync);
        }

        return ['imported' => $importedIds, 'synced' => true];
    }

    /**
     * Résout les ingrédients de recette d'une ressource.
     *
     * Si une RelationImportStack est fournie : enregistre les dépendances et ajoute les ingrédients
     * manquants à la pile (l'orchestrateur drainera la pile ensuite). Sinon : import inline comme avant.
     *
     * @param list<array{ingredient_dofusdb_id?: string, quantity?: int}> $recipeIngredients
     * @param array{integrate?: bool, dry_run?: bool, force_update?: bool, validate?: bool} $options
     * @return array{imported: list<int>, synced: bool, pending_added?: list<string>}
     */
    public function resolveAndSyncResourceRecipe(
        array $recipeIngredients,
        int $resourceId,
        array $options = [],
        ?RelationImportStack $stack = null
    ): array {
        $resource = Resource::find($resourceId);
        if ($resource === null) {
            return ['imported' => [], 'synced' => false];
        }
        if ($recipeIngredients === []) {
            if (!(bool) ($options['dry_run'] ?? false)) {
                $resource->recipeIngredients()->sync([]);
            }

            return ['imported' => [], 'synced' => true];
        }

        $dryRun = (bool) ($options['dry_run'] ?? false);

        if ($stack !== null) {
            $pendingAdded = $stack->registerRecipeDependents($resourceId, $recipeIngredients, $dryRun);

            return ['imported' => [], 'synced' => true, 'pending_added' => $pendingAdded];
        }

        return $this->resolveAndSyncResourceRecipeInline($recipeIngredients, $resourceId, $options);
    }

    /**
     * Comportement historique : import inline de chaque ingrédient manquant puis sync.
     *
     * @param list<array{ingredient_dofusdb_id?: string, quantity?: int}> $recipeIngredients
     * @param array{integrate?: bool, dry_run?: bool, force_update?: bool, validate?: bool} $options
     * @return array{imported: list<int>, synced: bool}
     */
    private function resolveAndSyncResourceRecipeInline(array $recipeIngredients, int $resourceId, array $options): array
    {
        $resource = Resource::find($resourceId);
        if ($resource === null) {
            return ['imported' => [], 'synced' => false];
        }

        $dryRun = (bool) ($options['dry_run'] ?? false);
        $runOptions = [
            'convert' => true,
            'validate' => ($options['validate'] ?? true) !== false,
            'integrate' => ($options['integrate'] ?? true) && !$dryRun,
            'dry_run' => $dryRun,
            'force_update' => (bool) ($options['force_update'] ?? false),
            'include_relations' => false,
            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
        ];

        $importedIds = [];
        $dofusdbIdsToResolve = array_unique(array_filter(array_map(
            static fn (array $row): string => (string) ($row['ingredient_dofusdb_id'] ?? ''),
            $recipeIngredients
        )));

        foreach ($dofusdbIdsToResolve as $dofusdbId) {
            if ($dofusdbId === '') {
                continue;
            }
            $existing = Resource::where('dofusdb_id', $dofusdbId)->first();
            if ($existing !== null) {
                continue;
            }
            $result = $this->orchestrator->runOne('dofusdb', 'item', (int) $dofusdbId, $runOptions);
            if ($result->isSuccess()) {
                $intResult = $result->getIntegrationResult();
                if ($intResult !== null && $intResult->isSuccess()) {
                    $data = $intResult->getData();
                    if (($data['table'] ?? '') === 'resources') {
                        $id = $intResult->getPrimaryId();
                        if ($id !== null) {
                            $importedIds[] = $id;
                        }
                    }
                }
            } else {
                Log::warning('RelationResolutionService: import ingrédient recette échoué', [
                    'dofusdb_id' => $dofusdbId,
                    'message' => $result->getMessage(),
                ]);
            }
        }

        $resourceIdsByDofusdbId = Resource::whereIn(
            'dofusdb_id',
            array_values($dofusdbIdsToResolve)
        )->pluck('id', 'dofusdb_id')->all();

        $sync = [];
        foreach ($recipeIngredients as $row) {
            $dofusdbId = (string) ($row['ingredient_dofusdb_id'] ?? '');
            $ingredientResourceId = $resourceIdsByDofusdbId[$dofusdbId] ?? null;
            if ($ingredientResourceId !== null) {
                $qty = (int) ($row['quantity'] ?? 1);
                $sync[$ingredientResourceId] = ['quantity' => (string) max(1, $qty)];
            }
        }

        if (!$dryRun) {
            $resource->recipeIngredients()->sync($sync);
        }

        return ['imported' => $importedIds, 'synced' => true];
    }
}
