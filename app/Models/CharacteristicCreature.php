<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Définition d’une caractéristique pour une entité du groupe créature (monster, class, npc).
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
 * @property array|null $applies_to
 * @property bool $is_competence
 * @property string|null $skill_characteristic_key
 * @property string|null $alternative_characteristic_key
 * @property string|null $skill_type
 * @property array|null $value_available
 * @property array|null $labels
 * @property array|null $validation
 * @property array|null $mastery_value_available
 * @property array|null $mastery_labels
 */
class CharacteristicCreature extends Model
{
    protected $table = 'characteristic_creature';

    /** S'applique à toutes les entités du groupe (défaut). */
    public const ENTITY_ALL = '*';

    public const ENTITY_MONSTER = 'monster';
    public const ENTITY_CLASS = 'class';
    public const ENTITY_NPC = 'npc';

    /** @var list<string> */
    public const ENTITIES = [self::ENTITY_MONSTER, self::ENTITY_CLASS, self::ENTITY_NPC];

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
        'applies_to',
        'is_competence',
        'skill_characteristic_key',
        'alternative_characteristic_key',
        'skill_type',
        'value_available',
        'labels',
        'validation',
        'mastery_value_available',
        'mastery_labels',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'min' => 'integer',
        'max' => 'integer',
        'required' => 'boolean',
        'sort_order' => 'integer',
        'is_competence' => 'boolean',
        'applies_to' => 'array',
        'value_available' => 'array',
        'labels' => 'array',
        'validation' => 'array',
        'mastery_value_available' => 'array',
        'mastery_labels' => 'array',
    ];

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }
}
