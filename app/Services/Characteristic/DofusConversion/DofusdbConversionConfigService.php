<?php

declare(strict_types=1);

namespace App\Services\Characteristic\DofusConversion;

use App\Models\DofusdbConversionConfig;
use Illuminate\Support\Facades\Cache;

/**
 * Lit la config de conversion DofusDB depuis la BDD (table dofusdb_conversion_config).
 *
 * Expose la même structure que config('dofusdb_conversion') pour compatibilité
 * avec DofusDbConversionFormulas.
 */
final class DofusdbConversionConfigService
{
    private const CACHE_KEY = 'dofusdb_conversion_config.full';

    private const CACHE_TTL_SECONDS = 3600;

    /**
     * Retourne la config complète (pass_through, mappings, limits_source, limits, etc.).
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            return $this->buildConfig();
        });
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildConfig(): array
    {
        $rows = DofusdbConversionConfig::all()->keyBy('key');

        $limitsSource = $rows->get(DofusdbConversionConfig::KEY_LIMITS_SOURCE)?->value;
        $limitsSource = is_array($limitsSource) && isset($limitsSource[0]) ? $limitsSource[0] : (is_string($limitsSource) ? $limitsSource : 'characteristics');

        return [
            'pass_through_characteristics' => $rows->get(DofusdbConversionConfig::KEY_PASS_THROUGH)?->value ?? $this->defaultPassThrough(),
            'characteristic_transformations' => $rows->get(DofusdbConversionConfig::KEY_TRANSFORMATIONS)?->value ?? ['monster' => [], 'class' => [], 'item' => [], 'spell' => []],
            'limits_source' => $limitsSource,
            'effect_id_to_characteristic' => $rows->get(DofusdbConversionConfig::KEY_EFFECT_TO_CHAR)?->value ?? [],
            'element_id_to_resistance' => $rows->get(DofusdbConversionConfig::KEY_ELEMENT_TO_RES)?->value ?? $this->defaultElementToResistance(),
            'limits' => $rows->get(DofusdbConversionConfig::KEY_LIMITS)?->value ?? ['monster' => [], 'class' => [], 'item' => []],
            'formulas' => [], // formules = BDD dofusdb_conversion_formulas
        ];
    }

    /** @return list<string> */
    private function defaultPassThrough(): array
    {
        return ['name', 'description', 'pa', 'pm', 'invocation', 'po'];
    }

    /** @return array<int, string> */
    private function defaultElementToResistance(): array
    {
        return [
            -1 => 'res_neutre',
            0 => 'res_neutre',
            1 => 'res_terre',
            2 => 'res_feu',
            3 => 'res_air',
            4 => 'res_eau',
        ];
    }
}
