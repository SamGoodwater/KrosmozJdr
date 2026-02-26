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
        'variables_allowed', 'dofusdb_effect_id',
    ];

    protected $casts = [
        'variables_allowed' => 'array',
        'dofusdb_effect_id' => 'integer',
    ];

    public function effects(): BelongsToMany
    {
        return $this->belongsToMany(Effect::class, 'effect_sub_effect')
            ->withPivot(['order', 'scope', 'value_min', 'value_max', 'dice_num', 'dice_side', 'params'])
            ->withTimestamps()
            ->orderByPivot('order');
    }
}
