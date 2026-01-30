<?php

namespace App\Console\Commands;

use App\Models\Type\ResourceType;
use App\Models\User;
use App\Services\Scrapping\DataCollect\ConfigDrivenDofusDbCollector;
use App\Services\Scrapping\Orchestrator\ScrappingOrchestrator;
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
        {--validate-only : Valide uniquement sans sauvegarder}
        {--dry-run : N\'écrit rien (si import)}
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
        {--json : Affiche en JSON}
        {--detailed : Affichage plus verbeux}';

    protected $description = 'Commande unique pour collect/search/import du scrapping (config-driven).';

    public function handle(ConfigDrivenDofusDbCollector $collector, ScrappingOrchestrator $orchestrator): int
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
        $doCompare = (bool) $this->option('compare');
        $json = (bool) $this->option('json');
        $detailed = (bool) $this->option('detailed');

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
                'compare' => $doCompare,
            ],
            'query' => [
                'entities' => $entities,
                'filters' => $filters,
                'options' => $options,
            ],
            'entities' => [],
        ];

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

                        $search = $collector->fetchManyResult($collectorEntity, $localFilters, $options);
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
                        $entityResult['items'][] = $collector->fetchOne($collectorEntity, (int) $id, [
                            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
                        ]);
                    }
                } else {
                    $search = $collector->fetchManyResult($collectorEntity, $filters, $options);
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

                if ($doCompare) {
                    foreach ($entityResult['ids'] as $id) {
                        $entityResult['previews'][] = $orchestrator->previewEntity($normalizedEntity, (int) $id);
                    }
                }

                if ($doSave) {
                    $importOptions = [
                        'skip_cache' => (bool) ($options['skip_cache'] ?? false),
                        'include_relations' => (bool) ((int) $this->option('include-relations')),
                        'force_update' => (bool) $this->option('force-update'),
                        'dry_run' => (bool) $this->option('dry-run'),
                        'validate_only' => (bool) $this->option('validate-only'),
                    ];

                    foreach ($entityResult['ids'] as $id) {
                        $entityResult['imported'][] = $this->importOne($orchestrator, $normalizedEntity, (int) $id, $importOptions);
                    }
                }

                if (!$json) {
                    if ($doCompare) {
                        $this->line("  - previews: " . count($entityResult['previews']));
                    }
                    if ($doSave) {
                        $ok = collect($entityResult['imported'])->filter(fn ($r) => (bool) ($r['success'] ?? false))->count();
                        $this->line("  - imports: {$ok}/" . count($entityResult['imported']));
                    }

                    if ($detailed && !empty($entityResult['ids'])) {
                        $this->line("  - ids: " . implode(', ', array_slice($entityResult['ids'], 0, 50)) . (count($entityResult['ids']) > 50 ? '…' : ''));
                    }
                }
            } catch (\Throwable $e) {
                $entityResult['errors'][] = $e->getMessage();
                if (!$json) {
                    $this->error('  - erreur: ' . $e->getMessage());
                }
            }

            $results['entities'][] = $entityResult;
        }

        if ($json) {
            $this->line(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return Command::SUCCESS;
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

    private function handleBatchImport(ScrappingOrchestrator $orchestrator): int
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

        $options = [
            'skip_cache' => (bool) $this->option('skip-cache'),
            'include_relations' => (bool) ((int) $this->option('include-relations')),
            'force_update' => (bool) $this->option('force-update'),
            'dry_run' => (bool) $this->option('dry-run'),
            'validate_only' => (bool) $this->option('validate-only'),
        ];

        $result = $orchestrator->importBatch($entities, $options);

        if ((bool) $this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $summary = $result['summary'] ?? [];
            $this->info('📦 Import batch terminé');
            $this->line('Total: ' . ($summary['total'] ?? 0));
            $this->line('Succès: ' . ($summary['success'] ?? 0));
            $this->line('Erreurs: ' . ($summary['errors'] ?? 0));
        }

        return ($result['success'] ?? false) ? Command::SUCCESS : Command::FAILURE;
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

        $typeId = $this->option('typeId');
        if ($typeId === null || $typeId === '') {
            $typeId = $this->option('type');
        }
        if ($typeId !== null && $typeId !== '') {
            $filters['typeId'] = $typeId;
        }

        return $filters;
    }

    /**
     * @return array{skip_cache?:bool, limit?:int, max_pages?:int, max_items?:int}
     */
    private function extractCollectOptions(): array
    {
        $options = [
            'skip_cache' => (bool) $this->option('skip-cache'),
            'limit' => max(1, min(200, (int) $this->option('limit'))),
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
    private function importOne(ScrappingOrchestrator $orchestrator, string $entity, int $id, array $options): array
    {
        $method = 'import' . ucfirst($entity);
        if (!method_exists($orchestrator, $method)) {
            return [
                'success' => false,
                'entity' => $entity,
                'id' => $id,
                'error' => "Méthode d'import introuvable: {$method}",
            ];
        }

        $res = $orchestrator->{$method}($id, $options);
        $res['entity'] = $entity;
        $res['id'] = $id;
        return $res;
    }
}

