<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property bool $in_menu
 * @property string $state
 * @property int $read_level
 * @property int $write_level
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
        'title',
        'slug',
        'in_menu',
        'state',
        'read_level',
        'write_level',
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
        'read_level' => 'integer',
        'write_level' => 'integer',
    ];

    /**
     * Get the user that created the page.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les sections de cette page.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'page_id')->orderBy('order');
    }

    /**
     * La page parente.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }
    /**
     * Les pages enfants.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id');
    }
    /**
     * Les campagnes associées à cette page.
     */
    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_page');
    }

    /**
     * Les utilisateurs associés à cette page.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'page_user');
    }

    /**
     * Les scénarios associés à cette page.
     */
    public function scenarios(): BelongsToMany
    {
        return $this->belongsToMany(Scenario::class, 'scenario_page');
    }

    // ============================================
    // 🔍 SCOPES
    // ============================================

    /**
     * Scope pour filtrer les pages "jouables" (anciennement published).
     */
    /** @param Builder<Page> $query @return Builder<Page> */
    public function scopePlayable(Builder $query): Builder
    {
        return $query->where('state', self::STATE_PLAYABLE);
    }

    /**
     * Scope pour filtrer les pages dans le menu.
     */
    /** @param Builder<Page> $query @return Builder<Page> */
    public function scopeInMenu(Builder $query): Builder
    {
        return $query->where('in_menu', true);
    }

    /**
     * Scope pour filtrer les pages lisibles pour un utilisateur.
     */
    /** @param Builder<Page> $query @return Builder<Page> */
    public function scopeReadableFor(Builder $query, ?User $user = null): Builder
    {
        $level = $user ? (int) $user->role : User::ROLE_GUEST;

        return $query->where(function ($q) use ($user, $level) {
            $q->where('read_level', '<=', $level);

            if ($user) {
                // Lisible si l'utilisateur est associé à la page
                $q->orWhereHas('users', function ($userQuery) use ($user) {
                    $userQuery->where('users.id', $user->id);
                });
            }
        });
    }

    /**
     * Scope pour trier par ordre de menu.
     */
    /** @param Builder<Page> $query @return Builder<Page> */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('menu_order');
    }

    /**
     * Scope pour récupérer les pages du menu (publiées, dans le menu, visibles, ordonnées).
     */
    /** @param Builder<Page> $query @return Builder<Page> */
    public function scopeForMenu(Builder $query, ?User $user = null): Builder
    {
        return $query->playable()
            ->inMenu()
            ->readableFor($user)
            ->ordered();
    }

    // ============================================
    // 🔧 MÉTHODES HELPER
    // ============================================

    /**
     * Vérifie si la page est "jouable" (affichable publiquement).
     */
    public function isPlayable(): bool
    {
        return $this->state === self::STATE_PLAYABLE;
    }

    /**
     * Vérifie si la page est lisible pour un utilisateur (niveau OU association).
     */
    public function isReadableFor(?User $user = null): bool
    {
        $level = $user ? (int) $user->role : User::ROLE_GUEST;
        if ((int) $this->read_level <= $level) {
            return true;
        }

        if (!$user) {
            return false;
        }

        if (!$this->relationLoaded('users')) {
            try {
                $this->load('users');
            } catch (\Exception $e) {
                return false;
            }
        }

        return $this->relationLoaded('users') && $this->users->contains($user->id);
    }

    /**
     * Vérifie si la page peut être vue par un utilisateur (état + read_level).
     */
    public function canBeViewedBy(?User $user = null): bool
    {
        // Les admins peuvent toujours voir
        if ($user && $user->isAdmin()) {
            return true;
        }

        // Les éditeurs voient tout (draft/raw inclus)
        if ($user && $this->canBeEditedBy($user)) {
            return true;
        }

        // Sinon : uniquement les pages "jouables"
        if (!$this->isPlayable()) {
            return false;
        }

        return $this->isReadableFor($user);
    }

    /**
     * Vérifie si la page peut être modifiée par un utilisateur selon write_level.
     */
    public function canBeEditedBy(?User $user = null): bool
    {
        // Les super_admin peuvent toujours modifier
        if ($user && $user->isSuperAdmin()) {
            return true;
        }

        if (!$user) {
            return false;
        }

        // Si l'utilisateur est l'auteur de la page, il peut la modifier
        if ($this->created_by === $user->id) {
            return true;
        }

        // Si l'utilisateur est associé à la page via la relation users, il peut la modifier
        if (!$this->relationLoaded('users')) {
            try {
                $this->load('users');
            } catch (\Exception $e) {
                // Si la relation ne peut pas être chargée, continuer avec les autres vérifications
            }
        }
        if ($this->relationLoaded('users') && $this->users->contains($user->id)) {
            return true;
        }

        $level = (int) $user->role;
        return $level >= (int) $this->write_level;
    }

    /**
     * Passe la page à l'état "jouable".
     */
    public function publish(): void
    {
        $this->update(['state' => self::STATE_PLAYABLE]);
    }

    /**
     * Archive la page.
     */
    public function archive(): void
    {
        $this->update(['state' => self::STATE_ARCHIVED]);
    }

    /**
     * Remet la page en brouillon.
     */
    public function setDraft(): void
    {
        $this->update(['state' => self::STATE_DRAFT]);
    }
}
