<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Entity\Spell;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Référentiel local des états DofusDB appliqués par les sorts.
 */
class SpellState extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'dofusdb_id',
        'name',
        'icon',
        'image',
        'prevents_spell_cast',
        'prevents_fight',
        'cant_be_moved',
        'cant_be_pushed',
        'cant_deal_damage',
        'invulnerable',
        'cant_switch_position',
        'incurable',
        'invulnerable_melee',
        'invulnerable_range',
        'cant_tackle',
        'cant_be_tackled',
        'display_turn_remaining',
        'is_main_state',
        'raw',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'dofusdb_id' => 'integer',
        'prevents_spell_cast' => 'boolean',
        'prevents_fight' => 'boolean',
        'cant_be_moved' => 'boolean',
        'cant_be_pushed' => 'boolean',
        'cant_deal_damage' => 'boolean',
        'invulnerable' => 'boolean',
        'cant_switch_position' => 'boolean',
        'incurable' => 'boolean',
        'invulnerable_melee' => 'boolean',
        'invulnerable_range' => 'boolean',
        'cant_tackle' => 'boolean',
        'cant_be_tackled' => 'boolean',
        'display_turn_remaining' => 'boolean',
        'is_main_state' => 'boolean',
        'raw' => 'array',
    ];

    public function spells()
    {
        return $this->belongsToMany(Spell::class, 'spell_spell_state')
            ->withPivot(['application_mode', 'dofus_effect_id', 'duration', 'dispellable', 'target_mask'])
            ->withTimestamps();
    }
}

