<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Entity\Creature;
use App\Models\Type\MonsterRace;
use App\Models\Entity\Scenario;
use App\Models\Entity\Campaign;
use App\Models\Entity\Spell;

/**
 * 
 *
 * @property int $id
 * @property int|null $creature_id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $dofus_version
 * @property bool $auto_update
 * @property int $size
 * @property int|null $monster_race_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read Creature|null $creature
 * @property-read MonsterRace|null $monsterRace
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $spellInvocations
 * @property-read int|null $spell_invocations_count
 * @method static \Database\Factories\Entity\MonsterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereCreatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereMonsterRaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Monster extends Model
{
    /** @use HasFactory<\Database\Factories\MonsterFactory> */
    use HasFactory;

    const SIZE = [
        0 => 'Minuscule',
        1 => 'Petit',
        2 => 'Moyen',
        3 => 'Grand',
        4 => 'Colossal',
        5 => 'Gigantesque',
    ];

    const HOSTILITY = [
        0 => 'Amical',
        1 => 'Curieux',
        2 => 'Neutre',
        3 => 'Hostile',
        4 => 'Aggressif',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'creature_id',
        'official_id',
        'dofusdb_id',
        'dofus_version',
        'auto_update',
        'size',
        'monster_race_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'size' => 'integer',
        'auto_update' => 'boolean',
    ];

    /**
     * Get the creature associated with the monster.
     */
    public function creature()
    {
        return $this->belongsTo(Creature::class, 'creature_id');
    }

    /**
     * Get the race of the monster.
     */
    public function monsterRace()
    {
        return $this->belongsTo(MonsterRace::class, 'monster_race_id');
    }

    /**
     * Les scénarios associés à ce monstre.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'monster_scenario');
    }

    /**
     * Les campagnes associées à ce monstre.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'monster_campaign');
    }

    /**
     * Les sorts d'invocation de ce monstre.
     */
    public function spellInvocations()
    {
        return $this->belongsToMany(Spell::class, 'spell_invocation');
    }
}
