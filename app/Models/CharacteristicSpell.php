<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Définition d’une caractéristique pour l’entité spell (groupe sort).
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
 * @property array|null $value_available
 */
class CharacteristicSpell extends Model
{
    protected $table = 'characteristic_spell';

    /** S'applique à toutes les entités du groupe (défaut). */
    public const ENTITY_ALL = '*';

    public const ENTITY_SPELL = 'spell';

    /** @var list<string> */
    public const ENTITIES = [self::ENTITY_SPELL];

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
        'value_available',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'min' => 'integer',
        'max' => 'integer',
        'required' => 'boolean',
        'sort_order' => 'integer',
        'value_available' => 'array',
    ];

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }
}
