<?php

namespace App\Services\Scrapping\Core\Integration;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectSubEffect;
use App\Models\EffectUsage;
use App\Models\Entity\Breed;
use App\Models\Entity\Condition;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Models\Type\ResourceType;
use App\Models\Type\SpellType;
use App\Models\User;
use App\Services\Scrapping\Catalog\DofusDbItemSuperTypeMappingService;
use App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService;
use App\Support\DofusDbElementId;
use App\Support\ElementBitmask;
use Illuminate\Support\Facades\Auth;
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
    public function __construct(
        private readonly ?DofusDbItemTypesCatalogService $itemTypesCatalog = null,
        private readonly ?DofusDbItemSuperTypeMappingService $superTypeMapping = null
    ) {}

    /**
     * Intègre les données converties pour un type d’entité.
     *
     * @param  string  $entityType  Type KrosmozJDR (ex. monster)
     * @param  array<string, array<string, mixed>>  $convertedData  Structure par modèle (creatures, monsters)
     * @param  array{dry_run?: bool, force_update?: bool, ignore_unvalidated?: bool, exclude_from_update?: list<string>}  $options
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
     * Indique si on remplacerait une entité existante (public pour pré-vérification batch).
     *
     * @param  bool  $forceUpdate  Valeur legacy force_update
     * @param  string|null  $replaceMode  'never' | 'draft_raw_only' | 'always'
     * @param  Creature|Item|resource|Consumable|Spell|Breed|Panoply|Monster|null  $existing  Entité existante (avec state)
     * @param  Creature|Item|resource|Consumable|Spell|Breed|Monster|null  $entityWithAutoUpdate  Entité portant le champ auto_update (Monster pour Creature)
     */
    public function wouldReplaceExisting(
        bool $forceUpdate,
        ?string $replaceMode,
        $existing,
        $entityWithAutoUpdate = null,
        bool $respectAutoUpdate = true
    ): bool {
        $entityForAutoUpdate = $entityWithAutoUpdate ?? $existing;
        if ($respectAutoUpdate && $entityForAutoUpdate !== null && isset($entityForAutoUpdate->auto_update)) {
            if (! (bool) $entityForAutoUpdate->auto_update) {
                return false;
            }
        }

        if ($replaceMode !== null && $replaceMode !== '') {
            if ($replaceMode === 'always') {
                return true;
            }
            if ($replaceMode === 'never') {
                return false;
            }
            if ($replaceMode === 'draft_raw_only' && $existing !== null) {
                $state = $existing->state ?? null;

                return $state === Creature::STATE_RAW || $state === Creature::STATE_DRAFT;
            }
        }

        return $forceUpdate;
    }

    /**
     * @param  array{dry_run?: bool, force_update?: bool, replace_mode?: string, respect_auto_update?: bool, ignore_unvalidated?: bool, exclude_from_update?: list<string>, property_whitelist?: list<string>}  $options
     */
    private function integrateMonster(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);
        $replaceMode = isset($options['replace_mode']) ? (string) $options['replace_mode'] : null;
        $respectAutoUpdate = (bool) ($options['respect_auto_update'] ?? true);
        $ignoreUnvalidated = (bool) ($options['ignore_unvalidated'] ?? false);
        /** @var list<string> $excludeFromUpdate */
        $excludeFromUpdate = $options['exclude_from_update'] ?? [];
        if (! is_array($excludeFromUpdate)) {
            $excludeFromUpdate = [];
        }
        /** @var list<string> $propertyWhitelist */
        $propertyWhitelist = $options['property_whitelist'] ?? [];
        if (! is_array($propertyWhitelist)) {
            $propertyWhitelist = [];
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
        if (! empty($monsterData['dofusdb_id'])) {
            $existingMonsterByDofus = Monster::where('dofusdb_id', (string) $monsterData['dofusdb_id'])->first();
        }
        $existingCreature = $existingMonsterByDofus?->creature ?? Creature::where('name', (string) ($creatureData['name'] ?? ''))->first();

        $doReplace = $this->wouldReplaceExisting($forceUpdate, $replaceMode, $existingCreature, $existingMonsterByDofus, $respectAutoUpdate);
        if ($existingMonsterByDofus && ! $doReplace) {
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

        if ($propertyWhitelist !== []) {
            $creatureAttributes = $this->filterByWhitelist($creatureAttributes, $propertyWhitelist);
        }
        if ($excludeFromUpdate !== []) {
            $creatureAttributes = $this->filterExcludedFromUpdate($creatureAttributes, $excludeFromUpdate);
        }

        $monsterUpdate = [
            'dofusdb_id' => $monsterData['dofusdb_id'] ?? null,
            'size' => $sizeInt,
            'monster_race_id' => $monsterRaceId,
        ];
        if ($propertyWhitelist !== []) {
            $monsterUpdate = $this->filterByWhitelist($monsterUpdate, $propertyWhitelist);
        }
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
     * @param  array<string, mixed>  $creatureData
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

        $optional = ['pa', 'pm', 'kamas', 'po', 'dodge_pa', 'dodge_pm', 'ini', 'vitality', 'res_neutre', 'res_terre', 'res_feu', 'res_air', 'res_eau', 'res_sagesse', 'res_vitalite', 'do_sagesse', 'do_vitalite'];
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
     * @param  array<string, mixed>  $data
     * @param  list<string>  $exclude
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
     * Restreint les clés au whitelist si non vide.
     *
     * @param  array<string, mixed>  $data
     * @param  list<string>  $whitelist
     * @return array<string, mixed>
     */
    private function filterByWhitelist(array $data, array $whitelist): array
    {
        if ($whitelist === []) {
            return $data;
        }
        $allowSet = array_fill_keys($whitelist, true);

        return array_intersect_key($data, $allowSet);
    }

    /**
     * Attache une image à l'entité via Media Library (addMediaFromUrl).
     * Respecte download_images et allowed_hosts (config scrapping.images).
     * Met à jour la colonne image de l'entité avec l'URL du média.
     *
     * @param  object  $entity  Modèle avec HasMedia et collection 'images'
     * @param  array{dry_run?: bool, download_images?: bool}  $options
     * @return bool true si le média a été attaché, false si ignoré ou erreur
     */
    public function attachImageFromUrl(object $entity, ?string $imageUrl, array $options = []): bool
    {
        if ($imageUrl === null || trim($imageUrl) === '') {
            return false;
        }
        if (! ($options['download_images'] ?? true)) {
            return false;
        }
        $url = trim($imageUrl);
        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            return false;
        }
        $host = (string) parse_url($url, PHP_URL_HOST);
        if ($host !== '') {
            $allowedHosts = config('scrapping.images.allowed_hosts', []);
            if ($allowedHosts !== [] && ! in_array(strtolower($host), array_map('strtolower', $allowedHosts), true)) {
                return false;
            }
        }
        if (! method_exists($entity, 'clearMediaCollection') || ! method_exists($entity, 'addMediaFromUrl')) {
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
     * @param  array{dry_run?: bool, force_update?: bool, replace_mode?: string, respect_auto_update?: bool, exclude_from_update?: list<string>, property_whitelist?: list<string>}  $options
     */
    private function integrateSpell(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);
        $replaceMode = isset($options['replace_mode']) ? (string) $options['replace_mode'] : null;
        $respectAutoUpdate = (bool) ($options['respect_auto_update'] ?? true);
        $excludeFromUpdate = is_array($options['exclude_from_update'] ?? null) ? $options['exclude_from_update'] : [];
        $propertyWhitelist = is_array($options['property_whitelist'] ?? null) ? $options['property_whitelist'] : [];

        $data = $convertedData['spells'] ?? [];
        if ($data === []) {
            return IntegrationResult::fail('Données converties incomplètes (spells manquant).');
        }

        $existingSpell = null;
        $existingByDofusId = false;
        if (! empty($data['dofusdb_id'])) {
            $existingSpell = Spell::where('dofusdb_id', (string) $data['dofusdb_id'])->first();
            $existingByDofusId = $existingSpell !== null;
        }
        if (! $existingSpell && ! empty($data['name'])) {
            $existingSpell = Spell::where('name', $data['name'])->first();
        }

        $doReplace = $this->wouldReplaceExisting($forceUpdate, $replaceMode, $existingSpell, null, $respectAutoUpdate);
        // Pour les sorts, un match dofusdb_id reste synchronisé à la source si auto_update le permet (sauf replace_mode=never).
        if ($replaceMode !== 'never' && $existingByDofusId && (! $respectAutoUpdate || ($existingSpell !== null && (bool) $existingSpell->auto_update))) {
            $doReplace = true;
        }
        if ($existingSpell && ! $doReplace) {
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

        [$poMin, $poMax] = $this->buildSpellPoMinMax($data);
        $payload = [
            'dofusdb_id' => $data['dofusdb_id'] ?? null,
            'name' => $this->localizedToString($data['name'] ?? null),
            'description' => $this->localizedToString($data['description'] ?? null),
            'pa' => (string) ($data['pa'] ?? '3'),
            'po_min' => $poMin,
            'po_max' => $poMax,
            'po_editable' => (bool) (isset($data['po_editable']) ? (int) $data['po_editable'] : true),
            'level' => (string) ($data['level'] ?? '1'),
            'cast_per_turn' => (string) ($data['cast_per_turn'] ?? '1'),
            'cast_per_target' => (string) (isset($data['cast_per_target']) ? $data['cast_per_target'] : '0'),
            'sight_line' => (bool) (isset($data['sight_line']) ? (int) $data['sight_line'] : true),
            'number_between_two_cast' => (string) (isset($data['number_between_two_cast']) ? $data['number_between_two_cast'] : '0'),
            /**
             * Pas de mapping depuis spell_global.elementId : côté Dofus ce champ sert au jet d’attaque
             * (intel / chance / force / agi), pas à l’élément des dégâts — voir inferAttackCharacteristicFromSpellRaw.
             * L’élément affiché est déduit uniquement des sous-effets (inferSpellElementMaskFromEffectsPayload).
             */
            'element' => null,
            'category' => (int) ($options['spell_category_hint'] ?? $data['category'] ?? Spell::CATEGORY_CREATURE),
            'is_magic' => (bool) (isset($data['is_magic']) ? (int) $data['is_magic'] : true),
            'powerful' => (int) ($data['powerful'] ?? 0),
            'resolution_mode' => (string) ($data['resolution_mode'] ?? Spell::RESOLUTION_ATTACK_ROLL),
            'attack_characteristic_key' => isset($data['attack_characteristic_key']) ? (string) $data['attack_characteristic_key'] : null,
            'save_characteristic_key' => isset($data['save_characteristic_key']) ? (string) $data['save_characteristic_key'] : null,
            'save_dc_formula' => isset($data['save_dc_formula']) ? (string) $data['save_dc_formula'] : null,
            'save_success_note' => isset($data['save_success_note']) ? (string) $data['save_success_note'] : null,
            'auto_success_if_willing_target' => (bool) ($data['auto_success_if_willing_target'] ?? false),
            'created_by' => $userId,
        ];
        if ($propertyWhitelist !== []) {
            $payload = $this->filterByWhitelist($payload, $propertyWhitelist);
        }
        if ($excludeFromUpdate !== []) {
            $payload = $this->filterExcludedFromUpdate($payload, $excludeFromUpdate);
        }

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

            // Intégration des effets de sort (EffectGroup, Effects, sous-effets, usages), si présents.
            $spellEffectsPayload = $convertedData['spell_effects'] ?? null;
            if (is_array($spellEffectsPayload)) {
                $this->integrateSpellEffectsForSpell($spell, $spellEffectsPayload);
                $inferredElementMask = $this->inferSpellElementMaskFromEffectsPayload($spellEffectsPayload);
                $spell->element = $inferredElementMask;
                $spell->save();
                $inferredTypeIds = $this->inferSpellTypeIdsFromEffectsPayload($spellEffectsPayload);
                if ($inferredTypeIds !== []) {
                    $spell->spellTypes()->sync($inferredTypeIds);
                }
            } else {
                $spell->element = null;
                $spell->save();
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
     * Construit la portée du sort (po_min, po_max).
     * Accepte des valeurs numériques ou des formules (ex. "[level]", "[level]*2").
     * 0 = soi-même, 1-1 = cac, 2-6 = plage.
     *
     * @param  array<string, mixed>  $data  Données converties du sort (spells)
     * @return array{0: string, 1: string} [po_min, po_max]
     */
    private function buildSpellPoMinMax(array $data): array
    {
        $minRaw = $data['spell_po_min'] ?? $data['po'] ?? null;
        $maxRaw = $data['spell_po_max'] ?? $data['po'] ?? null;
        if ($minRaw !== null && $maxRaw !== null) {
            return [(string) $minRaw, (string) $maxRaw];
        }
        $single = trim((string) ($data['po'] ?? '1'));
        if ($single === '') {
            return ['1', '1'];
        }
        if (str_contains($single, '-')) {
            $parts = explode('-', $single, 2);
            $min = trim($parts[0]) !== '' ? trim($parts[0]) : '1';
            $max = trim($parts[1] ?? '') !== '' ? trim($parts[1]) : $min;

            return [$min, $max];
        }

        return [$single, $single];
    }

    /**
     * Intègre les effets convertis d'un sort (EffectGroup, Effects, EffectSubEffect, EffectUsage).
     * Réutilise un Effect existant si sa signature de configuration (sous-effets) est identique.
     *
     * @param array{
     *   effect_group: array{name: string, slug: string},
     *   effects: list<array{
     *     degree: int,
     *     name: string,
     *     slug: string,
     *     description: string|null,
     *     sub_effects: list<array{
     *       order: int,
     *       sub_effect_slug: string,
     *       params: array<string, mixed>,
     *       crit_only: bool
     *     }>
     *   }>
     * } $payload
     */
    private function integrateSpellEffectsForSpell(Spell $spell, array $payload): void
    {
        $groupData = $payload['effect_group'] ?? null;
        $effectsData = $payload['effects'] ?? [];
        if (! is_array($groupData) || $effectsData === []) {
            return;
        }

        $groupName = (string) ($groupData['name'] ?? $spell->name);
        $baseSlug = (string) ($groupData['slug'] ?? '');
        if ($baseSlug === '') {
            $baseSlug = 'spell-'.$spell->id;
        }

        $slugToId = $this->collectSubEffectIdsFromSpellPayload($effectsData);
        $attachEffectIds = [];
        $localDefinition = null;

        foreach ($effectsData as $effectRow) {
            if (! is_array($effectRow)) {
                continue;
            }
            $degreeNum = isset($effectRow['degree']) && is_numeric($effectRow['degree']) ? (int) $effectRow['degree'] : 1;
            $effectName = (string) ($effectRow['name'] ?? $spell->name);
            $effectSlug = (string) ($effectRow['slug'] ?? '');

            $subEffectsRaw = $effectRow['sub_effects'] ?? [];
            if (! is_array($subEffectsRaw)) {
                $subEffectsRaw = [];
            }

            $targetType = (string) ($effectRow['target_type'] ?? Effect::TARGET_DIRECT);
            $area = isset($effectRow['area']) ? (string) $effectRow['area'] : null;
            $requiredCreatureLevel = isset($effectRow['required_creature_level']) && is_numeric($effectRow['required_creature_level'])
                ? (int) $effectRow['required_creature_level']
                : null;

            $normalizedRows = $this->normalizeSubEffectsRowsForSignature($subEffectsRaw, $slugToId);
            $signature = $normalizedRows !== [] ? $this->computeEffectConfigSignature($normalizedRows, $targetType, $area) : null;

            $existingDegree = ($signature !== null) ? EffectDegree::query()->where('config_signature', $signature)->first() : null;
            if ($existingDegree !== null) {
                $attachEffectIds[] = $existingDegree->effect_id;

                continue;
            }

            if ($localDefinition === null) {
                $defSlug = $baseSlug;
                $i = 0;
                while (Effect::query()->where('slug', $defSlug)->exists()) {
                    $i++;
                    $defSlug = $baseSlug.'-'.$i;
                }
                $localDefinition = Effect::create([
                    'name' => $groupName !== '' ? $groupName : $effectName,
                    'slug' => $defSlug,
                    'description' => $effectRow['description'] ?? null,
                    'target_type' => $targetType,
                ]);
            }

            if ($effectSlug === '') {
                $effectSlug = $localDefinition->slug.'-d'.$degreeNum;
            }
            $degSlug = $this->makeUniqueEffectDegreeSlug($effectSlug);

            $degreeModel = EffectDegree::query()->firstOrCreate(
                [
                    'effect_id' => $localDefinition->id,
                    'degree' => $degreeNum,
                ],
                [
                    'required_creature_level' => $requiredCreatureLevel,
                    'area' => $area,
                    'slug' => $degSlug,
                ]
            );
            if (! $degreeModel->wasRecentlyCreated) {
                $degreeModel->update([
                    'required_creature_level' => $requiredCreatureLevel,
                    'area' => $area,
                ]);
            }

            foreach ($subEffectsRaw as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $slug = (string) ($row['sub_effect_slug'] ?? '');
                if ($slug === '' || ! isset($slugToId[$slug])) {
                    continue;
                }
                $subId = $slugToId[$slug];

                $params = is_array($row['params'] ?? null) ? $row['params'] : [];
                $critOnly = (bool) ($row['crit_only'] ?? false);
                $order = isset($row['order']) && is_numeric($row['order']) ? (int) $row['order'] : 0;

                $condition = $this->integrateConditionFromParams($spell, $slug, $params);
                if ($condition !== null) {
                    $params['condition_id'] = $condition->id;
                    $params['condition_dofusdb_id'] = $condition->dofusdb_id;
                    if (! isset($params['condition_name']) || trim((string) $params['condition_name']) === '') {
                        $params['condition_name'] = $condition->name;
                    }
                }

                $alreadyExists = $degreeModel->effectSubEffects()
                    ->where($this->effectSubEffectDedupWhere($subId, $critOnly, $params))
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                $degreeModel->effectSubEffects()->create([
                    'sub_effect_id' => $subId,
                    'order' => $order,
                    'scope' => Effect::SCOPE_GENERAL,
                    'params' => $params,
                    'crit_only' => $critOnly,
                ]);
            }

            $degreeModel->load(['effectSubEffects', 'effect']);
            $newSignature = $this->rebuildConfigSignatureForEffectDegree($degreeModel);
            if ($newSignature !== null) {
                $degreeModel->update(['config_signature' => $newSignature]);
            }
        }

        if ($localDefinition !== null) {
            $attachEffectIds[] = $localDefinition->id;
        }
        $attachEffectIds = array_values(array_unique(array_filter($attachEffectIds)));
        if ($attachEffectIds !== []) {
            $spell->effects()->syncWithoutDetaching($attachEffectIds);
        }
    }

    private function makeUniqueEffectDegreeSlug(string $preferred): ?string
    {
        $preferred = trim($preferred);
        if ($preferred === '') {
            return null;
        }
        $slug = $preferred;
        $n = 0;
        while (EffectDegree::query()->where('slug', $slug)->exists()) {
            $n++;
            $slug = $preferred.'-'.$n;
        }

        return $slug;
    }

    /**
     * Simule la création des effets d'un sort sans écrire en base.
     * Retourne pour chaque effet du payload : action (create|reuse), existing_effect_id si réutilisation.
     *
     * @param  array{effect_group: array{name?: string, slug?: string}, effects: list<array{degree?: int, name?: string, slug?: string, target_type?: string, area?: string, sub_effects?: list}>}  $payload
     * @return list<array{index: int, degree: int, name: string, slug: string, target_type: string, area: string|null, sub_effects_count: int, action: 'create'|'reuse', existing_effect_id: int|null}>
     */
    public function simulateSpellEffects(array $payload): array
    {
        $effectsData = $payload['effects'] ?? [];
        if (! is_array($effectsData) || $effectsData === []) {
            return [];
        }

        $slugToId = $this->collectSubEffectIdsFromSpellPayload($effectsData);
        $plan = [];
        $index = 0;

        foreach ($effectsData as $effectRow) {
            if (! is_array($effectRow)) {
                continue;
            }
            $degree = isset($effectRow['degree']) && is_numeric($effectRow['degree']) ? (int) $effectRow['degree'] : 1;
            $name = (string) ($effectRow['name'] ?? '');
            $slug = (string) ($effectRow['slug'] ?? '');
            $targetType = (string) ($effectRow['target_type'] ?? Effect::TARGET_DIRECT);
            $area = isset($effectRow['area']) ? (string) $effectRow['area'] : null;
            $subEffectsRaw = $effectRow['sub_effects'] ?? [];
            $subEffectsCount = is_array($subEffectsRaw) ? count($subEffectsRaw) : 0;

            $normalizedRows = $this->normalizeSubEffectsRowsForSignature(is_array($subEffectsRaw) ? $subEffectsRaw : [], $slugToId);
            $signature = $normalizedRows !== [] ? $this->computeEffectConfigSignature($normalizedRows, $targetType, $area) : null;

            $action = 'create';
            $existingEffectId = null;
            if ($signature !== null) {
                $existing = EffectDegree::query()->where('config_signature', $signature)->first();
                if ($existing !== null) {
                    $action = 'reuse';
                    $existingEffectId = $existing->effect_id;
                }
            }

            $plan[] = [
                'index' => $index,
                'degree' => $degree,
                'name' => $name,
                'slug' => $slug,
                'target_type' => $targetType,
                'area' => $area,
                'sub_effects_count' => $subEffectsCount,
                'action' => $action,
                'existing_effect_id' => $existingEffectId,
            ];
            $index++;
        }

        return $plan;
    }

    /**
     * Collecte tous les slugs de sous-effets présents dans le payload et retourne slug => id.
     *
     * @param  list<array{sub_effects?: list<array{sub_effect_slug?: string}>}>  $effectsData
     * @return array<string, int>
     */
    private function collectSubEffectIdsFromSpellPayload(array $effectsData): array
    {
        $slugs = [];
        foreach ($effectsData as $effectRow) {
            if (! is_array($effectRow)) {
                continue;
            }
            foreach ($effectRow['sub_effects'] ?? [] as $row) {
                if (is_array($row) && ! empty($row['sub_effect_slug'])) {
                    $slugs[(string) $row['sub_effect_slug']] = true;
                }
            }
        }
        if ($slugs === []) {
            return [];
        }
        $wantedSlugs = array_keys($slugs);
        $slugToId = SubEffect::whereIn('slug', $wantedSlugs)->pluck('id', 'slug')->all();
        $missingSlugs = array_values(array_diff($wantedSlugs, array_keys($slugToId)));

        if ($missingSlugs !== []) {
            foreach ($missingSlugs as $slug) {
                SubEffect::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'type_slug' => $slug,
                        'template_text' => 'Effet '.$slug.'.',
                        'variables_allowed' => [],
                        'param_schema' => [
                            'action' => $slug,
                            'params' => [],
                        ],
                    ]
                );
            }
            Log::warning('Sub-effects manquants auto-créés pendant import de sort.', [
                'missing_slugs' => $missingSlugs,
            ]);
            $slugToId = SubEffect::whereIn('slug', $wantedSlugs)->pluck('id', 'slug')->all();
        }

        return $slugToId;
    }

    /**
     * Normalise les lignes sous-effets pour le calcul de signature : résolution slug → id, déduplication.
     *
     * @param  list<array{order?: int, sub_effect_slug?: string, params?: array, crit_only?: bool}>  $rows
     * @param  array<string, int>  $slugToId
     * @return list<array{order: int, sub_effect_id: int, crit_only: bool, characteristic: mixed, value_formula: mixed, value_formula_crit: mixed, value: mixed, condition_dofusdb_id: mixed}>
     */
    private function normalizeSubEffectsRowsForSignature(array $rows, array $slugToId): array
    {
        $seen = [];
        $out = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $slug = (string) ($row['sub_effect_slug'] ?? '');
            if ($slug === '' || ! isset($slugToId[$slug])) {
                continue;
            }
            $params = is_array($row['params'] ?? null) ? $row['params'] : [];
            $critOnly = (bool) ($row['crit_only'] ?? false);
            $order = isset($row['order']) && is_numeric($row['order']) ? (int) $row['order'] : 0;
            $char = $params['characteristic'] ?? null;
            $valueFormula = $params['value_formula'] ?? null;
            $valueFormulaCrit = $params['value_formula_crit'] ?? null;
            $value = $params['value'] ?? null;
            $stateDofusdbId = $params['condition_dofusdb_id'] ?? null;

            $dedupKey = $this->effectSubEffectDedupKey($slugToId[$slug], $critOnly, $params);
            if (isset($seen[$dedupKey])) {
                continue;
            }
            $seen[$dedupKey] = true;
            $out[] = [
                'order' => $order,
                'sub_effect_id' => $slugToId[$slug],
                'crit_only' => $critOnly,
                'characteristic' => $char,
                'value_formula' => $valueFormula,
                'value_formula_crit' => $valueFormulaCrit,
                'value' => $value,
                'condition_dofusdb_id' => $stateDofusdbId,
            ];
        }
        usort($out, static fn (array $a, array $b) => $a['order'] <=> $b['order']);

        return $out;
    }

    /**
     * Calcule une signature (hash) pour réutiliser un Effect existant.
     * Inclut target_type et area pour éviter de fusionner des effets directs/piège/glyphe.
     *
     * @param  list<array{order: int, sub_effect_id: int, crit_only: bool, characteristic: mixed, value_formula: mixed, value_formula_crit: mixed, value?: mixed, condition_dofusdb_id?: mixed}>  $normalizedRows
     */
    private function computeEffectConfigSignature(array $normalizedRows, string $targetType = Effect::TARGET_DIRECT, ?string $area = null): string
    {
        $parts = [];
        foreach ($normalizedRows as $r) {
            $parts[] = json_encode([
                'o' => $r['order'],
                's' => $r['sub_effect_id'],
                'c' => $r['crit_only'],
                'char' => $r['characteristic'] ?? null,
                'v' => $r['value_formula'] ?? null,
                'vcrit' => $r['value_formula_crit'] ?? null,
                'val' => $r['value'] ?? null,
                'condition_dofusdb_id' => $r['condition_dofusdb_id'] ?? null,
            ], JSON_UNESCAPED_UNICODE);
        }
        $parts[] = 't:'.$targetType;
        $parts[] = 'a:'.($area ?? '');

        return hash('sha256', implode("\n", $parts));
    }

    /**
     * Recalcule la config_signature d’un degré d’effet (target_type sur la définition, area sur le degré).
     */
    public function rebuildConfigSignatureForEffectDegree(EffectDegree $degree): ?string
    {
        $degree->loadMissing(['effectSubEffects', 'effect']);
        $rows = [];
        foreach ($degree->effectSubEffects as $ese) {
            $params = is_array($ese->params) ? $ese->params : [];
            $rows[] = [
                'order' => $ese->order,
                'sub_effect_id' => $ese->sub_effect_id,
                'crit_only' => (bool) $ese->crit_only,
                'characteristic' => $params['characteristic'] ?? null,
                'value_formula' => $params['value_formula'] ?? null,
                'value_formula_crit' => $params['value_formula_crit'] ?? null,
                'value' => $params['value'] ?? null,
                'condition_dofusdb_id' => $params['condition_dofusdb_id'] ?? null,
            ];
        }
        usort($rows, static fn (array $a, array $b) => $a['order'] <=> $b['order']);
        if ($rows === []) {
            return null;
        }

        $targetType = $degree->effect?->target_type ?? Effect::TARGET_DIRECT;

        return $this->computeEffectConfigSignature(
            $rows,
            $targetType,
            $degree->area
        );
    }

    /**
     * Clé de déduplication pour un pivot sous-effet (même action + params = même ligne).
     */
    private function effectSubEffectDedupKey(int $subEffectId, bool $critOnly, array $params): string
    {
        return $subEffectId.'|'.($critOnly ? '1' : '0').'|'
            .($params['characteristic'] ?? '').'|'.($params['value_formula'] ?? '').'|'
            .($params['value_formula_crit'] ?? '').'|'.($params['value'] ?? '').'|'
            .($params['condition_id'] ?? '').'|'.($params['condition_dofusdb_id'] ?? '');
    }

    /**
     * Conditions where pour vérifier l'existence d'un pivot identique (déduplication).
     *
     * @return array<string, mixed>
     */
    private function effectSubEffectDedupWhere(int $subEffectId, bool $critOnly, array $params): array
    {
        $where = [
            'sub_effect_id' => $subEffectId,
            'crit_only' => $critOnly,
            'params->characteristic' => $params['characteristic'] ?? null,
            'params->value_formula' => $params['value_formula'] ?? null,
            'params->value_formula_crit' => $params['value_formula_crit'] ?? null,
        ];
        if (array_key_exists('value', $params)) {
            $where['params->value'] = $params['value'];
        }
        if (array_key_exists('condition_dofusdb_id', $params)) {
            $where['params->condition_dofusdb_id'] = $params['condition_dofusdb_id'];
        }
        if (array_key_exists('condition_id', $params)) {
            $where['params->condition_id'] = $params['condition_id'];
        }

        return $where;
    }

    /**
     * Enregistre l'état DofusDB lié à un sous-effet de sort et le relie au sort.
     *
     * @param  array<string, mixed>  $params
     */
    private function integrateConditionFromParams(Spell $spell, string $subEffectSlug, array $params): ?Condition
    {
        if (! in_array($subEffectSlug, ['appliquer-etat', 's-appliquer-etat'], true)) {
            return null;
        }
        if (! isset($params['condition_dofusdb_id']) || ! is_numeric($params['condition_dofusdb_id'])) {
            return null;
        }

        $stateDofusdbId = (int) $params['condition_dofusdb_id'];
        if ($stateDofusdbId <= 0) {
            return null;
        }

        $flags = is_array($params['condition_flags'] ?? null) ? $params['condition_flags'] : [];
        $conditionName = isset($params['condition_name']) && is_string($params['condition_name']) && trim($params['condition_name']) !== ''
            ? trim($params['condition_name'])
            : 'Condition DofusDB #'.$stateDofusdbId;

        $condition = Condition::query()->updateOrCreate(
            ['dofusdb_id' => $stateDofusdbId],
            [
                'name' => $conditionName,
                'icon' => isset($params['condition_icon']) && is_string($params['condition_icon']) ? $params['condition_icon'] : null,
                'image' => isset($params['condition_image']) && is_string($params['condition_image']) ? $params['condition_image'] : null,
                'prevents_spell_cast' => (bool) data_get($flags, 'prevents_spell_cast', false),
                'prevents_fight' => (bool) data_get($flags, 'prevents_fight', false),
                'cant_be_moved' => (bool) data_get($flags, 'cant_be_moved', false),
                'cant_be_pushed' => (bool) data_get($flags, 'cant_be_pushed', false),
                'cant_deal_damage' => (bool) data_get($flags, 'cant_deal_damage', false),
                'invulnerable' => (bool) data_get($flags, 'invulnerable', false),
                'cant_switch_position' => (bool) data_get($flags, 'cant_switch_position', false),
                'incurable' => (bool) data_get($flags, 'incurable', false),
                'invulnerable_melee' => (bool) data_get($flags, 'invulnerable_melee', false),
                'invulnerable_range' => (bool) data_get($flags, 'invulnerable_range', false),
                'cant_tackle' => (bool) data_get($flags, 'cant_tackle', false),
                'cant_be_tackled' => (bool) data_get($flags, 'cant_be_tackled', false),
                'display_turn_remaining' => (bool) data_get($flags, 'display_turn_remaining', false),
                'is_main_state' => (bool) data_get($flags, 'is_main_state', false),
                'raw' => $flags !== [] ? $flags : null,
            ]
        );

        $applicationMode = $subEffectSlug === 's-appliquer-etat' ? 'self' : 'target';
        $dofusEffectId = isset($params['dofus_effect_id']) && is_numeric($params['dofus_effect_id'])
            ? (int) $params['dofus_effect_id']
            : null;

        DB::table('condition_spell')->updateOrInsert(
            [
                'spell_id' => $spell->id,
                'condition_id' => $condition->id,
                'application_mode' => $applicationMode,
                'dofus_effect_id' => $dofusEffectId,
            ],
            [
                'duration' => isset($params['duration']) && is_numeric($params['duration']) ? (int) $params['duration'] : null,
                'dispellable' => array_key_exists('dispellable', $params) ? (bool) $params['dispellable'] : null,
                'target_mask' => isset($params['target_mask']) && is_string($params['target_mask']) ? $params['target_mask'] : null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return $condition;
    }

    /**
     * Masque élémentaire du sort : uniquement params.dofus_element_id sur chaque sous-effet
     * (conversion : spell-level effectElement DofusDB, 0–4). Pas d’inférence depuis characteristic.
     *
     * @param  array{
     *   effects?: list<array{sub_effects?: list<array{params?: array<string, mixed>}>}>
     * }  $payload
     */
    private function inferSpellElementMaskFromEffectsPayload(array $payload): ?int
    {
        $effects = $payload['effects'] ?? [];
        if (! is_array($effects) || $effects === []) {
            return null;
        }

        $primaries = [];
        foreach ($effects as $effect) {
            if (! is_array($effect)) {
                continue;
            }
            $subEffects = $effect['sub_effects'] ?? [];
            if (! is_array($subEffects)) {
                continue;
            }
            foreach ($subEffects as $subEffect) {
                if (! is_array($subEffect)) {
                    continue;
                }
                $params = is_array($subEffect['params'] ?? null) ? $subEffect['params'] : [];
                $dofusEl = $params['dofus_element_id'] ?? null;
                if (! is_numeric($dofusEl)) {
                    continue;
                }
                $el = (int) $dofusEl;
                if ($el < 0 || $el > 4) {
                    continue;
                }
                $p = DofusDbElementId::toKrosmozElementPrimaryIndex($el);
                if ($p !== null && $p >= 0 && $p <= 4) {
                    $primaries[$p] = true;
                }
            }
        }

        if ($primaries === []) {
            return null;
        }

        return ElementBitmask::fromPrimaries(array_map('intval', array_keys($primaries)));
    }

    /**
     * Déduit les types de sort à partir des actions de sous-effets.
     *
     * @param  array{
     *   effects?: list<array{sub_effects?: list<array{sub_effect_slug?: string, params?: array<string, mixed>}>}>
     * }  $payload
     * @return list<int>
     */
    private function inferSpellTypeIdsFromEffectsPayload(array $payload): array
    {
        $effects = $payload['effects'] ?? [];
        if (! is_array($effects) || $effects === []) {
            return [];
        }

        $concepts = [];
        foreach ($effects as $effect) {
            if (! is_array($effect)) {
                continue;
            }
            $subEffects = $effect['sub_effects'] ?? [];
            if (! is_array($subEffects)) {
                continue;
            }
            foreach ($subEffects as $subEffect) {
                if (! is_array($subEffect)) {
                    continue;
                }
                $slug = $this->normalizeTextKey((string) ($subEffect['sub_effect_slug'] ?? ''));
                $params = is_array($subEffect['params'] ?? null) ? $subEffect['params'] : [];
                $valueText = $this->normalizeTextKey((string) ($params['value'] ?? ''));

                if ($slug === 'frapper' || str_contains($slug, 'attaque')) {
                    $concepts['degats'] = true;
                }
                if ($slug === 'soigner') {
                    $concepts['soin'] = true;
                }
                if ($slug === 'proteger') {
                    $concepts['protection'] = true;
                    $concepts['tank'] = true;
                }
                if ($slug === 'invoquer') {
                    $concepts['invocation'] = true;
                }
                if ($slug === 'booster') {
                    $valueFormula = isset($params['value_formula']) ? trim((string) $params['value_formula']) : '';
                    if ($valueFormula !== '' && str_starts_with($valueFormula, '-')) {
                        $concepts['entrave'] = true;
                    } else {
                        $concepts['amelioration'] = true;
                    }
                }
                if ($slug === 'retirer' || $slug === 'voler-caracteristiques') {
                    $concepts['entrave'] = true;
                }
                if ($slug === 'deplacer' || str_contains($slug, 'teleport') || str_contains($slug, 'position')) {
                    $concepts['placement'] = true;
                }

                if ($slug === 'autre' && $valueText !== '') {
                    if (preg_match('/\b(invoque|invocation)\b/u', $valueText) === 1) {
                        $concepts['invocation'] = true;
                    }
                    if (preg_match('/\b(soin|soigne)\b/u', $valueText) === 1) {
                        $concepts['soin'] = true;
                    }
                    if (preg_match('/\b(dommage|dommages|degat|degats|frappe)\b/u', $valueText) === 1) {
                        $concepts['degats'] = true;
                    }
                    if (preg_match('/\b(bouclier|protection|armure)\b/u', $valueText) === 1) {
                        $concepts['protection'] = true;
                        $concepts['tank'] = true;
                    }
                    if (preg_match('/\b(booste|augmente|bonus)\b/u', $valueText) === 1) {
                        $concepts['amelioration'] = true;
                    }
                    if (preg_match('/\b(retire|retrait|vole|malus)\b/u', $valueText) === 1) {
                        $concepts['entrave'] = true;
                    }
                    if (preg_match('/\b(deplace|attire|repousse|teleporte|position)\b/u', $valueText) === 1) {
                        $concepts['placement'] = true;
                    }
                }
            }
        }

        if ($concepts === []) {
            return [];
        }

        $byName = SpellType::query()->pluck('id', 'name')->all();
        if ($byName === []) {
            return [];
        }

        $normalizedToId = [];
        foreach ($byName as $name => $id) {
            $normalizedToId[$this->normalizeTextKey((string) $name)] = (int) $id;
        }

        $conceptToCandidates = [
            'invocation' => ['Invocation'],
            'degats' => ['Dégâts', 'Degats', 'Offensif'],
            'soin' => ['Soin'],
            'protection' => ['Protection', 'Défensif', 'Defensif'],
            'tank' => ['Tank', 'Défensif', 'Defensif'],
            'amelioration' => ['Amélioration', 'Amelioration', 'Buff'],
            'entrave' => ['Entrave', 'Débuff', 'Debuff'],
            'placement' => ['Placement', 'Téléportation', 'Teleportation'],
        ];

        $ids = [];
        foreach (array_keys($concepts) as $concept) {
            $candidates = $conceptToCandidates[$concept] ?? [];
            foreach ($candidates as $candidate) {
                $norm = $this->normalizeTextKey($candidate);
                if (isset($normalizedToId[$norm])) {
                    $ids[$normalizedToId[$norm]] = true;
                    break;
                }
            }
        }

        return array_map('intval', array_keys($ids));
    }

    private function normalizeTextKey(string $value): string
    {
        $v = trim(mb_strtolower($value));
        if ($v === '') {
            return '';
        }

        $v = str_replace(
            ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç', '’', '\''],
            ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c', '-', '-'],
            $v
        );
        $v = preg_replace('/[^a-z0-9\-_\s]/', '', $v) ?? $v;
        $v = preg_replace('/\s+/', '-', $v) ?? $v;

        return trim($v, '-');
    }

    /**
     * @param  array{dry_run?: bool, force_update?: bool, replace_mode?: string, respect_auto_update?: bool, exclude_from_update?: list<string>, property_whitelist?: list<string>}  $options
     */
    private function integrateBreed(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);
        $replaceMode = isset($options['replace_mode']) ? (string) $options['replace_mode'] : null;
        $respectAutoUpdate = (bool) ($options['respect_auto_update'] ?? true);
        $excludeFromUpdate = is_array($options['exclude_from_update'] ?? null) ? $options['exclude_from_update'] : [];
        $propertyWhitelist = is_array($options['property_whitelist'] ?? null) ? $options['property_whitelist'] : [];

        $data = $convertedData['breeds'] ?? $convertedData['classes'] ?? [];
        if ($data === []) {
            return IntegrationResult::fail('Données converties incomplètes (breeds manquant).');
        }

        $existingBreed = null;
        if (! empty($data['dofusdb_id'])) {
            $existingBreed = Breed::where('dofusdb_id', (string) $data['dofusdb_id'])->first();
        }
        if (! $existingBreed && ! empty($data['name'])) {
            $existingBreed = Breed::where('name', $data['name'])->first();
        }

        $doReplace = $this->wouldReplaceExisting($forceUpdate, $replaceMode, $existingBreed, null, $respectAutoUpdate);
        if ($existingBreed && ! $doReplace) {
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
            'name' => $this->localizedToString($data['name'] ?? null),
            'description' => $this->localizedToString($data['description'] ?? null),
            'description_fast' => $data['description_fast'] ?? null,
            'life_dice' => (string) ($data['life_dice'] ?? ''),
            'specificity' => $data['specificity'] ?? null,
            'created_by' => $userId,
        ];
        if ($propertyWhitelist !== []) {
            $payload = $this->filterByWhitelist($payload, $propertyWhitelist);
        }
        if ($excludeFromUpdate !== []) {
            $payload = $this->filterExcludedFromUpdate($payload, $excludeFromUpdate);
        }

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
     * @param  array{dry_run?: bool, force_update?: bool, replace_mode?: string, respect_auto_update?: bool, include_relations?: bool, exclude_from_update?: list<string>, property_whitelist?: list<string>}  $options
     */
    private function integrateItem(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);
        $replaceMode = isset($options['replace_mode']) ? (string) $options['replace_mode'] : null;
        $respectAutoUpdate = (bool) ($options['respect_auto_update'] ?? true);
        $excludeFromUpdate = is_array($options['exclude_from_update'] ?? null) ? $options['exclude_from_update'] : [];
        $propertyWhitelist = is_array($options['property_whitelist'] ?? null) ? $options['property_whitelist'] : [];

        $targetTable = $this->getItemTargetTable($convertedData);
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
        if (! $existing && ! empty($data['name'])) {
            $existing = match ($targetTable) {
                'items' => Item::where('name', $data['name'])->first(),
                'consumables' => Consumable::where('name', $data['name'])->first(),
                'resources' => Resource::where('name', $data['name'])->first(),
                default => Item::where('name', $data['name'])->first(),
            };
        }

        $doReplace = $this->wouldReplaceExisting($forceUpdate, $replaceMode, $existing, null, $respectAutoUpdate);
        if ($existing && ! $doReplace) {
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
            'rarity' => $rarity,
            'created_by' => $userId,
        ];
        if ($targetTable === 'items') {
            $rawPrice = $data['price'] ?? null;
            $payload['price_custom'] = $rawPrice !== null && $rawPrice !== ''
                ? (int) (is_numeric($rawPrice) ? $rawPrice : preg_replace('/\D/', '', (string) $rawPrice))
                : null;
        } else {
            $payload['price'] = $data['price'] !== null ? (string) $data['price'] : null;
        }
        if (in_array($targetTable, ['items', 'resources', 'consumables'], true)) {
            $effectRaw = $data['effect'] ?? null;
            $payload['effect'] = is_string($effectRaw) ? $effectRaw : (is_array($effectRaw) ? json_encode($effectRaw, JSON_UNESCAPED_UNICODE) : null);
        }
        if ($targetTable === 'items') {
            $bonusRaw = $data['bonus'] ?? null;
            $payload['bonus'] = is_string($bonusRaw) ? $bonusRaw : (is_array($bonusRaw) ? json_encode($bonusRaw, JSON_UNESCAPED_UNICODE) : null);
        }
        if ($propertyWhitelist !== []) {
            $payload = $this->filterByWhitelist($payload, $propertyWhitelist);
        }
        if ($excludeFromUpdate !== []) {
            $payload = $this->filterExcludedFromUpdate($payload, $excludeFromUpdate);
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
                if (isset($data['consumable_type_id']) && $data['consumable_type_id'] !== null) {
                    $payload['consumable_type_id'] = (int) $data['consumable_type_id'];
                }
                if ($existing instanceof Consumable) {
                    $existing->update($payload);
                    $entity = $existing;
                } else {
                    $entity = Consumable::create($payload);
                }
            } else {
                if (isset($data['item_type_id']) && $data['item_type_id'] !== null) {
                    $payload['item_type_id'] = (int) $data['item_type_id'];
                }
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
     * @param  list<array{ingredient_dofusdb_id: string, quantity: int}>  $recipeIngredients
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
     * Détermine la table cible (resources, consumables, items) à partir des données brutes (item).
     * Permet de ne convertir que le bloc cible (performance + affichage ciblé).
     */
    public function getItemTargetTableFromRaw(array $raw): string
    {
        $typeId = isset($raw['typeId']) ? (int) $raw['typeId'] : null;
        if ($typeId === null && isset($raw['type']) && is_array($raw['type']) && isset($raw['type']['id'])) {
            $typeId = (int) $raw['type']['id'];
        }

        return $this->resolveItemTargetTable($typeId, $raw);
    }

    /**
     * Détermine la table cible (resources, consumables, items) à partir des données converties.
     * Utilisé par l'orchestrateur pour la validation et par integrateItem.
     */
    public function getItemTargetTable(array $convertedData): string
    {
        if (isset($convertedData['resources']) && is_array($convertedData['resources']) && $convertedData['resources'] !== []
            && (! isset($convertedData['consumables']) || ! is_array($convertedData['consumables']) || $convertedData['consumables'] === [])
            && (! isset($convertedData['items']) || ! is_array($convertedData['items']) || $convertedData['items'] === [])) {
            return 'resources';
        }
        if (isset($convertedData['consumables']) && is_array($convertedData['consumables']) && $convertedData['consumables'] !== []
            && (! isset($convertedData['resources']) || ! is_array($convertedData['resources']) || $convertedData['resources'] === [])
            && (! isset($convertedData['items']) || ! is_array($convertedData['items']) || $convertedData['items'] === [])) {
            return 'consumables';
        }
        if (isset($convertedData['items']) && is_array($convertedData['items']) && $convertedData['items'] !== []
            && (! isset($convertedData['resources']) || ! is_array($convertedData['resources']) || $convertedData['resources'] === [])
            && (! isset($convertedData['consumables']) || ! is_array($convertedData['consumables']) || $convertedData['consumables'] === [])) {
            $typeId = isset($convertedData['items']['type_id']) ? (int) $convertedData['items']['type_id'] : null;

            return $this->resolveItemTargetTable($typeId);
        }

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
     * Détermine la table cible (items, consumables, resources) à partir du typeId DofusDB.
     *
     * Ordre volontaire :
     * 1. Catalogue DofusDB (superType du typeId) + item-super-types.json — aligné sur resolveResourceTypeId /
     *    resolveConsumableTypeId / resolveItemTypeId. Doit primer sur les registries : un typeId présent à tort
     *    dans resource_types ne doit pas forcer targetModel=resources (sinon category ≠ resource → type FK null).
     * 2. Registries Krosmoz (consumable_types, resource_types, item_types) si superType non mappé ou catalogue absent.
     *
     * @param  array<string, mixed>|null  $itemRaw  Réponse brute item (optionnel) pour inférer superTypeId si le catalogue ne connaît pas le typeId
     */
    private function resolveItemTargetTable(?int $typeId, ?array $itemRaw = null): string
    {
        if ($typeId === null || $typeId <= 0) {
            return 'items';
        }

        $superTypeId = $this->itemTypesCatalog?->getSuperTypeIdForTypeId($typeId, 'fr', false);
        if ($superTypeId === null && $this->itemTypesCatalog !== null && $itemRaw !== null) {
            $superTypeId = $this->itemTypesCatalog->inferSuperTypeIdFromItemRaw($itemRaw);
        }
        if ($superTypeId !== null && $this->superTypeMapping !== null) {
            $category = $this->superTypeMapping->getCategoryForSuperTypeId($superTypeId);
            if ($category !== null) {
                return match ($category) {
                    'resource' => 'resources',
                    'consumable' => 'consumables',
                    'equipment' => 'items',
                    default => 'items',
                };
            }
        }

        if (ConsumableType::where('dofusdb_type_id', $typeId)->exists()) {
            return 'consumables';
        }
        if (ResourceType::where('dofusdb_type_id', $typeId)->exists()) {
            return 'resources';
        }
        if (ItemType::where('dofusdb_type_id', $typeId)->exists()) {
            return 'items';
        }

        return 'items';
    }

    /**
     * @param  array{dry_run?: bool, force_update?: bool, replace_mode?: string, respect_auto_update?: bool, exclude_from_update?: list<string>, property_whitelist?: list<string>}  $options
     */
    private function integratePanoply(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);
        $replaceMode = isset($options['replace_mode']) ? (string) $options['replace_mode'] : null;
        $respectAutoUpdate = (bool) ($options['respect_auto_update'] ?? true);
        $excludeFromUpdate = is_array($options['exclude_from_update'] ?? null) ? $options['exclude_from_update'] : [];
        $propertyWhitelist = is_array($options['property_whitelist'] ?? null) ? $options['property_whitelist'] : [];

        $data = $convertedData['panoplies'] ?? [];
        if ($data === []) {
            return IntegrationResult::fail('Données converties incomplètes (panoplies manquant).');
        }

        $existingPanoply = null;
        $dofusdbId = isset($data['dofusdb_id']) ? (string) $data['dofusdb_id'] : null;
        if ($dofusdbId !== null && $dofusdbId !== '') {
            $existingPanoply = Panoply::where('dofusdb_id', $dofusdbId)->first();
        }
        if (! $existingPanoply && ! empty($data['name'])) {
            $existingPanoply = Panoply::where('name', $data['name'])->first();
        }

        $doReplace = $this->wouldReplaceExisting($forceUpdate, $replaceMode, $existingPanoply, null, $respectAutoUpdate);
        if ($existingPanoply && ! $doReplace) {
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
        if ($propertyWhitelist !== []) {
            $payload = $this->filterByWhitelist($payload, $propertyWhitelist);
        }
        if ($excludeFromUpdate !== []) {
            $payload = $this->filterExcludedFromUpdate($payload, $excludeFromUpdate);
        }

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
     * @param  string  $entityType  monster, spell, breed, class, item, panoply
     * @param  array<string, array<string, mixed>>  $convertedData  Structure par modèle (creatures, monsters, spells, …)
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
            if (! empty($monsterData['dofusdb_id'])) {
                $existingMonster = Monster::where('dofusdb_id', (string) $monsterData['dofusdb_id'])->first();
            }
            if (! $existingMonster && ! empty($creatureData['name'])) {
                $existingCreature = Creature::where('name', (string) $creatureData['name'])->first();
                $existingMonster = $existingCreature?->monster;
            }
            if (! $existingMonster) {
                return null;
            }
            $c = $existingMonster->creature;
            $sizeMap = [0 => 'tiny', 1 => 'small', 2 => 'medium', 3 => 'large', 4 => 'huge'];
            $sizeInt = $existingMonster->size ?? 2;
            $sizeStr = $sizeMap[$sizeInt] ?? 'medium';

            return array_merge(
                [
                    'id' => $existingMonster->id,
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
                    'res_sagesse' => $c?->res_sagesse,
                    'res_vitalite' => $c?->res_vitalite,
                    'do_sagesse' => $c?->do_sagesse,
                    'do_vitalite' => $c?->do_vitalite,
                    'ini' => $c?->ini,
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
            if (! $model) {
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
            $table = $this->getItemTargetTable($convertedData);
            $data = $convertedData[$table] ?? [];
            if ($data === []) {
                return null;
            }
            $dofusdbId = (string) ($data['dofusdb_id'] ?? '');
            $name = $data['name'] ?? '';
            $model = match ($table) {
                'resources' => Resource::where('dofusdb_id', $dofusdbId)->orWhere('name', $name)->first(),
                'consumables' => Consumable::where('dofusdb_id', $dofusdbId)->orWhere('name', $name)->first(),
                default => Item::where('dofusdb_id', $dofusdbId)->orWhere('name', $name)->first(),
            };
            if (! $model) {
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

    /**
     * Retourne le type d'entité UI (resource, consumable, equipment) pour un typeId DofusDB.
     * Utilisé pour l'affichage et la comparaison des relations « item » (recettes, drops).
     */
    public function resolveItemEntityType(?int $typeId): string
    {
        $table = $this->resolveItemTargetTable($typeId);

        return match ($table) {
            'resources' => 'resource',
            'consumables' => 'consumable',
            default => 'equipment',
        };
    }

    private function getSystemUserId(): int
    {
        $authUser = Auth::user();
        if ($authUser !== null) {
            return (int) $authUser->id;
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

    /**
     * Convertit un champ potentiellement localisé ({fr,en,...}) vers une chaîne.
     */
    private function localizedToString(mixed $value): string
    {
        if (is_string($value)) {
            return trim($value);
        }
        if (! is_array($value)) {
            return '';
        }
        foreach (['fr', 'en'] as $lang) {
            if (isset($value[$lang]) && is_string($value[$lang])) {
                return trim($value[$lang]);
            }
        }
        $first = reset($value);

        return is_string($first) ? trim($first) : '';
    }
}
