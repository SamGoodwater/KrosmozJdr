<?php

namespace App\Services\Scrapping\Core\Orchestrator;

use App\Models\Entity\Breed;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Services\Characteristic\Limit\CharacteristicLimitService;
use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Conversion\ConversionService;
use App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectsConversionService;
use App\Services\Scrapping\Core\Integration\IntegrationResult;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use App\Services\Scrapping\Core\Normalizer\SpellGlobalNormalizer;
use App\Services\Scrapping\Core\Relation\RelationImportStack;
use App\Services\Scrapping\Core\Relation\RelationResolutionService;

/**
 * Orchestrateur : enchaîne Collecte → Conversion → Validation → Intégration.
 *
 * Un run peut porter sur un objet (runOne) ou une liste (runMany).
 * Options : lang, validate, integrate, dry_run, force_update, include_relations.
 */
final class Orchestrator
{
    public function __construct(
        private ConfigLoader $configLoader,
        private CollectService $collectService,
        private ConversionService $conversionService,
        private CharacteristicLimitService $limitService,
        private IntegrationService $integrationService,
        private SpellEffectsConversionService $spellEffectsConversionService,
        private ?RelationResolutionService $relationResolutionService = null,
        private ?SpellGlobalNormalizer $spellGlobalNormalizer = null
    ) {
    }

    /**
     * Crée une instance avec les services par défaut (délègue à ScrappingPipelineFactory).
     */
    public static function default(): self
    {
        return ScrappingPipelineFactory::createDefault();
    }

    public function setRelationResolutionService(RelationResolutionService $service): void
    {
        $this->relationResolutionService = $service;
    }

    /**
     * Options pour runOne / runMany.
     *
     * @param array{
     *   lang?: string,
     *   validate?: bool,
     *   integrate?: bool,
     *   dry_run?: bool,
     *   force_update?: bool
     * } $options
     */
    private function contextFromOptions(array $options): array
    {
        return [
            'lang' => (string) ($options['lang'] ?? 'fr'),
            'run_id' => isset($options['run_id']) ? (string) $options['run_id'] : null,
        ];
    }

    private function integrationOptions(array $options): array
    {
        $exclude = $options['exclude_from_update'] ?? [];
        $excludeList = is_array($exclude) ? $exclude : [];
        $propertyWhitelist = $options['property_whitelist'] ?? [];
        $propertyWhitelist = is_array($propertyWhitelist) ? $propertyWhitelist : [];
        $replaceMode = isset($options['replace_mode']) ? (string) $options['replace_mode'] : null;
        $forceUpdate = (bool) ($options['force_update'] ?? false);
        if ($replaceMode === 'always') {
            $forceUpdate = true;
        } elseif ($replaceMode === 'never') {
            $forceUpdate = false;
        }

        return [
            'dry_run' => (bool) ($options['dry_run'] ?? false),
            'force_update' => $forceUpdate,
            'replace_mode' => $replaceMode,
            'ignore_unvalidated' => (bool) ($options['ignore_unvalidated'] ?? false),
            'exclude_from_update' => $excludeList,
            'property_whitelist' => $propertyWhitelist,
            'include_relations' => (bool) ($options['include_relations'] ?? true),
            'download_images' => (bool) ($options['download_images'] ?? true),
        ];
    }

