<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Caractéristique générale : propriétés communes et id unique.
 * Une ligne = une caractéristique (ex. PA créature, PA sort, PA objet = 3 lignes).
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $short_name
 * @property string|null $helper
 * @property string|null $descriptions
 * @property string|null $icon
 * @property string|null $color
 * @property string|null $unit
 * @property string $type
 * @property int $sort_order
 */
class Characteristic extends Model
{
    protected $table = 'characteristics';

    /** @var list<string> */
    protected $fillable = [
        'key',
        'name',
        'short_name',
        'helper',
        'descriptions',
        'icon',
        'color',
        'unit',
        'type',
        'sort_order',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function creatureRows(): HasMany
    {
        return $this->hasMany(CharacteristicCreature::class, 'characteristic_id');
    }

    public function objectRows(): HasMany
    {
        return $this->hasMany(CharacteristicObject::class, 'characteristic_id');
    }

    public function spellRows(): HasMany
    {
        return $this->hasMany(CharacteristicSpell::class, 'characteristic_id');
    }
}
