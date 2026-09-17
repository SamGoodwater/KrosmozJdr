<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Characteristic;
use App\Models\Scrapping\ScrappingEntityMapping;
use App\Models\Scrapping\ScrappingEntityMappingTarget;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Config\ScrappingMappingService;
use App\Services\Scrapping\Core\Config\ScrappingMappingValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Administration du mapping DofusDB → Krosmoz (remplace/complète les JSON par entité).
 *
 * @see docs/features/scrapping/README.md
 */
class ScrappingMappingController extends Controller
{
    public function __construct(
        private readonly ConfigLoader $configLoader,
        private readonly ScrappingMappingService $mappingService,
        private readonly ScrappingMappingValidator $mappingValidator,
    ) {}

    /**
     * Page liste : choix source/entité + liste des règles de mapping pour l'entité sélectionnée.
     */
    public function index(Request $request): InertiaResponse
    {
        $source = (string) $request->query('source', 'dofusdb');
        $entity = (string) $request->query('entity', '');
        $mappingKey = trim((string) $request->query('mapping_key', ''));

        $entities = $this->configLoader->listEntities($source);
        $entitiesWithMapping = $this->mappingService->listEntitiesWithMapping($source);

        $mappings = [];
        $mappingAudit = [];
        if ($entity !== '') {
            $mappings = $this->loadMappingsForEntity($source, $entity);
            try {
                $mappingAudit = $this->mappingValidator->validate(
                    $entity,
                    $this->configLoader->loadEntity($source, $entity)['mapping'] ?? []
                );
            } catch (\Throwable $exception) {
                $mappingAudit = [['path' => 'config', 'message' => $exception->getMessage()]];
            }
        }

        $characteristicsForSelect = Characteristic::orderBy('sort_order')->orderBy('key')
            ->get(['id', 'key', 'name'])
            ->map(fn ($c) => ['id' => $c->id, 'key' => $c->key, 'name' => $c->name ?? $c->key])
            ->values()
            ->all();

        return Inertia::render('Admin/scrapping-mappings/Index', [
            'source' => $source,
            'sources' => [['id' => 'dofusdb', 'label' => 'DofusDB']],
            'entity' => $entity,
            'mappingKey' => $mappingKey,
            'entities' => $entities,
            'entitiesWithMapping' => $entitiesWithMapping,
            'mappings' => $mappings,
            'mappingAudit' => $mappingAudit,
            'characteristicsForSelect' => $characteristicsForSelect,
        ]);
    }

