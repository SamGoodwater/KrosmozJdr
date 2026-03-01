<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mapping effectId DofusDB → sous-effet KrosmozJDR (sub_effect_slug + characteristic_source).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_MAPPING_EFFETS.md
 */
class DofusdbEffectMapping extends Model
{
    protected $table = 'dofusdb_effect_mappings';

    public const SOURCE_ELEMENT = 'element';
    public const SOURCE_CHARACTERISTIC = 'characteristic';
    public const SOURCE_NONE = 'none';

    protected $fillable = [
        'dofusdb_effect_id',
        'sub_effect_slug',
        'characteristic_source',
        'characteristic_key',
    ];

    protected $casts = [
        'dofusdb_effect_id' => 'integer',
    ];
}
