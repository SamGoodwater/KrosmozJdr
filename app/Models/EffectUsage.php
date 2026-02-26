<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Lien polymorphique entité (spell, item, consumable…) → effect.
 * level_min / level_max = tranche de niveau pour cet effet.
 *
 * @property int $id
 * @property string $entity_type
 * @property int $entity_id
 * @property int $effect_id
 * @property int|null $level_min
 * @property int|null $level_max
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Model $entity
 * @property-read Effect $effect
 */
class EffectUsage extends Model
{
    protected $table = 'effect_usages';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'effect_id',
        'level_min',
        'level_max',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'effect_id' => 'integer',
        'level_min' => 'integer',
        'level_max' => 'integer',
    ];

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function effect(): BelongsTo
    {
        return $this->belongsTo(Effect::class);
    }

    /** Entités supportées (nom court => classe). */
    private const ENTITY_TYPE_MAP = [
        'spell' => \App\Models\Entity\Spell::class,
        'item' => \App\Models\Entity\Item::class,
        'consumable' => \App\Models\Entity\Consumable::class,
        'resource' => \App\Models\Entity\Resource::class,
    ];

    /** Retourne la classe pour un type court (spell, item, …). */
    public static function entityTypeToClass(string $shortType): ?string
    {
        return self::ENTITY_TYPE_MAP[$shortType] ?? null;
    }
}
