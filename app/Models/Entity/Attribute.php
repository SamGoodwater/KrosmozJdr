<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Entity\Creature;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $usable
 * @property string $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @method static \Database\Factories\Entity\AttributeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute withoutTrashed()
 * @mixin \Eloquent
 */
class Attribute extends Model
{
    /** @use HasFactory<\\Database\\Factories\\AttributeFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'usable',
        'is_visible',
        'image',
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
     * Get the user that created the attribute.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les créatures associées à cet attribut.
     */
    public function creatures()
    {
        return $this->belongsToMany(Creature::class, 'attribute_creature');
    }
}
