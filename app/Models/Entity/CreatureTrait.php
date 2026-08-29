<?php

namespace App\Models\Entity;

use App\Models\Concerns\HasEntityImageMedia;
use App\Models\Concerns\VisibleToViewer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Référentiel des traits permanents de créature.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read Collection<int, Breed> $breeds
 * @property-read int|null $breeds_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, Specialization> $specializations
 * @property-read int|null $specializations_count
 * @method static \Database\Factories\Entity\CreatureTraitFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreatureTrait visibleToUser(?\App\Models\User $user)
 * @mixin \Eloquent
 */
class CreatureTrait extends Model implements HasMedia
{
    /** @use HasFactory<\\Database\\Factories\\Entity\\CreatureTraitFactory> */
    use HasEntityImageMedia, HasFactory, SoftDeletes, VisibleToViewer;

    public const STATE_RAW = 'raw';

    public const STATE_DRAFT = 'draft';

    public const STATE_AUTO = 'auto';

    public const STATE_PLAYABLE = 'playable';

    public const STATE_ARCHIVED = 'archived';

    public const MEDIA_PATH = 'images/entity/creature-traits';

    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    protected $fillable = ['name', 'description', 'state', 'read_level', 'write_level', 'image', 'created_by'];

    protected $casts = ['read_level' => 'integer', 'write_level' => 'integer'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function creatures()
    {
        return $this->belongsToMany(Creature::class, 'creature_creature_trait');
    }

    public function breeds()
    {
        return $this->belongsToMany(Breed::class, 'breed_creature_trait')->withPivot('level')->withTimestamps();
    }

    public function specializations()
    {
        return $this->belongsToMany(Specialization::class, 'creature_trait_specialization')->withPivot('level')->withTimestamps();
    }
}
