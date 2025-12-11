<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Page;
use App\Models\File;
use App\Enums\PageState;
use App\Enums\Visibility;
use App\Enums\SectionType;

/**
 * Modèle Eloquent Section
 * 
 * Représente une section dynamique appartenant à une page (bloc de contenu, composant Vue).
 * Gère l'ordre, le type, les paramètres dynamiques, la visibilité, l'état, les utilisateurs et fichiers associés.
 * Utilisé pour la construction flexible des pages et la gestion fine des droits d'accès.
 * 
 * Relations : page, users, files, createdBy
 *
 * @property int $id
 * @property int $page_id
 * @property string|null $title
 * @property string|null $slug
 * @property int $order
 * @property string $template
 * @property array<array-key, mixed>|null $settings
 * @property array<array-key, mixed>|null $data
 * @property string $is_visible
 * @property string $can_edit_role
 * @property string $state
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $files
 * @property-read int|null $files_count
 * @property-read \App\Models\Page $page
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\SectionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section withoutTrashed()
 * @mixin \Eloquent
 */
class Section extends Model
{
    /** @use HasFactory<\\Database\\Factories\\SectionFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Les états possibles pour une section.
     * @deprecated Utiliser PageState enum à la place (les sections utilisent les mêmes états que les pages)
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
        'page_id',
        'title',
        'slug',
        'order',
        'template',
        'settings',
        'data',
        'is_visible',
        'can_edit_role',
        'state',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
        'template' => SectionType::class,
        'settings' => 'array',
        'data' => 'array',
        'state' => PageState::class,
        'is_visible' => Visibility::class,
        'can_edit_role' => Visibility::class,
    ];

    /**
     * Get the user that created the section.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the page that owns the section.
     */
    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    /**
     * Les utilisateurs associés à cette section.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'section_user');
    }

    /**
     * Les fichiers liés à la section, triés par ordre.
     */
    public function files()
    {
        return $this->belongsToMany(File::class, 'file_section')
            ->withPivot('order')
            ->orderBy('file_section.order');
    }

    // ============================================
    // 🔍 SCOPES
    // ============================================

    /**
     * Scope pour filtrer les sections publiées.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('state', PageState::PUBLISHED->value);
    }

    /**
     * Scope pour filtrer les sections visibles pour un utilisateur.
     */
    public function scopeVisibleFor(Builder $query, ?User $user = null): Builder
    {
        return $query->where(function ($q) use ($user) {
            // Toujours visible pour les invités
            $q->where('is_visible', Visibility::GUEST->value);

            if ($user) {
                // Visible pour les utilisateurs connectés
                $q->orWhere('is_visible', Visibility::USER->value);

                // Visible selon le rôle
                if (in_array($user->role, [User::ROLE_GAME_MASTER, User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 3, 4, 5, 'game_master', 'admin', 'super_admin'])) {
                    $q->orWhere('is_visible', Visibility::GAME_MASTER->value);
                }

                if (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin'])) {
                    $q->orWhere('is_visible', Visibility::ADMIN->value);
                }

                // Visible si l'utilisateur est associé à la section
                $q->orWhereHas('users', function ($userQuery) use ($user) {
                    $userQuery->where('users.id', $user->id);
                });
            }
        });
    }

    /**
     * Scope pour trier par ordre.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    /**
     * Scope pour récupérer les sections affichables (publiées, visibles, ordonnées).
     */
    public function scopeDisplayable(Builder $query, ?User $user = null): Builder
    {
        return $query->published()
            ->visibleFor($user)
            ->ordered();
    }

    // ============================================
    // 🔧 MÉTHODES HELPER
    // ============================================

    /**
     * Vérifie si la section est publiée.
     */
    public function isPublished(): bool
    {
        return $this->state === PageState::PUBLISHED;
    }

    /**
     * Vérifie si la section est visible pour un utilisateur.
     */
    public function isVisibleFor(?User $user = null): bool
    {
        // is_visible est déjà un enum Visibility grâce au cast, donc on peut l'utiliser directement
        $visibility = $this->is_visible instanceof Visibility 
            ? $this->is_visible 
            : Visibility::tryFrom($this->is_visible);
        if (!$visibility) {
            return false;
        }

        return $visibility->isAccessibleBy($user);
    }