    /**
     * Création d'une règle de mapping + cibles.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'source' => 'required|string|max:64',
            'entity' => 'required|string|max:64',
            'mapping_key' => 'required|string|max:128',
            'from_path' => 'required|string|max:256',
            'from_lang_aware' => 'boolean',
            'characteristic_id' => 'nullable|integer|exists:characteristics,id',
            'formatters' => 'nullable|array',
            'formatters.*.name' => 'required|string|max:64',
            'formatters.*.args' => 'nullable|array',
            'sort_order' => 'integer|min:0',
            'targets' => 'required|array|min:1',
            'targets.*.target_model' => 'required|string|max:64',
            'targets.*.target_field' => 'required|string|max:64',
            'targets.*.sort_order' => 'integer|min:0',
        ]);
        $this->assertValidPayload((string) $validated['entity'], $validated);

        $maxSort = (int) ScrappingEntityMapping::where('source', $validated['source'])
            ->where('entity', $validated['entity'])
            ->max('sort_order');

        $mapping = ScrappingEntityMapping::create([
            'source' => $validated['source'],
            'entity' => $validated['entity'],
            'mapping_key' => $validated['mapping_key'],
            'from_path' => $validated['from_path'],
            'from_lang_aware' => (bool) ($validated['from_lang_aware'] ?? false),
            'characteristic_id' => $validated['characteristic_id'] ?? null,
            'formatters' => $validated['formatters'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $maxSort + 1,
        ]);
        $currentTargets = $mapping->targets()->get()->map(fn (ScrappingEntityMappingTarget $target): array => [
            'target_model' => $target->target_model,
            'target_field' => $target->target_field,
        ])->all();
        $this->assertValidPayload($mapping->entity, [
            'from_path' => $validated['from_path'] ?? $mapping->from_path,
            'characteristic_id' => array_key_exists('characteristic_id', $validated)
                ? $validated['characteristic_id']
                : $mapping->characteristic_id,
            'formatters' => array_key_exists('formatters', $validated)
                ? $validated['formatters']
                : $mapping->formatters,
            'targets' => $validated['targets'] ?? $currentTargets,
        ]);

        foreach ($validated['targets'] as $i => $t) {
            ScrappingEntityMappingTarget::create([
                'scrapping_entity_mapping_id' => $mapping->id,
                'target_model' => $t['target_model'],
                'target_field' => $t['target_field'],
                'sort_order' => (int) ($t['sort_order'] ?? $i),
            ]);
        }

        $mapping->load('targets', 'characteristic:id,key,name');

        return response()->json([
            'success' => true,
            'message' => 'Règle de mapping créée.',
            'mapping' => $this->formatMappingForResponse($mapping),
        ], 201);
    }

    /**
     * Mise à jour d'une règle de mapping.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $mapping = ScrappingEntityMapping::findOrFail($id);

        $validated = $request->validate([
            'mapping_key' => 'sometimes|string|max:128',
            'from_path' => 'sometimes|string|max:256',
            'from_lang_aware' => 'boolean',
            'characteristic_id' => 'nullable|integer|exists:characteristics,id',
            'formatters' => 'nullable|array',
            'formatters.*.name' => 'required|string|max:64',
            'formatters.*.args' => 'nullable|array',
            'sort_order' => 'integer|min:0',
            'targets' => 'sometimes|array|min:1',
            'targets.*.id' => 'nullable|integer|exists:scrapping_entity_mapping_targets,id',
            'targets.*.target_model' => 'required|string|max:64',
            'targets.*.target_field' => 'required|string|max:64',
            'targets.*.sort_order' => 'integer|min:0',
        ]);

        $mapping->fill(array_filter([
            'mapping_key' => $validated['mapping_key'] ?? null,
            'from_path' => $validated['from_path'] ?? null,
            'from_lang_aware' => isset($validated['from_lang_aware']) ? (bool) $validated['from_lang_aware'] : null,
            'characteristic_id' => $validated['characteristic_id'] ?? null,
            'formatters' => $validated['formatters'] ?? null,
            'sort_order' => $validated['sort_order'] ?? null,
        ], fn ($v) => $v !== null));
        $mapping->save();

        if (isset($validated['targets'])) {
            $mapping->targets()->delete();
            foreach ($validated['targets'] as $i => $t) {
                ScrappingEntityMappingTarget::create([
                    'scrapping_entity_mapping_id' => $mapping->id,
                    'target_model' => $t['target_model'],
                    'target_field' => $t['target_field'],
                    'sort_order' => (int) ($t['sort_order'] ?? $i),
                ]);
            }
        }

        $mapping->load('targets', 'characteristic:id,key,name');

        return response()->json([
            'success' => true,
            'message' => 'Règle de mapping mise à jour.',
            'mapping' => $this->formatMappingForResponse($mapping),
        ]);
    }

    /**
     * Suppression d'une règle de mapping (cascade sur les cibles).
     */
    public function destroy(int $id): JsonResponse
    {
        $mapping = ScrappingEntityMapping::findOrFail($id);
        $mapping->delete();

        return response()->json([
            'success' => true,
            'message' => 'Règle de mapping supprimée.',
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function loadMappingsForEntity(string $source, string $entity): array
    {
        $rows = ScrappingEntityMapping::where('source', $source)
            ->where('entity', $entity)
            ->with('targets', 'characteristic:id,key,name')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $rows->map(fn (ScrappingEntityMapping $m) => $this->formatMappingForResponse($m))->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function formatMappingForResponse(ScrappingEntityMapping $m): array
    {
        return [
            'id' => $m->id,
            'source' => $m->source,
            'entity' => $m->entity,
            'mapping_key' => $m->mapping_key,
            'from_path' => $m->from_path,
            'from_lang_aware' => $m->from_lang_aware,
            'characteristic_id' => $m->characteristic_id,
            'characteristic' => $m->relationLoaded('characteristic') && $m->characteristic
                ? ['id' => $m->characteristic->id, 'key' => $m->characteristic->key, 'name' => $m->characteristic->name]
                : null,
            'formatters' => $m->formatters ?? [],
            'sort_order' => $m->sort_order,
            'targets' => $m->relationLoaded('targets')
                ? $m->targets->map(fn ($t) => $t->toResponseArray())->values()->all()
                : [],
        ];
    }

    /**
     * Valide la cohérence métier d'une règle avant écriture.
     *
     * @param  array<string, mixed>  $payload
     */
    private function assertValidPayload(string $entity, array $payload): void
    {
        $characteristicId = $payload['characteristic_id'] ?? null;
        $characteristicKey = is_numeric($characteristicId)
            ? Characteristic::query()->whereKey((int) $characteristicId)->value('key')
            : null;
        $targets = [];
        foreach (is_array($payload['targets'] ?? null) ? $payload['targets'] : [] as $target) {
            if (! is_array($target)) {
                continue;
            }
            $targets[] = [
                'model' => (string) ($target['target_model'] ?? $target['model'] ?? ''),
                'field' => (string) ($target['target_field'] ?? $target['field'] ?? ''),
            ];
        }

        $errors = $this->mappingValidator->validate($entity, [[
            'from' => ['path' => (string) ($payload['from_path'] ?? '')],
            'to' => $targets,
            'formatters' => is_array($payload['formatters'] ?? null) ? $payload['formatters'] : [],
            'characteristic_key' => is_string($characteristicKey) ? $characteristicKey : null,
        ]]);
        if ($errors === []) {
            return;
        }

        throw ValidationException::withMessages([
            'mapping' => array_map(
                static fn (array $error): string => "{$error['path']} : {$error['message']}",
                $errors
            ),
        ]);
    }
}
