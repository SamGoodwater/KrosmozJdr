<?php

namespace App\Services\Scrapping\Core\Integration;

use App\Models\Entity\Breed;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Effect;
use App\Models\EffectGroup;
use App\Models\EffectSubEffect;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Models\EffectUsage;
use App\Models\SubEffect;
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
     * Calcule si on doit remplacer un enregistrement existant selon replace_mode ou force_update.
     *
     * @param bool $forceUpdate Valeur legacy force_update
     * @param string|null $replaceMode 'never' | 'draft_raw_only' | 'always'
     * @param Creature|Item|Resource|Consumable|Spell|Breed|Panoply|null $existing Entité existante (avec state)
     */
    private function shouldReplaceExisting(bool $forceUpdate, ?string $replaceMode, $existing): bool
    {
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
     * @param array{dry_run?: bool, force_update?: bool, replace_mode?: string, ignore_unvalidated?: bool, exclude_from_update?: list<string>, property_whitelist?: list<string>} $options
     */
    private function integrateMonster(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);
        $replaceMode = isset($options['replace_mode']) ? (string) $options['replace_mode'] : null;
        $ignoreUnvalidated = (bool) ($options['ignore_unvalidated'] ?? false);
        /** @var list<string> $excludeFromUpdate */
        $excludeFromUpdate = $options['exclude_from_update'] ?? [];
        if (!is_array($excludeFromUpdate)) {
            $excludeFromUpdate = [];
        }
        /** @var list<string> $propertyWhitelist */
        $propertyWhitelist = $options['property_whitelist'] ?? [];
        if (!is_array($propertyWhitelist)) {
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
        if (!empty($monsterData['dofusdb_id'])) {
            $existingMonsterByDofus = Monster::where('dofusdb_id', (string) $monsterData['dofusdb_id'])->first();
        }
        $existingCreature = $existingMonsterByDofus?->creature ?? Creature::where('name', (string) ($creatureData['name'] ?? ''))->first();

        $doReplace = $this->shouldReplaceExisting($forceUpdate, $replaceMode, $existingCreature);
        if ($existingMonsterByDofus && !$doReplace) {
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

        $optional = ['pa', 'pm', 'kamas', 'po', 'dodge_pa', 'dodge_pm', 'ini', 'vitality', 'res_neutre', 'res_terre', 'res_feu', 'res_air', 'res_eau'];
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
     * Restreint les clés au whitelist si non vide.
     *
     * @param array<string, mixed> $data
     * @param list<string> $whitelist
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
     * @param array{dry_run?: bool, force_update?: bool, replace_mode?: string, exclude_from_update?: list<string>, property_whitelist?: list<string>} $options
     */
    private function integrateSpell(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);
        $replaceMode = isset($options['replace_mode']) ? (string) $options['replace_mode'] : null;
        $excludeFromUpdate = is_array($options['exclude_from_update'] ?? null) ? $options['exclude_from_update'] : [];
        $propertyWhitelist = is_array($options['property_whitelist'] ?? null) ? $options['property_whitelist'] : [];

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

        $doReplace = $this->shouldReplaceExisting($forceUpdate, $replaceMode, $existingSpell);
        if ($existingSpell && !$doReplace) {
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
            'name' => (string) ($data['name'] ?? ''),
            'description' => (string) ($data['description'] ?? ''),
            'pa' => (string) ($data['pa'] ?? '3'),
            'po_min' => $poMin,
            'po_max' => $poMax,
            'po_editable' => (bool) (isset($data['po_editable']) ? (int) $data['po_editable'] : true),
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
     * @param array<string, mixed> $data Données converties du sort (spells)
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
        if (!is_array($groupData) || $effectsData === []) {
            return;
        }

        $groupSlug = (string) ($groupData['slug'] ?? '');
        $groupName = (string) ($groupData['name'] ?? $spell->name);

        $group = EffectGroup::firstOrCreate(
            ['slug' => $groupSlug !== '' ? $groupSlug : 'spell-' . $spell->id],
            ['name' => $groupName !== '' ? $groupName : $spell->name]
        );

        $slugToId = $this->collectSubEffectIdsFromSpellPayload($effectsData);

        foreach ($effectsData as $effectRow) {
            if (!is_array($effectRow)) {
                continue;
            }
            $degree = isset($effectRow['degree']) && is_numeric($effectRow['degree']) ? (int) $effectRow['degree'] : 1;
            $effectName = (string) ($effectRow['name'] ?? $spell->name);
            $effectSlug = (string) ($effectRow['slug'] ?? '');
            if ($effectSlug === '') {
                $effectSlug = $group->slug . '-' . $degree;
            }

            $subEffectsRaw = $effectRow['sub_effects'] ?? [];
            if (!is_array($subEffectsRaw)) {
                $subEffectsRaw = [];
            }

            $normalizedRows = $this->normalizeSubEffectsRowsForSignature($subEffectsRaw, $slugToId);
            $signature = $normalizedRows !== [] ? $this->computeEffectConfigSignature($normalizedRows) : null;

            $effect = null;
            if ($signature !== null) {
                $effect = Effect::where('config_signature', $signature)->first();
            }

            if ($effect === null) {
                $effect = Effect::where('slug', $effectSlug)->first();
                if ($effect !== null) {
                    // Évite les collisions slug_unique quand une ancienne importation a créé l'effet sans signature.
                    $effect->update([
                        'effect_group_id' => $group->id,
                        'degree' => $degree,
                        'name' => $effectName,
                        'description' => $effectRow['description'] ?? null,
                        'target_type' => (string) ($effectRow['target_type'] ?? \App\Models\Effect::TARGET_DIRECT),
                        'area' => isset($effectRow['area']) ? (string) $effectRow['area'] : null,
                        'config_signature' => $signature,
                    ]);
                } else {
                    $effect = Effect::create([
                        'effect_group_id' => $group->id,
                        'degree' => $degree,
                        'name' => $effectName,
                        'slug' => $effectSlug,
                        'description' => $effectRow['description'] ?? null,
                        'target_type' => (string) ($effectRow['target_type'] ?? \App\Models\Effect::TARGET_DIRECT),
                        'area' => isset($effectRow['area']) ? (string) $effectRow['area'] : null,
                        'config_signature' => $signature,
                    ]);
                }
            }

            EffectUsage::firstOrCreate(
                [
                    'entity_type' => 'spell',
                    'entity_id' => $spell->id,
                    'effect_id' => $effect->id,
                    'level_min' => $degree,
                    'level_max' => $degree,
                ],
                []
            );

            foreach ($subEffectsRaw as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $slug = (string) ($row['sub_effect_slug'] ?? '');
                if ($slug === '' || !isset($slugToId[$slug])) {
                    continue;
                }
                $subId = $slugToId[$slug];

                $params = is_array($row['params'] ?? null) ? $row['params'] : [];
                $critOnly = (bool) ($row['crit_only'] ?? false);
                $order = isset($row['order']) && is_numeric($row['order']) ? (int) $row['order'] : 0;

                $alreadyExists = $effect->effectSubEffects()
                    ->where($this->effectSubEffectDedupWhere($subId, $critOnly, $params))
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                $effect->effectSubEffects()->create([
                    'sub_effect_id' => $subId,
                    'order' => $order,
                    'scope' => Effect::SCOPE_GENERAL,
                    'params' => $params,
                    'crit_only' => $critOnly,
                ]);
            }
        }
    }

    /**
     * Simule la création des effets d'un sort sans écrire en base.
     * Retourne pour chaque effet du payload : action (create|reuse), existing_effect_id si réutilisation.
     *
     * @param array{effect_group: array{name?: string, slug?: string}, effects: list<array{degree?: int, name?: string, slug?: string, target_type?: string, area?: string, sub_effects?: list}>} $payload
     * @return list<array{index: int, degree: int, name: string, slug: string, target_type: string, area: string|null, sub_effects_count: int, action: 'create'|'reuse', existing_effect_id: int|null}>
     */
    public function simulateSpellEffects(array $payload): array
    {
        $effectsData = $payload['effects'] ?? [];
        if (!is_array($effectsData) || $effectsData === []) {
            return [];
        }

        $slugToId = $this->collectSubEffectIdsFromSpellPayload($effectsData);
        $plan = [];
        $index = 0;

        foreach ($effectsData as $effectRow) {
            if (!is_array($effectRow)) {
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
            $signature = $normalizedRows !== [] ? $this->computeEffectConfigSignature($normalizedRows) : null;

            $action = 'create';
            $existingEffectId = null;
            if ($signature !== null) {
                $existing = Effect::where('config_signature', $signature)->first();
                if ($existing !== null) {
                    $action = 'reuse';
                    $existingEffectId = $existing->id;
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
     * @param list<array{sub_effects?: list<array{sub_effect_slug?: string}>}> $effectsData
     * @return array<string, int>
     */
    private function collectSubEffectIdsFromSpellPayload(array $effectsData): array
    {
        $slugs = [];
        foreach ($effectsData as $effectRow) {
            if (!is_array($effectRow)) {
                continue;
            }
            foreach ($effectRow['sub_effects'] ?? [] as $row) {
                if (is_array($row) && !empty($row['sub_effect_slug'])) {
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
                        'template_text' => 'Effet ' . $slug . '.',
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
     * @param list<array{order?: int, sub_effect_slug?: string, params?: array, crit_only?: bool}> $rows
     * @param array<string, int> $slugToId
     * @return list<array{order: int, sub_effect_id: int, crit_only: bool, characteristic: mixed, value_formula: mixed, value_formula_crit: mixed, value: mixed}>
     */
    private function normalizeSubEffectsRowsForSignature(array $rows, array $slugToId): array
    {
        $seen = [];
        $out = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $slug = (string) ($row['sub_effect_slug'] ?? '');
            if ($slug === '' || !isset($slugToId[$slug])) {
                continue;
            }
            $params = is_array($row['params'] ?? null) ? $row['params'] : [];
            $critOnly = (bool) ($row['crit_only'] ?? false);
            $order = isset($row['order']) && is_numeric($row['order']) ? (int) $row['order'] : 0;
            $char = $params['characteristic'] ?? null;
            $valueFormula = $params['value_formula'] ?? null;
            $valueFormulaCrit = $params['value_formula_crit'] ?? null;
            $value = $params['value'] ?? null;

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
            ];
        }
        usort($out, static fn (array $a, array $b) => $a['order'] <=> $b['order']);

        return $out;
    }

    /**
     * Calcule une signature (hash) pour réutiliser un Effect existant.
     *
     * @param list<array{order: int, sub_effect_id: int, crit_only: bool, characteristic: mixed, value_formula: mixed, value_formula_crit: mixed, value?: mixed}> $normalizedRows
     */
    private function computeEffectConfigSignature(array $normalizedRows): string
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
            ], JSON_UNESCAPED_UNICODE);
        }

        return hash('sha256', implode("\n", $parts));
    }

    /**
     * Clé de déduplication pour un pivot sous-effet (même action + params = même ligne).
     */
    private function effectSubEffectDedupKey(int $subEffectId, bool $critOnly, array $params): string
    {
        return $subEffectId . '|' . ($critOnly ? '1' : '0') . '|'
            . ($params['characteristic'] ?? '') . '|' . ($params['value_formula'] ?? '') . '|'
            . ($params['value_formula_crit'] ?? '') . '|' . ($params['value'] ?? '');
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
        return $where;
    }

    /**
     * @param array{dry_run?: bool, force_update?: bool, replace_mode?: string, exclude_from_update?: list<string>, property_whitelist?: list<string>} $options
     */
    private function integrateBreed(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);
        $replaceMode = isset($options['replace_mode']) ? (string) $options['replace_mode'] : null;
        $excludeFromUpdate = is_array($options['exclude_from_update'] ?? null) ? $options['exclude_from_update'] : [];
        $propertyWhitelist = is_array($options['property_whitelist'] ?? null) ? $options['property_whitelist'] : [];

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

        $doReplace = $this->shouldReplaceExisting($forceUpdate, $replaceMode, $existingBreed);
        if ($existingBreed && !$doReplace) {
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
     * @param array{dry_run?: bool, force_update?: bool, replace_mode?: string, include_relations?: bool, exclude_from_update?: list<string>, property_whitelist?: list<string>} $options
     */
    private function integrateItem(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);
        $replaceMode = isset($options['replace_mode']) ? (string) $options['replace_mode'] : null;
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
        if (!$existing && !empty($data['name'])) {
            $existing = match ($targetTable) {
                'items' => Item::where('name', $data['name'])->first(),
                'consumables' => Consumable::where('name', $data['name'])->first(),
                'resources' => Resource::where('name', $data['name'])->first(),
                default => Item::where('name', $data['name'])->first(),
            };
        }

        $doReplace = $this->shouldReplaceExisting($forceUpdate, $replaceMode, $existing);
        if ($existing && !$doReplace) {
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
     * Détermine la table cible (resources, consumables, items) à partir des données brutes (item).
     * Permet de ne convertir que le bloc cible (performance + affichage ciblé).
     */
    public function getItemTargetTableFromRaw(array $raw): string
    {
        $typeId = isset($raw['typeId']) ? (int) $raw['typeId'] : null;

        return $this->resolveItemTargetTable($typeId);
    }

    /**
     * Détermine la table cible (resources, consumables, items) à partir des données converties.
     * Utilisé par l'orchestrateur pour la validation et par integrateItem.
     */
    public function getItemTargetTable(array $convertedData): string
    {
        if (isset($convertedData['resources']) && is_array($convertedData['resources']) && $convertedData['resources'] !== []
            && (!isset($convertedData['consumables']) || !is_array($convertedData['consumables']) || $convertedData['consumables'] === [])
            && (!isset($convertedData['items']) || !is_array($convertedData['items']) || $convertedData['items'] === [])) {
            return 'resources';
        }
        if (isset($convertedData['consumables']) && is_array($convertedData['consumables']) && $convertedData['consumables'] !== []
            && (!isset($convertedData['resources']) || !is_array($convertedData['resources']) || $convertedData['resources'] === [])
            && (!isset($convertedData['items']) || !is_array($convertedData['items']) || $convertedData['items'] === [])) {
            return 'consumables';
        }
        if (isset($convertedData['items']) && is_array($convertedData['items']) && $convertedData['items'] !== []
            && (!isset($convertedData['resources']) || !is_array($convertedData['resources']) || $convertedData['resources'] === [])
            && (!isset($convertedData['consumables']) || !is_array($convertedData['consumables']) || $convertedData['consumables'] === [])) {
            return 'items';
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
     * Priorité : item_types (équipements) puis consumable_types puis resource_types,
     * pour que les anneaux, armes, etc. soient bien routés vers items même si un doublon existe en base.
     */
    private function resolveItemTargetTable(?int $typeId): string
    {
        if ($typeId === null || $typeId <= 0) {
            return 'items';
        }
        if (ItemType::where('dofusdb_type_id', $typeId)->exists()) {
            return 'items';
        }
        if (ConsumableType::where('dofusdb_type_id', $typeId)->exists()) {
            return 'consumables';
        }
        if (ResourceType::where('dofusdb_type_id', $typeId)->exists()) {
            return 'resources';
        }

        return 'items';
    }

    /**
     * @param array{dry_run?: bool, force_update?: bool, replace_mode?: string, exclude_from_update?: list<string>, property_whitelist?: list<string>} $options
     */
    private function integratePanoply(array $convertedData, array $options = []): IntegrationResult
    {
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $forceUpdate = (bool) ($options['force_update'] ?? false);
        $replaceMode = isset($options['replace_mode']) ? (string) $options['replace_mode'] : null;
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
        if (!$existingPanoply && !empty($data['name'])) {
            $existingPanoply = Panoply::where('name', $data['name'])->first();
        }

        $doReplace = $this->shouldReplaceExisting($forceUpdate, $replaceMode, $existingPanoply);
        if ($existingPanoply && !$doReplace) {
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
            $table = $this->resolveItemTargetTable($typeId);
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
