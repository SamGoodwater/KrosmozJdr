<?php

declare(strict_types=1);

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Scrapping\Concerns\RespondsWithOrchestratorResult;
use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * API import scrapping : pipeline Collect → Conversion → Validation → Intégration.
 *
 * Ex. POST /api/scrapping/import/monster/31
 */
class ScrappingImportController extends Controller
{
    use RespondsWithOrchestratorResult;

    public function __construct(
        private CollectAliasResolver $aliasResolver,
        private ConfigLoader $configLoader,
        private Orchestrator $orchestrator,
    ) {}

    /**
     * Import d'un seul objet via le pipeline.
     *
     * @param string $entity monster, breed, spell, item ou alias (class → breed)
     * @param int $id ID DofusDB
     */
    public function importOne(Request $request, string $entity, int $id): JsonResponse
    {
        $resolved = $this->resolveEntityForImport($entity);
        $source = $resolved['source'];
        $entityKey = $resolved['entity'];

        try {
            $entities = $this->configLoader->listEntities($source);
            if (!in_array($entityKey, $entities, true)) {
                return response()->json([
                    'success' => false,
                    'message' => "Entité inconnue : {$entity}. Valeurs : " . implode(', ', $this->aliasResolver->listAliases()),
                    'timestamp' => now()->toISOString(),
                ], 422);
            }
        } catch (\Throwable $e) {
            Log::warning('Config scrapping introuvable ou invalide', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Configuration scrapping indisponible.',
                'timestamp' => now()->toISOString(),
            ], 503);
        }

        $options = $this->optionsFromRequest($request);

        try {
            $result = $this->orchestrator->runOne($source, $entityKey, $id, $options);

            return $this->resultToJson($result, 201);
        } catch (\Throwable $e) {
            Log::error('Erreur import scrapping', [
                'entity' => $entityKey,
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Options d'import alignées sur ScrappingController (replace_mode, exclude_from_update, etc.).
     *
     * @return array{convert: bool, validate: bool, integrate: bool, dry_run: bool, force_update: bool, include_relations: bool, lang: string, ...}
     */
    private function optionsFromRequest(Request $request): array
    {
        $replaceMode = $request->input('replace_mode');
        $replaceMode = is_string($replaceMode) && in_array($replaceMode, ['never', 'draft_raw_only', 'always'], true) ? $replaceMode : null;

        $excludeFromUpdate = $request->input('exclude_from_update');
        if (!is_array($excludeFromUpdate)) {
            $excludeFromUpdate = [];
        }
        $excludeFromUpdate = array_values(array_filter(array_map('strval', $excludeFromUpdate)));

        $propertyWhitelist = $request->input('property_whitelist');
        if (is_array($propertyWhitelist)) {
            $propertyWhitelist = array_values(array_filter(array_map('strval', $propertyWhitelist)));
        } else {
            $propertyWhitelist = is_string($propertyWhitelist)
                ? array_values(array_filter(array_map('trim', explode(',', $propertyWhitelist))))
                : [];
        }

        $forceUpdate = $request->boolean('force_update', false);
        if ($replaceMode === 'always') {
            $forceUpdate = true;
        } elseif ($replaceMode === 'never') {
            $forceUpdate = false;
        }

        return [
            'convert' => true,
            'validate' => $request->boolean('validate', true),
            'integrate' => !$request->boolean('validate_only', false) && !$request->boolean('dry_run', false),
            'dry_run' => $request->boolean('dry_run', false),
            'force_update' => $forceUpdate,
            'replace_mode' => $replaceMode,
            'include_relations' => $request->boolean('include_relations', true),
            'exclude_from_update' => $excludeFromUpdate,
            'property_whitelist' => $propertyWhitelist,
            'download_images' => $request->boolean('download_images', $request->boolean('with_images', true)),
            'lang' => (string) $request->input('lang', 'fr'),
        ];
    }
}
