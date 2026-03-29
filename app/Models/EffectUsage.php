<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Lien polymorphique entité (item, consumable, resource) → degré d’effet.
 * Les sorts utilisent la table {@see effect_spell} ; le seuil est sur {@see EffectDegree::required_creature_level}.
 *
 * @property int $id
 * @property string $entity_type
 * @property int $entity_id
 * @property int $effect_degree_id
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
        'item' => \App\Models\Entity\Item::class,
        'consumable' => \App\Models\Entity\Consumable::class,
        'resource' => \App\Models\Entity\Resource::class,
    ];

    public static function entityTypeToClass(string $shortType): ?string
    {
        return self::ENTITY_TYPE_MAP[$shortType] ?? null;
    }
}