    /**
     * Exécute le pipeline pour un seul objet (fetchOne → convert → validate → integrate).
     * Si ni convert ni validate ni integrate : retourne les données brutes uniquement.
     *
     * @param array{
     *   convert?: bool,
     *   lang?: string,
     *   validate?: bool,  défaut true ; false pour bypasser la validation
     *   integrate?: bool,
     *   dry_run?: bool,
     *   force_update?: bool,
     *   ignore_unvalidated?: bool,
     *   exclude_from_update?: list<string>
     * } $options
     */
    public function runOne(string $source, string $entity, int $id, array $options = []): OrchestratorResult
    {
        try {
            $raw = $this->collectService->fetchOne($source, $entity, $id);
            if ($raw === []) {
                return OrchestratorResult::fail("Aucune donnée collectée pour {$source}/{$entity}/{$id}.");
            }

            if ($entity === 'panoply' && ($raw['isCosmetic'] ?? false) === true) {
                return OrchestratorResult::fail('Panoplie cosmétique : seules les panoplies à bonus (stats) sont importables.');
            }

            $doConvert = !empty($options['convert']) || !empty($options['validate']) || !empty($options['integrate']);
            if (!$doConvert) {
                return OrchestratorResult::ok('OK', $raw, null, null, null, null);
            }

            return $this->executePipelineForOneRaw($source, $entity, $raw, $options);
        } catch (\Throwable $e) {
            return OrchestratorResult::fail($e->getMessage());
        }
    }

    /**
     * Exécute convert → validate → integrate avec des données brutes déjà collectées (sans collecte).
     * Utilisé pour monster quand on a déjà les données brutes (ex. avec spells/drops).
     *
     * @param array<string, mixed> $raw Données brutes déjà récupérées
     * @param array{
     *   convert?: bool,
     *   lang?: string,
     *   validate?: bool,
     *   integrate?: bool,
     *   dry_run?: bool,
     *   force_update?: bool,
     *   ignore_unvalidated?: bool,
     *   exclude_from_update?: list<string>
     * } $options
     */
    public function runOneWithRaw(string $source, string $entity, array $raw, array $options = []): OrchestratorResult
    {
        try {
            if ($entity === 'panoply' && ($raw['isCosmetic'] ?? false) === true) {
                return OrchestratorResult::fail('Panoplie cosmétique : seules les panoplies à bonus (stats) sont importables.');
            }

            $doConvert = !empty($options['convert']) || !empty($options['validate']) || !empty($options['integrate']);
            if (!$doConvert) {
                return OrchestratorResult::ok('OK', $raw, null, null, null, null);
            }

            return $this->executePipelineForOneRaw($source, $entity, $raw, $options);
        } catch (\Throwable $e) {
            return OrchestratorResult::fail($e->getMessage());
        }
    }

    /**
     * Exécute le pipeline complet pour un jeu de données brutes : enrichissement → conversion
     * → validation → intégration → relations. Partagé par runOne et runOneWithRaw.
     *
     * @param array<string, mixed> $raw
     * @param array<string, mixed> $options
     */
    private function executePipelineForOneRaw(string $source, string $entity, array $raw, array $options): OrchestratorResult
    {
        $context = $this->contextFromOptions($options);
        $context['entityType'] = $entity === 'breed' ? 'class' : $entity;
        $raw = $this->prepareRawForConversion($source, $entity, $raw, $options);
        $context = $this->prepareContextForEntity($entity, $raw, $context);

        $converted = $this->conversionService->convert($source, $entity, $raw, $context);

        if ($entity === 'spell') {
            $levels = $raw['levels'] ?? [];
            if ($levels !== []) {
                $effectsResult = $this->spellEffectsConversionService->convert($raw, $levels, [
                    'lang' => $context['lang'],
                ]);
                if ($effectsResult->hasEffects()) {
                    $converted['spell_effects'] = [
                        'effect_group' => $effectsResult->getEffectGroup(),
                        'effects' => $effectsResult->getEffects(),
                    ];
                }
            }
        }

        $entityConfig = $this->configLoader->loadEntity($source, $entity);
        $entityType = (string) ($entityConfig['target']['krosmozEntity'] ?? $entity);
        if ($entityType === 'item') {
            $entityType = $this->integrationService->getItemTargetTable($converted);
        }

        $doValidate = ($options['validate'] ?? true) !== false;
        if ($doValidate) {
            $converted = $this->limitService->clampConvertedData($converted, $entityType);
            $validationResult = $this->limitService->validate($converted, $entityType);
            if (!$validationResult->isValid()) {
                return OrchestratorResult::validationFailed(
                    'Validation échouée.',
                    $validationResult->getErrors(),
                    $raw,
                    $converted
                );
            }
        }

        $integrationResult = null;
        $relations = null;
        if (!empty($options['integrate'])) {
            $integrationResult = $this->integrationService->integrate(
                $entityType,
                $converted,
                $this->integrationOptions($options)
            );
            if (!$integrationResult->isSuccess()) {
                return OrchestratorResult::fail($integrationResult->getMessage());
            }
            if ($this->relationResolutionService !== null && ($options['include_relations'] ?? true)) {
                $relations = $this->resolveRelationsAndDrain($source, $entity, $entityType, $raw, $converted, $integrationResult, $options);
            }
        }

        return OrchestratorResult::ok(
            'OK',
            $raw,
            $converted,
            $integrationResult,
            null,
            null,
            $relations
        );
    }

