<?php

namespace App\Models\Type;

use App\Models\CharacteristicObject;
use App\Models\Entity\Item;
use App\Models\Type\Concerns\HasTypeRegistryFlags;
use App\Models\User;
use Database\Factories\ItemTypeFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 * @method static \Database\Factories\Type\ItemTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType withoutTrashed()
 * @property int|null $dofusdb_type_id
 * @property string $decision
 * @property int $seen_count
 * @property Carbon|null $last_seen_at
 * @property-read Collection<int, CharacteristicObject> $allowedCharacteristicObjects
 * @property-read int|null $allowed_characteristic_objects_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType allowed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType blocked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereDofusdbTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereSeenCount($value)
 * @property bool|null $show_in_catalog
 * @property bool|null $allow_scrap
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType allowScrap()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType visibleInCatalog()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereAllowScrap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereShowInCatalog($value)
 * @mixin \Eloquent
 */
class ItemType extends Model
{
    /** @use HasFactory<ItemTypeFactory> */
    use HasFactory, HasTypeRegistryFlags, SoftDeletes;

    public const STATE_RAW = 'raw';

    public const STATE_DRAFT = 'draft';

    public const STATE_AUTO = 'auto';

    public const STATE_PLAYABLE = 'playable';

    public const STATE_ARCHIVED = 'archived';

    public const DECISION_PENDING = 'pending';

    public const DECISION_ALLOWED = 'allowed';

    public const DECISION_BLOCKED = 'blocked';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'show_in_catalog' => false,
        'allow_scrap' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'dofusdb_type_id',
        'decision',
        'seen_count',
        'last_seen_at',
        'show_in_catalog',
        'allow_scrap',
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
        'read_level' => 'integer',
        'write_level' => 'integer',
        'dofusdb_type_id' => 'integer',
        'seen_count' => 'integer',
        'last_seen_at' => 'datetime',
        'show_in_catalog' => 'boolean',
        'allow_scrap' => 'boolean',
    ];

    /**
     * Scope: types en attente de validation UX.
     */
    public function scopePending($query)
    {
        return $query->where('decision', self::DECISION_PENDING);
    }

    /**
     * Scope: types bloqués (blacklist).
     */
    public function scopeBlocked($query)
    {
        return $query->where('decision', self::DECISION_BLOCKED);
    }

    /**
     * Indique si un typeId DofusDB est explicitement autorisé au scrap.
     *
     * Comportement:
     * - Si le type n'existe pas encore, il est créé en `allow_scrap=false` et la méthode retourne false.
     */
    public static function isDofusdbTypeAllowed(int $typeId): bool
    {
        $type = static::where('dofusdb_type_id', $typeId)->first();

        if (! $type) {
            static::touchDofusdbType($typeId);

            return false;
        }

        return (bool) $type->allow_scrap;
    }

    /**
     * Enregistre/actualise un typeId DofusDB détecté (pour revue dans le dashboard).
     *
     * @param  string|null  $label  Libellé optionnel pour initialiser ou améliorer `name`.
     */
    public static function touchDofusdbType(int $typeId, ?string $label = null): static
    {
        $placeholderName = "DofusDB type #{$typeId}";
        $name = $label ?: $placeholderName;

        /** @var static $type */
        $type = static::firstOrCreate(
            ['dofusdb_type_id' => $typeId],
            [
                'name' => $name,
                // On garde les colonnes historiques, mais la validation se fait via `decision`.
                'usable' => 0,
                'is_visible' => 'guest',
                'decision' => self::DECISION_PENDING,
                'allow_scrap' => false,
                'show_in_catalog' => false,
                'seen_count' => 0,
                'created_by' => User::getSystemUser()?->id,
            ]
        );

        // Si on a un meilleur label et que le nom actuel est un placeholder, on remplace
        if ($label && $type->name === $placeholderName) {
            $type->name = $label;
        }

        $type->seen_count = (int) ($type->seen_count ?? 0) + 1;
        $type->last_seen_at = now();
        $type->save();

        return $type;
    }

    /**
     * Get the user that created the item type.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les objets de ce type.
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'item_type_id');
    }

    /**
     * Définitions de caractéristiques (groupe object) qui sont réservées à ce type d'équipement.
     *
     * @return BelongsToMany<CharacteristicObject, self>
     */
    public function allowedCharacteristicObjects(): BelongsToMany
    {
        return $this->belongsToMany(CharacteristicObject::class, 'characteristic_object_item_type')
            ->withTimestamps();
    }
}
