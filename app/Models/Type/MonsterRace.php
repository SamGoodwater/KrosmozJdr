<?php

namespace App\Models\Type;

use App\Models\Entity\Monster;
use App\Models\Type\Concerns\HasTypeRegistryFlags;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $dofusdb_race_id
 * @property string $name
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $id_super_race
 * @property-read User|null $createdBy
 * @property-read Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @property-read Collection<int, MonsterRace> $subRaces
 * @property-read int|null $sub_races_count
 * @property-read MonsterRace|null $superRace
 *
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereDofusdbRaceId($value)
 *
 * @property bool|null $show_in_catalog
 * @property bool|null $allow_scrap
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace allowScrap()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace allowed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace visibleInCatalog()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereAllowScrap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereShowInCatalog($value)
 *
 * @mixin \Eloquent
 */
class MonsterRace extends Model
{
    /** @use HasFactory<\\Database\\Factories\\MonsterRaceFactory> */
    use HasFactory, HasTypeRegistryFlags, SoftDeletes;

    public const STATE_RAW = 'raw';

    public const STATE_DRAFT = 'draft';

    public const STATE_AUTO = 'auto';

    public const STATE_PLAYABLE = 'playable';

    public const STATE_ARCHIVED = 'archived';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'show_in_catalog' => false,
        'allow_scrap' => false,
    ];

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
        'show_in_catalog',
        'allow_scrap',
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
        'show_in_catalog' => 'boolean',
        'allow_scrap' => 'boolean',
    ];

    /**
     * Enregistre/actualise une race DofusDB vue pendant le scrapping.
     */
    public static function touchDofusdbRace(int $dofusdbRaceId, ?string $name = null): void
    {
        $dofusdbRaceId = (int) $dofusdbRaceId;
        if ($dofusdbRaceId === 0) {
            return;
        }

        try {
            $model = self::query()->where('dofusdb_race_id', $dofusdbRaceId)->first();
            if (! $model) {
                $model = new self;
                $model->dofusdb_race_id = $dofusdbRaceId;
                $model->name = $name ?: ("DofusDB race #{$dofusdbRaceId}");
                $model->state = self::STATE_DRAFT;
                $model->show_in_catalog = false;
                $model->allow_scrap = false;
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