    /**
     * Exécute le pipeline pour une liste (fetchMany → convert/validate/integrate par item).
     *
     * @param array<string, mixed> $filters
     * @param array{
     *   limit?: int,
     *   offset?: int,
     *   lang?: string,
     *   validate?: bool,
     *   integrate?: bool,
     *   dry_run?: bool,
     *   force_update?: bool,
     *   ignore_unvalidated?: bool,
     *   exclude_from_update?: list<string>
     * } $options
     */
    public function runMany(string $source, string $entity, array $filters = [], array $options = []): OrchestratorResult
    {
        try {
            $collectOptions = [
                'limit' => (int) ($options['limit'] ?? 0),
                'offset' => (int) ($options['offset'] ?? 0),
            ];
            if (isset($options['page_size'])) {
                $collectOptions['page_size'] = (int) $options['page_size'];
            }
            if (isset($options['skip_cache'])) {
                $collectOptions['skip_cache'] = (bool) $options['skip_cache'];
            }
            $result = $this->collectService->fetchMany($source, $entity, $filters, $collectOptions);
            $items = $result['items'];
            $meta = $result['meta'];

            $doConvert = !empty($options['convert']) || !empty($options['validate']) || !empty($options['integrate']);
            if (!$doConvert) {
                $totalApi = (int) ($meta['total'] ?? count($items));
                $collected = (int) ($meta['collected'] ?? count($items));
                $msg = $totalApi > 0
                    ? sprintf('%d objet(s) collectés (offset=%d, limit=%s, total API: %d)', $collected, $meta['offset'] ?? 0, $meta['limit'] === 0 ? 'tout' : (string) $meta['limit'], $totalApi)
                    : sprintf('%d objet(s) collectés (offset=%d, limit=%s)', $collected, $meta['offset'] ?? 0, $meta['limit'] === 0 ? 'tout' : (string) $meta['limit']);
                return OrchestratorResult::ok(
                    $msg,
                    null,
                    $items,
                    null,
                    null,
                    $meta
                );
            }

            $context = $this->contextFromOptions($options);
            $context['entityType'] = $entity === 'breed' ? 'class' : $entity;
            $entityConfig = $this->configLoader->loadEntity($source, $entity);
            $entityType = (string) ($entityConfig['target']['krosmozEntity'] ?? $entity);

            $convertedList = [];
            $allValidationErrors = [];
            $integrationResults = [];
            /** @var array<int, array<string, mixed>> $spellLevelsCache */
            $spellLevelsCache = [];
            /** @var array<int, array<string, mixed>|null> $recipeCache */
            $recipeCache = [];

            foreach ($items as $i => $raw) {
                if (!is_array($raw)) {
                    continue;
                }
                $raw = $this->prepareRawForConversion($source, $entity, $raw, $options, $spellLevelsCache, $recipeCache);
                $itemContext = $this->prepareContextForEntity($entity, $raw, $context);
                $converted = $this->conversionService->convert($source, $entity, $raw, $itemContext);

                if ($entity === 'spell') {
                    $levels = $raw['levels'] ?? [];
                    if ($levels !== []) {
                        $effectsResult = $this->spellEffectsConversionService->convert($raw, $levels, [
                            'lang' => $itemContext['lang'],
                        ]);
                        $converted['spell_effects'] = [
                            'effect_group' => $effectsResult->getEffectGroup(),
                            'effects' => $effectsResult->getEffects(),
                        ];
                    }
                }

                $convertedList[] = $converted;

                $entityTypeForItem = $entityType === 'item' ? $this->integrationService->getItemTargetTable($converted) : $entityType;

                $doValidate = ($options['validate'] ?? true) !== false;
                $itemValid = true;
                if ($doValidate) {
                    $converted = $this->limitService->clampConvertedData($converted, $entityTypeForItem);
                    $validationResult = $this->limitService->validate($converted, $entityTypeForItem);
                    if (!$validationResult->isValid()) {
                        $itemValid = false;
                        foreach ($validationResult->getErrors() as $err) {
                            $allValidationErrors[] = [
                                'path' => "item#{$i}.{$err['path']}",
                                'message' => $err['message'],
                            ];
                        }
                    }
                }

                // N'intégrer que les items dont la validation a réussi (ou lorsque la validation est désactivée).
                if ($itemValid && !empty($options['integrate']) && empty($entityConfig['meta']['catalogOnly'] ?? false)) {
                    $intResult = $this->integrationService->integrate(
                        $entityTypeForItem,
                        $converted,
                        $this->integrationOptions($options)
                    );
                    $integrationResults[] = $intResult;
                    if ($this->relationResolutionService !== null && ($options['include_relations'] ?? true) && $intResult->isSuccess()) {
                        $this->resolveRelationsAndDrain($source, $entity, $entityTypeForItem, $raw, $converted, $intResult, $options);
                    }
                } elseif (!empty($options['integrate']) && empty($entityConfig['meta']['catalogOnly'] ?? false) && !$itemValid) {
                    // Conserver l'alignement des index avec convertedList pour le rapport (item non intégré car invalide).
                    $integrationResults[] = IntegrationResult::fail('Validation échouée.');
                }
            }

            if ($allValidationErrors !== []) {
                return OrchestratorResult::fail(
                    'Validation échouée sur un ou plusieurs objets.',
                    $allValidationErrors
                );
            }

            $integrationResultsOrNull = $integrationResults !== [] ? $integrationResults : null;

            $totalApi = (int) ($meta['total'] ?? count($convertedList));
            $collected = (int) ($meta['collected'] ?? count($convertedList));
            $msg = $totalApi > 0
                ? sprintf('%d objet(s) traités (offset=%d, limit=%s, total API: %d)', $collected, $meta['offset'] ?? 0, $meta['limit'] === 0 ? 'tout' : (string) $meta['limit'], $totalApi)
                : sprintf('%d objet(s) traités (offset=%d, limit=%s)', $collected, $meta['offset'] ?? 0, $meta['limit'] === 0 ? 'tout' : (string) $meta['limit']);
            return OrchestratorResult::ok(
                $msg,
                null,
                $convertedList,
                null,
                $integrationResultsOrNull,
                $meta
            );
        } catch (\Throwable $e) {
            return OrchestratorResult::fail($e->getMessage());
        }
    }

