<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @mixin IdeHelperResourcetype
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Resource> $resources
 * @property-read int|null $resources_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resourcetype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resourcetype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resourcetype query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resourcetype whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resourcetype whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resourcetype whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resourcetype whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resourcetype whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resourcetype whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Resourcetype extends Model
{
    protected $fillable = [
        'name',
        'uniqid',
    ];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function resources()
    {
        return $this->hasMany(Resource::class, 'resourcetpe_id', 'id');
    }
}
