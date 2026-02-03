<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Définition d'une caractéristique pour une entité (source de vérité centrée entité).
 * Une ligne = une caractéristique attachée à une entité (entity + characteristic_key).
 *
 * @property int $id
 * @property string $entity
 * @property string $characteristic_key
 * @property string $name
 * @property string|null $short_name
 * @property string|null $helper
 * @property string|null $descriptions
 * @property string|null $icon
 * @property string|null $color
 * @property string|null $unit
 * @property int $sort_order
 * @property string|null $db_column
 * @property string $type
 * @property int|null $min
 * @property int|null $max
 * @property string|null $formula
 * @property string|null $formula_display
 * @property array|null $computation
 * @property string|null $default_value
 * @property bool $required
 * @property string|null $validation_message
 * @property bool $forgemagie_allowed
 * @property int $forgemagie_max
 * @property float|null $base_price_per_unit
 * @property float|null $rune_price_per_unit
 * @property array|null $applies_to
 * @property bool $is_competence
 * @property string|null $characteristic_id
 * @property string|null $alternative_characteristic_id
 * @property string|null $skill_type
 * @property array|null $value_available
 * @property array|null $labels
 * @property array|null $validation
 * @property array|null $mastery_value_available
 * @property array|null $mastery_labels
 */
class EntityCharacteristic extends Model
{
    protected $table = 'entity_characteristics';

    public const ENTITY_MONSTER = 'monster';
    public const ENTITY_CLASS = 'class';
    public const ENTITY_ITEM = 'item';
    public const ENTITY_SPELL = 'spell';
    public const ENTITY_RESOURCE = 'resource';
    public const ENTITY_CONSUMABLE = 'consumable';
    public const ENTITY_PANOPLY = 'panoply';

    /** @var list<string> */
    public const ENTITIES = [
        self::ENTITY_MONSTER,
        self::ENTITY_CLASS,
        self::ENTITY_ITEM,
        self::ENTITY_SPELL,
        self::ENTITY_RESOURCE,
        self::ENTITY_CONSUMABLE,
        self::ENTITY_PANOPLY,
    ];

    /** @var list<string> */
    protected $fillable = [
        'entity',
        'characteristic_key',
        'name',
        'short_name',
        'helper',
        'descriptions',
        'icon',
        'color',
        'unit',
        'sort_order',
        'db_column',
        'type',
        'min',
        'max',
        'formula',
        'formula_display',
        'computation',
        'default_value',
        'required',
        'validation_message',
        'forgemagie_allowed',
        'forgemagie_max',
        'base_price_per_unit',
        'rune_price_per_unit',
        'applies_to',
        'is_competence',
        'characteristic_id',
        'alternative_characteristic_id',
        'skill_type',
        'value_available',
        'labels',
        'validation',
        'mastery_value_available',
        'mastery_labels',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'sort_order' => 'integer',
        'min' => 'integer',
        'max' => 'integer',
        'required' => 'boolean',
        'forgemagie_allowed' => 'boolean',
        'forgemagie_max' => 'integer',
        'base_price_per_unit' => 'decimal:2',
        'rune_price_per_unit' => 'decimal:2',
        'computation' => 'array',
        'applies_to' => 'array',
        'is_competence' => 'boolean',
        'value_available' => 'array',
        'labels' => 'array',
        'validation' => 'array',
        'mastery_value_available' => 'array',
        'mastery_labels' => 'array',
    ];

}
