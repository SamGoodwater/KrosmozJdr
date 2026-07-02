<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Mapping effectId DofusDB → sous-effet KrosmozJDR (sub_effect_slug + characteristic_source).
 *
 * @see docs/features/effects/README.md
 * @property int $id
 * @property int $dofusdb_effect_id
 * @property string $sub_effect_slug
 * @property string $characteristic_source
 * @property string|null $characteristic_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereCharacteristicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereCharacteristicSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereDofusdbEffectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereSubEffectSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereUpdatedAt($value)
 * @mixin \Eloquent
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
