<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Pivot degré d’effet / sub_effect (ordre, scope, params).
 *
 * @property int $id
 * @property int $effect_degree_id
 * @property int $sub_effect_id
 * @property int $order
 * @property string $scope
 * @property int|null $value_min
 * @property int|null $value_max
 * @property int|null $dice_num
 * @property int|null $dice_side
 * @property array<array-key, mixed>|null $params
 * @property bool $crit_only
 * @property string|null $duration_formula
 * @property string|null $logic_group
 * @property string|null $logic_operator
 * @property string|null $logic_condition
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EffectDegree $effectDegree
 * @property-read SubEffect $subEffect
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereCritOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereDiceNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereDiceSide($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereDurationFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereEffectDegreeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereLogicCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereLogicGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereLogicOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereSubEffectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereValueMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereValueMin($value)
 *
 * @mixin \Eloquent
 */
class EffectSubEffect extends Model
{
    protected $table = 'effect_sub_effect';

    protected $fillable = [
        'effect_degree_id',
        'sub_effect_id',
        'order',
        'scope',
        'value_min',
        'value_max',
        'dice_num',
        'dice_side',
        'duration_formula',
        'logic_group',
        'logic_operator',
        'logic_condition',
        'params',
        'crit_only',
    ];

    protected $casts = [
        'effect_degree_id' => 'integer',
        'sub_effect_id' => 'integer',
        'order' => 'integer',
        'value_min' => 'integer',
        'value_max' => 'integer',
        'dice_num' => 'integer',
        'dice_side' => 'integer',
        'duration_formula' => 'string',
        'logic_group' => 'string',
        'logic_operator' => 'string',
        'logic_condition' => 'string',
        'params' => 'array',
        'crit_only' => 'boolean',
    ];

    public function effectDegree(): BelongsTo
    {
        return $this->belongsTo(EffectDegree::class);
    }

    public function subEffect(): BelongsTo
    {
        return $this->belongsTo(SubEffect::class);
    }
}
