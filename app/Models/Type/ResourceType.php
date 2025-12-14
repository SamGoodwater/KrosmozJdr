<?php

namespace App\Models\Type;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Entity\Resource;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $usable
 * @property string $is_visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @method static \Database\Factories\Type\ResourceTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType withoutTrashed()
 * @mixin \Eloquent
 */
class ResourceType extends Model
{
    /** @use HasFactory<\Database\Factories\ResourceTypeFactory> */
    use HasFactory, SoftDeletes;

    public const DECISION_PENDING = 'pending';
    public const DECISION_ALLOWED = 'allowed';
    public const DECISION_BLOCKED = 'blocked';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'dofusdb_type_id',
        'usable',
        'is_visible',
        'decision',
        'seen_count',
        'last_seen_at',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'usable' => 'integer',
        'dofusdb_type_id' => 'integer',
        'seen_count' => 'integer',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Scope: types explicitement autorisés (whitelist).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @example
     * ResourceType::query()->allowed()->get();
     */
    public function scopeAllowed($query)
    {
        return $query->where('decision', self::DECISION_ALLOWED);
    }

    /**
     * Scope: types bloqués (blacklist).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlocked($query)
    {
        return $query->where('decision', self::DECISION_BLOCKED);
    }

    /**
     * Scope: types en attente de validation UX.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('decision', self::DECISION_PENDING);
    }

    /**
     * Indique si un typeId DofusDB est explicitement autorisé en base.
     *
     * Comportement:
     * - Si le type n'existe pas encore, il est créé en `decision=pending` (à valider via UX)
     *   et la méthode retourne false (sécurité par défaut).
     *
     * @param int $typeId
     * @return bool
     *
     * @example
     * if (ResourceType::isDofusdbTypeAllowed(15)) {
     *   // traiter comme ressource
     * }
     */
    public static function isDofusdbTypeAllowed(int $typeId): bool
    {
        $type = static::where('dofusdb_type_id', $typeId)->first();

        if (!$type) {
            static::touchDofusdbType($typeId);
            return false;
        }

        return $type->decision === self::DECISION_ALLOWED;
    }

    /**
     * Enregistre/actualise un typeId DofusDB détecté (pour revue dans le dashboard).
     *
     * @param int $typeId
     * @param string|null $label Libellé optionnel pour initialiser ou améliorer `name`.
     * @return static
     *
     * @example
     * ResourceType::touchDofusdbType(35, 'Fleur');
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
                'usable' => 1,
                'is_visible' => 'guest',
                'decision' => self::DECISION_PENDING,
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
     * Get the user that created the resource type.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les ressources de ce type.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class, 'resource_type_id');
    }
}
