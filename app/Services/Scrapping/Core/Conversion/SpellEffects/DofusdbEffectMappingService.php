<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion\SpellEffects;

use App\Models\DofusdbEffectMapping;
use App\Services\Scrapping\Core\Conversion\SpellEffects\DofusDbEffectMapping as FallbackEffectMapping;
use Illuminate\Support\Facades\Cache;

/**
 * Résolution du mapping effectId DofusDB → sous-effet Krosmoz (BDD + cache + fallback constante).
 *
 * Lit d'abord la table dofusdb_effect_mappings (avec cache), sinon délègue à DofusDbEffectMapping (constante PHP).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_MAPPING_EFFETS.md
 */
final class DofusdbEffectMappingService
{
    private const CACHE_KEY = 'scrapping.dofusdb_effect_mappings';
    private const CACHE_TTL_SECONDS = 3600;

    public function __construct(
        private DofusdbEffectMapping $model
    ) {
    }

    /**
     * Retourne [sub_effect_slug, characteristic_source] ou [sub_effect_slug, characteristic_source, characteristic_key].
     * Si source = characteristic, characteristic_key peut être présent (Phase 2+).
     */
    public function getSubEffectForEffectId(int $effectId): ?array
    {
        $row = $this->findByEffectId($effectId);
        if ($row !== null) {
            $result = [$row->sub_effect_slug, $row->characteristic_source];
            if ($row->characteristic_key !== null && $row->characteristic_key !== '') {
                $result[] = $row->characteristic_key;
            }
            return $result;
        }
        return FallbackEffectMapping::getSubEffectForEffectId($effectId);
    }

    private function findByEffectId(int $effectId): ?DofusdbEffectMapping
    {
        $all = $this->getAllMappingsIndexedById();
        return $all[$effectId] ?? null;
    }

    /**
     * @return array<int, DofusdbEffectMapping>
     */
    private function getAllMappingsIndexedById(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            $rows = $this->model->newQuery()->get();
            $indexed = [];
            foreach ($rows as $row) {
                $indexed[$row->dofusdb_effect_id] = $row;
            }
            return $indexed;
        });
    }

    /** Invalide le cache après modification des mappings (store/update/destroy). */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
