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
 * @property string $reuse_type
 * @property int|null $reuse_amount
 * @property string|null $reuse_unit
 * @property string $duration_type
 * @property int|null $duration_amount
 * @property string|null $duration_unit
 * @property string|null $duration_note
 * @property string $range_type
 * @property int|null $range_amount
 * @property string|null $range_unit
 * @property string|null $range_sense
 * @property string|null $range_note
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
 *
 * @method static \Database\Factories\Entity\AptitudeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Aptitude visibleToUser(?\App\Models\User $user)
 *
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

    public const REUSE_UNLIMITED = 'unlimited';

    public const REUSE_SHORT_REST = 'short_rest';

    public const REUSE_LONG_REST = 'long_rest';

    public const REUSE_TIMED = 'timed';

    public const DURATION_INSTANT = 'instant';

    public const DURATION_TIMED = 'timed';

    public const DURATION_PERMANENT = 'permanent';

    public const DURATION_WHILE_CONDITION = 'while_condition';

    public const RANGE_SELF = 'self';

    public const RANGE_DISTANCE = 'distance';

    public const RANGE_SENSE = 'sense';

    public const MEDIA_PATH = 'images/entity/aptitudes';

    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    protected $fillable = [
        'name',
        'description',
        'effect',
        'reuse_type',
        'reuse_amount',
        'reuse_unit',
        'duration_type',
        'duration_amount',
        'duration_unit',
        'duration_note',
        'range_type',
        'range_amount',
        'range_unit',
        'range_sense',
        'range_note',
        'state',
        'read_level',
        'write_level',
        'image',
        'created_by',
    ];

    protected $casts = [
        'reuse_amount' => 'integer',
        'duration_amount' => 'integer',
        'range_amount' => 'integer',
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
