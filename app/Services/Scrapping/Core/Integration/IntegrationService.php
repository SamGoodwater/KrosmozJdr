<?php

namespace App\Services\Scrapping\Core\Integration;

use App\Models\Entity\Breed;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service d’intégration V2 : enregistre les données converties en base (ou simule).
 *
 * Pour l’instant : entité monster uniquement (Creature + Monster).
 * Option dry_run : pas d’écriture, retourne un résumé (would_create / would_update).
 */
final class IntegrationService
{
    /** typeId DofusDB -> table cible (resources, consumables, items). */
    private const ITEM_TYPE_TO_TABLE = [
        12 => 'consumables',
        15 => 'resources',
        35 => 'resources',
    ];

    /**
     * Intègre les données converties pour un type d’entité.
     *
     * @param string $entityType Type KrosmozJDR (ex. monster)
     * @param array<string, array<string, mixed>> $convertedData Structure par modèle (creatures, monsters)
     * @param array{dry_run?: bool, force_update?: bool, ignore_unvalidated?: bool, exclude_from_update?: list<string>} $options
     * @return IntegrationResult
     */
    public function integrate(string $entityType, array $convertedData, array $options = []): IntegrationResult
    {
        if ($entityType === 'monster') {
            return $this->integrateMonster($convertedData, $options);
        }
        if ($entityType === 'spell') {
            return $this->integrateSpell($convertedData, $options);
        }
        if ($entityType === 'breed' || $entityType === 'class') {
            return $this->integrateBreed($convertedData, $options);
        }
        if ($entityType === 'item') {
            return $this->integrateItem($convertedData, $options);
        }

        return IntegrationResult::fail("Type d'entité non supporté : {$entityType}");
    }

    /**
     * @param array{dry_run?: bool, force_update?: bool, ignore_unvalidated?: bool, exclude_from_update?: list<string>} $options
     */
    private function integrateMonster(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);
        $ignoreUnvalidated = (bool) ($options['ignore_unvalidated'] ?? false);
        /** @var list<string> $excludeFromUpdate */
        $excludeFromUpdate = $options['exclude_from_update'] ?? [];
        if (!is_array($excludeFromUpdate)) {
            $excludeFromUpdate = [];
        }

        $creatureData = $convertedData['creatures'] ?? [];
        $monsterData = $convertedData['monsters'] ?? [];

        if ($creatureData === [] || $monsterData === []) {
            return IntegrationResult::fail('Données converties incomplètes (creatures ou monsters manquants).');
        }

        if ($ignoreUnvalidated) {
            $inputRaceId = $monsterData['monster_race_id'] ?? null;
            if ($inputRaceId !== null && $inputRaceId !== '') {
                $resolvedRaceId = $this->resolveMonsterRaceId($inputRaceId);
                if ($resolvedRaceId === null) {
                    return IntegrationResult::ok(
                        null,
                        null,
                        'skipped',
                        'skipped',
                        'Race non validée (absente de la base), objet ignoré.'
                    );
                }
            }
        }

        $existingMonsterByDofus = null;
        if (!empty($monsterData['dofusdb_id'])) {
            $existingMonsterByDofus = Monster::where('dofusdb_id', (string) $monsterData['dofusdb_id'])->first();
        }
        $existingCreature = $existingMonsterByDofus?->creature ?? Creature::where('name', (string) ($creatureData['name'] ?? ''))->first();

        if ($existingMonsterByDofus && !$forceUpdate) {
            return IntegrationResult::ok(
                $existingCreature?->id,
                $existingMonsterByDofus->id,
                $dryRun ? 'would_skip' : 'skipped',
                $dryRun ? 'would_skip' : 'skipped',
                'Monstre déjà présent (dofusdb_id), ignoré.'
            );
        }

        if ($dryRun) {
            return IntegrationResult::ok(
                $existingCreature?->id,
                $existingMonsterByDofus?->id,
                $existingCreature ? 'would_update' : 'would_create',
                $existingMonsterByDofus ? 'would_update' : 'would_create',
                'Simulation : aucune écriture en base.'
            );
        }

        try {
            $userId = $this->getSystemUserId();
        } catch (\Throwable $e) {
            return IntegrationResult::fail($e->getMessage());
        }

        $creatureAttributes = $this->mapCreatureAttributes($creatureData, $userId);
        $sizeInt = $this->sizeStringToInt((string) ($monsterData['size'] ?? 'medium'));
        $monsterRaceId = $this->resolveMonsterRaceId($monsterData['monster_race_id'] ?? null);

        if ($existingCreature && $excludeFromUpdate !== []) {
            $creatureAttributes = $this->filterExcludedFromUpdate($creatureAttributes, $excludeFromUpdate);
        }

