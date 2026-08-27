<?php

namespace App\Models\Entity;

use App\Models\Concerns\HasEntityImageMedia;
use App\Models\Concerns\VisibleToViewer;
use App\Models\User;
use App\Support\DofusHyperlinkText;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Référentiel canonique des états/conditions de jeu.
 *
 * @property int $id
 * @property int|null $dofusdb_id
 * @property int|null $canonical_condition_id
 * @property string $name
 * @property string|null $description
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $icon
 * @property string|null $image
 * @property bool $dissipable
 * @property array<array-key, mixed>|null $raw
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read Condition|null $canonical
 * @property-read Collection<int, Condition> $aliases
 * @property-read Collection<int, Creature> $creatures
 * @property-read Collection<int, Spell> $spells
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
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
 * @property-read int|null $creatures_count
 * @property-read int|null $spells_count
 *
 * @method static \Database\Factories\Entity\ConditionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCantBeMoved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCantBePushed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCantBeTackled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCantDealDamage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCantSwitchPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCantTackle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereDisplayTurnRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereDissipable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereIncurable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereInvulnerable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereInvulnerableMelee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereInvulnerableRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereIsMainState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition wherePreventsFight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition wherePreventsSpellCast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition visibleToUser(?\App\Models\User $user)
 *
 * @mixin \Eloquent
 */
class Condition extends Model implements HasMedia
{
    /** @use HasFactory<\\Database\\Factories\\Entity\\ConditionFactory> */
    use HasEntityImageMedia, HasFactory, SoftDeletes, VisibleToViewer;

    public const STATE_RAW = 'raw';

    public const STATE_DRAFT = 'draft';

    public const STATE_AUTO = 'auto';

    public const STATE_PLAYABLE = 'playable';

    public const STATE_ARCHIVED = 'archived';

    public const MEDIA_PATH = 'images/entity/conditions';

    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    /**
     * Flags mécaniques (combat) exposés au JDR, hors métadonnées d’UI Dofus.
     *
     * @var array<string, string>
     */
    public const MECHANICAL_FLAG_LABELS = [
        'prevents_spell_cast' => 'Empêche de lancer des sorts',
        'prevents_fight' => 'Empêche de combattre',
        'cant_be_moved' => 'Ne peut pas être déplacé',
        'cant_be_pushed' => 'Ne peut pas être poussé',
        'cant_deal_damage' => 'Ne peut pas infliger de dégâts',
        'invulnerable' => 'Invulnérable',
        'cant_switch_position' => 'Ne peut pas échanger de position',
        'incurable' => 'Incurable',
        'invulnerable_melee' => 'Invulnérable au corps à corps',
        'invulnerable_range' => 'Invulnérable à distance',
        'cant_tackle' => 'Ne peut pas tacler',
        'cant_be_tackled' => 'Ne peut pas être taclé',
    ];

    /** @var list<string> */
    protected $fillable = ['dofusdb_id', 'canonical_condition_id', 'name', 'description', 'state', 'read_level', 'write_level', 'icon', 'image', 'prevents_spell_cast', 'prevents_fight', 'cant_be_moved', 'cant_be_pushed', 'cant_deal_damage', 'invulnerable', 'cant_switch_position', 'incurable', 'invulnerable_melee', 'invulnerable_range', 'cant_tackle', 'cant_be_tackled', 'display_turn_remaining', 'is_main_state', 'dissipable', 'raw', 'created_by'];

    /** @var array<string, string> */
    protected $casts = ['dofusdb_id' => 'integer', 'canonical_condition_id' => 'integer', 'read_level' => 'integer', 'write_level' => 'integer', 'prevents_spell_cast' => 'boolean', 'prevents_fight' => 'boolean', 'cant_be_moved' => 'boolean', 'cant_be_pushed' => 'boolean', 'cant_deal_damage' => 'boolean', 'invulnerable' => 'boolean', 'cant_switch_position' => 'boolean', 'incurable' => 'boolean', 'invulnerable_melee' => 'boolean', 'invulnerable_range' => 'boolean', 'cant_tackle' => 'boolean', 'cant_be_tackled' => 'boolean', 'display_turn_remaining' => 'boolean', 'is_main_state' => 'boolean', 'dissipable' => 'boolean', 'raw' => 'array'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * État JDR de base équivalent (Pesanteur, Empoisonné, …), si le scrap a un canon.
     *
     * @example
     * $raw->canonical?->name;
     */
    public function canonical(): BelongsTo
    {
        return $this->belongsTo(self::class, 'canonical_condition_id');
    }

    /**
     * Jetons Dofus qui pointent vers cet état de base.
     *
     * @example
     * $pesanteur->aliases()->count();
     */
    public function aliases(): HasMany
    {
        return $this->hasMany(self::class, 'canonical_condition_id');
    }

    public function creatures()
    {
        return $this->belongsToMany(Creature::class, 'condition_creature');
    }

    public function spells()
    {
        return $this->belongsToMany(Spell::class, 'condition_spell')->withPivot(['application_mode', 'dofus_effect_id', 'duration', 'dispellable', 'target_mask'])->withTimestamps();
    }

    /**
     * Valeurs booléennes des flags mécaniques.
     *
     * @return array<string, bool>
     *
     * @example
     * $condition->mechanicalFlagValues()['cant_be_moved'];
     */
    public function mechanicalFlagValues(): array
    {
        $out = [];
        foreach (array_keys(self::MECHANICAL_FLAG_LABELS) as $key) {
            $out[$key] = (bool) $this->{$key};
        }

        return $out;
    }

    /**
     * Flags mécaniques actifs, avec libellé JDR.
     *
     * @return list<array{key: string, label: string}>
     *
     * @example
     * $condition->activeMechanicalFlags();
     * // [['key' => 'cant_be_moved', 'label' => 'Ne peut pas être déplacé']]
     */
    public function activeMechanicalFlags(): array
    {
        $out = [];
        foreach (self::MECHANICAL_FLAG_LABELS as $key => $label) {
            if ((bool) $this->{$key}) {
                $out[] = ['key' => $key, 'label' => $label];
            }
        }

        return $out;
    }

    /**
     * Nettoie les hyperliens DofusDB (`{{spell,…::Libellé}}`) à l’écriture.
     */
    public function setNameAttribute(?string $value): void
    {
        $this->attributes['name'] = DofusHyperlinkText::toDisplayLabel($value);
    }
}
