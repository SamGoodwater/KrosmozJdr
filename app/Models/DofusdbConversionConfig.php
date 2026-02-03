<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Entrée de config de conversion DofusDB (clé → valeur JSON).
 *
 * Clés : pass_through_characteristics, characteristic_transformations, limits_source,
 * effect_id_to_characteristic, element_id_to_resistance, limits.
 *
 * @property string $key
 * @property array|null $value
 */
class DofusdbConversionConfig extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'dofusdb_conversion_config';

    protected $fillable = ['key', 'value'];

    /** @var array<string, string> */
    protected $casts = [
        'value' => 'array',
    ];

    public const KEY_PASS_THROUGH = 'pass_through_characteristics';
    public const KEY_TRANSFORMATIONS = 'characteristic_transformations';
    public const KEY_LIMITS_SOURCE = 'limits_source';
    public const KEY_EFFECT_TO_CHAR = 'effect_id_to_characteristic';
    public const KEY_ELEMENT_TO_RES = 'element_id_to_resistance';
    public const KEY_LIMITS = 'limits';
}
