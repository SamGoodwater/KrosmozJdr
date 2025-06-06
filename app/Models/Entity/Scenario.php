<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Page;
use App\Models\Entity\Campaign;
use App\Models\Entity\Npc;
use App\Models\Entity\Monster;
use App\Models\Entity\Item;
use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use App\Models\Entity\Shop;
use App\Models\Entity\Spell;
use App\Models\Entity\Panoply;
use App\Models\Type\ScenarioLink;
use App\Models\File;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $slug
 * @property string|null $keyword
 * @property bool $is_public
 * @property int $state
 * @property int $usable
 * @property string $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ScenarioLink> $scenarioLinks
 * @property-read int|null $scenario_links_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\Entity\ScenarioFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario withoutTrashed()
 * @mixin \Eloquent
 */
class Scenario extends Model
{
    /** @use HasFactory<\\Database\\Factories\\ScenarioFactory> */
    use HasFactory, SoftDeletes;

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
        'state',
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
        'is_public' => 'boolean',
        'state' => 'integer',
        'usable' => 'integer',
    ];

    /**
     * Get the user that created the scenario.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les utilisateurs associés à ce scénario.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'scenario_user');
    }
    /**
     * Les pages associées à ce scénario.
     */
    public function pages()
    {
        return $this->belongsToMany(Page::class, 'scenario_page');
    }
    /**
     * Les campagnes associées à ce scénario.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_scenario');
    }
    /**
     * Les PNJ associés à ce scénario.
     */
    public function npcs()
    {
        return $this->belongsToMany(Npc::class, 'npc_scenario');
    }
    /**
     * Les monstres associés à ce scénario.
     */
    public function monsters()
    {
        return $this->belongsToMany(Monster::class, 'monster_scenario');
    }
    /**
     * Les objets associés à ce scénario.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_scenario');
    }
    /**
     * Les consommables associés à ce scénario.
     */
    public function consumables()
    {
        return $this->belongsToMany(Consumable::class, 'consumable_scenario');
    }
    /**
     * Les ressources associées à ce scénario.
     */
    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'resource_scenario');
    }
    /**
     * Les boutiques associées à ce scénario.
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'scenario_shop');
    }
    /**
     * Les sorts associés à ce scénario.
     */
    public function spells()
    {
        return $this->belongsToMany(Spell::class, 'scenario_spell');
    }
    /**
     * Les panoplies associées à ce scénario.
     */
    public function panoplies()
    {
        return $this->belongsToMany(Panoply::class, 'scenario_panoply');
    }
    /**
     * Les liens de scénario (scénario -> next_scenario).
     */
    public function scenarioLinks()
    {
        return $this->hasMany(ScenarioLink::class, 'scenario_id');
    }
    /**
     * Les fichiers liés à ce scénario, triés par ordre.
     */
    public function files()
    {
        return $this->belongsToMany(File::class, 'file_scenario')
            ->withPivot('order')
            ->orderBy('file_scenario.order');
    }
}
