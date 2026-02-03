<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Type d'effet de sort (référentiel).
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $category
 * @property string|null $description
 * @property string $value_type
 * @property string|null $element
 * @property string|null $unit
 * @property bool $is_positive
 * @property int $sort_order
 * @property int|null $dofusdb_effect_id
 * @property \Illuminate\Database\Eloquent\Collection<int, SpellEffect> $spellEffects
 */
class SpellEffectType extends Model
{
    protected $table = 'spell_effect_types';

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'value_type',
        'element',
        'unit',
        'is_positive',
        'sort_order',
        'dofusdb_effect_id',
    ];

    protected $casts = [
        'is_positive' => 'boolean',
        'sort_order' => 'integer',
        'dofusdb_effect_id' => 'integer',
    ];

    public const VALUE_TYPE_FIXED = 'fixed';
    public const VALUE_TYPE_DICE = 'dice';
    public const VALUE_TYPE_PERCENT = 'percent';

    public const CATEGORY_DAMAGE = 'damage';
    public const CATEGORY_HEAL = 'heal';
    public const CATEGORY_SHIELD = 'shield';
    public const CATEGORY_AP = 'ap';
    public const CATEGORY_PM = 'pm';
    public const CATEGORY_RANGE = 'range';
    public const CATEGORY_BUFF_STAT = 'buff_stat';
    public const CATEGORY_DEBUFF_STAT = 'debuff_stat';
    public const CATEGORY_BUFF_DAMAGE = 'buff_damage';
    public const CATEGORY_DEBUFF_DAMAGE = 'debuff_damage';
    public const CATEGORY_RESISTANCE = 'resistance';
    public const CATEGORY_STATE = 'state';
    public const CATEGORY_PLACEMENT = 'placement';
    public const CATEGORY_TELEPORT = 'teleport';
    public const CATEGORY_SUMMON = 'summon';
    public const CATEGORY_GLYPH_TRAP = 'glyph_trap';
    public const CATEGORY_ZONE = 'zone';
    public const CATEGORY_CRITICAL = 'critical';
    public const CATEGORY_REFLECT = 'reflect';
    public const CATEGORY_STEAL = 'steal';
    public const CATEGORY_DAMAGE_OVER_TIME = 'damage_over_time';
    public const CATEGORY_HEAL_OVER_TIME = 'heal_over_time';
    public const CATEGORY_LOCK = 'lock';
    public const CATEGORY_LINE_OF_SIGHT = 'line_of_sight';
    public const CATEGORY_INVISIBILITY = 'invisibility';
    public const CATEGORY_PROSPECTING = 'prospecting';
    public const CATEGORY_OTHER = 'other';

    public const ELEMENT_NEUTRAL = 'neutral';
    public const ELEMENT_EARTH = 'earth';
    public const ELEMENT_FIRE = 'fire';
    public const ELEMENT_WATER = 'water';
    public const ELEMENT_AIR = 'air';

    /**
     * @return list<string>
     */
    public static function categories(): array
    {
        return [
            self::CATEGORY_DAMAGE,
            self::CATEGORY_HEAL,
            self::CATEGORY_HEAL_OVER_TIME,
            self::CATEGORY_SHIELD,
            self::CATEGORY_AP,
            self::CATEGORY_PM,
            self::CATEGORY_RANGE,
            self::CATEGORY_BUFF_STAT,
            self::CATEGORY_DEBUFF_STAT,
            self::CATEGORY_BUFF_DAMAGE,
            self::CATEGORY_DEBUFF_DAMAGE,
            self::CATEGORY_RESISTANCE,
            self::CATEGORY_STATE,
            self::CATEGORY_PLACEMENT,
            self::CATEGORY_TELEPORT,
            self::CATEGORY_SUMMON,
            self::CATEGORY_GLYPH_TRAP,
            self::CATEGORY_ZONE,
            self::CATEGORY_CRITICAL,
            self::CATEGORY_REFLECT,
            self::CATEGORY_STEAL,
            self::CATEGORY_DAMAGE_OVER_TIME,
            self::CATEGORY_LOCK,
            self::CATEGORY_LINE_OF_SIGHT,
            self::CATEGORY_INVISIBILITY,
            self::CATEGORY_PROSPECTING,
            self::CATEGORY_OTHER,
        ];
    }

    /**
     * @return list<string>
     */
    public static function valueTypes(): array
    {
        return [self::VALUE_TYPE_FIXED, self::VALUE_TYPE_DICE, self::VALUE_TYPE_PERCENT];
    }

    /**
     * @return list<string>
     */
    public static function elements(): array
    {
        return ['', self::ELEMENT_NEUTRAL, self::ELEMENT_EARTH, self::ELEMENT_FIRE, self::ELEMENT_WATER, self::ELEMENT_AIR];
    }

    public function spellEffects(): HasMany
    {
        return $this->hasMany(SpellEffect::class, 'spell_effect_type_id');
    }
}
