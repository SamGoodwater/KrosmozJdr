<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Effet appliqué à un sort (instance).
 *
 * @property int $id
 * @property int $spell_id
 * @property int $spell_effect_type_id
 * @property int|null $value_min
 * @property int|null $value_max
 * @property int|null $dice_num
 * @property int|null $dice_side
 * @property int|null $duration
 * @property string $target_scope
 * @property string|null $zone_shape
 * @property bool $dispellable
 * @property int $order
 * @property string|null $raw_description
 * @property int|null $summon_monster_id
 * @property-read Spell $spell
 * @property-read SpellEffectType $spellEffectType
 * @property-read Monster|null $summonMonster
 */
class SpellEffect extends Model
{
    protected $table = 'spell_effects';

    protected $fillable = [
        'spell_id',
        'spell_effect_type_id',
        'value_min',
        'value_max',
        'dice_num',
        'dice_side',
        'duration',
        'target_scope',
        'zone_shape',
        'dispellable',
        'order',
        'raw_description',
        'summon_monster_id',
    ];

    protected $casts = [
        'value_min' => 'integer',
        'value_max' => 'integer',
        'dice_num' => 'integer',
        'dice_side' => 'integer',
        'duration' => 'integer',
        'dispellable' => 'boolean',
        'order' => 'integer',
        'summon_monster_id' => 'integer',
    ];

    public const TARGET_SELF = 'self';
    public const TARGET_ALLY = 'ally';
    public const TARGET_ENEMY = 'enemy';
    public const TARGET_CELL = 'cell';
    public const TARGET_ZONE = 'zone';

    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class);
    }

    public function spellEffectType(): BelongsTo
    {
        return $this->belongsTo(SpellEffectType::class);
    }

    public function summonMonster(): BelongsTo
    {
        return $this->belongsTo(Monster::class, 'summon_monster_id');
    }
}
