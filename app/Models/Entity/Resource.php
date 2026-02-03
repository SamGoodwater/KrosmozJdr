<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Type\ResourceType;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Scenario;
use App\Models\Entity\Shop;
use App\Models\Entity\Campaign;

/**
 * 
 *
 * @property int $id
 * @property string|null $dofusdb_id
 * @property int|null $official_id
 * @property string $name
 * @property string|null $description
 * @property string $level
 * @property string|null $price
 * @property string|null $weight
 * @property int $rarity
 * @property string $dofus_version
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property bool $auto_update
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $resource_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read ResourceType|null $resourceType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @method static \Database\Factories\Entity\ResourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereResourceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource withoutTrashed()
 * @mixin \Eloquent
 */
class Resource extends Model
{
    /** @use HasFactory<\Database\Factories\ResourceFactory> */
    use HasFactory, SoftDeletes;

    public const STATE_RAW = 'raw';
    public const STATE_DRAFT = 'draft';
    public const STATE_PLAYABLE = 'playable';
    public const STATE_ARCHIVED = 'archived';

    const RARITY = [
        0 => 'Commun',
        1 => 'Peu commun',
        2 => 'Rare',
        3 => 'Très rare',
        4 => 'Légendaire',
        5 => 'Unique',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'dofusdb_id',
        'official_id',
        'name',
        'description',
        'level',
        'price',
        'weight',
        'rarity',
        'dofus_version',
        'state',
        'read_level',
        'write_level',
        'image',
        'auto_update',
        'resource_type_id',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'official_id' => 'integer',
        'rarity' => 'integer',
        'read_level' => 'integer',
        'write_level' => 'integer',
        'auto_update' => 'boolean',
    ];

    /**
     * Get the user that created the resource.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the type of the resource.
     */
    public function resourceType()
    {
        return $this->belongsTo(ResourceType::class, 'resource_type_id');
    }

    /**
     * Les consommables utilisant cette ressource.
     */
    public function consumables()
    {
        return $this->belongsToMany(Consumable::class, 'consumable_resource')->withPivot('quantity');
    }
    /**
     * Les créatures utilisant cette ressource.
     */
    public function creatures()
    {
        return $this->belongsToMany(Creature::class, 'creature_resource')->withPivot('quantity');
    }
    /**
     * Les objets utilisant cette ressource.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_resource')->withPivot('quantity');
    }

    /**
     * Recette de fabrication : ressources (ingrédients) nécessaires avec quantités.
     * Une ressource craftable est fabriquée à partir d'autres ressources.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Resource, Resource>
     */
    public function recipeIngredients()
    {
        return $this->belongsToMany(
            Resource::class,
            'resource_recipe',
            'resource_id',
            'ingredient_resource_id'
        )->withPivot('quantity');
    }
    /**
     * Les scénarios associés à cette ressource.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'resource_scenario');
    }

    /**
     * Les campagnes associées à cette ressource.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'resource_campaign');
    }
    /**
     * Les boutiques associées à cette ressource.
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'resource_shop')->withPivot('quantity', 'price', 'comment');
    }
}