        $monsterUpdate = [
            'dofusdb_id' => $monsterData['dofusdb_id'] ?? null,
            'size' => $sizeInt,
            'monster_race_id' => $monsterRaceId,
        ];
        if ($excludeFromUpdate !== []) {
            $monsterUpdate = $this->filterExcludedFromUpdate($monsterUpdate, $excludeFromUpdate);
        }

        try {
            DB::beginTransaction();

            if ($existingCreature) {
                if ($creatureAttributes !== []) {
                    $existingCreature->update($creatureAttributes);
                }
                $creature = $existingCreature;
                $creatureAction = 'updated';
            } else {
                $creature = Creature::create($creatureAttributes);
                $creatureAction = 'created';
            }

            $existingMonster = $existingMonsterByDofus ?? Monster::where('creature_id', $creature->id)->first();

            if ($existingMonster) {
                $payload = $monsterUpdate;
                if ($payload === []) {
                    $payload = [
                        'dofusdb_id' => $existingMonster->dofusdb_id,
                        'size' => $existingMonster->size,
                        'monster_race_id' => $existingMonster->monster_race_id,
                    ];
                } else {
                    $payload['dofusdb_id'] = $payload['dofusdb_id'] ?? $existingMonster->dofusdb_id;
                }
                $existingMonster->update($payload);
                $monster = $existingMonster;
                $monsterAction = 'updated';
            } else {
                $monster = Monster::create([
                    'creature_id' => $creature->id,
                    'dofusdb_id' => $monsterData['dofusdb_id'] ?? null,
                    'size' => $sizeInt,
                    'monster_race_id' => $monsterRaceId,
                ]);
                $monsterAction = 'created';
            }

            DB::commit();

            Log::info('Intégration monstre', [
                'creature_id' => $creature->id,
                'monster_id' => $monster->id,
                'creature_action' => $creatureAction,
                'monster_action' => $monsterAction,
            ]);

            return IntegrationResult::ok(
                $creature->id,
                $monster->id,
                $creatureAction,
                $monsterAction,
                "Monstre intégré : {$creatureAction} creature, {$monsterAction} monster.",
                ['creature' => $creature->toArray(), 'monster' => $monster->toArray()]
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur intégration monstre', ['error' => $e->getMessage()]);

            return IntegrationResult::fail($e->getMessage(), ['exception' => $e->getMessage()]);
        }
    }

    /**
     * @param array<string, mixed> $creatureData
     * @return array<string, mixed>
     */
    private function mapCreatureAttributes(array $creatureData, int $createdBy): array
    {
        $attrs = [
            'name' => (string) ($creatureData['name'] ?? ''),
            'level' => (string) ($creatureData['level'] ?? '1'),
            'life' => (string) ($creatureData['life'] ?? '1'),
            'strong' => (string) ($creatureData['strength'] ?? '0'),
            'intel' => (string) ($creatureData['intelligence'] ?? '0'),
            'agi' => (string) ($creatureData['agility'] ?? '0'),
            'sagesse' => (string) ($creatureData['wisdom'] ?? '0'),
            'chance' => (string) ($creatureData['chance'] ?? '0'),
            'image' => $creatureData['image'] ?? null,
            'created_by' => $createdBy,
        ];

        $optional = ['pa', 'pm', 'kamas', 'po', 'dodge_pa', 'dodge_pm', 'vitality', 'res_neutre', 'res_terre', 'res_feu', 'res_air', 'res_eau'];
        foreach ($optional as $key) {
            if (array_key_exists($key, $creatureData) && $creatureData[$key] !== null) {
                $attrs[$key] = (string) $creatureData[$key];
            }
        }

        return $attrs;
    }

    private function sizeStringToInt(string $size): int
    {
        $map = [
            'tiny' => 0,
            'small' => 1,
            'medium' => 2,
            'large' => 3,
            'huge' => 4,
        ];

        return $map[$size] ?? 2;
    }

    /**
     * Retire des données les clés listées dans exclude (pour ne pas écraser à la mise à jour).
     *
     * @param array<string, mixed> $data
     * @param list<string> $exclude
     * @return array<string, mixed>
     */
    private function filterExcludedFromUpdate(array $data, array $exclude): array
    {
        if ($exclude === []) {
            return $data;
        }
        $excludeSet = array_fill_keys($exclude, true);

        return array_diff_key($data, $excludeSet);
    }

    private function resolveMonsterRaceId(mixed $monsterRaceId): ?int
    {
        if ($monsterRaceId === null) {
            return null;
        }
        $id = is_numeric($monsterRaceId) ? (int) $monsterRaceId : null;
        if ($id === null) {
            return null;
        }
        $exists = DB::table('monster_races')->where('id', $id)->exists();

        return $exists ? $id : null;
    }

