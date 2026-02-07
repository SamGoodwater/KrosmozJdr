<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\Page;
use App\Enums\SectionType;
use App\Models\Concerns\HasMediaCustomNaming;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Modèle Eloquent Section
 * 
 * Représente une section dynamique appartenant à une page (bloc de contenu, composant Vue).
 * Gère l'ordre, le type, les paramètres dynamiques, la visibilité, l'état, les utilisateurs et fichiers associés.
 * Utilisé pour la construction flexible des pages et la gestion fine des droits d'accès.
 * 
 * Relations : page, users, createdBy ; médias via Media Library (collection files)
 *
 * @property int $id
 * @property int $page_id
 * @property string|null $title
 * @property string|null $slug
 * @property int $order
 * @property \App\Enums\SectionType $template
 * @property array<array-key, mixed>|null $settings
 * @property array<array-key, mixed>|null $data
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Media> $media
 * @property-read int|null $media_count
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
class Section extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\SectionFactory> */
    use HasFactory, SoftDeletes, InteractsWithMedia, HasMediaCustomNaming;

    public const STATE_RAW = 'raw';
    public const STATE_DRAFT = 'draft';
    public const STATE_PLAYABLE = 'playable';
    public const STATE_ARCHIVED = 'archived';
    
    /** Répertoire Media Library pour ce modèle. */
    public const MEDIA_PATH = 'sections/files';

    /** Motif de nommage pour la collection files (placeholders: [name], [date], [id], [uniqid]). */
    public const MEDIA_FILE_PATTERN_FILES = '[id]-[date]-[uniqid]';

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
        'type',
        'settings',
        'data',
        'params',
        'state',
        'read_level',
        'write_level',
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
        'type' => SectionType::class,
        'settings' => 'array',
        'data' => 'array',
        'params' => 'array',
        'read_level' => 'integer',
        'write_level' => 'integer',
    ];

    /**
     * Get the user that created the section.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the page that owns the section.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    /**
     * Les utilisateurs associés à cette section.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'section_user');
    }

    /**
     * Enregistre les conversions média (WebP + miniature) pour la collection "files".
     * Les images sont converties en WebP pour réduire la taille et servies en miniature.
     *
     * @see https://spatie.be/docs/laravel-medialibrary/v11/converting-images/defining-conversions
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // En test, pas de conversions pour éviter les échecs avec Storage::fake()
        if (app()->environment('testing')) {
            return;
        }

        $this->addMediaConversion('thumb')
            ->performOnCollections('files')
            ->width(368)
            ->height(232)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('webp')
            ->performOnCollections('files')
            ->format('webp')
            ->nonQueued();
    }

    // ============================================
    // 🔍 SCOPES
    // ============================================

    /**
     * Scope pour filtrer les sections jouables.
     */
    /** @param Builder<Section> $query @return Builder<Section> */
    public function scopePlayable(Builder $query): Builder
    {
        return $query->where('state', self::STATE_PLAYABLE);
    }

    /**
     * Alias historique (préférer `playable()`).
     */
    /** @param Builder<Section> $query @return Builder<Section> */
    public function scopePublished(Builder $query): Builder
    {
        return $this->scopePlayable($query);
    }

    /**
     * Scope pour filtrer les sections lisibles pour un utilisateur.
     */
    /** @param Builder<Section> $query @return Builder<Section> */
    public function scopeReadableFor(Builder $query, ?User $user = null): Builder
    {
        $level = $user ? (int) $user->role : User::ROLE_GUEST;

        return $query->where(function ($q) use ($user, $level) {
            $q->where('read_level', '<=', $level);

            if ($user) {
                $q->orWhereHas('users', function ($userQuery) use ($user) {
                    $userQuery->where('users.id', $user->id);
                });
            }
        });
    }

    /**
     * Scope pour trier par ordre.
     */
    /** @param Builder<Section> $query @return Builder<Section> */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    /**
     * Scope pour récupérer les sections affichables (publiées, visibles, ordonnées).
     */
    /** @param Builder<Section> $query @return Builder<Section> */
    public function scopeDisplayable(Builder $query, ?User $user = null): Builder
    {
        return $query->playable()
            ->readableFor($user)
            ->ordered();
    }

    // ============================================
    // 🔧 MÉTHODES HELPER
    // ============================================

    /**
     * Vérifie si la section est publiée.
     */
    public function isPlayable(): bool
    {
        return $this->state === self::STATE_PLAYABLE;
    }

    /**
     * Vérifie si la section est lisible pour un utilisateur.
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
     * Vérifie si la section peut être vue par un utilisateur (état + visibilité).
     */
    public function canBeViewedBy(?User $user = null): bool
    {
        // Les admins peuvent toujours voir
        if ($user && $user->isAdmin()) {
            return true;
        }

        // Auteurs/éditeurs voient leurs drafts/raw
        if ($user && $this->canBeEditedBy($user)) {
            return true;
        }

        // Sinon : uniquement "jouable"
        if (!$this->isPlayable()) {
            return false;
        }

        return $this->isReadableFor($user);
    }

    /**
     * Publie la section.
     */
    public function publish(): void
    {
        $this->update(['state' => self::STATE_PLAYABLE]);
    }

    /**
     * Archive la section.
     */
    public function archive(): void
    {
        $this->update(['state' => self::STATE_ARCHIVED]);
    }

    /**
     * Vérifie si la section peut être modifiée par un utilisateur selon write_level,
     * et en respectant aussi les droits d'écriture sur la page parente.
     * 
     * **Logique de vérification :**
     * - Les super_admin peuvent toujours modifier
     * - L'auteur de la section peut modifier sa section (même sans niveau de permission)
     *   → Il ne peut modifier que sa propre section, pas les autres sections de la page
     * - Les utilisateurs associés à la section peuvent modifier (mais doivent avoir les droits sur la page)
     * - Sinon : l'utilisateur doit avoir un niveau \(\ge\) `write_level` de la section
     *   ET pouvoir modifier la page parente (selon ses propres règles d'écriture)
     * 
     * @param User|null $user Utilisateur (null pour invité)
     * @return bool True si l'utilisateur peut modifier la section
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

        // Vérifier selon write_level de la section
        $level = (int) $user->role;
        if ($level < (int) $this->write_level) {
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
