<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Pivot effect / sub_effect (ordre, scope, params). */
class EffectSubEffect extends Model
{
    protected $table = 'effect_sub_effect';

    protected $fillable = [
        'effect_id',
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
        'effect_id' => 'integer',
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

    public function effect(): BelongsTo
    {
        return $this->belongsTo(Effect::class);
    }

    public function subEffect(): BelongsTo
    {
        return $this->belongsTo(SubEffect::class);
    }
}
