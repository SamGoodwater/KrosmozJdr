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
use App\Enums\PageState;
use App\Enums\Visibility;

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
 * @property \App\Enums\Visibility $is_visible
 * @property bool $in_menu
 * @property \App\Enums\Visibility $can_edit_role
 * @property \App\Enums\PageState $state
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
     * @deprecated Utiliser PageState enum à la place
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
        'can_edit_role',
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
        'state' => PageState::class,
        'is_visible' => Visibility::class,
        'can_edit_role' => Visibility::class,
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
     * Scope pour filtrer les pages publiées.
     */
    /** @param Builder<Page> $query @return Builder<Page> */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('state', PageState::PUBLISHED->value);
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
     * Scope pour filtrer les pages visibles pour un utilisateur.
     */
    /** @param Builder<Page> $query @return Builder<Page> */
    public function scopeVisibleFor(Builder $query, ?User $user = null): Builder
    {
        $allowedVisibilities = [Visibility::GUEST->value];

        if ($user) {
            $allowedVisibilities[] = Visibility::USER->value;

            if ($user->isGameMaster()) {
                $allowedVisibilities[] = Visibility::GAME_MASTER->value;
            }

            if ($user->isAdmin()) {
                $allowedVisibilities[] = Visibility::ADMIN->value;
            }
        }

        $allowedVisibilities = array_values(array_unique($allowedVisibilities));

        return $query->where(function ($q) use ($user, $allowedVisibilities) {
            $q->whereIn('is_visible', $allowedVisibilities);

            if ($user) {
                // Visible si l'utilisateur est associé à la page
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
        return $query->published()
            ->inMenu()
            ->visibleFor($user)
            ->ordered();
    }

    // ============================================
    // 🔧 MÉTHODES HELPER
    // ============================================

    /**
     * Vérifie si la page est publiée.
     */
    public function isPublished(): bool
    {
        return $this->state === PageState::PUBLISHED;
    }

    /**
     * Vérifie si la page est visible pour un utilisateur.
     */
    public function isVisibleFor(?User $user = null): bool
    {
        return $this->is_visible->isAccessibleBy($user);
    }

    /**
     * Vérifie si la page peut être vue par un utilisateur (état + visibilité).
     */
    public function canBeViewedBy(?User $user = null): bool
    {
        // Les admins peuvent toujours voir
        if ($user && $user->isAdmin()) {
            return true;
        }

        // Doit être publiée (ou en preview pour les auteurs)
        if (!$this->isPublished() && !($user && $this->created_by === $user->id)) {
            return false;
        }

        return $this->isVisibleFor($user);
    }

    /**
     * Vérifie si la page peut être modifiée par un utilisateur selon can_edit_role.
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
        // Charger la relation si elle n'est pas déjà chargée
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

        return $this->can_edit_role->isAccessibleBy($user);
    }

    /**
     * Publie la page.
     */
    public function publish(): void
    {
        $this->update(['state' => PageState::PUBLISHED->value]);
    }

    /**
     * Archive la page.
     */
    public function archive(): void
    {
        $this->update(['state' => PageState::ARCHIVED->value]);
    }

    /**
     * Met la page en prévisualisation.
     */
    public function setPreview(): void
    {
        $this->update(['state' => PageState::PREVIEW->value]);
    }

    /**
     * Remet la page en brouillon.
     */
    public function setDraft(): void
    {
        $this->update(['state' => PageState::DRAFT->value]);
    }
}
