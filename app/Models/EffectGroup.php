<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Groupe d'effets (degres de puissance). */
class EffectGroup extends Model
{
    protected $table = 'effect_groups';

    protected $fillable = ['name', 'slug'];

    public function effects(): HasMany
    {
        return $this->hasMany(Effect::class, 'effect_group_id');
    }
}
