<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\GenerativeAi;

use App\Http\Controllers\Controller;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\CostEstimator;
use App\Services\GenerativeAi\GenerationConfigLoader;
use App\Services\GenerativeAi\Specializations\SpecializationRegistry;
use App\Support\EntityModelRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * Schéma JSON attendu pour un type convertible (bouton Exemple de l’onglet JSON).
 *
 * @example GET /api/ia/schema/spells
 */
class IaSchemaController extends Controller
{
    public function __invoke(Request $request, string $entityType): JsonResponse
    {
        abort_unless($request->user()?->isAdmin() === true, 403);

        $plural = EntityModelRegistry::normalizeType($entityType);
        $action = app(CostEstimator::class)->actionForEntityType($plural);

        try {
            $spec = app(SpecializationRegistry::class)->forAction($action);
        } catch (InvalidArgumentException) {
            return response()->json([
                'success' => false,
                'message' => 'Type non convertible par IA.',
            ], 422);
        }

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
            'schema' => $schema,
            'example' => $example,
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
