<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Relation;

use App\Models\Entity\Creature;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use App\Services\Scrapping\Core\Orchestrator\OrchestratorResult;
use Illuminate\Support\Facades\Log;

/**
 * Résolution des relations pour l'import monster.
 *
 * Importe les entités liées (sorts, drops) via le pipeline puis synchronise
 * les associations sur la créature (creature_spell, creature_resource).
 *
 * @see docs/50-Fonctionnalités/Scrapping/Architecture/RELATIONS.md
 */
final class RelationResolutionService
{
    public function __construct(
        private Orchestrator $orchestrator
    ) {
    }

    /**
     * Résout les relations monster (spells, drops) et synchronise sur la créature.
     *
     * Pour chaque sort dans rawData['spells'] : runOne spell → récupère l'ID KrosmozJDR → sync creature_spell.
     * Pour chaque drop dans rawData['drops'] : runOne item → si entité intégrée est une Resource, récupère l'ID → sync creature_resource.
     *
     * @param array<string, mixed> $rawData Données brutes du monstre (avec spells, drops)
     * @param int $creatureId ID de la créature KrosmozJDR
     * @param array{convert?: bool, validate?: bool, integrate?: bool, dry_run?: bool, force_update?: bool} $options Options du pipeline
     * @return array{spell_ids: list<int>, resource_ids: list<int>, related_results: list<array{type: string, id: int, success: bool, krosmoz_id: int|null}>}
     */
    public function resolveAndSyncMonsterRelations(array $rawData, int $creatureId, array $options = []): array
    {
        $integrate = (bool) ($options['integrate'] ?? true);
        $dryRun = (bool) ($options['dry_run'] ?? false);

        $v2Options = [
            'convert' => true,
            'validate' => true,
            'integrate' => $integrate && !$dryRun,
            'dry_run' => $dryRun,
            'force_update' => (bool) ($options['force_update'] ?? false),
        ];

        $importedSpellIds = [];
        $relatedResults = [];

        if (isset($rawData['spells']) && is_array($rawData['spells'])) {
            foreach ($rawData['spells'] as $spellData) {
                $spellId = isset($spellData['id']) ? (int) $spellData['id'] : 0;
                if ($spellId <= 0) {
                    continue;
                }
                $result = $this->orchestrator->runOne('dofusdb', 'spell', $spellId, $v2Options);
                $krosmozId = null;
                if ($result->isSuccess()) {
                    $intResult = $result->getIntegrationResult();
                    if ($intResult !== null && $intResult->isSuccess()) {
                        $krosmozId = $intResult->getPrimaryId();
                        if ($krosmozId !== null) {
                            $importedSpellIds[] = $krosmozId;
                        }
                    }
                } else {
                    Log::warning('RelationResolutionService: import sort échoué', [
                        'spell_id' => $spellId,
                        'message' => $result->getMessage(),
                    ]);
                }
                $relatedResults[] = [
                    'type' => 'spell',
                    'id' => $spellId,
                    'success' => $result->isSuccess(),
                    'krosmoz_id' => $krosmozId,
                ];
            }
        }

        $importedResourceIds = [];
        if (isset($rawData['drops']) && is_array($rawData['drops'])) {
            foreach ($rawData['drops'] as $dropData) {
                $itemId = isset($dropData['id']) ? (int) $dropData['id'] : 0;
                if ($itemId <= 0) {
                    continue;
                }
                $result = $this->orchestrator->runOne('dofusdb', 'item', $itemId, $v2Options);
                $krosmozId = null;
                if ($result->isSuccess()) {
                    $intResult = $result->getIntegrationResult();
                    if ($intResult !== null && $intResult->isSuccess()) {
                        $data = $intResult->getData();
                        $table = $data['table'] ?? 'items';
                        if ($table === 'resources') {
                            $krosmozId = $intResult->getPrimaryId();
                            if ($krosmozId !== null) {
                                $importedResourceIds[] = $krosmozId;
                            }
                        }
                    }
                } else {
                    Log::warning('RelationResolutionService: import item (drop) échoué', [
                        'item_id' => $itemId,
                        'message' => $result->getMessage(),
                    ]);
                }
                $relatedResults[] = [
                    'type' => 'resource',
                    'id' => $itemId,
                    'success' => $result->isSuccess(),
                    'krosmoz_id' => $krosmozId,
                ];
            }
        }

        $creature = Creature::find($creatureId);
        if ($creature !== null && !$dryRun) {
            $validSpellIds = array_values(array_unique(array_filter($importedSpellIds)));
            if ($validSpellIds !== []) {
                $creature->spells()->sync($validSpellIds);
            }
            $validResourceIds = array_values(array_unique(array_filter($importedResourceIds)));
            if ($validResourceIds !== []) {
                $creature->resources()->sync(array_fill_keys($validResourceIds, ['quantity' => '1']));
            }
        }

        return [
            'spell_ids' => array_values(array_unique(array_filter($importedSpellIds))),
            'resource_ids' => array_values(array_unique(array_filter($importedResourceIds))),
            'related_results' => $relatedResults,
        ];
    }
}