    /**
     * Vérifie si la section peut être vue par un utilisateur (état + visibilité).
     */
    public function canBeViewedBy(?User $user = null): bool
    {
        // Les admins peuvent toujours voir
        if ($user && in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }

        // Doit être publiée (ou en preview pour les auteurs)
        if (!$this->isPublished() && !($user && $this->created_by === $user->id)) {
            return false;
        }

        return $this->isVisibleFor($user);
    }

    /**
     * Publie la section.
     */
    public function publish(): void
    {
        $this->update(['state' => PageState::PUBLISHED->value]);
    }

    /**
     * Archive la section.
     */
    public function archive(): void
    {
        $this->update(['state' => PageState::ARCHIVED->value]);
    }

    /**
     * Vérifie si la section peut être modifiée par un utilisateur selon can_edit_role.
     * 
     * **Logique de vérification :**
     * - Les super_admin peuvent toujours modifier
     * - L'auteur de la section peut modifier sa section (même sans niveau de permission)
     *   → Il ne peut modifier que sa propre section, pas les autres sections de la page
     * - Les utilisateurs associés à la section peuvent modifier (mais doivent avoir les droits sur la page)
     * - Sinon : l'utilisateur doit avoir des droits supérieurs ou égaux au `can_edit_role` de la section
     *   ET des droits supérieurs ou égaux au `can_edit_role` de la page parente
     * 
     * @param User|null $user Utilisateur (null pour invité)
     * @return bool True si l'utilisateur peut modifier la section
     */
    public function canBeEditedBy(?User $user = null): bool
    {
        // Les super_admin peuvent toujours modifier
        if ($user && in_array($user->role, [User::ROLE_SUPER_ADMIN, 5, 'super_admin'])) {
            return true;
        }

        if (!$user) {
            return false;
        }

        // Si l'utilisateur est l'auteur de la section, il peut la modifier
        // (même sans avoir le niveau de permission requis sur la section ou la page)
        // L'auteur peut modifier uniquement sa propre section, pas les autres sections de la page
        if ($this->created_by === $user->id) {
            return true;
        }

        // Si l'utilisateur est associé à la section via la relation users, il peut la modifier
        // (même sans avoir le niveau de permission requis)
        // MAIS il faut vérifier les droits sur la page parente
        // car l'utilisateur associé peut modifier cette section, mais doit pouvoir modifier la page
        if (!$this->relationLoaded('users')) {
            try {
                $this->load('users');
            } catch (\Exception $e) {
                // Si la relation ne peut pas être chargée, continuer avec les autres vérifications
            }
        }
        if ($this->relationLoaded('users') && $this->users->contains($user->id)) {
            // Charger la page si nécessaire pour vérifier les droits
            if (!$this->relationLoaded('page')) {
                try {
                    $this->load('page');
                } catch (\Exception $e) {
                    // Si la page ne peut pas être chargée, on considère que l'utilisateur associé peut modifier
                    return true;
                }
            }
            
            // Vérifier les droits sur la page
            if ($this->relationLoaded('page') && $this->page) {
                return $this->page->canBeEditedBy($user);
            }
            
            return true;
        }

        // Vérifier selon can_edit_role de la section
        $sectionEditRole = $this->can_edit_role instanceof Visibility 
            ? $this->can_edit_role 
            : Visibility::tryFrom($this->can_edit_role ?? 'admin');
        if (!$sectionEditRole) {
            return false;
        }

        // L'utilisateur doit avoir les droits sur la section
        if (!$sectionEditRole->isAccessibleBy($user)) {
            return false;
        }

        // Vérifier AUSSI les droits sur la page parente
        // Charger la page si nécessaire
        if (!$this->relationLoaded('page')) {
            try {
                $this->load('page');
            } catch (\Exception $e) {
                // Si la page ne peut pas être chargée, on considère que c'est OK si les droits sur la section sont OK
                return true;
            }
        }
        
        // Si la page est chargée, vérifier les droits sur la page
        if ($this->relationLoaded('page') && $this->page) {
            return $this->page->canBeEditedBy($user);
        }
        
        // Si la page n'est pas chargée, on retourne true car les droits sur la section sont OK
        return true;
    }
}