    /**
     * Enrichit le payload brut avec la recette (ingredientIds + quantities) si l'objet a une recette.
     * Appelle l'API /recipes?resultId= pour obtenir les quantités réelles.
     *
     * @param array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private function enrichRawWithRecipe(
        string $source,
        string $entity,
        array $raw,
        ?array &$recipeCache = null
    ): array
    {
        if (!in_array($entity, ['item', 'resource', 'consumable'], true)) {
            return $raw;
        }
        $hasRecipe = (bool) ($raw['hasRecipe'] ?? false);
        $recipeIds = $raw['recipeIds'] ?? [];
        if (!$hasRecipe && (!is_array($recipeIds) || $recipeIds === [])) {
            return $raw;
        }
        $resultId = isset($raw['id']) && is_numeric($raw['id']) ? (int) $raw['id'] : 0;
        if ($resultId <= 0) {
            return $raw;
        }
        if ($recipeCache !== null && array_key_exists($resultId, $recipeCache)) {
            $recipe = $recipeCache[$resultId];
        } else {
            $recipe = $this->collectService->fetchRecipeByResultId($source, $resultId);
            if ($recipeCache !== null) {
                $recipeCache[$resultId] = $recipe;
            }
        }
        if ($recipe !== null) {
            $raw['recipe'] = $recipe;
        }

        return $raw;
    }

    /**
     * Prépare les données brutes avant conversion (enrichissements communs + spécifiques entité).
     *
     * @param array<string, mixed> $raw
     * @param array<string, mixed> $options
     * @param array<int, array<string, mixed>>|null $spellLevelsCache Cache local runMany pour éviter les requêtes répétées.
     * @param array<int, array<string, mixed>|null>|null $recipeCache Cache local runMany des recettes par resultId.
     * @return array<string, mixed>
     */
    private function prepareRawForConversion(
        string $source,
        string $entity,
        array $raw,
        array $options,
        ?array &$spellLevelsCache = null,
        ?array &$recipeCache = null
    ): array {
        $raw = $this->enrichRawWithRecipe($source, $entity, $raw, $recipeCache);

        if ($entity !== 'spell') {
            return $raw;
        }

        $spellId = isset($raw['id']) && is_numeric($raw['id']) ? (int) $raw['id'] : 0;
        if ($spellId > 0) {
            if ($spellLevelsCache !== null && array_key_exists($spellId, $spellLevelsCache)) {
                $raw['levels'] = $spellLevelsCache[$spellId];
            } else {
                $levels = $this->collectService->fetchSpellLevelsBySpellId($source, $spellId, [
                    'skip_cache' => (bool) ($options['skip_cache'] ?? false),
                ]);
                $raw['levels'] = $levels;
                if ($spellLevelsCache !== null) {
                    $spellLevelsCache[$spellId] = $levels;
                }
            }
        }

        if ($this->spellGlobalNormalizer !== null) {
            $raw['spell_global'] = $this->spellGlobalNormalizer->build($raw);
        }

        return $raw;
    }

