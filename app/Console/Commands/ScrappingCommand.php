<?php

namespace App\Console\Commands;

use App\Models\Entity\Resource;
use App\Models\Type\ResourceType;
use App\Models\User;
use App\Services\Scrapping\Catalog\DofusDbItemSuperTypeMappingService;
use App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService;
use App\Services\Scrapping\Catalog\DofusDbMonsterRacesCatalogService;
use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use App\Services\Scrapping\Core\Preview\ScrappingPreviewBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Commande unique pour le scrapping (collect/search/import).
 *
 * @description
 * Objectif : remplacer la multiplication de commandes par un point d'entrée unique.
 * - `--collect=...` : collecte (via configs JSON) et affiche le résultat
 * - `--save` ou `--import=...` : lance l'import via l'orchestrateur
 * - `--compare` : utilise la prévisualisation (raw/converted/existing)
 *
 * @example
 * php artisan scrapping --collect=monster --id=31 --compare
 * php artisan scrapping --collect=item --name="Bouftou" --limit=50
 * php artisan scrapping --import=monster --ids=31,32 --skip-cache
 */
class ScrappingCommand extends Command
{
    protected $signature = 'scrapping
        {--collect= : Entités à collecter (ex: monster,item,spell)}
        {--import= : Entités à importer (collect + conversion + intégration) (ex: monster,item)}
        {--batch= : Fichier JSON d\'import en lot (tableau ou {entities:[...]})}
        {--sync-resource-types : Synchronise les item-types "Ressource" (superTypeId=9) dans resource_types}
        {--decision=pending : (sync-resource-types) Décision par défaut (pending|allowed)}
        {--save : Sauvegarde (équivalent import si --collect est fourni)}
        {--compare : Prévisualise et compare avec la DB (raw/converted/existing)}
        {--include-relations=1 : Inclure les relations (1/0) pour import/preview}
        {--force-update : Force la mise à jour même si l\'entité existe déjà}
        {--replace-existing : Alias de --force-update}
        {--validate-only : Valide uniquement sans sauvegarder}
        {--no-validate : Ne pas valider (validation activée par défaut)}
        {--dry-run : N\'écrit rien (si import)}
        {--exclude-from-update= : Champs à ne pas écraser à l\'update (ex: name,image,level)}
        {--ignore-unvalidated : Ignorer les objets dont la race/type n\'est pas validé}
        {--lang=fr : Langue pour la conversion (pickLang)}
        {--type-name= : Filtre par nom de type/catégorie (ex: Ressource, Pierre brute)}
        {--race-name= : Filtre par nom de race (ex: Bandits d\'Amakna)}
        {--noimage : Désactive le téléchargement/stockage d\'images}
        {--skip-cache : Ignore le cache HTTP lors de la collecte}
        {--id= : ID unique DofusDB}
        {--ids= : Liste d\'IDs DofusDB (séparés par des virgules)}
        {--resource-types= : Pour resource: utilise les typeId depuis resource_types (allowed)}
        {--per-type=1 : Pour resource-types=allowed: itère typeId par typeId (1/0)}
        {--name= : Filtre de recherche (texte)}
        {--typeId= : Filtre typeId DofusDB (items)}
        {--type= : Alias de --typeId}
        {--raceId= : Filtre raceId (monsters)}
        {--breedId= : Filtre breedId (spells)}
        {--levelMin= : Filtre level minimum (monsters)}
        {--levelMax= : Filtre level maximum (monsters)}
        {--limit=50 : Taille de page}
        {--start-skip=0 : Skip initial (pour reprendre une pagination)}
        {--max-pages=0 : Nombre max de pages (0 = illimité)}
        {--max-items=0 : Nombre max d\'items au total (0 = illimité)}
        {--output= : Sortie : raw (tout DofusDB), useful (propriétés utiles), summary (comptes)}
        {--useful= : Si output=useful : raw,converted,validated,compared (1 ou plusieurs, séparés par des virgules)}
        {--json : Sortie JSON (pour traitement ; sinon sortie lisible et colorée)}';

    protected $description = 'Commande unique pour collect/search/import du scrapping (config-driven).';

    public function handle(CollectService $collectService, Orchestrator $orchestrator): int
    {
        if ((bool) $this->option('sync-resource-types')) {
            return $this->handleSyncResourceTypes();
        }

        if ($this->option('batch')) {
            return $this->handleBatchImport($orchestrator);
        }

        $collectEntities = $this->parseEntityList((string) ($this->option('collect') ?? ''));
        $importEntities = $this->parseEntityList((string) ($this->option('import') ?? ''));

        $doSave = (bool) $this->option('save') || !empty($importEntities);
        $outputMode = (string) $this->option('output');
        if ($outputMode !== '' && !in_array($outputMode, ['raw', 'useful', 'summary'], true)) {
            $this->error("Option --output invalide: {$outputMode}. Valeurs: raw, useful, summary.");
            return Command::FAILURE;
        }
        $usefulInclude = $this->parseUsefulInclude((string) $this->option('useful'), $outputMode);
        $outputAsJson = (bool) $this->option('json');
        $outputVerbose = !$outputAsJson;

        $entities = !empty($importEntities) ? $importEntities : $collectEntities;
        if (empty($entities)) {
            $this->error("Aucune entité fournie. Utilise --collect=... ou --import=...");
            return Command::FAILURE;
        }

        if ((bool) $this->option('noimage')) {
            config(['scrapping.images.enabled' => false]);
        }

        $ids = $this->parseIds(
            $this->option('id'),
            $this->option('ids')
        );

        $filters = $this->extractFilters($ids);
        $options = $this->extractCollectOptions();

        $results = [
            'mode' => [
                'collect' => !empty($collectEntities),
                'import' => !empty($importEntities),
                'save' => $doSave,
                'output' => $outputMode !== '' ? $outputMode : null,
                'useful_include' => $outputMode === 'useful' ? $usefulInclude : null,
            ],
            'query' => [
                'entities' => $entities,
                'filters' => $filters,
                'options' => $options,
            ],
            'entities' => [],
        ];
        if ($outputMode === 'summary') {
            $results['summary'] = ['collected' => 0, 'converted' => 0, 'validated' => 0, 'integrated' => 0];
        }

        foreach ($entities as $entity) {
            $normalizedEntity = $this->normalizeEntity($entity);

            $this->info("➡️  {$normalizedEntity}");

            // Cas spécial: resource = items côté DofusDB, mais import via importResource
            $collectorEntity = $normalizedEntity === 'resource' ? 'item' : $normalizedEntity;

            $entityResult = [
                'entity' => $normalizedEntity,
                'collectorEntity' => $collectorEntity,
                'ids' => [],
                'items' => [],
                'imported' => [],
                'previews' => [],
                'errors' => [],
            ];

            try {
                // Mode resource-types=allowed: on dérive les typeId depuis la DB.
                $resourceTypesMode = $normalizedEntity === 'resource' && (string) ($this->option('resource-types') ?? '') === 'allowed';

                if ($resourceTypesMode) {
                    $typeIds = ResourceType::query()
                        ->allowed()
                        ->whereNotNull('dofusdb_type_id')
                        ->pluck('dofusdb_type_id')
                        ->map(fn ($v) => (int) $v)
                        ->values()
                        ->all();

                    if (empty($typeIds)) {
                        throw new \RuntimeException("Aucun typeId autorisé trouvé dans resource_types (decision=allowed).");
                    }

                    $perType = (bool) ((int) $this->option('per-type'));
                    $entityResult['resourceTypeIds'] = $typeIds;
                    $entityResult['ids'] = [];
                    $entityResult['items'] = [];
                    $entityResult['metaByType'] = [];

                    $typeIdLoops = $perType ? $typeIds : [null];
                    foreach ($typeIdLoops as $onlyTypeId) {
                        $localFilters = $filters;
                        if ($onlyTypeId !== null) {
                            $localFilters['typeId'] = $onlyTypeId;
                        }

                        $search = $collectService->fetchManyResult('dofusdb', $collectorEntity, $localFilters, $options);
                        $items = $search['items'] ?? [];
                        $entityResult['items'] = array_merge($entityResult['items'], $items);
                        $entityResult['metaByType'][] = [
                            'typeId' => $onlyTypeId,
                            'meta' => $search['meta'] ?? [],
                            'returned' => is_array($items) ? count($items) : 0,
                        ];

                        foreach ($items as $row) {
                            if (is_array($row) && isset($row['id'])) {
                                $entityResult['ids'][] = (int) $row['id'];
                            }
                        }
                    }

                    $entityResult['ids'] = array_values(array_unique(array_filter($entityResult['ids'])));
                    $this->line("  - trouvés: " . count($entityResult['ids']));
                } elseif (!empty($ids)) {
                    $entityResult['ids'] = $ids;

                    // Collecte unitaire (fetchOne) pour avoir du contenu même en mode IDs.
                    $entityResult['items'] = [];
                    foreach ($ids as $id) {
                            $entityResult['items'][] = $collectService->fetchOne('dofusdb', $collectorEntity, (int) $id, [
                            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
                        ]);
                    }
                } else {
                    $search = $collectService->fetchManyResult('dofusdb', $collectorEntity, $filters, $options);
                    $items = $search['items'] ?? [];
                    $entityResult['items'] = $items;
                    $entityResult['meta'] = $search['meta'] ?? [];

                    $foundIds = [];
                    foreach ($items as $row) {
                        if (is_array($row) && isset($row['id'])) {
                            $foundIds[] = (int) $row['id'];
                        }
                    }
                    $foundIds = array_values(array_unique(array_filter($foundIds)));
                    $entityResult['ids'] = $foundIds;

                    $this->line("  - trouvés: " . count($foundIds));
                }

                if ($outputMode === 'useful') {
                    $configLoader = app(ConfigLoader::class);
                    $integrationService = app(IntegrationService::class);
                    $entityConfig = $configLoader->loadEntity('dofusdb', $collectorEntity);
                    $entityTypeKrosmoz = (string) ($entityConfig['target']['krosmozEntity'] ?? $collectorEntity);
                    $runOptsBase = ['convert' => true, 'validate' => !(bool) $this->option('no-validate'), 'integrate' => false, 'lang' => (string) $this->option('lang', 'fr')];
                    $includeRaw = in_array('raw', $usefulInclude, true);
                    $includeConverted = in_array('converted', $usefulInclude, true);
                    $includeValidated = in_array('validated', $usefulInclude, true);
                    $includeCompared = in_array('compared', $usefulInclude, true);
                    $entityResult['output_items'] = [];
                    foreach ($entityResult['items'] as $idx => $rawItem) {
                        if (!is_array($rawItem)) {
                            continue;
                        }
                        $rawUseful = ScrappingPreviewBuilder::buildRawUseful($rawItem, $entityConfig);
                        $itemOut = ['dofusdb_id' => $rawItem['id'] ?? null, 'index' => $idx];
                        $converted = null;
                        $merged = [];
                        $validationErrors = [];
                        $existing = null;
                        $validationValid = null;
                        if ($includeConverted || $includeValidated || $includeCompared) {
                            $runResult = $orchestrator->runOneWithRaw('dofusdb', $collectorEntity, $rawItem, $runOptsBase);
                            $converted = $runResult->getConverted();
                            $validationErrors = $runResult->getValidationErrors();
                            $validationValid = $runResult->isSuccess();
                            $merged = ScrappingPreviewBuilder::mergeConverted($converted ?? []);
                            if ($includeConverted || $includeCompared) {
                                $existing = $integrationService->getExistingAttributesForComparison($entityTypeKrosmoz, $converted ?? []);
                            }
                        }
                        if ($includeCompared) {
                            $itemOut['krosmoz_id'] = isset($existing['id']) ? (int) $existing['id'] : null;
                        }
                        $errorPaths = [];
                        foreach ($validationErrors as $err) {
                            $path = $err['path'] ?? '';
                            if ($path !== '') {
                                $errorPaths[$path] = true;
                                $base = explode('.', $path)[0] ?? $path;
                                $errorPaths[$base] = true;
                            }
                        }
                        $allKeys = array_values(array_unique(array_keys($rawUseful + $merged + ($existing ?? []))));
                        $properties = [];
                        foreach ($allKeys as $key) {
                            $prop = [];
                            if ($includeRaw) {
                                $prop['raw_value'] = $rawUseful[$key] ?? null;
                            }
                            if ($includeConverted) {
                                $prop['converted_value'] = $merged[$key] ?? null;
                            }
                            if ($includeCompared && $existing !== null) {
                                $prop['existing_value'] = $existing[$key] ?? null;
                            }
                            if ($includeValidated) {
                                $prop['valid'] = !isset($errorPaths[$key]);
                            }
                            $properties[$key] = $prop;
                        }
                        $itemOut['properties'] = $properties;
                        if ($includeValidated) {
                            $itemOut['validation_valid'] = $validationValid;
                            $itemOut['validation_errors'] = $validationErrors;
                        }
                        $recipeIngredients = $merged['recipe_ingredients'] ?? [];
                        $relationObjectsMissing = [];
                        if (is_array($recipeIngredients)) {
                            foreach ($recipeIngredients as $row) {
                                $id = (string) ($row['ingredient_dofusdb_id'] ?? '');
                                if ($id !== '' && ! Resource::where('dofusdb_id', $id)->exists()) {
                                    $relationObjectsMissing[] = [
                                        'dofusdb_id' => $id,
                                        'quantity' => (int) ($row['quantity'] ?? 1),
                                    ];
                                }
                            }
                        }
                        $itemOut['relation_objects_missing'] = $relationObjectsMissing;
                        $entityResult['output_items'][] = $itemOut;
                    }
                    $entityResult['items'] = array_map(
                        fn ($raw) => is_array($raw) && isset($raw['id']) ? ['dofusdb_id' => $raw['id']] : [],
                        $entityResult['items']
                    );
                }

                if ($outputMode === 'summary') {
                    $summaryCounts = ['collected' => count($entityResult['items']), 'converted' => 0, 'validated' => 0];
                    foreach ($entityResult['items'] as $rawItem) {
                        if (!is_array($rawItem) || !isset($rawItem['id'])) {
                            continue;
                        }
                        $summaryCounts['converted']++;
                        $runResult = $orchestrator->runOneWithRaw('dofusdb', $collectorEntity, $rawItem, [
                            'convert' => true,
                            'validate' => !(bool) $this->option('no-validate'),
                            'integrate' => false,
                            'lang' => (string) $this->option('lang', 'fr'),
                        ]);
                        if ($runResult->isSuccess()) {
                            $summaryCounts['validated']++;
                        }
                    }
                    $results['summary']['collected'] = ($results['summary']['collected'] ?? 0) + $summaryCounts['collected'];
                    $results['summary']['converted'] = ($results['summary']['converted'] ?? 0) + $summaryCounts['converted'];
                    $results['summary']['validated'] = ($results['summary']['validated'] ?? 0) + $summaryCounts['validated'];
                }

                if ($doSave) {
                    $importOptions = $this->buildImportOptions($options);

                    foreach ($entityResult['ids'] as $id) {
                        $entityResult['imported'][] = $this->importOne($orchestrator, $normalizedEntity, (int) $id, $importOptions);
                    }
                    if ($outputMode === 'summary') {
                        $integratedCount = collect($entityResult['imported'])->filter(fn ($r) => (bool) ($r['success'] ?? false))->count();
                        $results['summary']['integrated'] = ($results['summary']['integrated'] ?? 0) + $integratedCount;
                    }
                }
                if ($outputMode === 'summary' && !$doSave) {
                    $results['summary']['integrated'] = $results['summary']['integrated'] ?? 0;
                }

                if ($outputVerbose && !$outputAsJson) {
                    if ($doSave) {
                        $ok = collect($entityResult['imported'])->filter(fn ($r) => (bool) ($r['success'] ?? false))->count();
                        $this->line("  - imports: <info>{$ok}</info>/" . count($entityResult['imported']));
                    }
                }
            } catch (\Throwable $e) {
                $entityResult['errors'][] = $e->getMessage();
            }

            $results['entities'][] = $entityResult;
        }

        if ($outputAsJson) {
            $this->line(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->renderVerboseOutput($results, $outputMode);
        }

        return Command::SUCCESS;
    }

    /**
     * Parse --useful=raw,converted,validated en liste de chaînes.
     * Si vide et output=useful, défaut : ['raw','converted','validated'].
     *
     * @return list<string>
     */
    private function parseUsefulInclude(string $optionValue, string $outputMode): array
    {
        if ($outputMode !== 'useful') {
            return [];
        }
        $optionValue = trim($optionValue);
        if ($optionValue === '') {
            return ['raw', 'converted', 'validated'];
        }
        $allowed = ['raw', 'converted', 'validated', 'compared'];
        $parts = array_map('trim', explode(',', $optionValue));
        $out = [];
        foreach ($parts as $p) {
            if ($p !== '' && in_array($p, $allowed, true)) {
                $out[] = $p;
            }
        }
        return array_values(array_unique($out));
    }

    /**
     * Affiche la sortie en mode verbose (lisible, coloré).
     *
     * @param array<string, mixed> $results
     */
    private function renderVerboseOutput(array $results, string $outputMode): void
    {
        $this->newLine();
        if (isset($results['summary'])) {
            $s = $results['summary'];
            $this->info('📊 Résumé');
            $this->line('  Collectés : <info>' . (int) ($s['collected'] ?? 0) . '</info>');
            $this->line('  Convertis : <info>' . (int) ($s['converted'] ?? 0) . '</info>');
            $this->line('  Validés   : <info>' . (int) ($s['validated'] ?? 0) . '</info>');
            $this->line('  Intégrés  : <info>' . (int) ($s['integrated'] ?? 0) . '</info>');
            return;
        }
        foreach ($results['entities'] ?? [] as $entityResult) {
            $entity = $entityResult['entity'] ?? '?';
            $this->line('<comment>▶ ' . $entity . '</comment>');
            $count = isset($entityResult['output_items']) ? count($entityResult['output_items']) : count($entityResult['items'] ?? []);
            $this->line('  Objets : <info>' . $count . '</info>');
            if ($outputMode === 'raw' && !empty($entityResult['items'])) {
                foreach ($entityResult['items'] as $i => $rawItem) {
                    if (!is_array($rawItem)) {
                        continue;
                    }
                    $id = $rawItem['id'] ?? $i;
                    $this->line('  --- DofusDB #' . $id . ' ---');
                    $this->line('    ' . $this->shortValue($rawItem));
                }
            }
            if (!empty($entityResult['errors'])) {
                foreach ($entityResult['errors'] as $err) {
                    $this->line('  <error>✗ ' . $this->formatErrorMessage((string) $err) . '</error>');
                }
            }
            if ($outputMode === 'useful' && !empty($entityResult['output_items'])) {
                $usefulInclude = $results['mode']['useful_include'] ?? [];
                $includeCompared = in_array('compared', $usefulInclude, true);
                foreach ($entityResult['output_items'] as $i => $item) {
                    $dofusId = $item['dofusdb_id'] ?? $i;
                    if ($includeCompared && array_key_exists('krosmoz_id', $item)) {
                        $krosmozId = $item['krosmoz_id'];
                        $krosmozLabel = $krosmozId !== null ? '#' . $krosmozId : '(non importé)';
                        $this->line('  --- DofusDB #' . $dofusId . ' - KrosmozJDR - ' . $krosmozLabel . ' ---');
                    } else {
                        $this->line('  --- DofusDB #' . $dofusId . ' ---');
                    }
                    $valid = $item['validation_valid'] ?? null;
                    if ($valid !== null) {
                        $this->line($valid ? '  <info>✓ Validé</info>' : '  <error>✗ Invalide</error>');
                    }
                    foreach ($item['properties'] ?? [] as $key => $prop) {
                        $hasAny = array_key_exists('raw_value', $prop) || array_key_exists('converted_value', $prop)
                            || array_key_exists('existing_value', $prop);
                        if (!$hasAny) {
                            continue;
                        }
                        $this->line('    <comment>' . $key . '</comment> :');
                        if (array_key_exists('raw_value', $prop)) {
                            $v = $prop['raw_value'];
                            $hasRaw = $v !== null && $v !== '' && (!is_array($v) || $v !== []);
                            $this->line('      DofusDB   : ' . ($hasRaw ? '<comment>' . $this->formatValueForDisplay($key, $v) . '</comment>' : '—'));
                        }
                                        if (array_key_exists('converted_value', $prop) || array_key_exists('valid', $prop)) {
                            $cv = $prop['converted_value'] ?? null;
                            $validMark = array_key_exists('valid', $prop)
                                ? ($prop['valid'] ? ' <info>✓</info>' : ' <error>✗</error>')
                                : '';
                            $hasConverted = $cv !== null && $cv !== '' && (!is_array($cv) || $cv !== []);
                            $this->line('      Converti  : ' . ($hasConverted ? $this->formatValueForDisplay($key, $cv) : '—') . $validMark);
                        }
                        if (array_key_exists('existing_value', $prop)) {
                            $ev = $prop['existing_value'];
                            $hasExisting = $ev !== null && $ev !== '' && (!is_array($ev) || $ev !== []);
                            $this->line('      Krosmoz   : ' . ($hasExisting ? $this->formatValueForDisplay($key, $ev) : '—'));
                        }
                    }
                    if (!empty($item['relation_objects_missing'])) {
                        $this->line('    <comment>Relations à importer (objets non en base) :</comment>');
                        foreach ($item['relation_objects_missing'] as $rel) {
                            $this->line('      - DofusDB #' . $rel['dofusdb_id'] . ' x' . ($rel['quantity'] ?? 1));
                        }
                    }
                }
            }
            $this->newLine();
        }
    }

    private function shortValue(mixed $v): string
    {
        if (is_array($v)) {
            return json_encode($v, JSON_UNESCAPED_UNICODE);
        }
        $s = (string) $v;
        return mb_strlen($s) > 40 ? mb_substr($s, 0, 37) . '…' : $s;
    }

    /** Longueur max d'affichage pour une valeur (évite le wrap terminal et les lignes répétées). */
    private const DISPLAY_VALUE_MAX_LENGTH = 200;

    /**
     * Formate une valeur pour l'affichage verbose : ajoute le nom à côté des IDs type et le label pour la rareté.
     * Les champs texte longs (description, name) ne sont pas tronqués côté contenu, mais une seule ligne est émise
     * (sans retours à la ligne) pour éviter la répétition du préfixe "DofusDB" / "Converti" en sortie.
     */
    private function formatValueForDisplay(string $key, mixed $value): string
    {
        if ($key === 'recipe_ingredients' && is_array($value)) {
            return $this->formatRecipeIngredientsForDisplay($value);
        }

        $truncate = !in_array($key, ['description', 'name'], true);
        $base = $truncate ? $this->shortValue($value) : $this->longValue($value);
        if ($base === '—' || $base === '') {
            return $base;
        }

        // Une seule ligne : pas de retours à la ligne (évite répétition du préfixe en sortie).
        $base = str_replace(["\r\n", "\n", "\r"], ' ', $base);
        // Valeurs brutes longues (ex. JSON i18n name/description) : tronquer pour une ligne lisible ; pas les chaînes déjà converties.
        if (is_array($value) && mb_strlen($base) > self::DISPLAY_VALUE_MAX_LENGTH) {
            $base = mb_substr($base, 0, self::DISPLAY_VALUE_MAX_LENGTH - 1) . '…';
        }

        if ($key === 'type_id' && is_numeric($value)) {
            $typeId = (int) $value;
            if ($typeId > 0) {
                $name = app(DofusDbItemTypesCatalogService::class)->fetchName($typeId, 'fr', true);
                if ($name !== null && $name !== '') {
                    return $typeId . ' (' . $name . ')';
                }
            }
        }

        if ($key === 'resource_type_id' && is_numeric($value)) {
            $id = (int) $value;
            if ($id > 0) {
                $rt = ResourceType::find($id);
                if ($rt !== null && $rt->name !== null && $rt->name !== '') {
                    return $id . ' (' . $rt->name . ')';
                }
                $name = app(DofusDbItemTypesCatalogService::class)->fetchName($id, 'fr', true);
                if ($name !== null && $name !== '') {
                    return $id . ' (' . $name . ')';
                }
            }
        }

        if ($key === 'rarity' && is_numeric($value)) {
            $r = (int) $value;
            $label = Resource::RARITY[$r] ?? null;
            if ($label !== null) {
                return $r . ' (' . $label . ')';
            }
        }

        return $base;
    }

    /**
     * Valeur affichée sans troncature (pour description, name).
     */
    private function longValue(mixed $v): string
    {
        if (is_array($v)) {
            return json_encode($v, JSON_UNESCAPED_UNICODE);
        }

        return (string) $v;
    }

    /**
     * Formate recipe_ingredients (liste d'ingrédients + quantités) pour l'affichage verbose.
     * Accepte : [ { ingredient_dofusdb_id, quantity } ], [ { ingredient_resource_id, quantity } ],
     * ou { ingredientIds: [], quantities: [] } (brut DofusDB).
     */
    private function formatRecipeIngredientsForDisplay(mixed $value): string
    {
        if (!is_array($value)) {
            return '—';
        }
        $parts = [];
        if (isset($value['ingredientIds'], $value['quantities']) && is_array($value['ingredientIds']) && is_array($value['quantities'])) {
            foreach ($value['ingredientIds'] as $idx => $id) {
                $qty = $value['quantities'][$idx] ?? 1;
                $parts[] = (string) $id . ' x' . (int) $qty;
            }
        } else {
            foreach ($value as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $id = $row['ingredient_dofusdb_id'] ?? $row['ingredient_resource_id'] ?? null;
                $qty = (int) ($row['quantity'] ?? 1);
                if ($id !== null && $id !== '') {
                    $name = null;
                    if (isset($row['ingredient_resource_id']) && is_numeric($row['ingredient_resource_id'])) {
                        $r = Resource::find((int) $row['ingredient_resource_id']);
                        $name = $r?->name;
                    }
                    $parts[] = $name !== null && $name !== '' ? $name . ' (#' . $id . ') x' . $qty : (string) $id . ' x' . $qty;
                }
            }
        }
        return $parts === [] ? '—' : implode(', ', $parts);
    }

    /**
     * Rend un message d'erreur HTTP lisible (extrait le message JSON si présent).
     */
    private function formatErrorMessage(string $err): string
    {
        if (preg_match('/^(.*?status code (\d+):)\s*(\{.*)$/s', $err, $m)) {
            $prefix = trim($m[1]);
            $jsonStr = trim($m[3]);
            $decoded = json_decode($jsonStr, true);
            if (is_array($decoded) && isset($decoded['message']) && is_string($decoded['message'])) {
                return $prefix . ' ' . $decoded['message'];
            }
        }
        return mb_strlen($err) > 120 ? mb_substr($err, 0, 117) . '…' : $err;
    }

    private function handleSyncResourceTypes(): int
    {
        $decision = (string) $this->option('decision');
        if (!in_array($decision, [ResourceType::DECISION_PENDING, ResourceType::DECISION_ALLOWED], true)) {
            $this->error("Option --decision invalide: {$decision}. Valeurs acceptées: pending|allowed");
            return Command::FAILURE;
        }

        $limit = max(1, min(200, (int) $this->option('limit')));
        $maxPages = max(0, (int) $this->option('max-pages'));
        $dryRun = (bool) $this->option('dry-run');

        $baseUrl = (string) config('scrapping.data_collect.dofusdb_base_url', 'https://api.dofusdb.fr');
        $lang = (string) config('scrapping.data_collect.default_language', 'fr');
        $timeout = (int) config('scrapping.data_collect.timeout', 30);
        $ua = (string) config('scrapping.data_collect.user_agent', 'KrosmozJDR-Scrapping/1.0');

        $this->info('🚀 Sync DofusDB item-types (Ressource)');
        $this->line("BaseUrl={$baseUrl} lang={$lang} limit={$limit} max-pages=" . ($maxPages ?: '∞') . ' decision=' . $decision . ' dry-run=' . ($dryRun ? 'yes' : 'no'));
        $this->newLine();

        $skip = max(0, (int) $this->option('start-skip'));
        $page = 0;
        $stats = [
            'seen' => 0,
            'resource_types' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        while (true) {
            $page++;
            if ($maxPages > 0 && $page > $maxPages) {
                break;
            }

            $url = rtrim($baseUrl, '/') . '/item-types';
            $query = [
                'lang' => $lang,
                '$limit' => $limit,
                '$skip' => $skip,
            ];

            try {
                $resp = Http::timeout($timeout)
                    ->withHeaders([
                        'User-Agent' => $ua,
                        'Accept' => 'application/json',
                    ])
                    ->get($url, $query);

                if (!$resp->successful()) {
                    $stats['errors']++;
                    $this->error("Erreur DofusDB ({$resp->status()}) sur /item-types");
                    Log::warning('scrapping sync-resource-types dofusdb failed', [
                        'status' => $resp->status(),
                        'body' => $resp->body(),
                    ]);
                    return Command::FAILURE;
                }

                $payload = $resp->json();
                $data = is_array($payload) ? ($payload['data'] ?? null) : null;
                if (!is_array($data)) {
                    $stats['errors']++;
                    $this->error('Réponse inattendue de DofusDB (clé data manquante).');
                    return Command::FAILURE;
                }

                if (count($data) === 0) {
                    break;
                }

                $received = count($data);
                $total = is_array($payload) && isset($payload['total']) ? (int) $payload['total'] : 0;
                $apiLimit = is_array($payload) && isset($payload['limit']) ? (int) $payload['limit'] : 0;
                $effectiveStep = $apiLimit > 0 ? $apiLimit : $received;

                foreach ($data as $row) {
                    $stats['seen']++;

                    $typeId = isset($row['id']) ? (int) $row['id'] : 0;
                    $superTypeId = isset($row['superTypeId']) ? (int) $row['superTypeId'] : 0;

                    if ($typeId <= 0) {
                        $stats['skipped']++;
                        continue;
                    }

                    // Ressources uniquement: superTypeId=9
                    if ($superTypeId !== 9) {
                        continue;
                    }

                    $stats['resource_types']++;

                    $name = null;
                    if (isset($row['name']) && is_array($row['name'])) {
                        $name = $row['name']['fr'] ?? $row['name'][$lang] ?? null;
                        if (!is_string($name)) {
                            $name = null;
                        }
                    }
                    $label = $name ? $name : "DofusDB type #{$typeId}";

                    if ($dryRun) {
                        continue;
                    }

                    /** @var ResourceType $model */
                    $model = ResourceType::firstOrNew(['dofusdb_type_id' => $typeId]);

                    $wasExisting = $model->exists;
                    $oldName = $model->name;
                    $oldDecision = $model->decision;

                    $model->name = $oldName && !str_starts_with($oldName, 'DofusDB type #') ? $oldName : $label;

                    if (!$wasExisting) {
                        $model->decision = $decision;
                    } elseif (!in_array($oldDecision, [ResourceType::DECISION_ALLOWED, ResourceType::DECISION_BLOCKED, ResourceType::DECISION_PENDING], true)) {
                        $model->decision = $decision;
                    }

                    if (!$wasExisting) {
                        $model->state = ResourceType::STATE_PLAYABLE;
                        $model->read_level = User::ROLE_GUEST;
                        $model->write_level = User::ROLE_ADMIN;
                    }

                    $model->save();

                    if ($wasExisting) {
                        $stats['updated']++;
                    } else {
                        $stats['created']++;
                    }
                }

                $this->line("Page {$page} OK (skip={$skip}) | seen={$stats['seen']} resource_types={$stats['resource_types']} created={$stats['created']} updated={$stats['updated']}");

                $skip += $effectiveStep;

                if ($total > 0 && $skip >= $total) {
                    break;
                }
                if ($total <= 0 && $received < $limit) {
                    break;
                }
            } catch (\Throwable $e) {
                $stats['errors']++;
                $this->error('Erreur réseau/HTTP: ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        $this->newLine();
        $this->info('📊 Résumé sync resource-types');
        $this->line('Seen rows: ' . $stats['seen']);
        $this->line('Resource types found: ' . $stats['resource_types']);
        $this->line('Created: ' . $stats['created']);
        $this->line('Updated: ' . $stats['updated']);
        $this->line('Errors: ' . $stats['errors']);

        return Command::SUCCESS;
    }

    private function handleBatchImport(Orchestrator $orchestrator): int
    {
        $path = (string) $this->option('batch');
        if ($path === '' || !is_file($path)) {
            $this->error("Fichier batch introuvable: {$path}");
            return Command::FAILURE;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            $this->error("Impossible de lire le fichier: {$path}");
            return Command::FAILURE;
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            $this->error("JSON invalide: {$path}");
            return Command::FAILURE;
        }

        $entities = isset($decoded['entities']) && is_array($decoded['entities']) ? $decoded['entities'] : $decoded;
        if (!is_array($entities) || empty($entities)) {
            $this->error("Le fichier doit contenir un tableau d'entités ou un objet avec clé 'entities'.");
            return Command::FAILURE;
        }

        $excludeRaw = $this->option('exclude-from-update');
        $excludeFromUpdate = is_string($excludeRaw) && $excludeRaw !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $excludeRaw))))
            : [];

        $options = [
            'validate' => !(bool) $this->option('no-validate'),
            'integrate' => !(bool) $this->option('dry-run') && !(bool) $this->option('validate-only'),
            'dry_run' => (bool) $this->option('dry-run'),
            'force_update' => (bool) $this->option('force-update') || (bool) $this->option('replace-existing'),
            'lang' => (string) $this->option('lang', 'fr'),
            'exclude_from_update' => $excludeFromUpdate,
            'ignore_unvalidated' => (bool) $this->option('ignore-unvalidated'),
        ];

        $results = [];
        $errorCount = 0;
        foreach ($entities as $i => $entity) {
            $type = $entity['type'] ?? '';
            $eid = (int) ($entity['id'] ?? 0);
            $entityKey = $this->apiTypeToEntity($type);
            if ($entityKey === null) {
                $results[] = ['index' => $i, 'type' => $type, 'id' => $eid, 'success' => false, 'error' => 'Type non supporté'];
                $errorCount++;
                continue;
            }
            $result = $orchestrator->runOne('dofusdb', $entityKey, $eid, $options);
            $results[] = [
                'index' => $i,
                'type' => $type,
                'id' => $eid,
                'success' => $result->isSuccess(),
                'error' => $result->isSuccess() ? null : $result->getMessage(),
            ];
            if (!$result->isSuccess()) {
                $errorCount++;
            }
        }

        $result = [
            'success' => $errorCount === 0,
            'summary' => ['total' => count($entities), 'success' => count($entities) - $errorCount, 'errors' => $errorCount],
            'results' => $results,
        ];

        if ((bool) $this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $summary = $result['summary'];
            $this->info('📦 Import batch terminé');
            $this->line('Total: ' . $summary['total']);
            $this->line('Succès: ' . $summary['success']);
            $this->line('Erreurs: ' . $summary['errors']);
        }

        return $result['success'] ? Command::SUCCESS : Command::FAILURE;
    }

    private function apiTypeToEntity(string $type): ?string
    {
        return match (strtolower($type)) {
            'class' => 'breed',
            'monster' => 'monster',
            'spell' => 'spell',
            'panoply' => 'panoply',
            'item', 'resource', 'consumable', 'equipment' => 'item',
            default => null,
        };
    }

    /**
     * @return array<int,string>
     */
    private function parseEntityList(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }
        $parts = array_filter(array_map('trim', explode(',', $raw)));
        return array_values(array_unique($parts));
    }

    private function normalizeEntity(string $entity): string
    {
        $entity = strtolower(trim($entity));
        return match ($entity) {
            'classe' => 'class',
            default => $entity,
        };
    }

    /**
     * @param mixed $id
     * @param mixed $ids
     * @return array<int,int>
     */
    private function parseIds(mixed $id, mixed $ids): array
    {
        $out = [];
        if (is_string($id) && $id !== '' && ctype_digit($id)) {
            $out[] = (int) $id;
        } elseif (is_int($id)) {
            $out[] = (int) $id;
        }

        if (is_string($ids) && $ids !== '') {
            foreach (explode(',', $ids) as $p) {
                $p = trim($p);
                if ($p !== '' && ctype_digit($p)) {
                    $out[] = (int) $p;
                }
            }
        }

        $out = array_values(array_unique(array_filter($out)));
        return $out;
    }

    /**
     * @param array<int,int> $ids
     * @return array<string,mixed>
     */
    private function extractFilters(array $ids): array
    {
        $filters = [];

        if (!empty($ids)) {
            $filters['ids'] = $ids;
            return $filters;
        }

        foreach (['name', 'raceId', 'breedId', 'levelMin', 'levelMax'] as $k) {
            $v = $this->option($k);
            if ($v !== null && $v !== '') {
                $filters[$k] = $v;
            }
        }

        $typeName = $this->option('type-name');
        if ($typeName !== null && is_string($typeName) && $typeName !== '') {
            $lang = (string) $this->option('lang', 'fr');
            $itemTypesCatalog = app(DofusDbItemTypesCatalogService::class);
            $typeIds = $itemTypesCatalog->resolveTypeIdsByName($typeName, $lang);
            if ($typeIds !== []) {
                $superTypeMapping = app(DofusDbItemSuperTypeMappingService::class);
                $excluded = $superTypeMapping->getExcludedTypeIds();
                $filters['typeIds'] = array_values(array_diff($typeIds, $excluded));
            }
        } else {
            $typeId = $this->option('typeId');
            if ($typeId === null || $typeId === '') {
                $typeId = $this->option('type');
            }
            if ($typeId !== null && $typeId !== '') {
                $filters['typeId'] = $typeId;
            }
        }

        $raceName = $this->option('race-name');
        if ($raceName !== null && is_string($raceName) && $raceName !== '') {
            $lang = (string) $this->option('lang', 'fr');
            $monsterRacesCatalog = app(DofusDbMonsterRacesCatalogService::class);
            $raceId = $monsterRacesCatalog->findRaceIdByName($raceName, $lang);
            if ($raceId !== null) {
                $filters['raceId'] = $raceId;
            }
        }

        return $filters;
    }

    /**
     * @return array{skip_cache?: bool, include_relations?: bool, force_update: bool, dry_run: bool, validate_only: bool, lang: string, exclude_from_update: list<string>, ignore_unvalidated: bool}
     */
    private function buildImportOptions(array $collectOptions): array
    {
        $excludeRaw = $this->option('exclude-from-update');
        $excludeFromUpdate = [];
        if (is_string($excludeRaw) && $excludeRaw !== '') {
            $excludeFromUpdate = array_values(array_filter(array_map('trim', explode(',', $excludeRaw))));
        }

        return [
            'skip_cache' => (bool) ($collectOptions['skip_cache'] ?? false),
            'include_relations' => (bool) ((int) $this->option('include-relations')),
            'force_update' => (bool) $this->option('force-update') || (bool) $this->option('replace-existing'),
            'dry_run' => (bool) $this->option('dry-run'),
            'validate' => !(bool) $this->option('no-validate'),
            'validate_only' => (bool) $this->option('validate-only'),
            'lang' => (string) $this->option('lang', 'fr'),
            'exclude_from_update' => $excludeFromUpdate,
            'ignore_unvalidated' => (bool) $this->option('ignore-unvalidated'),
        ];
    }

    /**
     * @return array{skip_cache?:bool, limit?:int, max_pages?:int, max_items?:int}
     */
    private function extractCollectOptions(): array
    {
        $options = [
            'skip_cache' => (bool) $this->option('skip-cache'),
            'limit' => max(0, min(10000, (int) $this->option('limit'))),
            'start_skip' => max(0, (int) $this->option('start-skip')),
            // 0 = illimité (borné côté collector)
            'max_pages' => max(0, min(200, (int) $this->option('max-pages'))),
            'max_items' => max(0, min(20000, (int) $this->option('max-items'))),
        ];

        return $options;
    }

    /**
     * @return array<string,mixed>
     */
    private function importOne(Orchestrator $orchestrator, string $entity, int $id, array $options): array
    {
        $entityKey = $this->apiTypeToEntity($entity);
        if ($entityKey === null) {
            return [
                'success' => false,
                'entity' => $entity,
                'id' => $id,
                'error' => "Type non supporté: {$entity}",
            ];
        }

        $runOptions = [
            'validate' => ($options['validate'] ?? true) !== false,
            'integrate' => !($options['dry_run'] ?? false) && !($options['validate_only'] ?? false),
            'dry_run' => (bool) ($options['dry_run'] ?? false),
            'force_update' => (bool) ($options['force_update'] ?? false),
            'lang' => (string) ($options['lang'] ?? 'fr'),
            'exclude_from_update' => (array) ($options['exclude_from_update'] ?? []),
            'ignore_unvalidated' => (bool) ($options['ignore_unvalidated'] ?? false),
            'include_relations' => (bool) ($options['include_relations'] ?? true),
        ];
        $result = $orchestrator->runOne('dofusdb', $entityKey, $id, $runOptions);

        $data = $result->getIntegrationResult()?->getData();
        return [
            'success' => $result->isSuccess(),
            'entity' => $entity,
            'id' => $id,
            'data' => $data,
            'error' => $result->isSuccess() ? null : $result->getMessage(),
        ];
    }
}

