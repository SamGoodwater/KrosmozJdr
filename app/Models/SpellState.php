<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Entity\Spell;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Référentiel local des états DofusDB appliqués par les sorts.
 *
 * @property int $id
 * @property int $dofusdb_id
 * @property string|null $name
 * @property string|null $icon
 * @property string|null $image
 * @property bool $prevents_spell_cast
 * @property bool $prevents_fight
 * @property bool $cant_be_moved
 * @property bool $cant_be_pushed
 * @property bool $cant_deal_damage
 * @property bool $invulnerable
 * @property bool $cant_switch_position
 * @property bool $incurable
 * @property bool $invulnerable_melee
 * @property bool $invulnerable_range
 * @property bool $cant_tackle
 * @property bool $cant_be_tackled
 * @property bool $display_turn_remaining
 * @property bool $is_main_state
 * @property array<array-key, mixed>|null $raw
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCantBeMoved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCantBePushed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCantBeTackled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCantDealDamage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCantSwitchPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCantTackle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereDisplayTurnRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereIncurable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereInvulnerable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereInvulnerableMelee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereInvulnerableRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereIsMainState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState wherePreventsFight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState wherePreventsSpellCast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereUpdatedAt($value)
 * @mixin \Eloquent
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
