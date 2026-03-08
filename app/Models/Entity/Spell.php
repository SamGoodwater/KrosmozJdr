<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Scenario;
use App\Models\Entity\Campaign;
use App\Models\Type\SpellType;
use App\Models\Entity\Monster;
use App\Models\EffectUsage;
use App\Models\SpellState;
use App\Models\SpellEffect;
use App\Models\Concerns\HasEntityImageMedia;
use App\Support\ElementConstants;
use Spatie\MediaLibrary\HasMedia;

/**
 * 
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $name
 * @property string $description
 * @property string|null $effect
 * @property string|null $area Zone d'impact (déléguée au premier effet lié ; voir Effect::area)
 * @property string $level
 * @property string|null $po_min Portée min (valeur ou formule, ex. "0", "[level]")
 * @property string|null $po_max Portée max (valeur ou formule, ex. "1", "6")
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
 * @property string $resolution_mode
 * @property string|null $attack_characteristic_key
 * @property string|null $save_characteristic_key
 * @property string|null $save_dc_formula
 * @property string|null $save_success_note
 * @property string $state
 * @property int $read_level
 * @property int $write_level
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereNumberBetweenTwoCast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereNumberBetweenTwoCastEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePoMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePoMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePoEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePowerful($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereSightLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell withoutTrashed()
 * @mixin \Eloquent
 */
class Spell extends Model implements HasMedia
{
    /** @use HasFactory<\\Database\\Factories\\SpellFactory> */
    use HasFactory, SoftDeletes, HasEntityImageMedia;

    public const STATE_RAW = 'raw';
    public const STATE_DRAFT = 'draft';
    public const STATE_PLAYABLE = 'playable';
    public const STATE_ARCHIVED = 'archived';

    public const CATEGORY_CLASS = 0;
    public const CATEGORY_CREATURE = 1;
    public const CATEGORY_LEARNABLE = 2;
    public const CATEGORY_CONSUMABLE = 3;

    public const RESOLUTION_ATTACK_ROLL = 'attack_roll';
    public const RESOLUTION_SAVING_THROW = 'saving_throw';
    public const RESOLUTION_AUTO_SUCCESS = 'auto_success';

    /** Répertoire Media Library pour ce modèle. */
    public const MEDIA_PATH = 'images/entity/spells';

    /** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */
    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    /** @deprecated Utiliser ElementConstants::ELEMENT */
    const ELEMENT = ElementConstants::ELEMENT;

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
        'level',
        'po_min',
        'po_max',
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
        'resolution_mode',
        'attack_characteristic_key',
        'save_characteristic_key',
        'save_dc_formula',
        'save_success_note',
        'state',
        'read_level',
        'write_level',
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
        'element' => 'integer',
        'category' => 'integer',
        'powerful' => 'integer',
        'read_level' => 'integer',
        'write_level' => 'integer',
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
     * Les breeds (affichées « Classes ») associées à ce sort.
     */
    public function breeds()
    {
        return $this->belongsToMany(Breed::class, 'breed_spell', 'spell_id', 'breed_id');
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
     * Les effets de ce sort (instances liées aux types d'effet).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<SpellEffect, $this>
     */
    public function spellEffects()
    {
        return $this->hasMany(SpellEffect::class);
    }

    /**
     * Les monstres invoqués par ce sort.
     */
    public function monsters()
    {
        return $this->belongsToMany(Monster::class, 'spell_invocation');
    }

    /**
     * Les états que ce sort peut appliquer (sur cible ou lanceur).
     */
    public function spellStates()
    {
        return $this->belongsToMany(SpellState::class, 'spell_spell_state')
            ->withPivot(['application_mode', 'dofus_effect_id', 'duration', 'dispellable', 'target_mask'])
            ->withTimestamps();
    }

    /**
     * Usages d'effets unifiés (effect_usage) pour ce sort.
     */
    public function effectUsages()
    {
        return $this->morphMany(EffectUsage::class, 'entity');
    }

    /**
     * Portée affichable : "min-max" ou valeur unique à partir de po_min/po_max.
     * 0 = soi-même, 1-1 = cac, 2-6 = plage.
     */
    public function getPoDisplayAttribute(): string
    {
        $min = $this->po_min ?? '1';
        $max = $this->po_max ?? $this->po_min ?? '1';

        return $min === $max ? $min : $min . '-' . $max;
    }

    /**
     * Zone d'impact du sort (déléguée au premier effet lié).
     * La colonne area a été déplacée sur Effect ; cet accesseur assure la rétrocompatibilité.
     *
     * @return string|null Notation zone (point, line-1x9, cross-2, circle-2, rect-3x4) ou null
     */
    public function getAreaAttribute(): ?string
    {
        $usage = $this->effectUsages()->with('effect')->first();

        return $usage?->effect?->area;
    }
}
