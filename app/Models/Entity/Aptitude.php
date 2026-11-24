<?php

namespace App\Models\Entity;

use App\Models\Concerns\HasEntityImageMedia;
use App\Models\Concerns\VisibleToViewer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Aptitude de spécialisation : bonus naturel (hors PA / élément / Wakfu).
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $effect
 * @property string|null $reuse
 * @property string|null $duration
 * @property string|null $range
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read Collection<int, Specialization> $specializations
 * @property-read MediaCollection<int, Media> $media
 * @method static \Database\Factories\Entity\AptitudeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude visibleToUser(?\App\Models\User $user)
 * @property-read int|null $media_count
 * @property-read int|null $specializations_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereReuse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude withoutTrashed()
 * @mixin \Eloquent
 */
class Aptitude extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\Entity\AptitudeFactory> */
    use HasEntityImageMedia, HasFactory, SoftDeletes, VisibleToViewer;

    public const STATE_RAW = 'raw';

    public const STATE_DRAFT = 'draft';

    public const STATE_AUTO = 'auto';

    public const STATE_PLAYABLE = 'playable';

    public const STATE_ARCHIVED = 'archived';

    public const MEDIA_PATH = 'images/entity/aptitudes';

    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    protected $fillable = [
        'name',
        'description',
        'effect',
        'reuse',
        'duration',
        'range',
        'state',
        'read_level',
        'write_level',
        'image',
        'created_by',
    ];

    protected $casts = [
        'read_level' => 'integer',
        'write_level' => 'integer',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(Specialization::class, 'aptitude_specialization')
            ->withPivot('level')
            ->withTimestamps();
    }
}
