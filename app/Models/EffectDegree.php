<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Degré d’un effet : zone, slug, seuil de niveau requis, sous-effets.
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/ZONE_NOTATION.md
 */
class EffectDegree extends Model
{
    protected $table = 'effect_degrees';

    protected $fillable = [
        'effect_id',
        'degree',
        'required_creature_level',
        'area',
        'slug',
        'config_signature',
    ];

    protected $casts = [
        'effect_id' => 'integer',
        'degree' => 'integer',
        'required_creature_level' => 'integer',
    ];

    public function effect(): BelongsTo
    {
        return $this->belongsTo(Effect::class);
    }

    public function effectSubEffects(): HasMany
    {
        return $this->hasMany(EffectSubEffect::class)->orderBy('order');
    }

    public function effectUsages(): HasMany
    {
        return $this->hasMany(EffectUsage::class);
    }
}
