<?php

namespace App\Models\Type;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Entity\Resource;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $usable
 * @property string $is_visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @method static \Database\Factories\Type\ResourceTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType withoutTrashed()
 * @mixin \Eloquent
 */
class ResourceType extends Model
{
    /** @use HasFactory<\Database\Factories\ResourceTypeFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
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
     * Get the user that created the resource type.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les ressources de ce type.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class, 'resource_type_id');
    }
}
