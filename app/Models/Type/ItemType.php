<?php

namespace App\Models\Type;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Entity\Item;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @method static \Database\Factories\Type\ItemTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType withoutTrashed()
 * @mixin \Eloquent
 */
class ItemType extends Model
{
    /** @use HasFactory<\Database\Factories\ItemTypeFactory> */
    use HasFactory, SoftDeletes;

    public const STATE_RAW = 'raw';
    public const STATE_DRAFT = 'draft';
    public const STATE_PLAYABLE = 'playable';
    public const STATE_ARCHIVED = 'archived';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'state',
        'read_level',
        'write_level',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_level' => 'integer',
        'write_level' => 'integer',
    ];

    /**
     * Get the user that created the item type.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les objets de ce type.
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'item_type_id');
    }
}
