<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Définition globale d'une caractéristique (source de vérité en base).
 *
 * @property string $id
 * @property string|null $db_column
 * @property string $name
 * @property string|null $short_name
 * @property string|null $description
 * @property string $type
 * @property string|null $unit
 * @property string|null $icon
 * @property string|null $color
 * @property int $sort_order
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
class Characteristic extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'characteristics';

    /** @var list<string> */
    protected $fillable = [
        'id',
        'db_column',
        'name',
        'short_name',
        'description',
        'type',
        'unit',
        'icon',
        'color',
        'sort_order',
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
        'applies_to' => 'array',
        'is_competence' => 'boolean',
        'value_available' => 'array',
        'labels' => 'array',
        'validation' => 'array',
        'mastery_value_available' => 'array',
        'mastery_labels' => 'array',
    ];

    /**
     * Règles par entité (monster, class, item).
     *
     * @return HasMany<CharacteristicEntity>
     */
    public function entityDefinitions(): HasMany
    {
        return $this->hasMany(CharacteristicEntity::class, 'characteristic_id', 'id');
    }

    /**
     * Caractéristique principale (pour compétences).
     *
     * @return BelongsTo<Characteristic, self>
     */
    public function mainCharacteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class, 'characteristic_id', 'id');
    }

    /**
     * Caractéristique alternative (pour compétences).
     *
     * @return BelongsTo<Characteristic, self>
     */
    public function alternativeCharacteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class, 'alternative_characteristic_id', 'id');
    }

    /**
     * Liaisons slot d'équipement ↔ caractéristique.
     *
     * @return HasMany<EquipmentSlotCharacteristic>
     */
    public function equipmentSlotCharacteristics(): HasMany
    {
        return $this->hasMany(EquipmentSlotCharacteristic::class, 'characteristic_id', 'id');
    }
}
