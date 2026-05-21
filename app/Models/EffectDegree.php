<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Degré d’un effet : zone, slug, seuil de niveau requis, sous-effets.
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/ZONE_NOTATION.md
 * @property int $id
 * @property int $effect_id
 * @property int $degree
 * @property int|null $required_creature_level
 * @property string|null $area
 * @property string|null $slug
 * @property string|null $config_signature
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Effect $effect
 * @property-read Collection<int, EffectSubEffect> $effectSubEffects
 * @property-read int|null $effect_sub_effects_count
 * @property-read Collection<int, EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectDegree newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectDegree newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectDegree query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectDegree whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectDegree whereConfigSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectDegree whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectDegree whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectDegree whereEffectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectDegree whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectDegree whereRequiredCreatureLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectDegree whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectDegree whereUpdatedAt($value)
 * @mixin \Eloquent
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