    /**
     * Prépare le contexte de conversion selon l'entité.
     *
     * @param array<string, mixed> $raw
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    private function prepareContextForEntity(string $entity, array $raw, array $context): array
    {
        if ($entity === 'item') {
            $context['targetModel'] = $this->integrationService->getItemTargetTableFromRaw($raw);
        }

        return $context;
    }

    /**
     * Normalise les données brutes monster pour la résolution des relations : l’API DofusDB renvoie
     * spells comme tableau d’IDs [10198, 10199] et drops avec objectId/count au lieu de itemId/quantity.
     *
     * @param array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private function normalizeMonsterRawForRelations(array $raw): array
    {
        $out = $raw;
        if (isset($raw['spells']) && is_array($raw['spells'])) {
            $spells = [];
            foreach ($raw['spells'] as $s) {
                $id = is_array($s) ? (int) ($s['id'] ?? 0) : (int) $s;
                if ($id > 0) {
                    $spells[] = ['id' => $id];
                }
            }
            $out['spells'] = $spells;
        }
        if (isset($raw['drops']) && is_array($raw['drops'])) {
            $drops = [];
            foreach ($raw['drops'] as $d) {
                if (!is_array($d)) {
                    continue;
                }
                $itemId = (int) ($d['itemId'] ?? $d['objectId'] ?? $d['id'] ?? 0);
                if ($itemId <= 0) {
                    continue;
                }
                $qty = (int) ($d['quantity'] ?? $d['count'] ?? 1);
                $drops[] = ['itemId' => $itemId, 'id' => $itemId, 'quantity' => max(1, $qty)];
            }
            $out['drops'] = $drops;
        }
        return $out;
    }

    /**
     * Selon le type d'entité intégrée, enregistre les dépendances sur la pile (sorts, monstres,
     * items, recettes) puis vide la pile. Une seule pile est utilisée par run pour toutes les relations.
     * Retourne la liste des relations (type + id DofusDB) pour affichage dans le tableau (monster → spells, drops).
     *
     * @param array<string, mixed> $raw Données brutes (pour monster: spells/drops ; pour spell: summon)
     * @param array<string, array<string, mixed>> $converted Données converties
     * @return list<array{type: string, id: int}>
     */
    private function resolveRelationsAndDrain(
        string $source,
        string $entity,
        string $entityType,
        array $raw,
        array $converted,
        \App\Services\Scrapping\Core\Integration\IntegrationResult $integrationResult,
        array $options
    ): array {
        $stack = $options['relation_import_stack'] ?? null;
        if ($stack === null) {
            $stack = new RelationImportStack();
        }
        $relOptions = [
            'integrate' => true,
            'dry_run' => (bool) ($options['dry_run'] ?? false),
            'force_update' => (bool) ($options['force_update'] ?? false),
            'validate' => ($options['validate'] ?? true) !== false,
            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
        ];

        $relations = [];

        if ($entityType === 'resource' || $entityType === 'resources') {
            $primaryId = $integrationResult->getPrimaryId();
            if ($primaryId !== null) {
                $recipeIngredients = $converted['resources']['recipe_ingredients'] ?? [];
                $this->relationResolutionService->resolveAndSyncResourceRecipe(
                    is_array($recipeIngredients) ? $recipeIngredients : [],
                    (int) $primaryId,
                    $relOptions,
                    $stack
                );
            }
        } elseif ($entityType === 'breed' || $entity === 'breed' || $entity === 'class') {
            $breedId = $integrationResult->getPrimaryId();
            $breedDofusdbId = isset($raw['id']) ? (int) $raw['id'] : 0;
            if ($breedId !== null && $breedDofusdbId > 0) {
                $this->relationResolutionService->resolveAndSyncBreedSpells(
                    (int) $breedId,
                    $source,
                    $breedDofusdbId,
                    $relOptions,
                    $stack
                );
            }
        } elseif ($entityType === 'spell' || $entity === 'spell') {
            $spellId = $integrationResult->getPrimaryId();
            $summon = $raw['summon'] ?? null;
            $monsterDofusdbId = (is_array($summon) && isset($summon['id'])) ? (string) $summon['id'] : '';
            if ($spellId !== null && $monsterDofusdbId !== '') {
                $this->relationResolutionService->resolveAndSyncSpellInvocation(
                    (int) $spellId,
                    $monsterDofusdbId,
                    $relOptions,
                    $stack
                );
            }
        } elseif ($entityType === 'monster') {
            $creatureId = $integrationResult->getCreatureId();
            if ($creatureId !== null) {
                $normalizedRaw = $this->normalizeMonsterRawForRelations($raw);
                $this->relationResolutionService->resolveAndSyncMonsterRelations(
                    $normalizedRaw,
                    (int) $creatureId,
                    $relOptions,
                    $stack
                );
                foreach ($normalizedRaw['spells'] ?? [] as $s) {
                    $id = (int) ($s['id'] ?? 0);
                    if ($id > 0) {
                        $relations[] = ['type' => 'spell', 'id' => $id];
                    }
                }
                foreach ($normalizedRaw['drops'] ?? [] as $d) {
                    $id = (int) ($d['itemId'] ?? $d['id'] ?? 0);
                    if ($id > 0) {
                        $relations[] = ['type' => 'item', 'id' => $id];
                    }
                }
            }
        } elseif ($entityType === 'panoply') {
            $panoplyId = $integrationResult->getPrimaryId();
            if ($panoplyId !== null) {
                $itemDofusdbIds = [];
                $items = $raw['items'] ?? [];
                if (is_array($items)) {
                    foreach ($items as $itemRow) {
                        if (!is_array($itemRow)) {
                            continue;
                        }
                        $id = (int) ($itemRow['id'] ?? 0);
                        if ($id > 0) {
                            $itemDofusdbIds[] = $id;
                            $relations[] = ['type' => 'item', 'id' => $id];
                        }
                    }
                }
                $this->relationResolutionService->resolveAndSyncPanoplyItems(
                    (int) $panoplyId,
                    $itemDofusdbIds,
                    $relOptions,
                    $stack
                );
            }
        }

        $this->drainRelationImportStack($stack, $options);

        return $relations;
    }

