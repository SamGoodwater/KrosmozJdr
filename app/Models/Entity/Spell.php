<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Entity\Classe;
use App\Models\Entity\Creature;
use App\Models\Entity\Scenario;
use App\Models\Entity\Campaign;
use App\Models\Type\SpellType;
use App\Models\Entity\Monster;

/**
 * 
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $name
 * @property string $description
 * @property string|null $effect
 * @property int $area
 * @property string $level
 * @property string $po
 * @property bool $po_editable
 * @property string $pa
 * @property string $cast_per_turn
 * @property string $cast_per_target
 * @property bool $sight_line
 * @property string $number_between_two_cast
 * @property bool $number_between_two_cast_editable
 * @property int $element
 * @property int $category
 * @property bool $is_magic
 * @property int $powerful
 * @property int $usable
 * @property string $is_visible
 * @property string|null $image
 * @property bool $auto_update
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SpellType> $spellTypes
 * @property-read int|null $spell_types_count
 * @method static \Database\Factories\Entity\SpellFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastPerTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastPerTurn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereElement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereIsMagic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereNumberBetweenTwoCast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereNumberBetweenTwoCastEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePoEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePowerful($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereSightLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell withoutTrashed()
 * @mixin \Eloquent
 */
class Spell extends Model
{
    /** @use HasFactory<\\Database\\Factories\\SpellFactory> */
    use HasFactory, SoftDeletes;

    const ELEMENT = [
        0 => 'Neutre',
        1 => 'Terre',
        2 => 'Feu',
        3 => 'Air',
        4 => 'Eau',
        5 => 'Neutre-Terre',
        6 => 'Neutre-Feu',
        7 => 'Neutre-Air',
        8 => 'Neutre-Eau',
        9 => 'Terre-Feu',
        10 => 'Terre-Air',
        11 => 'Terre-Eau',
        12 => 'Feu-Air',
        13 => 'Feu-Eau',
        14 => 'Air-Eau',
        15 => 'Neutre-Terre-Feu',
        16 => 'Neutre-Terre-Air',
        17 => 'Neutre-Terre-Eau',
        18 => 'Neutre-Feu-Air',
        19 => 'Neutre-Feu-Eau',
        20 => 'Neutre-Air-Eau',
        21 => 'Terre-Feu-Air',
        22 => 'Terre-Feu-Eau',
        23 => 'Terre-Air-Eau',
        24 => 'Feu-Air-Eau',
        25 => 'Neutre-Terre-Feu-Air',
        26 => 'Neutre-Terre-Feu-Eau',
        27 => 'Neutre-Terre-Air-Eau',
        28 => 'Neutre-Feu-Air-Eau',
        29 => 'Neutre-Terre-Feu-Air-Eau',

    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'official_id',
        'dofusdb_id',
        'name',
        'description',
        'effect',
        'area',
        'level',
        'po',
        'po_editable',
        'pa',
        'cast_per_turn',
        'cast_per_target',
        'sight_line',
        'number_between_two_cast',
        'number_between_two_cast_editable',
        'element',
        'category',
        'is_magic',
        'powerful',
        'usable',
        'is_visible',
        'image',
        'auto_update',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'area' => 'integer',
        'element' => 'integer',
        'category' => 'integer',
        'powerful' => 'integer',
        'usable' => 'integer',
        'po_editable' => 'boolean',
        'sight_line' => 'boolean',
        'number_between_two_cast_editable' => 'boolean',
        'is_magic' => 'boolean',
        'auto_update' => 'boolean',
    ];

    /**
     * Get the user that created the spell.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les classes associées à ce sort.
     */
    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'class_spell', 'spell_id', 'classe_id');
    }

    /**
     * Les créatures associées à ce sort.
     */
    public function creatures()
    {
        return $this->belongsToMany(Creature::class, 'creature_spell');
    }
    /**
     * Les scénarios associés à ce sort.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'scenario_spell');
    }
    /**
     * Les campagnes associées à ce sort.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_spell');
    }
    /**
     * Les types de ce sort.
     */
    public function spellTypes()
    {
        return $this->belongsToMany(SpellType::class, 'spell_type');
    }
    /**
     * Les monstres invoqués par ce sort.
     */
    public function monsters()
    {
        return $this->belongsToMany(Monster::class, 'spell_invocation');
    }
}
