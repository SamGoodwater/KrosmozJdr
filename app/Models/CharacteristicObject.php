<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Définition d’une caractéristique pour une entité du groupe objet (item, consumable, resource, panoply).
 *
 * @property int $id
 * @property int $characteristic_id
 * @property string $entity
 * @property string|null $db_column
 * @property int|null $min
 * @property int|null $max
 * @property string|null $formula
 * @property string|null $formula_display
 * @property string|null $default_value
 * @property bool $required
 * @property string|null $validation_message
 * @property string|null $conversion_formula
 * @property int $sort_order
 * @property bool $forgemagie_allowed
 * @property int $forgemagie_max
 * @property float|null $base_price_per_unit
 * @property float|null $rune_price_per_unit
 * @property array|null $value_available
 */
class CharacteristicObject extends Model
{
    protected $table = 'characteristic_object';

    /** S'applique à toutes les entités du groupe (défaut). */
    public const ENTITY_ALL = '*';

    public const ENTITY_ITEM = 'item';
    public const ENTITY_CONSUMABLE = 'consumable';
    public const ENTITY_RESOURCE = 'resource';
    public const ENTITY_PANOPLY = 'panoply';

    /** @var list<string> */
    public const ENTITIES = [
        self::ENTITY_ITEM,
        self::ENTITY_CONSUMABLE,
        self::ENTITY_RESOURCE,
        self::ENTITY_PANOPLY,
    ];

    /** @var list<string> */
    protected $fillable = [
        'characteristic_id',
        'entity',
        'db_column',
        'min',
        'max',
        'formula',
        'formula_display',
        'default_value',
        'required',
        'validation_message',
        'conversion_formula',
        'sort_order',
        'forgemagie_allowed',
        'forgemagie_max',
        'base_price_per_unit',
        'rune_price_per_unit',
        'value_available',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'min' => 'integer',
        'max' => 'integer',
        'required' => 'boolean',
        'sort_order' => 'integer',
        'forgemagie_allowed' => 'boolean',
        'forgemagie_max' => 'integer',
        'base_price_per_unit' => 'decimal:2',
        'rune_price_per_unit' => 'decimal:2',
        'value_available' => 'array',
    ];

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }
}
