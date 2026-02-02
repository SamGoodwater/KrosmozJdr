<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pour un slot d'équipement : une caractéristique avec bracket_max, forgemagie_max et prix par unité.
 *
 * @property int $id
 * @property string $equipment_slot_id
 * @property string $characteristic_id
 * @property array $bracket_max
 * @property int|null $forgemagie_max
 * @property float|null $base_price_per_unit
 * @property float|null $rune_price_per_unit
 */
class EquipmentSlotCharacteristic extends Model
{
    protected $table = 'equipment_slot_characteristics';

    /** @var list<string> */
    protected $fillable = [
        'equipment_slot_id',
        'characteristic_id',
        'bracket_max',
        'forgemagie_max',
        'base_price_per_unit',
        'rune_price_per_unit',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'bracket_max' => 'array',
        'forgemagie_max' => 'integer',
        'base_price_per_unit' => 'decimal:2',
        'rune_price_per_unit' => 'decimal:2',
    ];

    /**
     * Slot d'équipement.
     *
     * @return BelongsTo<EquipmentSlot, self>
     */
    public function equipmentSlot(): BelongsTo
    {
        return $this->belongsTo(EquipmentSlot::class, 'equipment_slot_id', 'id');
    }

    /**
     * Caractéristique.
     *
     * @return BelongsTo<Characteristic, self>
     */
    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class, 'characteristic_id', 'id');
    }
}
