<?php

namespace App\Services\Scrapping\Core\Orchestrator;

use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Limit\CharacteristicLimitService;
use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Conversion\ConversionService;
use App\Services\Scrapping\Core\Conversion\FormatterApplicator;
use App\Services\Scrapping\Core\Integration\IntegrationResult;
use App\Services\Scrapping\Core\Integration\IntegrationService;
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
        private ?RelationResolutionService $relationResolutionService = null
    ) {
    }

    /**
     * Crée une instance avec les services par défaut.
     */
    public static function default(): self
    {
        $configLoader = ConfigLoader::default();

        $conversionService = app(DofusConversionService::class);
        $getter = app(CharacteristicGetterService::class);

        $orchestrator = new self(
            $configLoader,
            new CollectService($configLoader),
            new ConversionService(
                $configLoader,
                new FormatterApplicator($conversionService, $getter),
                $conversionService
            ),
            app(CharacteristicLimitService::class),
            new IntegrationService(),
            null
        );
        $orchestrator->setRelationResolutionService(new RelationResolutionService($orchestrator));

        return $orchestrator;
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
        ];
    }

    private function integrationOptions(array $options): array
    {
        $exclude = $options['exclude_from_update'] ?? [];
        $excludeList = is_array($exclude) ? $exclude : [];

        return [
            'dry_run' => (bool) ($options['dry_run'] ?? false),
            'force_update' => (bool) ($options['force_update'] ?? false),
            'ignore_unvalidated' => (bool) ($options['ignore_unvalidated'] ?? false),
            'exclude_from_update' => $excludeList,
            'include_relations' => (bool) ($options['include_relations'] ?? true),
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

            $raw = $this->enrichRawWithRecipe($source, $entity, $raw);
            $context = $this->contextFromOptions($options);
            $context['entityType'] = $entity === 'breed' ? 'class' : $entity;
            $converted = $this->conversionService->convert($source, $entity, $raw, $context);

            $entityConfig = $this->configLoader->loadEntity($source, $entity);
            $entityType = (string) ($entityConfig['target']['krosmozEntity'] ?? $entity);
            if ($entityType === 'item') {
                $entityType = $this->integrationService->getItemTargetTable($converted);
            }

            $doValidate = ($options['validate'] ?? true) !== false;
            if ($doValidate) {
                $validationResult = $this->limitService->validate($converted, $entityType);
                if (!$validationResult->isValid()) {
                    return OrchestratorResult::fail(
                        'Validation échouée.',
                        $validationResult->getErrors()
                    );
                }
            }

            $integrationResult = null;
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
                    $this->resolveRelationsAndDrain($source, $entity, $entityType, $raw, $converted, $integrationResult, $options);
                }
            }

            return OrchestratorResult::ok(
                'OK',
                $raw,
                $converted,
                $integrationResult,
                null,
                null
            );
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

            $raw = $this->enrichRawWithRecipe($source, $entity, $raw);
            $context = $this->contextFromOptions($options);
            $context['entityType'] = $entity === 'breed' ? 'class' : $entity;
            $converted = $this->conversionService->convert($source, $entity, $raw, $context);

            $entityConfig = $this->configLoader->loadEntity($source, $entity);
            $entityType = (string) ($entityConfig['target']['krosmozEntity'] ?? $entity);
            if ($entityType === 'item') {
                $entityType = $this->integrationService->getItemTargetTable($converted);
            }

            $doValidate = ($options['validate'] ?? true) !== false;
            if ($doValidate) {
                $validationResult = $this->limitService->validate($converted, $entityType);
                if (!$validationResult->isValid()) {
                    return OrchestratorResult::fail(
                        'Validation échouée.',
                        $validationResult->getErrors()
                    );
                }
            }

            $integrationResult = null;
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
                    $this->resolveRelationsAndDrain($source, $entity, $entityType, $raw, $converted, $integrationResult, $options);
                }
            }

            return OrchestratorResult::ok(
                'OK',
                $raw,
                $converted,
                $integrationResult,
                null,
                null
            );
        } catch (\Throwable $e) {
            return OrchestratorResult::fail($e->getMessage());
        }
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

            foreach ($items as $i => $raw) {
                if (!is_array($raw)) {
                    continue;
                }
                $raw = $this->enrichRawWithRecipe($source, $entity, $raw);
                $converted = $this->conversionService->convert($source, $entity, $raw, $context);
                $convertedList[] = $converted;

                $entityTypeForItem = $entityType === 'item' ? $this->integrationService->getItemTargetTable($converted) : $entityType;

                $doValidate = ($options['validate'] ?? true) !== false;
                if ($doValidate) {
                    $validationResult = $this->limitService->validate($converted, $entityTypeForItem);
                    if (!$validationResult->isValid()) {
                        foreach ($validationResult->getErrors() as $err) {
                            $allValidationErrors[] = [
                                'path' => "item#{$i}.{$err['path']}",
                                'message' => $err['message'],
                            ];
                        }
                    }
                }

                if (!empty($options['integrate']) && empty($entityConfig['meta']['catalogOnly'] ?? false)) {
                    $intResult = $this->integrationService->integrate(
                        $entityTypeForItem,
                        $converted,
                        $this->integrationOptions($options)
                    );
                    $integrationResults[] = $intResult;
                    if ($this->relationResolutionService !== null && ($options['include_relations'] ?? true) && $intResult->isSuccess()) {
                        $this->resolveRelationsAndDrain($source, $entity, $entityTypeForItem, $rawItem, $converted, $intResult, $options);
                    }
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
    private function enrichRawWithRecipe(string $source, string $entity, array $raw): array
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
        $recipe = $this->collectService->fetchRecipeByResultId($source, $resultId);
        if ($recipe !== null) {
            $raw['recipe'] = $recipe;
        }

        return $raw;
    }

    /**
     * Selon le type d'entité intégrée, enregistre les dépendances sur la pile (sorts, monstres,
     * items, recettes) puis vide la pile. Une seule pile est utilisée par run pour toutes les relations.
     *
     * @param array<string, mixed> $raw Données brutes (pour monster: spells/drops ; pour spell: summon)
     * @param array<string, array<string, mixed>> $converted Données converties
     */
    private function resolveRelationsAndDrain(
        string $source,
        string $entity,
        string $entityType,
        array $raw,
        array $converted,
        \App\Services\Scrapping\Core\Integration\IntegrationResult $integrationResult,
        array $options
    ): void {
        $stack = $options['relation_import_stack'] ?? null;
        if ($stack === null) {
            $stack = new RelationImportStack();
        }
        $relOptions = [
            'integrate' => true,
            'dry_run' => (bool) ($options['dry_run'] ?? false),
            'force_update' => (bool) ($options['force_update'] ?? false),
            'validate' => ($options['validate'] ?? true) !== false,
        ];

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
                $this->relationResolutionService->resolveAndSyncMonsterRelations(
                    $raw,
                    (int) $creatureId,
                    $relOptions,
                    $stack
                );
            }
        }

        $this->drainRelationImportStack($stack, $options);
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

            $result = $this->runOne($source, $entity, (int) $dofusdbId, $runOptions);

            $primaryId = null;
            $table = null;
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
            $stack->onImported($entity, $dofusdbId, $primaryId, $table, $dryRun);
        }
    }
}
