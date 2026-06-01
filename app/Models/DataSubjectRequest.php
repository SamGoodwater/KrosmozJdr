<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $status
 * @property Carbon $requested_at
 * @property Carbon|null $confirmed_at
 * @property Carbon|null $processed_at
 * @property Carbon|null $expires_at
 * @property array<array-key, mixed>|null $meta
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereUserId($value)
 * @mixin \Eloquent
 */
class DataSubjectRequest extends Model
{
    use HasFactory;

    public const TYPE_EXPORT = 'export';

    public const TYPE_ERASURE = 'erasure';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'requested_at',
        'confirmed_at',
        'processed_at',
        'expires_at',
        'meta',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'processed_at' => 'datetime',
        'expires_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
