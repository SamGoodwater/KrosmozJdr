<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Type\ConsumableType;
use App\Models\Entity\Resource;
use App\Models\Entity\Creature;
use App\Models\Entity\Scenario;
use App\Models\Entity\Campaign;
use App\Models\Entity\Shop;

/**
 * 
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description
 * @property string|null $effect
 * @property string|null $level
 * @property string|null $recipe
 * @property string|null $price
 * @property int $rarity
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string $dofus_version
 * @property string|null $image
 * @property bool $auto_update
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $consumable_type_id
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read ConsumableType|null $consumableType
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @method static \Database\Factories\Entity\ConsumableFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereConsumableTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereRecipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable withoutTrashed()
 * @mixin \Eloquent
 */
class Consumable extends Model
{
    /** @use HasFactory<\Database\Factories\ConsumableFactory> */
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
        'official_id',
        'dofusdb_id',
        'name',
        'description',
        'effect',
        'level',
        'recipe',
        'price',
        'rarity',
        'state',
        'read_level',
        'write_level',
        'dofus_version',
        'image',
        'auto_update',
        'consumable_type_id',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rarity' => 'integer',
        'read_level' => 'integer',
        'write_level' => 'integer',
        'auto_update' => 'boolean',
    ];

    /**
     * Get the user that created the consumable.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the type of the consumable.
     */
    public function consumableType()
    {
        return $this->belongsTo(ConsumableType::class, 'consumable_type_id');
    }

    /**
     * Les ressources nécessaires à ce consommable.
     */
    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'consumable_resource')->withPivot('quantity');
    }

    /**
     * Les créatures associées à ce consommable.
     */
    public function creatures()
    {
        return $this->belongsToMany(Creature::class, 'consumable_creature')->withPivot('quantity');
    }

    /**
     * Les scénarios associés à ce consommable.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'consumable_scenario');
    }

    /**
     * Les campagnes associées à ce consommable.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'consumable_campaign');
    }

    /**
     * Les boutiques associées à ce consommable.
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'consumable_shop')->withPivot('quantity', 'price', 'comment');
    }
}
