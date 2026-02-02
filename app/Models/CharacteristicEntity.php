<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Valeurs et règles par entité (monster, class, item) pour une caractéristique.
 *
 * @property int $id
 * @property string $characteristic_id
 * @property string $entity
 * @property int|null $min
 * @property int|null $max
 * @property string|null $formula
 * @property string|null $formula_display
 * @property string|null $default_value
 * @property bool $required
 * @property string|null $validation_message
 * @property bool $forgemagie_allowed
 * @property int $forgemagie_max
 * @property float|null $base_price_per_unit
 * @property float|null $rune_price_per_unit
 */
class CharacteristicEntity extends Model
{
    protected $table = 'characteristic_entities';

    public const ENTITY_MONSTER = 'monster';

    public const ENTITY_CLASS = 'class';

    public const ENTITY_ITEM = 'item';

    public const ENTITY_SPELL = 'spell';

    /** Entités supportées pour la validation (limites min/max, required). */
    public const VALIDATION_ENTITIES = [
        self::ENTITY_MONSTER,
        self::ENTITY_CLASS,
        self::ENTITY_ITEM,
        self::ENTITY_SPELL,
    ];

    /** @var list<string> */
    protected $fillable = [
        'characteristic_id',
        'entity',
        'min',
        'max',
        'formula',
        'formula_display',
        'default_value',
        'required',
        'validation_message',
        'forgemagie_allowed',
        'forgemagie_max',
        'base_price_per_unit',
        'rune_price_per_unit',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'min' => 'integer',
        'max' => 'integer',
        'required' => 'boolean',
        'forgemagie_allowed' => 'boolean',
        'forgemagie_max' => 'integer',
        'base_price_per_unit' => 'decimal:2',
        'rune_price_per_unit' => 'decimal:2',
    ];

    /**
     * Caractéristique parente.
     *
     * @return BelongsTo<Characteristic, self>
     */
    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class, 'characteristic_id', 'id');
    }
}
