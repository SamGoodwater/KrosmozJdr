<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Page;
use App\Models\File;
use App\Models\Entity\Scenario;
use App\Models\Entity\Item;
use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use App\Models\Entity\Shop;
use App\Models\Entity\Npc;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\Entity\Panoply;


/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $slug
 * @property string|null $keyword
 * @property int $is_public
 * @property int $progress_state
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, File> $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Page> $pages
 * @property-read int|null $pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\Entity\CampaignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereProgressState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withoutTrashed()
 * @mixin \Eloquent
 */
class Campaign extends Model
{
    /** @use HasFactory<\Database\Factories\Entity\CampaignFactory> */
    use HasFactory, SoftDeletes;

    public const STATE_RAW = 'raw';
    public const STATE_DRAFT = 'draft';
    public const STATE_PLAYABLE = 'playable';
    public const STATE_ARCHIVED = 'archived';

    public const PROGRESS_STATES = [
        0 => 'En cours',
        1 => 'Terminée',
        2 => 'En pause',
        3 => 'Annulée',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'slug',
        'keyword',
        'is_public',
        'progress_state',
        'state',
        'read_level',
        'write_level',
        'image',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
        'progress_state' => 'integer',
        'read_level' => 'integer',
        'write_level' => 'integer',
    ];

    /**
     * Get the user that created the campaign.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les utilisateurs associés à cette campagne.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'campaign_user');
    }
    /**
     * Les scénarios de cette campagne.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'campaign_scenario');
    }
    /**
     * Les pages de cette campagne.
     */
    public function pages()
    {
        return $this->belongsToMany(Page::class, 'campaign_page');
    }
    /**
     * Les objets de cette campagne.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_campaign');
    }
    /**
     * Les consommables de cette campagne.
     */
    public function consumables()
    {
        return $this->belongsToMany(Consumable::class, 'consumable_campaign');
    }
    /**
     * Les ressources de cette campagne.
     */
    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'resource_campaign');
    }
    /**
     * Les boutiques de cette campagne.
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'campaign_shop');
    }
    /**
     * Les PNJ de cette campagne.
     */
    public function npcs()
    {
        return $this->belongsToMany(Npc::class, 'npc_campaign');
    }
    /**
     * Les monstres de cette campagne.
     */
    public function monsters()
    {
        return $this->belongsToMany(Monster::class, 'monster_campaign');
    }
    /**
     * Les sorts de cette campagne.
     */
    public function spells()
    {
        return $this->belongsToMany(Spell::class, 'campaign_spell');
    }
    /**
     * Les panoplies de cette campagne.
     */
    public function panoplies()
    {
        return $this->belongsToMany(Panoply::class, 'campaign_panoply');
    }
    /**
     * Les fichiers liés à cette campagne, triés par ordre.
     */
    public function files()
    {
        return $this->belongsToMany(File::class, 'file_campaign')
            ->withPivot('order')
            ->orderBy('file_campaign.order');
    }
}
