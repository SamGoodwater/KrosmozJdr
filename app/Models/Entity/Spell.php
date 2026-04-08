<?php

namespace App\Models\Entity;

use App\Models\Concerns\HasEntityImageMedia;
use App\Models\Effect;
use App\Models\SpellEffect;
use App\Models\SpellState;
use App\Models\Type\SpellType;
use App\Models\User;
use App\Support\AreaConstants;
use App\Support\ElementConstants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $name
 * @property string $description Toujours non null en base ; une valeur API `null` est normalisée en chaîne vide.
 * @property string|null $effect
 * @property string $level
 * @property string|null $po_min Portée min (valeur ou formule, ex. "0", "[level]")
 * @property string|null $po_max Portée max (valeur ou formule, ex. "1", "6")
 * @property bool $po_editable
 * @property string $pa
 * @property string $cast_per_turn
 * @property string $cast_per_target
 * @property bool $sight_line
 * @property string $number_between_two_cast
 * @property int $element
 * @property int $category
 * @property bool $is_magic
 * @property int $powerful
 * @property string $resolution_mode
 * @property string|null $attack_characteristic_key
 * @property string|null $save_characteristic_key
 * @property string|null $save_dc_formula
 * @property string|null $save_success_note
 * @property bool $auto_success_if_willing_target Réussite auto si la cible est consentante
 * @property bool $allows_reaction Utilisable comme réaction de combat (PA non récupérés au tour suivant)
 * @property string|null $casting_time Temps d'incantation (texte libre)
 * @property bool|null $ritual_available Utilisable en mode rituel (null = non renseigné)
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
 *
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
 *
 * @mixin \Eloquent
 */
class Spell extends Model implements HasMedia
{
    /** @use HasFactory<\\Database\\Factories\\SpellFactory> */
    use HasEntityImageMedia, HasFactory, SoftDeletes;

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

    /** @deprecated Utiliser AreaConstants::SHAPE_ID_MAP */
    public const AREAS_SHAPE = AreaConstants::SHAPE_ID_MAP;

    /** Répertoire Media Library pour ce modèle. */
    public const MEDIA_PATH = 'images/entity/spells';

    /** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */
    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    /** @deprecated Utiliser ElementConstants::ELEMENT */
    const ELEMENT = ElementConstants::ELEMENT;

    /**
     * Colonnes NOT NULL : les FormRequest acceptent souvent `nullable` alors que MySQL refuse NULL.
     * Valeurs alignées sur les défauts du schéma (migrations `entity_spells` et colonnes ajoutées ensuite).
     *
     * @var array<string, bool|int|string>
     */
    private const ATTRIBUTE_FALLBACK_WHEN_NULL = [
        'description' => '',
        'level' => '1',
        'po_min' => '1',
        'po_max' => '1',
        'pa' => '3',
        'cast_per_turn' => '1',
        'cast_per_target' => '0',
        'number_between_two_cast' => '0',
        'po_editable' => true,
        'sight_line' => true,
        'element' => 0,
        'category' => 0,
        'is_magic' => true,
        'powerful' => 0,
        'resolution_mode' => self::RESOLUTION_ATTACK_ROLL,
        'state' => self::STATE_DRAFT,
        'read_level' => 0,
        'write_level' => 3,
        'auto_update' => true,
        'auto_success_if_willing_target' => false,
        'allows_reaction' => false,
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
        'level',
        'po_min',
        'po_max',
        'po_editable',
        'pa',
        'casting_time',
        'ritual_available',
        'cast_per_turn',
        'cast_per_target',
        'sight_line',
        'number_between_two_cast',
        'element',
        'category',
        'is_magic',
        'powerful',
        'resolution_mode',
        'attack_characteristic_key',
        'save_characteristic_key',
        'save_dc_formula',
        'save_success_note',
        'auto_success_if_willing_target',
        'allows_reaction',
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
        'is_magic' => 'boolean',
        'auto_update' => 'boolean',
        'auto_success_if_willing_target' => 'boolean',
        'allows_reaction' => 'boolean',
        'ritual_available' => 'boolean',
    ];

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if ($value === null && array_key_exists($key, self::ATTRIBUTE_FALLBACK_WHEN_NULL)) {
            $value = self::ATTRIBUTE_FALLBACK_WHEN_NULL[$key];
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Stockage texte : évite les types non string et les avertissements PHP 8.4+ sur (string) null.
     */
    public function setDescriptionAttribute(mixed $value): void
    {
        $this->attributes['description'] = $value === null ? '' : (string) $value;
    }

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
     * Définitions d’effets liées à ce sort (pivot effect_spell).
     */
    public function effects()
    {
        return $this->belongsToMany(Effect::class, 'effect_spell');
    }

    /**
     * Portée affichable : "min-max" ou valeur unique à partir de po_min/po_max.
     * 0 = soi-même, 1-1 = cac, 2-6 = plage.
     */
    public function getPoDisplayAttribute(): string
    {
        $min = $this->po_min ?? '1';
        $max = $this->po_max ?? $this->po_min ?? '1';

        return $min === $max ? $min : $min.' - '.$max;
    }

    /**
     * Zone d’impact affichée (premier degré du premier effet lié).
     *
     * @return string|null Notation zone (point, line-1x9, …) ou null
     */
    public function getAreaAttribute(): ?string
    {
        $effect = $this->effects()->with('degrees')->first();
        $deg = $effect?->degrees->sortBy('degree')->first();

        return $deg?->area;
    }
}
