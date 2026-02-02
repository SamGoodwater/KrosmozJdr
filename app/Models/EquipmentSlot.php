<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Slot d'équipement (arme, chapeau, cape, etc.).
 *
 * @property string $id
 * @property string $name
 * @property int $sort_order
 */
class EquipmentSlot extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'equipment_slots';

    /** @var list<string> */
    protected $fillable = [
        'id',
        'name',
        'sort_order',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Caractéristiques associées à ce slot (bracket_max, forgemagie_max, prix).
     *
     * @return HasMany<EquipmentSlotCharacteristic>
     */
    public function slotCharacteristics(): HasMany
    {
        return $this->hasMany(EquipmentSlotCharacteristic::class, 'equipment_slot_id', 'id');
    }
}