    /**
     * @param array{dry_run?: bool, force_update?: bool} $options
     */
    private function integrateSpell(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);

        $data = $convertedData['spells'] ?? [];
        if ($data === []) {
            return IntegrationResult::fail('Données converties incomplètes (spells manquant).');
        }

        $existingSpell = null;
        if (!empty($data['dofusdb_id'])) {
            $existingSpell = Spell::where('dofusdb_id', (string) $data['dofusdb_id'])->first();
        }
        if (!$existingSpell && !empty($data['name'])) {
            $existingSpell = Spell::where('name', $data['name'])->first();
        }

        if ($existingSpell && !$forceUpdate) {
            return IntegrationResult::okEntity(
                $existingSpell->id,
                $dryRun ? 'would_skip' : 'skipped',
                'Sort déjà présent, ignoré.',
                ['spell' => $existingSpell->toArray()]
            );
        }

        if ($dryRun) {
            return IntegrationResult::okEntity(
                $existingSpell?->id ?? 0,
                $existingSpell ? 'would_update' : 'would_create',
                'Simulation : aucune écriture en base.',
                []
            );
        }

        try {
            $userId = $this->getSystemUserId();
        } catch (\Throwable $e) {
            return IntegrationResult::fail($e->getMessage());
        }

        $payload = [
            'dofusdb_id' => $data['dofusdb_id'] ?? null,
            'name' => (string) ($data['name'] ?? ''),
            'description' => (string) ($data['description'] ?? ''),
            'image' => $data['image'] ?? null,
            'pa' => (string) ($data['pa'] ?? '3'),
            'po' => (string) ($data['po'] ?? '1'),
            'area' => (int) ($data['area'] ?? 0),
            'level' => (string) ($data['level'] ?? '1'),
            'cast_per_turn' => (string) ($data['cast_per_turn'] ?? '1'),
            'created_by' => $userId,
        ];

        try {
            DB::beginTransaction();
            if ($existingSpell) {
                $existingSpell->update($payload);
                $spell = $existingSpell;
                $action = 'updated';
            } else {
                $spell = Spell::create($payload);
                $action = 'created';
            }
            DB::commit();
            Log::info('Intégration sort', ['spell_id' => $spell->id, 'action' => $action]);

            return IntegrationResult::okEntity(
                $spell->id,
                $action,
                "Sort intégré : {$action}.",
                ['spell' => $spell->toArray()]
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur intégration sort', ['error' => $e->getMessage()]);

            return IntegrationResult::fail($e->getMessage());
        }
    }

    /**
     * @param array{dry_run?: bool, force_update?: bool} $options
     */
    private function integrateBreed(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);

        $data = $convertedData['breeds'] ?? $convertedData['classes'] ?? [];
        if ($data === []) {
            return IntegrationResult::fail('Données converties incomplètes (breeds manquant).');
        }

        $existingBreed = null;
        if (!empty($data['dofusdb_id'])) {
            $existingBreed = Breed::where('dofusdb_id', (string) $data['dofusdb_id'])->first();
        }
        if (!$existingBreed && !empty($data['name'])) {
            $existingBreed = Breed::where('name', $data['name'])->first();
        }

        if ($existingBreed && !$forceUpdate) {
            return IntegrationResult::okEntity(
                $existingBreed->id,
                $dryRun ? 'would_skip' : 'skipped',
                'Classe déjà présente, ignorée.',
                ['breed' => $existingBreed->toArray()]
            );
        }

        if ($dryRun) {
            return IntegrationResult::okEntity(
                $existingBreed?->id ?? 0,
                $existingBreed ? 'would_update' : 'would_create',
                'Simulation : aucune écriture en base.',
                []
            );
        }

        try {
            $userId = $this->getSystemUserId();
        } catch (\Throwable $e) {
            return IntegrationResult::fail($e->getMessage());
        }

        $payload = [
            'dofusdb_id' => $data['dofusdb_id'] ?? null,
            'name' => (string) ($data['name'] ?? ''),
            'description' => (string) ($data['description'] ?? ''),
            'description_fast' => $data['description_fast'] ?? null,
            'life' => (string) ($data['life'] ?? ''),
            'life_dice' => (string) ($data['life_dice'] ?? ''),
            'specificity' => $data['specificity'] ?? null,
            'image' => $data['image'] ?? null,
            'created_by' => $userId,
        ];

