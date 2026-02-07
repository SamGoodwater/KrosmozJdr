<?php

namespace App\Services\Scrapping\Core\Integration;

use App\Models\Entity\Breed;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Models\Type\ResourceType;
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
    /** typeId DofusDB -> table cible (fallback si résolution DB échoue). */
    private const ITEM_TYPE_TO_TABLE = [
        12 => 'consumables',
        15 => 'resources',
        35 => 'resources',
    ];

    public function __construct()
    {
    }

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
        if ($entityType === 'item' || $entityType === 'resources' || $entityType === 'consumables' || $entityType === 'items') {
            return $this->integrateItem($convertedData, $options);
        }
        if ($entityType === 'panoply') {
            return $this->integratePanoply($convertedData, $options);
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
        unset($creatureAttributes['image']);
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

            $this->attachImageFromUrl($creature, $creatureData['image'] ?? null, $options);

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

    /**
     * Attache une image à l'entité via Media Library (addMediaFromUrl).
     * Respecte download_images et allowed_hosts (config scrapping.images).
     * Met à jour la colonne image de l'entité avec l'URL du média.
     *
     * @param object $entity Modèle avec HasMedia et collection 'images'
     * @param array{dry_run?: bool, download_images?: bool} $options
     * @return bool true si le média a été attaché, false si ignoré ou erreur
     */
    public function attachImageFromUrl(object $entity, ?string $imageUrl, array $options = []): bool
    {
        if ($imageUrl === null || trim($imageUrl) === '') {
            return false;
        }
        if (!($options['download_images'] ?? true)) {
            return false;
        }
        $url = trim($imageUrl);
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            return false;
        }
        $host = (string) parse_url($url, PHP_URL_HOST);
        if ($host !== '') {
            $allowedHosts = config('scrapping.images.allowed_hosts', []);
            if ($allowedHosts !== [] && !in_array(strtolower($host), array_map('strtolower', $allowedHosts), true)) {
                return false;
            }
        }
        if (!method_exists($entity, 'clearMediaCollection') || !method_exists($entity, 'addMediaFromUrl')) {
            return false;
        }
        try {
            $entity->clearMediaCollection('images');
            $ext = pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
            $customName = method_exists($entity, 'getMediaFileNameForCollection')
                ? $entity->getMediaFileNameForCollection('images', $ext)
                : null;
            $adder = $entity->addMediaFromUrl($url);
            if ($customName !== null && $customName !== '') {
                $adder->usingFileName($customName);
            }
            $media = $adder->toMediaCollection('images');
            $entity->update(['image' => $media->getUrl()]);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Integration attach image failed', ['url' => $url, 'error' => $e->getMessage()]);

            return false;
        }
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

        $po = $this->buildSpellPo($data);
        $payload = [
            'dofusdb_id' => $data['dofusdb_id'] ?? null,
            'name' => (string) ($data['name'] ?? ''),
            'description' => (string) ($data['description'] ?? ''),
            'pa' => (string) ($data['pa'] ?? '3'),
            'po' => $po,
            'po_editable' => (bool) (isset($data['po_editable']) ? (int) $data['po_editable'] : true),
            'area' => (int) ($data['area'] ?? 0),
            'level' => (string) ($data['level'] ?? '1'),
            'cast_per_turn' => (string) ($data['cast_per_turn'] ?? '1'),
            'cast_per_target' => (string) (isset($data['cast_per_target']) ? $data['cast_per_target'] : '0'),
            'sight_line' => (bool) (isset($data['sight_line']) ? (int) $data['sight_line'] : true),
            'number_between_two_cast' => (string) (isset($data['number_between_two_cast']) ? $data['number_between_two_cast'] : '0'),
            'number_between_two_cast_editable' => (bool) (isset($data['number_between_two_cast_editable']) ? (int) $data['number_between_two_cast_editable'] : true),
            'element' => (int) ($data['element'] ?? 0),
            'category' => (int) ($data['category'] ?? 0),
            'is_magic' => (bool) (isset($data['is_magic']) ? (int) $data['is_magic'] : true),
            'powerful' => (int) ($data['powerful'] ?? 0),
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
            $this->attachImageFromUrl($spell, $data['image'] ?? null, $options);
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
     * Construit la portée du sort (po) : une valeur (0-6) ou une plage "min-max" si spell_po_min et spell_po_max sont fournis.
     *
     * @param array<string, mixed> $data Données converties du sort (spells)
     * @return string Portée : "1", "3" ou "1-3" par exemple
     */
    private function buildSpellPo(array $data): string
    {
        $min = isset($data['spell_po_min']) && is_numeric($data['spell_po_min']) ? (int) $data['spell_po_min'] : null;
        $max = isset($data['spell_po_max']) && is_numeric($data['spell_po_max']) ? (int) $data['spell_po_max'] : null;
        if ($min !== null && $max !== null) {
            return $min === $max ? (string) $min : $min . '-' . $max;
        }

        return (string) ($data['po'] ?? '1');
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
            $this->attachImageFromUrl($breed, $data['image'] ?? null, $options);
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
     * @param array{dry_run?: bool, force_update?: bool, include_relations?: bool} $options
     */
    private function integrateItem(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);

        $typeId = isset($convertedData['items']['type_id']) ? (int) $convertedData['items']['type_id'] : null;
        $targetTable = $this->resolveItemTargetTable($typeId);
        $data = $convertedData[$targetTable] ?? $convertedData['items'] ?? $convertedData['resources'] ?? $convertedData['consumables'] ?? [];
        if ($data === []) {
            return IntegrationResult::fail('Données converties incomplètes (items/resources/consumables manquant).');
        }

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
            'created_by' => $userId,
        ];
        if ($targetTable === 'items') {
            $effectStr = $data['effect'] ?? null;
            $payload['effect'] = is_string($effectStr) ? $effectStr : (is_array($data['effect'] ?? null) ? json_encode($data['effect'], JSON_UNESCAPED_UNICODE) : null);
            $bonusRaw = $data['bonus'] ?? null;
            $payload['bonus'] = is_string($bonusRaw) ? $bonusRaw : (is_array($bonusRaw) ? json_encode($bonusRaw, JSON_UNESCAPED_UNICODE) : null);
        }

        try {
            DB::beginTransaction();
            if ($targetTable === 'resources') {
                $payload['weight'] = $data['weight'] ?? null;
                if (isset($data['resource_type_id']) && $data['resource_type_id'] !== null) {
                    $payload['resource_type_id'] = (int) $data['resource_type_id'];
                }
                if ($existing instanceof Resource) {
                    $existing->update($payload);
                    $entity = $existing;
                } else {
                    $entity = Resource::create($payload);
                }
                if ($options['include_relations'] ?? true) {
                    $this->syncResourceRecipe($entity, $data['recipe_ingredients'] ?? []);
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
            $this->attachImageFromUrl($entity, $data['image'] ?? null, $options);
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

    /**
     * Synchronise la recette d'une ressource (pivot resource_recipe) à partir des
     * ingrédients convertis (recipe_ingredients issus de recipeIds DofusDB).
     * Seuls les ingrédients déjà présents en base (Resource avec ce dofusdb_id) sont liés.
     *
     * @param list<array{ingredient_dofusdb_id: string, quantity: int}> $recipeIngredients
     */
    private function syncResourceRecipe(Resource $resource, array $recipeIngredients): void
    {
        if ($recipeIngredients === []) {
            $resource->recipeIngredients()->sync([]);

            return;
        }
        $dofusdbIds = array_map(
            static fn (array $row): string => (string) ($row['ingredient_dofusdb_id'] ?? ''),
            $recipeIngredients
        );
        $dofusdbIds = array_filter($dofusdbIds, static fn (string $id): bool => $id !== '');
        if ($dofusdbIds === []) {
            $resource->recipeIngredients()->sync([]);

            return;
        }
        $resourceIdsByDofusdbId = Resource::whereIn('dofusdb_id', $dofusdbIds)->pluck('id', 'dofusdb_id')->all();
        $sync = [];
        foreach ($recipeIngredients as $row) {
            $dofusdbId = (string) ($row['ingredient_dofusdb_id'] ?? '');
            $ingredientResourceId = $resourceIdsByDofusdbId[$dofusdbId] ?? null;
            if ($ingredientResourceId !== null) {
                $qty = (int) ($row['quantity'] ?? 1);
                $sync[$ingredientResourceId] = ['quantity' => (string) max(1, $qty)];
            }
        }
        $resource->recipeIngredients()->sync($sync);
    }

    /**
     * Détermine la table cible (resources, consumables, items) à partir des données converties.
     * Utilisé par l'orchestrateur pour la validation et par integrateItem.
     */
    public function getItemTargetTable(array $convertedData): string
    {
        $typeId = isset($convertedData['items']['type_id']) ? (int) $convertedData['items']['type_id'] : null;
        if ($typeId === null) {
            $typeId = isset($convertedData['resources']['type_id']) ? (int) $convertedData['resources']['type_id'] : null;
        }
        if ($typeId === null) {
            $typeId = isset($convertedData['consumables']['type_id']) ? (int) $convertedData['consumables']['type_id'] : null;
        }

        return $this->resolveItemTargetTable($typeId);
    }

    /**
     * Détermine la table cible (resources, consumables, items) à partir du typeId DofusDB.
     * Utilise les registres resource_types, consumable_types, item_types (dofusdb_type_id).
     */
    private function resolveItemTargetTable(?int $typeId): string
    {
        if ($typeId === null || $typeId <= 0) {
            return 'items';
        }
        if (ResourceType::where('dofusdb_type_id', $typeId)->exists()) {
            return 'resources';
        }
        if (ConsumableType::where('dofusdb_type_id', $typeId)->exists()) {
            return 'consumables';
        }
        if (ItemType::where('dofusdb_type_id', $typeId)->exists()) {
            return 'items';
        }

        return self::ITEM_TYPE_TO_TABLE[$typeId] ?? 'items';
    }

    /**
     * @param array{dry_run?: bool, force_update?: bool} $options
     */
    private function integratePanoply(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);

        $data = $convertedData['panoplies'] ?? [];
        if ($data === []) {
            return IntegrationResult::fail('Données converties incomplètes (panoplies manquant).');
        }

        $existingPanoply = null;
        $dofusdbId = isset($data['dofusdb_id']) ? (string) $data['dofusdb_id'] : null;
        if ($dofusdbId !== null && $dofusdbId !== '') {
            $existingPanoply = Panoply::where('dofusdb_id', $dofusdbId)->first();
        }
        if (!$existingPanoply && !empty($data['name'])) {
            $existingPanoply = Panoply::where('name', $data['name'])->first();
        }

        if ($existingPanoply && !$forceUpdate) {
            return IntegrationResult::okEntity(
                $existingPanoply->id,
                $dryRun ? 'would_skip' : 'skipped',
                'Panoplie déjà présente, ignorée.',
                ['panoply' => $existingPanoply->toArray()]
            );
        }

        if ($dryRun) {
            return IntegrationResult::okEntity(
                $existingPanoply?->id ?? 0,
                $existingPanoply ? 'would_update' : 'would_create',
                'Simulation : aucune écriture en base.',
                []
            );
        }

        try {
            $userId = $this->getSystemUserId();
        } catch (\Throwable $e) {
            return IntegrationResult::fail($e->getMessage());
        }

        $bonus = $data['bonus'] ?? null;
        $bonusStr = is_array($bonus) ? json_encode($bonus, JSON_UNESCAPED_UNICODE) : (string) $bonus;
        $payload = [
            'dofusdb_id' => $data['dofusdb_id'] ?? null,
            'name' => (string) ($data['name'] ?? ''),
            'description' => isset($data['description']) && (string) $data['description'] !== '' ? (string) $data['description'] : null,
            'bonus' => $bonusStr !== '' ? $bonusStr : null,
            'state' => Panoply::STATE_RAW,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => $userId,
        ];

        try {
            DB::beginTransaction();

            if ($existingPanoply) {
                $existingPanoply->update($payload);
                $panoply = $existingPanoply;
                $action = 'updated';
            } else {
                $panoply = Panoply::create($payload);
                $action = 'created';
            }

            $itemDofusdbIds = $data['item_dofusdb_ids'] ?? [];
            if (is_array($itemDofusdbIds) && $itemDofusdbIds !== []) {
                $itemIds = Item::whereIn('dofusdb_id', array_map('strval', $itemDofusdbIds))->pluck('id')->all();
                $panoply->items()->sync($itemIds);
            }

            DB::commit();
            Log::info('Intégration panoplie', ['panoply_id' => $panoply->id, 'action' => $action]);

            return IntegrationResult::okEntity(
                $panoply->id,
                $action,
                "Panoplie intégrée : {$action}.",
                ['panoply' => $panoply->toArray()]
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur intégration panoplie', ['error' => $e->getMessage()]);

            return IntegrationResult::fail($e->getMessage());
        }
    }

    /**
     * Retourne les attributs de l'entité existante (si trouvée) avec les mêmes clés que les données converties (merged).
     * Utilisé pour la sortie verbose de la commande scrapping (comparaison DofusDB / converti / existant).
     *
     * @param string $entityType monster, spell, breed, class, item, panoply
     * @param array<string, array<string, mixed>> $convertedData Structure par modèle (creatures, monsters, spells, …)
     * @return array<string, mixed>|null Attributs avec clés "converted" (ex. strength, intelligence) ou null si pas trouvé
     */
    public function getExistingAttributesForComparison(string $entityType, array $convertedData): ?array
    {
        if ($entityType === 'monster') {
            $creatureData = $convertedData['creatures'] ?? [];
            $monsterData = $convertedData['monsters'] ?? [];
            if ($creatureData === [] || $monsterData === []) {
                return null;
            }
            $existingMonster = null;
            if (!empty($monsterData['dofusdb_id'])) {
                $existingMonster = Monster::where('dofusdb_id', (string) $monsterData['dofusdb_id'])->first();
            }
            if (!$existingMonster && !empty($creatureData['name'])) {
                $existingCreature = Creature::where('name', (string) $creatureData['name'])->first();
                $existingMonster = $existingCreature?->monster;
            }
            if (!$existingMonster) {
                return null;
            }
            $c = $existingMonster->creature;
            $sizeMap = [0 => 'tiny', 1 => 'small', 2 => 'medium', 3 => 'large', 4 => 'huge'];
            $sizeInt = $existingMonster->size ?? 2;
            $sizeStr = $sizeMap[$sizeInt] ?? 'medium';
            return array_merge(
                [
                    'name' => $c?->name,
                    'level' => $c?->level,
                    'life' => $c?->life,
                    'strength' => $c?->strong,
                    'intelligence' => $c?->intel,
                    'agility' => $c?->agi,
                    'wisdom' => $c?->sagesse,
                    'chance' => $c?->chance,
                    'pa' => $c?->pa,
                    'pm' => $c?->pm,
                    'po' => $c?->po,
                    'image' => $c?->image,
                    'vitality' => $c?->vitality,
                    'res_neutre' => $c?->res_neutre,
                    'res_terre' => $c?->res_terre,
                    'res_feu' => $c?->res_feu,
                    'res_air' => $c?->res_air,
                    'res_eau' => $c?->res_eau,
                ],
                [
                    'dofusdb_id' => $existingMonster->dofusdb_id,
                    'size' => $sizeStr,
                    'monster_race_id' => $existingMonster->monster_race_id,
                ]
            );
        }

        if ($entityType === 'spell' || $entityType === 'breed' || $entityType === 'class') {
            $data = $convertedData['spells'] ?? $convertedData['breeds'] ?? $convertedData['classes'] ?? [];
            if ($data === []) {
                return null;
            }
            $model = $entityType === 'spell'
                ? Spell::where('dofusdb_id', (string) ($data['dofusdb_id'] ?? ''))->orWhere('name', $data['name'] ?? '')->first()
                : Breed::where('dofusdb_id', (string) ($data['dofusdb_id'] ?? ''))->orWhere('name', $data['name'] ?? '')->first();
            if (!$model) {
                return null;
            }
            return $model->toArray();
        }

        if ($entityType === 'panoply') {
            $data = $convertedData['panoplies'] ?? [];
            if ($data === []) {
                return null;
            }
            $p = Panoply::where('dofusdb_id', (string) ($data['dofusdb_id'] ?? ''))->orWhere('name', $data['name'] ?? '')->first();
            return $p ? $p->toArray() : null;
        }

        if ($entityType === 'item') {
            $data = $convertedData['items'] ?? $convertedData['resources'] ?? $convertedData['consumables'] ?? [];
            if ($data === []) {
                return null;
            }
            $typeId = isset($data['type_id']) ? (int) $data['type_id'] : null;
            $table = self::ITEM_TYPE_TO_TABLE[$typeId] ?? 'items';
            $dofusdbId = (string) ($data['dofusdb_id'] ?? '');
            $name = $data['name'] ?? '';
            $model = match ($table) {
                'resources' => Resource::where('dofusdb_id', $dofusdbId)->orWhere('name', $name)->first(),
                'consumables' => Consumable::where('dofusdb_id', $dofusdbId)->orWhere('name', $name)->first(),
                default => Item::where('dofusdb_id', $dofusdbId)->orWhere('name', $name)->first(),
            };
            if (!$model) {
                return null;
            }
            $out = $model->toArray();
            if ($model instanceof Resource) {
                $model->load('recipeIngredients');
                $recipeIngredients = [];
                foreach ($model->recipeIngredients as $ing) {
                    $recipeIngredients[] = [
                        'ingredient_resource_id' => $ing->id,
                        'ingredient_dofusdb_id' => $ing->dofusdb_id ?? (string) $ing->id,
                        'quantity' => (int) ($ing->pivot->quantity ?? 1),
                    ];
                }
                $out['recipe_ingredients'] = $recipeIngredients;
            }
            return $out;
        }

        return null;
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
