<?php

namespace App\Models\Type;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Entity\Spell;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $color
 * @property string|null $icon
 * @property int $usable
 * @property string $is_visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Database\Factories\Type\SpellTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType withoutTrashed()
 * @mixin \Eloquent
 */
class SpellType extends Model
{
    /** @use HasFactory<\Database\Factories\SpellTypeFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'color',
        'icon',
        'usable',
        'is_visible',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'usable' => 'integer',
    ];

    /**
     * Get the user that created the spell type.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les sorts de ce type.
     */
    public function spells()
    {
        return $this->belongsToMany(Spell::class, 'spell_type');
    }
}
