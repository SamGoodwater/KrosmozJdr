<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Enums\EntityState;
use App\Services\GenerativeAi\Specializations\Specialization;
use Illuminate\Database\Eloquent\Model; // pragma: allowlist secret
use RuntimeException;

/**
 * Gardes conversion IA : action = type d’URL, et force pour playable/archived.
 *
 * @example app(ConversionSafety::class)->assertRequest($request);
 */
final class ConversionSafety
{
    /**
     * @var list<string>
     */
    private const PROTECTED_STATES = [
        EntityState::Playable->value,
        EntityState::Archived->value,
    ];

    public function __construct(private readonly CostEstimator $estimator) {}

    public function expectedAction(string $entityType): string
    {
        return $this->estimator->actionForEntityType($entityType);
    }

    public function assertActionMatches(string $action, string $entityType): void
    {
        $expected = $this->expectedAction($entityType);
        if ($action !== $expected) {
            throw new RuntimeException(
                "L’action IA « {$action} » ne correspond pas au type « {$entityType} » (attendu : {$expected})."
            );
        }
    }

    public function assertRequest(ConversionRequest $request, Specialization $spec): void
    {
        $this->assertActionMatches($request->action, $request->entityType);
        if ($spec->entityType() !== $this->normalizeEntityType($request->entityType)) {
            throw new RuntimeException(
                "L’action IA « {$request->action} » ne correspond pas au type « {$request->entityType} »."
            );
        }
        if ($request->entityId === null) {
            return;
        }
        $modelClass = FewShotExamplePool::ENTITY_MODELS[$spec->entityType()] ?? null;
        if ($modelClass === null) {
            return;
        }
        $entity = $modelClass::query()->find($request->entityId);
        if (! $entity instanceof Model) {
            throw new RuntimeException('Entité introuvable.');
        }
        $this->assertMayOverwrite($entity, $request->force);
    }

    public function assertMayOverwrite(Model $entity, bool $force): void
    {
        if (! $this->isProtected($entity)) {
            return;
        }
        if ($force) {
            return;
        }

        throw new RuntimeException(
            'Fiche jouable ou archivée : passez force=true (ou --force) pour écraser le contenu.'
        );
    }

    public function isProtected(Model $entity): bool
    {
        if ($this->isProtectedState($entity->getAttribute('state'))) {
            return true;
        }
        if (! method_exists($entity, 'creature')) {
            return false;
        }
        $creature = $entity->getRelationValue('creature') ?? $entity->creature()->first();
        if (! is_object($creature) || ! method_exists($creature, 'getAttribute')) {
            return false;
        }

        return $this->isProtectedState($creature->getAttribute('state'));
    }

    private function isProtectedState(mixed $state): bool
    {
        return is_string($state) && in_array($state, self::PROTECTED_STATES, true);
    }

    private function normalizeEntityType(string $entityType): string
    {
        return match ($entityType) {
            'monsters', 'monster' => 'monster',
            'spells', 'spell' => 'spell',
            'npcs', 'npc' => 'npc',
            'items', 'item' => 'item',
            'consumables', 'consumable' => 'consumable',
            default => rtrim($entityType, 's'),
        };
    }
}
