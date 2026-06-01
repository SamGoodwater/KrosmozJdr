<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ObjectEffectAction;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Resource;
use Database\Factories\ObjectEffectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Effet simple lié à un objet jeu (item, consommable, ressource) : action + cible optionnelle + valeur optionnelle.
 *
 * @property int $id
 * @property string $object_effectable_type
 * @property int $object_effectable_id
 * @property ObjectEffectAction $action
 * @property int|null $characteristic_id
 * @property int|null $monster_id
 * @property int|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Characteristic|null $characteristic
 * @property-read Monster|null $monster
 * @property-read Model|\Eloquent $objectEffectable
 * @method static \Database\Factories\ObjectEffectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ObjectEffect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ObjectEffect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ObjectEffect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ObjectEffect whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ObjectEffect whereCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ObjectEffect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ObjectEffect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ObjectEffect whereMonsterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ObjectEffect whereObjectEffectableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ObjectEffect whereObjectEffectableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ObjectEffect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ObjectEffect whereValue($value)
 * @mixin \Eloquent
 */
class ObjectEffect extends Model
{
    /** @use HasFactory<ObjectEffectFactory> */
    use HasFactory;

    /**
     * Alias court (item, consumable, resource) → classe Eloquent, pour les formulaires / API.
     */
    public static function entityTypeToClass(string $shortType): ?string
    {
        return match ($shortType) {
            'item' => Item::class,
            'consumable' => Consumable::class,
            'resource' => Resource::class,
            default => null,
        };
    }

    protected $fillable = [
        'object_effectable_type',
        'object_effectable_id',
        'action',
        'characteristic_id',
        'monster_id',
        'value',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'action' => ObjectEffectAction::class,
            'characteristic_id' => 'integer',
            'monster_id' => 'integer',
            'value' => 'integer',
        ];
    }

    public function objectEffectable(): MorphTo
    {
        return $this->morphTo();
    }

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }

    public function monster(): BelongsTo
    {
        return $this->belongsTo(Monster::class);
    }
}
