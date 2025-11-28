<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\Entity\Attribute;
use App\Models\Entity\Item;
use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use App\Models\Entity\Capability;
use App\Models\Entity\Classe;
use App\Models\Entity\Specialization;
use App\Models\Entity\Shop;
use App\Models\Entity\Scenario;
use App\Models\Entity\Panoply;
use App\Models\Type\MonsterRace;
use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Models\Type\ResourceType;
use App\Models\Type\SpellType;
use App\Models\Entity\Spell;
use App\Models\Entity\Campaign;
use App\Models\Page;
use App\Models\Section;

/**
 * Modèle User central du projet Krosmoz JDR.
 * 
 * Gère l'authentification, les rôles, l'avatar, les notifications et les relations avec les entités du jeu.
 * 
 * Champs principaux :
 * - id, name, email, password, role, avatar
 * - notifications_enabled, notification_channels
 * 
 * Relations :
 * - scénarios, campagnes, pages, sections, et entités créées
 *
 * @property int $id Identifiant unique
 * @property string $name Nom d'utilisateur
 * @property string $email Email
 * @property string $password Mot de passe (hashé)
 * @property string $role Rôle (voir self::ROLES)
 * @property string|null $avatar Chemin de l'avatar ou null
 * @property bool $notifications_enabled Notifications activées ?
 * @property array $notification_channels Canaux de notification
 * @method bool wantsNotification(string $type = null) L'utilisateur veut-il des notifications ?
 * @method array notificationChannels() Retourne les canaux de notification
 * @method bool wantsProfileNotification() Toujours true (modif profil)
 * @method string avatarPath() URL de l'avatar (jamais null)
 * @method bool verifyRole(string|int $role) Possède au moins le rôle donné
 * @method bool updateRole(User $user) Peut-il modifier le rôle d'un autre ?
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Page> $createdPages
 * @property-read int|null $created_pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $createdSections
 * @property-read int|null $created_sections_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Page> $pages
 * @property-read int|null $pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNotificationChannels($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNotificationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Attribute> $createdAttributes
 * @property-read int|null $created_attributes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Capability> $createdCapabilities
 * @property-read int|null $created_capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Classe> $createdClasses
 * @property-read int|null $created_classes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ConsumableType> $createdConsumableTypes
 * @property-read int|null $created_consumable_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Consumable> $createdConsumables
 * @property-read int|null $created_consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ItemType> $createdItemTypes
 * @property-read int|null $created_item_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $createdItems
 * @property-read int|null $created_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MonsterRace> $createdMonsterRaces
 * @property-read int|null $created_monster_races_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Panoply> $createdPanoplies
 * @property-read int|null $created_panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ResourceType> $createdResourceTypes
 * @property-read int|null $created_resource_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $createdResources
 * @property-read int|null $created_resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $createdScenarios
 * @property-read int|null $created_scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $createdShops
 * @property-read int|null $created_shops_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Specialization> $createdSpecializations
 * @property-read int|null $created_specializations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SpellType> $createdSpellTypes
 * @property-read int|null $created_spell_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $createdSpells
 * @property-read int|null $created_spells_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read string $role_name
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    const ROLES = [
        0 => 'guest', // Visiteur non connecté
        1 => 'user', // Utilisateur inscrit
        2 => 'player', // Joueur participant à une campagne/scénario
        3 => 'game_master', // Meneur de jeu
        4 => 'admin', // Administrateur
        5 => 'super_admin', // Administrateur suprême (unique)
    ];

    const ROLE_GUEST = 0; // Visiteur non connecté
    const ROLE_USER = 1; // Utilisateur inscrit
    const ROLE_PLAYER = 2; // Joueur participant à une campagne/scénario
    const ROLE_GAME_MASTER = 3; // Meneur de jeu
    const ROLE_ADMIN = 4; // Administrateur
    const ROLE_SUPER_ADMIN = 5; // Administrateur suprême (unique)

    const NOTIFICATION_CHANNELS = ['database', 'email'];

    public const DEFAULT_AVATAR = 'storage/images/avatar/default_avatar_head.webp';
    
    /**
     * ID de l'utilisateur système (pour les imports automatiques)
     * Note: L'ID réel peut varier, on utilise l'email pour l'identifier
     */
    const SYSTEM_USER_ID = 0; // ID théorique (peut ne pas être utilisé si auto-increment)
    const SYSTEM_USER_EMAIL = 'system@krosmozjdr.local'; // Email unique pour identifier l'utilisateur système

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'notifications_enabled',
        'notification_channels',
        'is_system',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'notifications_enabled' => 'boolean',
        'notification_channels' => 'array',
        'is_system' => 'boolean',
    ];

    /**
     * Retourne true si l'utilisateur souhaite recevoir des notifications (hors notification de profil).
     *
     * @param string|null $type Type de notification (optionnel)
     * @return bool
     */
    public function wantsNotification(?string $type = null): bool
    {
        return $this->notifications_enabled;
    }

    /**
     * Retourne la liste des canaux de notification préférés de l'utilisateur.
     *
     * @return array Liste des canaux (ex: ['database', 'email'])
     */
    public function notificationChannels(): array
    {
        return $this->notification_channels ?? ['database'];
    }

    /**
     * Retourne toujours true : l'utilisateur doit toujours être notifié pour la modification de son profil.
     *
     * @return bool
     */
    public function wantsProfileNotification(): bool
    {
        return true;
    }

    /**
     * Retourne l'URL de l'avatar de l'utilisateur (jamais null).
     *
     * @return string URL absolue de l'avatar
     */
    public function avatarPath(): string
    {
        if (!$this->avatar) {
            return asset(self::DEFAULT_AVATAR);
        }
        // Si le chemin commence déjà par 'storage/', utiliser asset() directement
        // Sinon, utiliser Storage::url() qui ajoute '/storage/'
        if (str_starts_with($this->avatar, 'storage/')) {
            return asset($this->avatar);
        }
        return Storage::url($this->avatar);
    }

    /**
     * Retourne le nom du rôle de l'utilisateur.
     *
     * @return string Nom du rôle
     */
    public function getRoleNameAttribute(): string
    {
        return self::ROLES[$this->role] ?? 'unknown';
    }

    /**
     * Retourne la valeur entière du rôle (par nom ou par valeur).
     *
     * @param string|int $role Nom du rôle (ex: 'admin') ou valeur entière (ex: 4)
     * @return int|null Valeur entière du rôle ou null si invalide
     */
    public static function roleValue(string|int $role): ?int
    {
        if (is_int($role)) {
            return array_key_exists($role, self::ROLES) ? $role : null;
        }
        return array_search($role, self::ROLES, true) !== false ? array_search($role, self::ROLES, true) : null;
    }

    /**
     * Vérifie si l'utilisateur possède au moins le rôle donné (par nom ou par valeur).
     *
     * @param string|int $role Nom du rôle (ex: 'admin') ou valeur entière (ex: 4)
     * @return bool
     */
    public function verifyRole(string|int $role): bool
    {
        $roleValue = self::roleValue($role);
        if ($roleValue === null || !is_int($roleValue)) return false;
        if ($this->role === array_key_last(self::ROLES)) return true; // super_admin
        return $this->role >= $roleValue;
    }

    /**
     * Vérifie si l'utilisateur peut modifier le rôle d'un autre utilisateur.
     *
     * @param User $user Utilisateur à modifier
     * @return bool
     */
    public function updateRole(User $user): bool
    {
        // Seuls les admins et super_admins peuvent modifier les rôles
        return $this->verifyRole(User::ROLE_ADMIN) && // admin = 4
            // Un admin ne peut pas modifier le rôle d'un super_admin
            $user->role !== User::ROLE_SUPER_ADMIN && // super_admin = 5
            // Un admin ne peut pas se modifier lui-même
            $this->id !== $user->id;
    }

    /**
     * Relations: entités créées par l'utilisateur (hasMany)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdAttributes()
    {
        return $this->hasMany(Attribute::class, 'created_by');
    }
    public function createdItems()
    {
        return $this->hasMany(Item::class, 'created_by');
    }
    public function createdConsumables()
    {
        return $this->hasMany(Consumable::class, 'created_by');
    }
    public function createdResources()
    {
        return $this->hasMany(Resource::class, 'created_by');
    }
    public function createdCapabilities()
    {
        return $this->hasMany(Capability::class, 'created_by');
    }
    public function createdClasses()
    {
        return $this->hasMany(Classe::class, 'created_by');
    }
    public function createdSpecializations()
    {
        return $this->hasMany(Specialization::class, 'created_by');
    }
    public function createdMonsterRaces()
    {
        return $this->hasMany(MonsterRace::class, 'created_by');
    }
    public function createdShops()
    {
        return $this->hasMany(Shop::class, 'created_by');
    }
    public function createdScenarios()
    {
        return $this->hasMany(Scenario::class, 'created_by');
    }
    public function createdSections()
    {
        return $this->hasMany(Section::class, 'created_by');
    }
    public function createdPanoplies()
    {
        return $this->hasMany(Panoply::class, 'created_by');
    }
    public function createdSpellTypes()
    {
        return $this->hasMany(SpellType::class, 'created_by');
    }
    public function createdConsumableTypes()
    {
        return $this->hasMany(ConsumableType::class, 'created_by');
    }
    public function createdItemTypes()
    {
        return $this->hasMany(ItemType::class, 'created_by');
    }
    public function createdResourceTypes()
    {
        return $this->hasMany(ResourceType::class, 'created_by');
    }
    public function createdSpells()
    {
        return $this->hasMany(Spell::class, 'created_by');
    }
    public function createdPages()
    {
        return $this->hasMany(Page::class, 'created_by');
    }

    /**
     * Relations: scénarios auxquels l'utilisateur participe (belongsToMany)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'scenario_user');
    }

    /**
     * Relations: campagnes auxquelles l'utilisateur participe (belongsToMany)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_user');
    }

    /**
     * Relations: pages auxquelles l'utilisateur participe (belongsToMany)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pages()
    {
        return $this->belongsToMany(Page::class, 'page_user');
    }

    /**
     * Relations: sections auxquelles l'utilisateur participe (belongsToMany)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_user');
    }

    /**
     * Vérifie si l'utilisateur peut se connecter
     * Les utilisateurs système ne peuvent pas se connecter
     * 
     * @return bool
     */
    public function canLogin(): bool
    {
        return !$this->is_system;
    }

    /**
     * Récupère l'utilisateur système (pour les imports automatiques)
     * 
     * @return User|null
     */
    public static function getSystemUser(): ?User
    {
        return static::where('email', self::SYSTEM_USER_EMAIL)->first();
    }
}
