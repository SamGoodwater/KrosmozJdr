<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Sous-effet (atome : taper, soigner, vol_pa.
 *
 * ..).
 *
 * @property int $id
 * @property string $slug
 * @property string $type_slug
 * @property string|null $template_text
 * @property string|null $formula
 * @property array<array-key, mixed>|null $variables_allowed
 * @property array<array-key, mixed>|null $param_schema
 * @property int|null $dofusdb_effect_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EffectDegree> $effectDegrees
 * @property-read int|null $effect_degrees_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereDofusdbEffectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereParamSchema($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereTemplateText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereTypeSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereVariablesAllowed($value)
 * @mixin \Eloquent
 */
class SubEffect extends Model
{
    protected $table = 'sub_effects';

    protected $fillable = [
        'slug', 'type_slug', 'template_text', 'formula',
        'variables_allowed', 'param_schema', 'dofusdb_effect_id',
    ];

    protected $casts = [
        'variables_allowed' => 'array',
        'param_schema' => 'array',
        'dofusdb_effect_id' => 'integer',
    ];

    public function effectDegrees(): BelongsToMany
    {
        return $this->belongsToMany(EffectDegree::class, 'effect_sub_effect', 'sub_effect_id', 'effect_degree_id')
            ->withPivot([
                'order', 'scope', 'value_min', 'value_max', 'dice_num', 'dice_side',
                'duration_formula', 'logic_group', 'logic_operator', 'logic_condition',
                'params', 'crit_only',
            ])
            ->withTimestamps()
            ->orderByPivot('order');
    }
}
