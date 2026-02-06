<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Type\ItemType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Définition d’une caractéristique pour une entité du groupe objet (item, consumable, resource, panoply).
 *
 * @property int $id
 * @property int $characteristic_id
 * @property string $entity
 * @property string|null $db_column
 * @property string|null $min Valeur fixe, formule ou table JSON
 * @property string|null $max Valeur fixe, formule ou table JSON
 * @property string|null $formula
 * @property string|null $formula_display
 * @property string|null $default_value
 * @property string|null $conversion_formula
 * @property array|null $conversion_dofus_sample Niveau → valeur Dofus (ex. {"1":1,"200":200})
 * @property array|null $conversion_krosmoz_sample Niveau → valeur Krosmoz (ex. {"1":1,"20":20})
 * @property bool $forgemagie_allowed
 * @property int $forgemagie_max
 * @property float|null $base_price_per_unit
 * @property float|null $rune_price_per_unit
 * @property array|null $value_available
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ItemType> $allowedItemTypes
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
        'conversion_formula',
        'conversion_dofus_sample',
        'conversion_krosmoz_sample',
        'conversion_sample_rows',
        'forgemagie_allowed',
        'forgemagie_max',
        'base_price_per_unit',
        'rune_price_per_unit',
        'value_available',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'conversion_dofus_sample' => 'array',
        'conversion_krosmoz_sample' => 'array',
        'conversion_sample_rows' => 'array',
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

    /**
     * Types d'équipement (item_types) pour lesquels cette caractéristique est autorisée.
     * Vide = tous les types ; sinon la caractéristique ne s'applique qu'aux types listés.
     *
     * @return BelongsToMany<ItemType, self>
     */
    public function allowedItemTypes(): BelongsToMany
    {
        return $this->belongsToMany(ItemType::class, 'characteristic_object_item_type')
            ->withTimestamps();
    }
}
