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
        'effect_id', 'sub_effect_id', 'order', 'scope',
        'value_min', 'value_max', 'dice_num', 'dice_side', 'params',
    ];

    protected $casts = [
        'effect_id' => 'integer',
        'sub_effect_id' => 'integer',
        'order' => 'integer',
        'value_min' => 'integer',
        'value_max' => 'integer',
        'dice_num' => 'integer',
        'dice_side' => 'integer',
        'params' => 'array',
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
