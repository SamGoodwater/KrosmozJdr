<?php

declare(strict_types=1);

namespace App\Services\Entity;

use App\Enums\EntityState;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Resource;
use App\Models\User;
use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use App\Services\Scrapping\Core\Orchestrator\OrchestratorResult;
use App\Support\DofusdbRefreshableEntities;
use App\Support\EntityModelRegistry;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Rafraîchit une fiche locale depuis DofusDB (preview ou intégration).
 *
 * @example
 * $service->run($spell, $gm, 'preview', false);
 */
class EntityDofusdbRefreshService
{
    public function __construct(
        private readonly Orchestrator $orchestrator,
        private readonly CollectAliasResolver $aliasResolver,
    ) {}

    /**
     * @return array{success: bool, message: string, data: array<string, mixed>, diagnostics?: list<array<string, mixed>>}
     */
    public function run(Model $entity, User $actor, string $mode, bool $force): array
    {
        $dofusdbId = $this->resolveDofusdbId($entity);
        $this->assertTypeAllowsScrap($entity);
        $alias = $this->pipelineAliasFor($entity);
        $resolved = $this->resolveSourceEntity($alias);
        $state = $this->entityState($entity);
        $isProtected = in_array($state, [EntityState::Playable->value, EntityState::Archived->value], true);

        if ($isProtected && $mode !== 'preview') {
            if (! $actor->isAdmin()) {
                throw new HttpException(422, 'Fiche jouable ou archivée : un administrateur doit forcer la mise à jour DofusDB.');
            }
            if (! $force) {
                throw new HttpException(422, 'Fiche jouable ou archivée : cochez « forcer » pour écraser le contenu.');
            }
        } elseif ($force && ! $actor->isAdmin()) {
            throw new HttpException(422, 'Seul un administrateur peut forcer la mise à jour d’une fiche.');
        }

        $options = $this->orchestratorOptions($mode, $isProtected && $force && $actor->isAdmin());
        $result = $this->orchestrator->runOne($resolved['source'], $resolved['entity'], $dofusdbId, $options);

        return $this->formatResult($result, $mode, $dofusdbId);
    }

    /**
     * @return array{source: string, entity: string}
     */
    private function resolveSourceEntity(string $alias): array
    {
        $cfg = $this->aliasResolver->resolve($alias);
        if ($cfg !== null) {
            return [
                'source' => (string) ($cfg['source'] ?? 'dofusdb'),
                'entity' => (string) ($cfg['entity'] ?? $alias),
            ];
        }

        return ['source' => 'dofusdb', 'entity' => $alias === 'class' ? 'breed' : $alias];
    }

    /**
     * @return array<string, mixed>
     */
    private function orchestratorOptions(string $mode, bool $adminForce): array
    {
        $imagesOnly = $mode === 'images_only';
        $preview = $mode === 'preview';

        return [
            'convert' => true,
            'validate' => true,
            'integrate' => ! $preview,
            'dry_run' => $preview,
            'force_update' => $adminForce || ! $preview,
            'replace_mode' => $adminForce ? 'always' : null,
            'respect_auto_update' => ! $adminForce,
            'include_relations' => ! $imagesOnly,
            'exclude_from_update' => $imagesOnly ? DofusdbRefreshableEntities::IMAGE_ONLY_EXCLUDE : [],
            'download_images' => $imagesOnly || $mode === 'full' || $preview,
            'lang' => 'fr',
        ];
    }

    private function resolveDofusdbId(Model $entity): int
    {
        $raw = $entity->getAttribute('dofusdb_id');
        if ($raw === null || $raw === '') {
            throw new HttpException(422, 'Cette fiche n’a pas d’identifiant DofusDB.');
        }
        if (! is_numeric($raw) || (int) $raw < 1) {
            throw new HttpException(422, 'Identifiant DofusDB invalide sur cette fiche.');
        }

        return (int) $raw;
    }

    /**
     * Bloque la maj unitaire si le type / la race de la fiche a `allow_scrap` à false.
     */
    private function assertTypeAllowsScrap(Model $entity): void
    {
        $registry = match (true) {
            $entity instanceof Item => $entity->itemType()->first(),
            $entity instanceof Resource => $entity->resourceType()->first(),
            $entity instanceof Consumable => $entity->consumableType()->first(),
            $entity instanceof Monster => $entity->monsterRace()->first(),
            default => null,
        };

        if ($registry === null) {
            return;
        }

        if (! (bool) $registry->getAttribute('allow_scrap')) {
            throw new HttpException(422, 'Le type de cette fiche n’autorise pas la mise à jour depuis DofusDB.');
        }
    }

    private function pipelineAliasFor(Model $entity): string
    {
        $class = $entity::class;
        foreach (DofusdbRefreshableEntities::ALIASES as $type => $alias) {
            $mapped = EntityModelRegistry::modelMap()[$type] ?? null;
            if ($mapped === $class) {
                return $alias;
            }
        }

        throw new HttpException(422, 'Ce type d’entité n’est pas importable depuis DofusDB.');
    }

    private function entityState(Model $entity): ?string
    {
        $state = $entity->getAttribute('state');
        if (is_string($state) && $state !== '') {
            return $state;
        }
        if (method_exists($entity, 'creature')) {
            $creature = $entity->getRelationValue('creature') ?? $entity->creature()->first();
            $creatureState = is_object($creature) ? $creature->getAttribute('state') : null;
            if (is_string($creatureState) && $creatureState !== '') {
                return $creatureState;
            }
        }

        return null;
    }

    /**
     * @return array{success: bool, message: string, data: array<string, mixed>, diagnostics?: list<array<string, mixed>>}
     */
    private function formatResult(OrchestratorResult $result, string $mode, int $dofusdbId): array
    {
        $payload = [
            'success' => $result->isSuccess(),
            'message' => $result->getMessage() !== ''
                ? $result->getMessage()
                : ($result->isSuccess() ? 'Mise à jour DofusDB effectuée.' : 'Échec de la mise à jour DofusDB.'),
            'data' => [
                'mode' => $mode,
                'dofusdb_id' => $dofusdbId,
                'raw' => $result->getRaw(),
                'converted' => $result->getConverted(),
                'validation_errors' => $result->getValidationErrors(),
                'existing' => $result->getIntegrationResult()?->getData(),
            ],
        ];
        if ($result->getDiagnostics() !== []) {
            $payload['diagnostics'] = $result->getDiagnostics();
        }
        if (! $result->isSuccess() && $mode !== 'preview') {
            throw new HttpException(400, $payload['message']);
        }

        return $payload;
    }
}
