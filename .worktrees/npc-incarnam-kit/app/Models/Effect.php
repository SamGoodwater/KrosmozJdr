<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Entity\Spell;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * Définition d’effet (généralités). Les degrés (zone, seuil, sous-effets) : {@see EffectDegree}.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $description
 * @property string $target_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EffectDegree> $degrees
 * @property-read int|null $degrees_count
 * @property-read Collection<int, EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @property-read Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereUpdatedAt($value)
 * @mixin \Eloquent
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
