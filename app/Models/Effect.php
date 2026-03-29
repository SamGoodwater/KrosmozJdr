<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Entity\Spell;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Définition d’effet (généralités). Les degrés (zone, seuil, sous-effets) : {@see EffectDegree}.
 */
class Effect extends Model
{
    protected $table = 'effects';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'target_type',
    ];

    /** Cible : application directe sur la cible (défaut). */
    public const TARGET_DIRECT = 'direct';

    public const TARGET_TRAP = 'trap';

    public const TARGET_GLYPH = 'glyph';

    public const SCOPE_GENERAL = 'general';

    public const SCOPE_COMBAT = 'combat';

    public const SCOPE_OUT_OF_COMBAT = 'out_of_combat';

    public function degrees(): HasMany
    {
        return $this->hasMany(EffectDegree::class)->orderBy('degree');
    }

    public function spells(): BelongsToMany
    {
        return $this->belongsToMany(Spell::class, 'effect_spell');
    }

    /**
     * Usages polymorphiques (items, consommables…) pointant vers un degré de cet effet.
     */
    public function effectUsages(): HasManyThrough
    {
        return $this->hasManyThrough(EffectUsage::class, EffectDegree::class, 'effect_id', 'effect_degree_id');
    }
}