        try {
            DB::beginTransaction();
            if ($existingBreed) {
                $existingBreed->update($payload);
                $breed = $existingBreed;
                $action = 'updated';
            } else {
                $breed = Breed::create($payload);
                $action = 'created';
            }
            DB::commit();
            Log::info('Intégration breed (classe)', ['breed_id' => $breed->id, 'action' => $action]);

            return IntegrationResult::okEntity(
                $breed->id,
                $action,
                "Classe intégrée : {$action}.",
                ['breed' => $breed->toArray()]
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur intégration breed (classe)', ['error' => $e->getMessage()]);

            return IntegrationResult::fail($e->getMessage());
        }
    }

    /**
     * @param array{dry_run?: bool, force_update?: bool} $options
     */
    private function integrateItem(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);

        $typeId = isset($convertedData['items']['type_id']) ? (int) $convertedData['items']['type_id'] : null;
        $data = $convertedData['items'] ?? $convertedData['resources'] ?? $convertedData['consumables'] ?? [];
        if ($data === []) {
            return IntegrationResult::fail('Données converties incomplètes (items/resources/consumables manquant).');
        }

        $targetTable = self::ITEM_TYPE_TO_TABLE[$typeId] ?? 'items';

        $existing = null;
        $dofusdbId = isset($data['dofusdb_id']) ? (string) $data['dofusdb_id'] : null;
        if ($dofusdbId) {
            $existing = match ($targetTable) {
                'items' => Item::where('dofusdb_id', $dofusdbId)->first(),
                'consumables' => Consumable::where('dofusdb_id', $dofusdbId)->first(),
                'resources' => Resource::where('dofusdb_id', $dofusdbId)->first(),
                default => Item::where('dofusdb_id', $dofusdbId)->first(),
            };
        }
        if (!$existing && !empty($data['name'])) {
            $existing = match ($targetTable) {
                'items' => Item::where('name', $data['name'])->first(),
                'consumables' => Consumable::where('name', $data['name'])->first(),
                'resources' => Resource::where('name', $data['name'])->first(),
                default => Item::where('name', $data['name'])->first(),
            };
        }

        if ($existing && !$forceUpdate) {
            $id = $existing->id;
            return IntegrationResult::okEntity(
                $id,
                $dryRun ? 'would_skip' : 'skipped',
                'Objet déjà présent, ignoré.',
                ['table' => $targetTable, 'entity' => $existing->toArray()]
            );
        }

        if ($dryRun) {
            return IntegrationResult::okEntity(
                $existing?->id ?? 0,
                $existing ? 'would_update' : 'would_create',
                'Simulation : aucune écriture en base.',
                ['table' => $targetTable]
            );
        }

        try {
            $userId = $this->getSystemUserId();
        } catch (\Throwable $e) {
            return IntegrationResult::fail($e->getMessage());
        }

        $rarity = isset($data['rarity']) ? (int) $data['rarity'] : 0;
        $payload = [
            'dofusdb_id' => $data['dofusdb_id'] ?? null,
            'name' => (string) ($data['name'] ?? ''),
            'description' => (string) ($data['description'] ?? ''),
            'level' => (string) ($data['level'] ?? '1'),
            'price' => $data['price'] !== null ? (string) $data['price'] : null,
            'rarity' => $rarity,
            'image' => $data['image'] ?? null,
            'created_by' => $userId,
        ];

        try {
            DB::beginTransaction();
            if ($targetTable === 'resources') {
                $payload['weight'] = $data['weight'] ?? null;
                if ($existing instanceof Resource) {
                    $existing->update($payload);
                    $entity = $existing;
                } else {
                    $entity = Resource::create($payload);
                }
            } elseif ($targetTable === 'consumables') {
                if ($existing instanceof Consumable) {
                    $existing->update($payload);
                    $entity = $existing;
                } else {
                    $entity = Consumable::create($payload);
                }
            } else {
                if ($existing instanceof Item) {
                    $existing->update($payload);
                    $entity = $existing;
                } else {
                    $entity = Item::create($payload);
                }
            }
            $action = $existing ? 'updated' : 'created';
            DB::commit();
            Log::info('Intégration item', ['id' => $entity->id, 'table' => $targetTable, 'action' => $action]);

            return IntegrationResult::okEntity(
                $entity->id,
                $action,
                "Objet intégré : {$action} ({$targetTable}).",
                ['table' => $targetTable, 'entity' => $entity->toArray()]
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur intégration item', ['error' => $e->getMessage()]);

            return IntegrationResult::fail($e->getMessage());
        }
    }

    private function getSystemUserId(): int
    {
        if (auth()->check()) {
            return (int) auth()->id();
        }
        $systemUser = User::getSystemUser();
        if ($systemUser) {
            return $systemUser->id;
        }
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if ($admin) {
            return $admin->id;
        }
        $user = User::first();
        if ($user) {
            return $user->id;
        }

        throw new \RuntimeException('Aucun utilisateur disponible pour les imports. Exécutez le seeder.');
    }
}
