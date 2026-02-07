<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Type\ItemType;
use App\Models\Entity\Resource;
use App\Models\Entity\Panoply;
use App\Models\Entity\Campaign;
use App\Models\Entity\Shop;
use App\Models\Entity\Scenario;
use App\Models\Concerns\HasEntityImageMedia;
use Spatie\MediaLibrary\HasMedia;

/**
 * 
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $name
 * @property string|null $level
 * @property string|null $description
 * @property string|null $effect
 * @property string|null $bonus
 * @property string|null $recipe
 * @property string|null $price
 * @property int $rarity
 * @property string $dofus_version
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property bool $auto_update
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $item_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read User|null $createdBy
 * @property-read ItemType|null $itemType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @method static \Database\Factories\Entity\ItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereItemTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRecipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item withoutTrashed()
 * @mixin \Eloquent
 */
class Item extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory, SoftDeletes, HasEntityImageMedia;

    public const STATE_RAW = 'raw';
    public const STATE_DRAFT = 'draft';
    public const STATE_PLAYABLE = 'playable';
    public const STATE_ARCHIVED = 'archived';

    /** Répertoire Media Library pour ce modèle. */
    public const MEDIA_PATH = 'images/entity/items';

    /** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */
    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'official_id',
        'dofusdb_id',
        'name',
        'level',
        'description',
        'effect',
        'bonus',
        'recipe',
        'price',
        'rarity',
        'dofus_version',
        'state',
        'read_level',
        'write_level',
        'image',
        'auto_update',
        'item_type_id',
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
     * Get the user that created the item.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the type of the item.
     */
    public function itemType()
    {
        return $this->belongsTo(ItemType::class, 'item_type_id');
    }

    /**
     * Les ressources nécessaires à cet objet.
     */
    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'item_resource')->withPivot('quantity');
    }

    /**
     * Les panoplies associées à cet objet.
     */
    public function panoplies()
    {
        return $this->belongsToMany(Panoply::class, 'item_panoply');
    }

    /**
     * Les scénarios associés à cet objet.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'item_scenario');
    }

    /**
     * Les campagnes associées à cet objet.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'item_campaign');
    }

    /**
     * Les boutiques associées à cet objet.
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'item_shop')->withPivot('quantity', 'price', 'comment');
    }
}