    /**
     * Vide la pile d'import des relations : pour chaque élément en attente, runOne puis
     * onImported pour mettre à jour toutes les tables de relation (recettes, breed_spell,
     * creature_spell, creature_resource, spell_invocation).
     *
     * @param array{lang?: string, validate?: bool, integrate?: bool, dry_run?: bool, force_update?: bool} $options
     */
    private function drainRelationImportStack(RelationImportStack $stack, array $options): void
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $runOptions = array_merge($options, [
            'relation_import_stack' => $stack,
            'convert' => true,
            'validate' => ($options['validate'] ?? true) !== false,
            'integrate' => ($options['integrate'] ?? true) && !$dryRun,
        ]);

        while ($stack->hasPending()) {
            $next = $stack->popPending();
            if ($next === null) {
                break;
            }
            $source = (string) ($next['source'] ?? 'dofusdb');
            $entity = $next['entity'] ?? 'item';
            $dofusdbId = (string) ($next['dofusdb_id'] ?? '');
            if ($dofusdbId === '') {
                continue;
            }

            $primaryId = null;
            $table = null;
            $existing = $this->resolveExistingRelationImportState($entity, $dofusdbId);
            if ($existing !== null) {
                $primaryId = $existing['primary_id'];
                $table = $existing['table'];
            } else {
                $result = $this->runOne($source, $entity, (int) $dofusdbId, $runOptions);
                if ($result->isSuccess()) {
                    $intResult = $result->getIntegrationResult();
                    if ($intResult !== null && $intResult->isSuccess()) {
                        $data = $intResult->getData();
                        $table = isset($data['table']) ? (string) $data['table'] : null;
                        if ($entity === 'monster') {
                            $primaryId = $intResult->getMonsterId() ?? $intResult->getCreatureId();
                        } else {
                            $primaryId = $intResult->getPrimaryId();
                        }
                    }
                }
            }
            $stack->onImported($entity, $dofusdbId, $primaryId, $table, $dryRun);
        }
    }

    /**
     * Retourne l'état d'une entité déjà importée pour éviter un runOne redondant dans le drain.
     *
     * @return array{primary_id: int, table: string|null}|null
     */
    private function resolveExistingRelationImportState(string $entity, string $dofusdbId): ?array
    {
        if ($dofusdbId === '') {
            return null;
        }

        if ($entity === 'spell') {
            $spell = Spell::query()->where('dofusdb_id', $dofusdbId)->first();
            return $spell !== null ? ['primary_id' => (int) $spell->id, 'table' => 'spells'] : null;
        }

        if ($entity === 'breed' || $entity === 'class') {
            $breed = Breed::query()->where('dofusdb_id', $dofusdbId)->first();
            return $breed !== null ? ['primary_id' => (int) $breed->id, 'table' => 'breeds'] : null;
        }

        if ($entity === 'monster') {
            $monster = Monster::query()->where('dofusdb_id', $dofusdbId)->first();
            return $monster !== null ? ['primary_id' => (int) $monster->id, 'table' => 'monsters'] : null;
        }

        if ($entity === 'item') {
            $resource = Resource::query()->where('dofusdb_id', $dofusdbId)->first();
            if ($resource !== null) {
                return ['primary_id' => (int) $resource->id, 'table' => 'resources'];
            }
            $item = Item::query()->where('dofusdb_id', $dofusdbId)->first();
            if ($item !== null) {
                return ['primary_id' => (int) $item->id, 'table' => 'items'];
            }
            $consumable = Consumable::query()->where('dofusdb_id', $dofusdbId)->first();
            if ($consumable !== null) {
                return ['primary_id' => (int) $consumable->id, 'table' => 'consumables'];
            }
        }

        return null;
    }
}
