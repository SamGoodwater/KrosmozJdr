<?php

namespace App\Models\Type;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Entity\Monster;

/**
 * 
 *
 * @property int $id
 * @property int|null $dofusdb_race_id
 * @property string $name
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $id_super_race
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MonsterRace> $subRaces
 * @property-read int|null $sub_races_count
 * @property-read MonsterRace|null $superRace
 * @method static \Database\Factories\Type\MonsterRaceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereIdSuperRace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace withoutTrashed()
 * @mixin \Eloquent
 */
class MonsterRace extends Model
{
    /** @use HasFactory<\\Database\\Factories\\MonsterRaceFactory> */
    use HasFactory, SoftDeletes;

    public const STATE_RAW = 'raw';
    public const STATE_DRAFT = 'draft';
    public const STATE_PLAYABLE = 'playable';
    public const STATE_ARCHIVED = 'archived';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'dofusdb_race_id',
        'name',
        'state',
        'read_level',
        'write_level',
        'created_by',
        'id_super_race',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_level' => 'integer',
        'write_level' => 'integer',
        'dofusdb_race_id' => 'integer',
    ];

    /**
     * Enregistre/actualise une race DofusDB vue pendant le scrapping.
     */
    public static function touchDofusdbRace(int $dofusdbRaceId, ?string $name = null): void
    {
        $dofusdbRaceId = (int) $dofusdbRaceId;
        if ($dofusdbRaceId === 0) return;

        try {
            $model = self::query()->where('dofusdb_race_id', $dofusdbRaceId)->first();
            if (!$model) {
                $model = new self();
                $model->dofusdb_race_id = $dofusdbRaceId;
                $model->name = $name ?: ("DofusDB race #{$dofusdbRaceId}");
                $model->state = self::STATE_DRAFT;
                $model->read_level = 0;
                $model->write_level = 3;
                $model->created_by = null;
                $model->id_super_race = null;
                $model->save();
                return;
            }

            if (is_string($name) && $name !== '' && $model->name !== $name) {
                $model->name = $name;
                $model->save();
            }
        } catch (\Throwable) {
            // best effort
        }
    }

    /**
     * Get the user that created the monster race.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the super race (parent race) of this monster race.
     */
    public function superRace()
    {
        return $this->belongsTo(MonsterRace::class, 'id_super_race');
    }

    /**
     * Les monstres de cette race.
     */
    public function monsters()
    {
        return $this->hasMany(Monster::class, 'monster_race_id');
    }

    /**
     * Les sous-races de cette race.
     */
    public function subRaces()
    {
        return $this->hasMany(MonsterRace::class, 'id_super_race');
    }
}
