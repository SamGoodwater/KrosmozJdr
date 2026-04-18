<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Lien polymorphique entité (item, consumable, resource) → degré d’effet.
 *
 * Les sorts utilisent la table {@see effect_spell} ; le seuil est sur {@see EffectDegree::required_creature_level}.
 *
 * @property int $id
 * @property string $entity_type
 * @property int $entity_id
 * @property int $effect_degree_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EffectDegree $effectDegree
 * @property-read Model|\Eloquent $entity
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereEffectDegreeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EffectUsage extends Model
{
    protected $table = 'effect_usages';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'effect_degree_id',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'effect_degree_id' => 'integer',
    ];

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function effectDegree(): BelongsTo
    {
        return $this->belongsTo(EffectDegree::class);
    }

    /** Entités supportées (nom court => classe). Les sorts ne passent plus par cette table. */
    private const ENTITY_TYPE_MAP = [
        'item' => Item::class,
        'consumable' => Consumable::class,
        'resource' => Entity\Resource::class,
    ];

    public static function entityTypeToClass(string $shortType): ?string
    {
        return self::ENTITY_TYPE_MAP[$shortType] ?? null;
    }
}
