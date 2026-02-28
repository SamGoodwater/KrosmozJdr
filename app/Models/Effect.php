<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Effet (conteneur de sous-effets). Niveau sur effect_usage. */
class Effect extends Model
{
    protected $table = 'effects';

    protected $fillable = [
        'name', 'slug', 'description', 'effect_group_id', 'degree', 'config_signature',
    ];

    protected $casts = [
        'effect_group_id' => 'integer',
        'degree' => 'integer',
    ];

    public const SCOPE_GENERAL = 'general';
    public const SCOPE_COMBAT = 'combat';
    public const SCOPE_OUT_OF_COMBAT = 'out_of_combat';

    public function effectGroup(): BelongsTo
    {
        return $this->belongsTo(EffectGroup::class, 'effect_group_id');
    }

    /** Lignes pivot (une par instance de sous-effet, même action possible plusieurs fois). */
    public function effectSubEffects(): HasMany
    {
        return $this->hasMany(EffectSubEffect::class)->orderBy('order');
    }

    /** Sous-effets uniques (legacy / API). Pour la liste complète avec params, utiliser effectSubEffects. */
    public function subEffects(): BelongsToMany
    {
        return $this->belongsToMany(SubEffect::class, 'effect_sub_effect')
            ->withPivot(['order', 'scope', 'value_min', 'value_max', 'dice_num', 'dice_side', 'params'])
            ->withTimestamps()
            ->orderByPivot('order');
    }

    public function effectUsages(): HasMany
    {
        return $this->hasMany(EffectUsage::class);
    }
}
