<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\GenerativeAi;

use App\Http\Controllers\Controller;
use App\Services\Entity\GenericEntityJsonInjector;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\CostEstimator;
use App\Services\GenerativeAi\GenerationConfigLoader;
use App\Services\GenerativeAi\Specializations\SpecializationRegistry;
use App\Support\EntityModelRegistry;
use Illuminate\Database\Eloquent\Model; // pragma: allowlist secret
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Schéma / exemple JSON pour l’onglet Sources (convertible IA ou fillable générique).
 *
 * @example GET /api/ia/schema/spells
 * @example GET /api/ia/schema/campaigns
 */
class IaSchemaController extends Controller
{
    public function __invoke(Request $request, string $entityType): JsonResponse
    {
        abort_unless($request->user()?->isAdmin() === true, 403);

        $plural = EntityModelRegistry::normalizeType($entityType);
        $modelClass = EntityModelRegistry::modelMap()[$plural] ?? null;
        if ($modelClass === null) {
            return response()->json([
                'success' => false,
                'message' => 'Type d’entité inconnu.',
            ], 422);
        }

        $spec = app(SpecializationRegistry::class)->tryForEntityType($plural);
        if ($spec !== null) {
            $action = app(CostEstimator::class)->actionForEntityType($plural);
            $singular = match ($plural) {
                'monsters' => 'monster',
                'spells' => 'spell',
                'npcs' => 'npc',
                'items' => 'item',
                'consumables' => 'consumable',
                default => $spec->entityType(),
            };
            $profile = GenerationConfigLoader::default()->forEntity($spec->entityType());
            $conversion = new ConversionRequest(
                action: $action,
                entityType: $singular,
                entityId: null,
                userId: $request->user()?->id,
            );
            $schema = $spec->jsonSchema($profile, $conversion);
            $example = $this->exampleFromSchema($schema);

            return response()->json([
                'success' => true,
                'entity_type' => $plural,
                'action' => $action,
                'mode' => 'specialization',
                'schema' => $schema,
                'example' => $example,
            ]);
        }

        /** @var Model $blank */
        $blank = new $modelClass;
        $pack = app(GenericEntityJsonInjector::class)->schemaAndExample($blank);

        return response()->json([
            'success' => true,
            'entity_type' => $plural,
            'action' => 'inject',
            'mode' => 'generic',
            'schema' => $pack['schema'],
            'example' => $pack['example'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    private function exampleFromSchema(array $schema): array
    {
        $properties = $schema['properties'] ?? null;
        if (! is_array($properties)) {
            return [];
        }

        $example = [];
        foreach ($properties as $key => $definition) {
            if (! is_string($key) || $key === '') {
                continue;
            }
            $example[$key] = $this->exampleValue(is_array($definition) ? $definition : []);
        }

        return $example;
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function exampleValue(array $definition): mixed
    {
        $type = $definition['type'] ?? null;
        if (is_array($type)) {
            $type = $type[0] ?? 'string';
        }

        return match ($type) {
            'integer', 'number' => 0,
            'boolean' => false,
            'array' => [],
            'object' => $this->exampleFromSchema($definition),
            default => '',
        };
    }
}
