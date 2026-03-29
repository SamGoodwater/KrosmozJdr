<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** Sous-effet (atome : taper, soigner, vol_pa...). */
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
