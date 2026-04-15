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
