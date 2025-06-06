<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Entity\Npc;
use App\Models\Entity\Item;
use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use App\Models\Entity\Scenario;
use App\Models\Entity\Campaign;
use App\Models\Entity\Panoply;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $location
 * @property int $price
 * @property int $usable
 * @property string $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $npc_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read Npc|null $npc
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @method static \Database\Factories\Entity\ShopFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereNpcId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop withoutTrashed()
 * @mixin \Eloquent
 */
class Shop extends Model
{
    /** @use HasFactory<\\Database\\Factories\\ShopFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'location',
        'price',
        'usable',
        'is_visible',
        'image',
        'created_by',
        'npc_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'integer',
        'usable' => 'integer',
    ];

    /**
     * Get the user that created the shop.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the NPC associated with the shop.
     */
    public function npc()
    {
        return $this->belongsTo(Npc::class, 'npc_id');
    }
    /**
     * Les objets vendus dans cette boutique.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_shop')->withPivot('quantity', 'price', 'comment');
    }
    /**
     * Les panoplies vendues dans cette boutique.
     */
    public function panoplies()
    {
        return $this->belongsToMany(Panoply::class, 'panoply_shop');
    }
    /**
     * Les consommables vendus dans cette boutique.
     */
    public function consumables()
    {
        return $this->belongsToMany(Consumable::class, 'consumable_shop')->withPivot('quantity', 'price', 'comment');
    }
    /**
     * Les ressources vendues dans cette boutique.
     */
    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'resource_shop')->withPivot('quantity', 'price', 'comment');
    }
    /**
     * Les scénarios associés à cette boutique.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'scenario_shop');
    }
    /**
     * Les campagnes associées à cette boutique.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_shop');
    }
}
