<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Section;
use App\Models\Entity\Campaign;
use App\Models\Entity\Scenario;

/**
 * Modèle Eloquent Page
 * 
 * Représente une page dynamique du site (menu, arborescence, sections, droits, etc.).
 * Gère la hiérarchie, la visibilité, l'état, les utilisateurs associés, les campagnes et scénarios liés.
 * Utilisé pour la construction dynamique du contenu et la gestion des droits d'accès.
 * 
 * Relations : sections, parent, children, users, campaigns, scenarios, createdBy
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $is_visible
 * @property bool $in_menu
 * @property string $state
 * @property int|null $parent_id
 * @property int $menu_order
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Page> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read Page|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\PageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereInMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereMenuOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @mixin \Eloquent
 */
class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Les états possibles pour une page.
     */
    const STATES = [
        'brouillon' => 0,
        'prévisualisation' => 1,
        'publié' => 2,
        'archivé' => 3,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'is_visible',
        'in_menu',
        'state',
        'parent_id',
        'menu_order',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'in_menu' => 'boolean',
    ];

    /**
     * Get the user that created the page.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les sections de cette page.
     */
    public function sections()
    {
        return $this->hasMany(Section::class, 'page_id')->orderBy('order');
    }

    /**
     * La page parente.
     */
    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }
    /**
     * Les pages enfants.
     */
    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }
    /**
     * Les campagnes associées à cette page.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_page');
    }

    /**
     * Les utilisateurs associés à cette page.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'page_user');
    }

    /**
     * Les scénarios associés à cette page.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'scenario_page');
    }
}
